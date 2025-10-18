<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk menangani alokasi Nomor Antrian dan Tanggal Kuota kunjungan.
 */
class AntrianService
{
    private const MAX_QUOTA_PER_DAY = 5;

    /**
     * Mengalokasikan nomor antrian dan tanggal kuota kunjungan berikutnya.
     * Jika kuota hari ini penuh (5 pasien), akan dialokasikan ke hari berikutnya.
     *
     * @param PengajuanSurat $pengajuan Model PengajuanSurat yang sudah sukses bayar.
     * @return array Hasil alokasi.
     */
    public function alokasiAntrian(PengajuanSurat $pengajuan): array
    {
        $limit = self::MAX_QUOTA_PER_DAY;
        // Gunakan Waktu Jakarta
        $currentDate = Carbon::now('Asia/Jakarta'); 

        DB::beginTransaction(); // Mulai transaksi manual

        try {
            Log::info("ANTRIAN DEBUG: Memulai alokasi antrian untuk Order ID: {$pengajuan->midtrans_order_id}.");

            // 1. Tentukan tanggal kunjungan dan nomor antrian
            $tanggalKunjungan = $currentDate->copy();
            $nomorAntrian = 1;

            // Loop untuk mencari kuota kosong
            while (true) {
                // Skip Hari Sabtu dan Minggu (Asumsi layanan libur)
                if ($tanggalKunjungan->isWeekend()) {
                    $tanggalKunjungan->addDay();
                    continue; 
                }

                // Hitung kuota yang sudah terisi untuk tanggal ini
                $countToday = PengajuanSurat::where('tanggal_kuota', $tanggalKunjungan->toDateString())
                    ->whereIn('payment_status', ['SUCCESS', 'SETTLEMENT'])
                    ->count();

                Log::info("ANTRIAN DEBUG: Tanggal [{$tanggalKunjungan->toDateString()}]: Kuota terisi {$countToday}/{$limit}.");

                if ($countToday < $limit) {
                    $nomorAntrian = $countToday + 1;
                    break; // Slot ditemukan
                }

                // Kuota penuh, pindah ke hari berikutnya
                $tanggalKunjungan->addDay();
            }

            // 2. Lakukan Update Data pada Model
            $pengajuan->nomor_antrian = $nomorAntrian;
            $pengajuan->tanggal_kuota = $tanggalKunjungan->toDateString();
            
            // Lakukan update payment_status ke SETTLEMENT di sini juga, 
            // memastikan perubahan status akan di-commit
            $pengajuan->payment_status = 'SETTLEMENT'; 

            $pengajuan->save(); 

            DB::commit(); // COMMIT berhasil!

            Log::info("ANTRIAN DEBUG: COMMIT berhasil! Antrian dialokasikan: No. {$nomorAntrian}, Tgl: {$tanggalKunjungan->toDateString()}.");

            return [
                'success' => true,
                'antrian' => $nomorAntrian,
                // Mengembalikan format tanggal yang user-friendly (03 October 2025)
                'tanggal' => $tanggalKunjungan->format('d F Y'), 
            ];

        } catch (\Exception $e) {
            DB::rollBack(); // ROLLBACK jika ada error
            
            // !!! CARI LOG INI DI LARAVEL.LOG !!!
            Log::error("ANTRIAN DEBUG: ROLLBACK GAGAL MENGALOKASIKAN ANTRIAN untuk ID {$pengajuan->id}. ERROR: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'antrian' => null,
                'tanggal' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
    
}
