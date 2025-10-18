<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Tambahkan import Carbon untuk logika tanggal

/**
 * Service untuk menangani semua interaksi dengan WhatsApp Gateway.
 * Saat ini dikonfigurasi untuk API dash.pushwa.com.
 */
class WhatsAppService
{
    protected $client;

    public function __construct()
    {
        // Inisialisasi Guzzle Client
        $this->client = new Client();
    }

    // --- LOGIKA API CALL (DIPAKAI OLEH SEMUA METHOD DI BAWAH) ---

    private function callPushWaApi(string $nomorWa, string $message): void
    {
        try {
            // Kredensial PushWa Dash dari .env
            $url = env('PUSHWA_DASH_URL');
            $token = env('PUSHWA_DASH_TOKEN');

            if (!$url || !$token) {
                Log::warning('PUSHWA_DASH_URL atau PUSHWA_DASH_TOKEN belum dikonfigurasi. Notifikasi WA tidak terkirim.');
                return;
            }

            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'token' => $token, // Token dikirim di body
                    'target' => $nomorWa, // Nomor tujuan (diperlukan 62xxx)
                    'type' => "text",
                    'delay' => "1",
                    'message' => $message, // Isi pesan
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($statusCode === 200 || $statusCode === 201) {
                Log::info("WhatsApp notification sent successfully via PushWa Dash.");
            } else {
                Log::error("Failed to send WA notification ({$statusCode}) via PushWa Dash. Response: " . json_encode($responseBody));
            }
        } catch (\Exception $e) {
            Log::error("WA Gateway (PushWa Dash) connection error: " . $e->getMessage());
        }
    }


    // --- METHOD NOTIFIKASI PEMBAYARAN PERTAMA (DARI PengajuanSuratController) ---

    /**
     * Mengirim detail pembayaran Midtrans Snap URL ke nomor WA pendaftar.
     * @param PengajuanSurat $pengajuan
     * @param string $paymentUrl Midtrans Snap Redirect URL
     */
    public function sendPaymentNotification(PengajuanSurat $pengajuan, string $paymentUrl): void
    {
        $nomorWa = preg_replace('/^0/', '62', $pengajuan->no_hp);
        $nomorWa = str_replace(['-', ' '], '', $nomorWa);

        // langsung gunakan array dari casts
        $jenisSuratArray = $pengajuan->jenis_surat;

        if (is_array($jenisSuratArray) && count($jenisSuratArray) > 0) {
            $daftarSurat = implode("\n- ", $jenisSuratArray);
            $detailSurat = "Jenis Surat yang di-Order:\n- {$daftarSurat}\n\n";
        } else {
            $detailSurat = "Jenis Surat yang di-Order: Tidak ditemukan.\n\n";
        }

        $message = "Halo *{$pengajuan->nama}*,\n\n";
        $message .= "Terima kasih telah mengajukan surat. Berikut detail surat yang Anda ajukan:\n\n";
        $message .= $detailSurat;
        $message .= "Order ID: *{$pengajuan->midtrans_order_id}*\n";
        $message .= "Total Pembayaran: *Rp" . number_format($pengajuan->total_harga, 0, ',', '.') . "*\n\n";
        $message .= "Silakan klik tautan dibawah ini untuk pembayaran:\n";
        $message .= "➡️  {$paymentUrl} \n\n";
        $message .= "Terima kasih.";

        $this->callPushWaApi($nomorWa, $message);
    }


    // --- METHOD NOTIFIKASI ANTRIAN KEDUA (DARI MidtransController) ---

    /**
     * Mengirim konfirmasi nomor antrian dan tanggal kunjungan.
     * @param PengajuanSurat $pengajuan
     * @param array $antrianResult Array hasil alokasi antrian (keys: 'antrian', 'tanggal').
     */
    public function sendAntrianNotification(PengajuanSurat $pengajuan, array $antrianResult): void
    {
        // 1. Format Nomor HP ke format Internasional (62xxxxxxxxxx)
        $nomorWa = preg_replace('/^0/', '62', $pengajuan->no_hp);
        $nomorWa = str_replace(['-', ' '], '', $nomorWa);

        // Ambil data dari hasil alokasi
        $nomorAntrian = $antrianResult['antrian'] ?? 'N/A';
        $tanggalKunjungan = $antrianResult['tanggal'] ?? 'N/A';
        $today = Carbon::now('Asia/Jakarta')->format('d F Y');

        // Tentukan pesan khusus jika tanggalnya rollover
        if ($tanggalKunjungan != $today) {
            $infoTanggal = "PENTING: Jadwal pemeriksaan Anda dialokasikan untuk *tanggal {$tanggalKunjungan}* karena kuota hari ini (Tgl {$today}) sudah penuh.";
        } else {
            $infoTanggal = "Jadwal pemeriksaan Anda adalah *hari ini*, tanggal {$tanggalKunjungan}.";
        }

        // 2. Siapkan Pesan
        $message = "🎉 *PEMBAYARAN BERHASIL & ALOKASI ANTRIAN* 🎉\n\n";
        $message .= "Halo *{$pengajuan->nama}*,\n\n";
        $message .= "Kami konfirmasi pembayaran Anda telah sukses (Order ID: *{$pengajuan->midtrans_order_id}*).\n\n";

        $message .= "Berikut detail kunjungan dan pemeriksaan Anda:\n";
        $message .= "=============================\n";
        $message .= "Nomor Antrian: *{$nomorAntrian}*\n";
        $message .= "Tanggal Kunjungan: *{$tanggalKunjungan}*\n";
        $message .= "=============================\n\n";

        $message .= "*Catatan Penting:*\n";
        $message .= "{$infoTanggal}\n";
        $message .= "Mohon datang ke Rumah Sakit sebelum jam 12.30 WIB untuk registrasi dan pemeriksaan lab.\n\n";
        $message .= "Terima kasih.";

        // 3. Panggil API
        $this->callPushWaApi($nomorWa, $message);
    }
    public function sendSuratNotification(string $nama, string $nomor, array $links): void
    {
        if (empty($links)) {
            Log::warning("Tidak ada surat untuk dikirim ke $nama.");
            return;
        }

        // Format nomor WA
        $nomorWa = preg_replace('/^0/', '62', $nomor);
        $nomorWa = str_replace(['-', ' '], '', $nomorWa);

        // Susun isi pesan
        $pesan = "📄 *SURAT KETERANGAN SIAP DIUNDUH*\n\n";
        $pesan .= "Halo *{$nama}*,\n";
        $pesan .= "Berikut surat Anda yang telah selesai diproses oleh RSUD Aceh Singkil:\n\n";

        foreach ($links as $i => $link) {
            $pesan .= ($i + 1) . ". " . $link . "\n";
        }

        $pesan .= "\nTerima kasih atas kepercayaan Anda.\n- RSUD Aceh Singkil -";

        // Kirim lewat gateway WA
        $this->callPushWaApi($nomorWa, $pesan);
    }
}
