<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppService;

class ResumeSuratController extends Controller
{
    /**
     * Tampilkan daftar pasien beserta total surat dan tanggal terakhir
     */
    public function index()
    {
        $pasien = PengajuanSurat::where('payment_status', 'SETTLEMENT') // 🔹 hanya yang sudah settlement
            ->select('nik', 'nama', 'jenis_surat', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('nik')
            ->map(function ($items) {
                $total_surat = $items->sum(function ($item) {
                    return count($item->jenis_surat ?? []);
                });

                $terakhir = $items->max('created_at');

                return (object)[
                    'nik' => $items->first()->nik,
                    'nama' => $items->first()->nama,
                    'total_surat' => $total_surat,
                    'terakhir' => $terakhir,
                ];
            })
            ->sortByDesc('terakhir');

        return view('admin.resume.resume-pengajuan', compact('pasien'));
    }

    /**
     * Tampilkan detail pengajuan pasien berdasarkan NIK
     */
    public function show($nik)
    {
        $data = PengajuanSurat::where('nik', $nik)->get();

        if ($data->isEmpty()) {
            abort(404, 'Data pasien tidak ditemukan');
        }

        $data->transform(function ($item) {
            $item->suratStatus = collect($item->jenis_surat ?? [])->map(function ($jenis) use ($item) {
                $normalized = strtolower(str_replace(' ', '_', $jenis));

                $tableMap = [
                    'kejiwaan' => [
                        'table' => 'surat_kejiwaan',
                        'preview' => 'surat-kejiwaan.preview',
                    ],
                    'keterangan_sehat' => [
                        'table' => 'surat_kesehatan',
                        'preview' => 'surat-kesehatan.preview',
                    ],
                    'bebas_narkoba' => [
                        'table' => 'surat_bebas_narkoba',
                        'preview' => 'surat-bebas-narkoba.preview',
                    ],
                ];

                $config = $tableMap[$normalized] ?? null;
                if (!$config) return null;

                $suratData = DB::table($config['table'])->where('pengajuan_id', $item->id)->first();

                $isFilled = $suratData !== null;
                $routeCreate = match ($normalized) {
                    'kejiwaan' => route('surat-jiwa.create', $item->id),
                    'bebas_narkoba' => route('surat-narkoba.create', $item->id),
                    'keterangan_sehat' => route('surat-kesehatan.create', $item->id),
                    default => null
                };

                $pdfPath = $suratData->pdf_path ?? null;
                $previewRoute = $suratData ? route($config['preview'], $suratData->id) : null;
                $pdfRoute = $suratData ? route('resume.generate-pdf', [
                    'jenis' => $normalized,
                    'id' => $suratData->id
                ]) : null;

                return [
                    'jenis' => $jenis,
                    'isFilled' => $isFilled,
                    'routeCreate' => $routeCreate,
                    'previewRoute' => $previewRoute,
                    'pdfRoute' => $pdfRoute,
                    'pdfPath' => $pdfPath,
                ];
            })->filter();

            return $item;
        });

        return view('admin.resume.detail-resume', [
            'nama' => $data->first()->nama,
            'nik'  => $nik,
            'data' => $data,
        ]);
    }

    /**
     * Generate PDF untuk surat tertentu (umum untuk semua jenis surat)
     */
    public function generatePdf($jenis, $id)
    {
        $jenisNormalized = strtolower(str_replace(' ', '_', $jenis));

        switch ($jenisNormalized) {
            case 'keterangan_sehat':
                $table = 'surat_kesehatan';
                $view = 'admin.surat.kesehatan_pdf';
                break;

            case 'bebas_narkoba':
                $table = 'surat_bebas_narkoba';
                $view = 'admin.surat.bebas_narkoba_pdf';
                break;

            case 'kejiwaan':
                $table = 'surat_kejiwaan';
                $view = 'admin.surat.kejiwaan_pdf';
                break;

            default:
                return redirect()->back()->with('error', 'Jenis surat tidak dikenali.');
        }

        // 🔹 Ambil data surat
        $surat = DB::table($table)->where('id', $id)->first();
        if (!$surat) {
            return back()->with('error', 'Data surat tidak ditemukan.');
        }

        // 🔹 Ambil data pengajuan
        $pengajuan = PengajuanSurat::find($surat->pengajuan_id);
        if (!$pengajuan) {
            return back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        // 🔹 Ambil data dokter
        $dokter = DB::table('dokter')->where('id', $surat->dokter_id ?? null)->first();

        // 🔹 Ambil alamat lengkap pasien
        $alamat = $this->getAlamatLengkap($pengajuan);

        // 🔹 Buat nama file rapi
        $namaPasien = preg_replace('/[^A-Za-z0-9_-]/', '_', strtolower($pengajuan->nama));
        $filename = "{$jenisNormalized}_{$namaPasien}_{$surat->id}.pdf";
        $pdfPath = "surat/{$jenisNormalized}/{$filename}";

        // 🔹 Pilih nama variabel yang sesuai untuk Blade
        $data = [
            'pengajuan_surat' => $pengajuan,
            'dokter' => $dokter,
            'alamat' => $alamat,
        ];

        // Tambahkan nama variabel surat yang sesuai untuk tiap jenis
        if ($jenisNormalized === 'keterangan_sehat') {
            $data['surat_kesehatan'] = $surat;
        } elseif ($jenisNormalized === 'bebas_narkoba') {
            $data['surat_bebas_narkoba'] = $surat;
        } elseif ($jenisNormalized === 'kejiwaan') {
            $data['surat_kejiwaan'] = $surat;
        }

        // 🔹 Generate PDF dari view yang sesuai
        $pdf = Pdf::loadView($view, $data)->setPaper('A4', 'portrait');

        // 🔹 Simpan ke storage/public
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // 🔹 Update database dengan path PDF
        DB::table($table)->where('id', $id)->update([
            'pdf_path' => $pdfPath,
            'updated_at' => now(),
        ]);

        return back()->with('success', "PDF berhasil dibuat: {$filename}");
    }

    /**
     * Fungsi bantu untuk alamat lengkap pasien
     */
    private function getAlamatLengkap($pengajuan)
    {
        if (!$pengajuan) return '-';

        $prov = DB::table('tbl_provinsi')->where('id', $pengajuan->provinsi)->value('provinsi');
        $kab = DB::table('tbl_kabkot')->where('id', $pengajuan->kabupaten)->value('kabupaten_kota');
        $kec = DB::table('tbl_kecamatan')->where('id', $pengajuan->kecamatan)->value('kecamatan');
        $desa = DB::table('tbl_kelurahan')->where('id', $pengajuan->desa)->value('kelurahan');
        $alamat_detail = $pengajuan->alamat_detail ?? '';

        $alamat = array_filter([$alamat_detail, $desa, $kec, $kab, $prov]);
        return implode(', ', $alamat);
    }



    public function sendSuratViaWA($id)
    {
        $pengajuan = PengajuanSurat::find($id);
        if (!$pengajuan) {
            return back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        if (!$pengajuan->no_hp) {
            return back()->with('error', "Nomor WhatsApp pasien belum tersedia.");
        }

        $links = [];

        foreach ($pengajuan->jenis_surat ?? [] as $jenis) {
            $normalized = strtolower(str_replace(' ', '_', $jenis));
            $tableMap = [
                'kejiwaan' => 'surat_kejiwaan',
                'keterangan_sehat' => 'surat_kesehatan',
                'bebas_narkoba' => 'surat_bebas_narkoba',
            ];

            if (!isset($tableMap[$normalized])) continue;

            $suratData = DB::table($tableMap[$normalized])->where('pengajuan_id', $pengajuan->id)->first();
            if ($suratData && $suratData->pdf_path) {
                $links[] = asset('storage/' . $suratData->pdf_path);
            }
        }

        if (empty($links)) {
            return back()->with('error', 'Belum ada PDF surat yang tersedia.');
        }

        // ✅ Pastikan nomor WA valid
        $nomor = $pengajuan->no_hp;
        $nomor = preg_replace('/^0/', '62', $nomor);
        $nomor = str_replace(['-', ' '], '', $nomor);

        if (!$nomor) {
            return back()->with('error', 'Nomor WhatsApp pasien tidak valid.');
        }

        $waService = new WhatsAppService();
        $waService->sendSuratNotification($pengajuan->nama, $nomor, $links);

        return back()->with('success', 'Link surat berhasil dikirim ke WhatsApp pasien.');
    }
}
