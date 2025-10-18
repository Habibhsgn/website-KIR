<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanSurat;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\WhatsAppService;
use App\Services\AntrianService; // <-- [1] IMPORT AntrianService BARU
use Carbon\Carbon;

class PengajuanSuratController extends Controller
{
    protected $waService;
    protected $antrianService; // <-- [2] Property untuk AntrianService

    /**
     * Constructor untuk injeksi dependensi.
     */
    public function __construct(WhatsAppService $waService, AntrianService $antrianService) // <-- [3] Inject AntrianService
    {
        // Setup Midtrans Config Global
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        $this->waService = $waService;
        $this->antrianService = $antrianService; // <-- [4] Simpan instance
    }

    /**
     * Menyimpan pengajuan surat dan memicu transaksi Midtrans.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $midtransOrderId = null;
        try {
            // 1. Validasi Input
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nik' => 'required|digits:16',
                'jenis_kelamin' => 'required|string|max:10',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'pendidikan' => 'required|string|max:255',
                'pekerjaan' => 'nullable|string|max:255',
                'status' => 'required|string|max:50',
                'agama' => 'required|string|max:50',
                'keperluan' => 'required|string',
                'alamat_detail' => 'required|string',
                'provinsi' => 'required|string',
                'kabupaten' => 'required|string',
                'kecamatan' => 'required|string',
                'desa' => 'required|string',
                'jenis_surat' => 'required|array|min:1',
                'total_harga' => 'required',
                'email' => 'required|email|max:255',
                'no_hp' => 'required|string|max:20',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Harap lengkapi semua kolom yang wajib diisi.',
                'errors' => $e->errors()
            ], 422);
        }

        // Bersihkan harga dan buat Order ID
        $cleanPrice = (int) preg_replace('/[^0-9]/', '', $request->total_harga);

        if ($cleanPrice <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Total harga harus lebih dari nol.'
            ], 400);
        }

        $midtransOrderId = 'PS-' . time() . '-' . rand(100, 999);

        // Mulai Transaksi Database
        DB::beginTransaction();

        try {
            // 2. Simpan Pengajuan Surat ke Database
            $pengajuan = PengajuanSurat::create([
                'nama' => $request->nama,
                'nik' => $request->nik,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'pendidikan' => $request->pendidikan,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'kecamatan' => $request->kecamatan,
                'desa' => $request->desa,
                'pekerjaan' => $request->pekerjaan,
                'status' => $request->status,
                'agama' => $request->agama,
                'no_hp' => $request->no_hp,
                'email' => $request->email,
                'keperluan' => $request->keperluan,
                'alamat_detail' => $request->alamat_detail,
                'jenis_surat' => $request->jenis_surat,
                'total_harga' => $cleanPrice,
                'midtrans_order_id' => $midtransOrderId,
                'payment_status' => 'PENDING',
                // nomor_antrian dan tanggal_kuota dikosongkan dulu
            ]);


            // 3. Setup Midtrans Configuration (diulang untuk safety)
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // 4. Buat Parameter Transaksi Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => $cleanPrice,
                ],
                'customer_details' => [
                    'first_name' => $pengajuan->nama,
                    'email' => $pengajuan->email,
                    'phone' => $pengajuan->no_hp,
                ],
                'callbacks' => [
                    'finish' => env('MIDTRANS_FINISH_URL'),
                    'unfinish' => env('MIDTRANS_UNFINISH_URL'),
                    'error' => env('MIDTRANS_ERROR_URL'),
                ]
            ];

            // 5. Dapatkan Redirect URL dari Midtrans API
            $snapResponse = Snap::createTransaction($params);
            $snapUrl = $snapResponse->redirect_url;

            // 6. Update data di DB dengan Snap URL
            $pengajuan->update([
                'snap_redirect_url' => $snapUrl,
                'snap_token' => $snapResponse->token ?? null,
            ]);

            // COMMIT: Data tersimpan permanen
            DB::commit();

            // 7. Panggil WhatsApp Service untuk Kirim Notifikasi Pembayaran
            $this->waService->sendPaymentNotification($pengajuan, $snapUrl);

            // 8. Kirim Respons JSON ke Frontend
            return response()->json([
                'success' => true,
                'redirect_url' => $snapUrl,
                'message' => 'Transaksi berhasil dibuat. Jangan tutup halaman ini — sistem sedang menyiapkan halaman pembayaran Midtrans.'
            ]);
        } catch (\Exception $e) {
            // ROLLBACK: Batalkan penyimpanan PengajuanSurat
            DB::rollBack();

            Log::error('Midtrans/DB Error for Order ID ' . ($midtransOrderId ?? 'N/A') . ': ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pengajuan. Error sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    // ----------------------------------------------------------------------
    // # METODE REDIRECT USER (GET)
    // ----------------------------------------------------------------------

    /**
     * Menampilkan halaman status pembayaran setelah redirect dari Midtrans (GET).
     */
    public function statusPembayaran(Request $request)
    {
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;

        Log::info("User Redirect (GET) ke halaman status. Order: {$orderId}, Status: {$transactionStatus}.");

        // 🔍 Coba ambil data dari database
        $pengajuan = PengajuanSurat::where('midtrans_order_id', $orderId)->first();

        // 🔸 Default values
        $nomorAntrian = '-';
        $tanggalKunjungan = '-';
        $message = 'Status pembayaran Anda sudah diterima.';

        if ($pengajuan) {
            $nomorAntrian = $pengajuan->nomor_antrian ?? '-';
            $tanggalKunjungan = $pengajuan->tanggal_kuota
                ? Carbon::parse($pengajuan->tanggal_kuota)->translatedFormat('d F Y')
                : '-';
            $message = match (strtoupper($pengajuan->payment_status)) {
                'SETTLEMENT' => 'Pembayaran berhasil dan nomor antrian sudah dialokasi, Silahkan datang ke Rumah Sakit untuk dilakukan pengecekan oleh Dokter/Laboratorium sebelum jam 12:30 WIB',
                'PENDING'    => 'Pembayaran Anda sedang diproses.',
                'FAILED'     => 'Pembayaran gagal atau dibatalkan.',
                default      => 'Status pembayaran tidak diketahui.',
            };
        }

        // 🔸 Kirim semua data ke view
        return view('status_pembayaran', [
            'orderId' => $orderId,
            'status' => strtoupper($transactionStatus ?? $pengajuan->payment_status ?? 'UNKNOWN'),
            'nomorAntrian' => $nomorAntrian,
            'tanggalKunjungan' => $tanggalKunjungan,
            'message' => $message,
        ]);
    }


