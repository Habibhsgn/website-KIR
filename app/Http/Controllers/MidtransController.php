<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\PengajuanSurat;
use App\Services\AntrianService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MidtransController extends Controller
{
    protected $antrianService;
    protected $waService;

    public function __construct(AntrianService $antrianService, WhatsAppService $waService)
    {
        $this->antrianService = $antrianService;
        $this->waService = $waService;
    }

    public function notification(Request $request)
    {
        // ***** LOG KRITIS: Jika ini tercetak, berarti Webhook BERHASIL MASUK *****
        Log::info('!!! MIDTRANS ENTRY POINT SUCCESS. Processing webhook now. !!!');
        // ************************************************************************

        // 1. Setup Konfigurasi Midtrans
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');

        // 2. Buat instance Notification
        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::error("Midtrans Notification Setup Error: " . $e->getMessage());
            return response()->json(['message' => 'Notification setup failed'], 500);
        }

        // Ambil data penting
        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;

        // 3. Cari Pengajuan Surat
        $pengajuan = PengajuanSurat::where('midtrans_order_id', $orderId)->first();

        if (!$pengajuan) {
            Log::warning('Pengajuan Surat not found for Order ID: ' . $orderId);
            return response()->json(['message' => 'Pengajuan Surat not found'], 404);
        }

        // Pencegahan proses berulang
        if ($pengajuan->payment_status == 'SUCCESS' && $pengajuan->nomor_antrian != null) {
            Log::info("Order ID {$orderId} already SUCCESS and Antrian allocated, skipping update.");
            return response()->json(['message' => 'Already processed'], 200);
        }

        $newStatus = $pengajuan->payment_status;

        // 4. Tentukan Status Pembayaran Baru
        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $newStatus = 'SETTLEMENT'; // Pemicu Kritis
        } else if ($transactionStatus == 'pending') {
            $newStatus = 'PENDING';
        } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $newStatus = 'FAILED';
        } else if ($transactionStatus == 'challenge') {
            $newStatus = 'CHALLENGE';
        }

        $isSettlement = ($newStatus === 'SETTLEMENT');

        // 5. Update Status Pembayaran
        if ($pengajuan->payment_status != $newStatus) {
            $pengajuan->payment_status = $newStatus;
            $pengajuan->save();
            Log::info("Status Pengajuan ID {$pengajuan->id} (Order ID: {$orderId}) updated to {$newStatus}");
        }

        // 6. Alokasi Antrian dan Kirim Notifikasi (HANYA JIKA SETTLEMENT DAN BELUM DIALOKASIKAN)
        if ($isSettlement && $pengajuan->nomor_antrian === null) {
            Log::info("MIDTRANS DEBUG: SETTLEMENT terdeteksi dan Antrian BELUM dialokasikan. Memanggil AntrianService.");

            $antrianResult = $this->antrianService->alokasiAntrian($pengajuan);

            if ($antrianResult['success']) {
                Log::info("MIDTRANS DEBUG: Alokasi Antrian BERHASIL. Mengirim notifikasi WA kedua.");

                $pengajuan->refresh();

                $this->waService->sendAntrianNotification($pengajuan, $antrianResult);

                // Update status final ke SUCCESS setelah semua proses antrian selesai
                $pengajuan->payment_status = 'SUCCESS';
                $pengajuan->save();
            } else {
                // Log kegagalan alokasi
                Log::error("MIDTRANS DEBUG: Alokasi Antrian GAGAL untuk Order ID: {$orderId}. Cek log ANTRIAN DEBUG di AntrianService.");
            }
        }

        // 7. Respons ke Midtrans (Wajib HTTP 200 OK)
        return response()->json(['message' => 'Notification processed successfully'], 200);
    }

}