    // ----------------------------------------------------------------------
    // # METODE WEBHOOK HANDLER (POST)
    // ----------------------------------------------------------------------

    /**
     * Webhook Handler: Menerima notifikasi status pembayaran dari Midtrans (POST /api/midtrans-callback).
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function midtransCallback(Request $request)
    {
        Log::info('=== MIDTRANS WEBHOOK RECEIVED ===');

        try {
            // === 1️⃣ Log semua header untuk debugging ===
            Log::info('Webhook Headers:', $request->headers->all());

            // === 2️⃣ Ambil body mentah (raw JSON) ===
            $raw = $request->getContent();
            Log::info('Raw Payload:', ['raw' => $raw]);

            // === 3️⃣ Decode JSON jika ada ===
            $notification = json_decode($raw, true);

            // === 4️⃣ Jika gagal decode, fallback ke form-data ===
            if (empty($notification)) {
                $notification = $request->all();
                Log::info('Payload form-data detected:', $notification);
            } else {
                Log::info('Decoded JSON Payload:', $notification);
            }

            // === 5️⃣ Cek isi final payload ===
            Log::info('Final Parsed Payload:', $notification);

            // Ambil data penting
            $orderId           = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus       = $notification['fraud_status'] ?? null;
            $statusCode        = $notification['status_code'] ?? null;
            $grossAmount       = $notification['gross_amount'] ?? null;
            $signatureReceived = $notification['signature_key'] ?? null;

            // === 6️⃣ Validasi payload wajib ===
            if (!$orderId || !$transactionStatus) {
                Log::error("❌ Webhook GAGAL: Payload Midtrans tidak lengkap.", $notification);
                return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
            }

            // === 7️⃣ Ambil data order dari DB ===
            $pengajuan = PengajuanSurat::where('midtrans_order_id', $orderId)->first();

            if (!$pengajuan) {
                Log::error("❌ Webhook GAGAL: Order ID {$orderId} tidak ditemukan di database.");
                return response()->json(['status' => 'error', 'message' => 'Order ID not found'], 404);
            }

            // === 8️⃣ Verifikasi signature Midtrans ===
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . Config::$serverKey);

            if ($signatureReceived !== $expectedSignature) {
                Log::error("❌ Invalid signature key untuk Order ID {$orderId}", [
                    'expected' => $expectedSignature,
                    'received' => $signatureReceived,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature key'], 403);
            }

            // === 9️⃣ Tentukan status baru ===
            $newPaymentStatus = match ($transactionStatus) {
                'capture'     => $fraudStatus === 'accept' ? 'SETTLEMENT' : 'PENDING',
                'settlement'  => 'SETTLEMENT',
                'pending'     => 'PENDING',
                'cancel', 'expire', 'deny' => 'FAILED',
                default       => $pengajuan->payment_status,
            };

            // === 🔟 Jika status berubah, proses update ===
            if ($newPaymentStatus !== $pengajuan->payment_status) {
                Log::info("Webhook DITERIMA: Update status Order ID {$orderId} → {$newPaymentStatus}");

                if ($newPaymentStatus === 'SETTLEMENT') {
                    // === ✅ Panggil service antrian jika pembayaran berhasil ===
                    $antrianResult = $this->antrianService->alokasiAntrian($pengajuan);

                    if ($antrianResult['success']) {
                        Log::info("✅ Webhook BERHASIL: Antrian dialokasikan untuk Order ID {$orderId}");
                        $this->waService->sendAntrianNotification($pengajuan, $antrianResult);
                    } else {
                        Log::error("⚠️ Gagal alokasi antrian untuk Order ID {$orderId}", [
                            'error' => $antrianResult['error']
                        ]);
                    }
                } else {
                    $pengajuan->update(['payment_status' => $newPaymentStatus]);
                    Log::info("✅ Status Order ID {$orderId} diperbarui menjadi {$newPaymentStatus}");
                }
            } else {
                Log::info("ℹ️ Tidak ada perubahan status untuk Order ID {$orderId}");
            }

            // === 11️⃣ Response sukses ke Midtrans ===
            return response()->json(['status' => 'ok', 'message' => 'Notification processed'], 200);
        } catch (\Throwable $e) {
            Log::error('❌ Exception di Midtrans Callback:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }
    /**
     * Menampilkan daftar dokter dari tabel dokter di database db_kir.
     */
    public function daftarDokter()
    {
        $dokter = DB::table('dokter')
            ->select('nama', 'jabatan', 'instansi')
            ->orderBy('nama')
            ->get();

        return view('welcome', compact('dokter'));
    }
}
