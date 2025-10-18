<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratKesehatanController extends Controller
{
    public function create($pengajuan_id)
    {
        $pengajuan = PengajuanSurat::findOrFail($pengajuan_id);
        $dokter = Dokter::all();
        $alamat = $this->getAlamatLengkap($pengajuan);
        return view('admin.surat.form-surat-kesehatan', compact('pengajuan', 'dokter', 'alamat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan_surat,id',
            'dokter_id' => 'required|exists:dokter,id',
            'nomor_surat' => 'required|string|max:255', // ✅ nomor surat
            'tanggal_pemeriksaan' => 'required|date',
            'hasil' => 'required|string',
        ]);

        // Ambil tanggal dari input
        $tanggal = strtotime($validated['tanggal_pemeriksaan']);

        // Array hari dan bulan Indonesia
        $hariIndo = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $bulanIndo = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $hari = $hariIndo[date('l', $tanggal)];
        $tanggalAngka = date('j', $tanggal);
        $bulan = $bulanIndo[date('n', $tanggal)];
        $tahun = date('Y', $tanggal);

        // Hasil otomatis dalam format lengkap
        $hasilKalimat = "Pada Hari ini {$hari}, Tanggal {$tanggalAngka} Bulan {$bulan} Tahun {$tahun} telah diperiksa keadaan badannya ternyata {$validated['hasil']}.";

        DB::table('surat_kesehatan')->insert([
            'pengajuan_id' => $validated['pengajuan_id'],
            'dokter_id' => $validated['dokter_id'],
            'nomor_surat' => $validated['nomor_surat'], // ✅ simpan nomor surat
            'tanggal_pemeriksaan' => $validated['tanggal_pemeriksaan'],
            'hasil' => $validated['hasil'],
            'isi_keterangan' => $hasilKalimat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pengajuan = PengajuanSurat::find($validated['pengajuan_id']);
        $alamat = $this->getAlamatLengkap($pengajuan);

        return redirect()->route('resume.show', $pengajuan->nik)
            ->with('success', 'Surat Keterangan Sehat berhasil disimpan.');
    }

    private function getAlamatLengkap($pengajuan)
    {
        // Ambil data wilayah
        $prov = DB::table('tbl_provinsi')->where('id', $pengajuan->provinsi)->value('provinsi');
        $kab = DB::table('tbl_kabkot')->where('id', $pengajuan->kabupaten)->value('kabupaten_kota');
        $kec = DB::table('tbl_kecamatan')->where('id', $pengajuan->kecamatan)->value('kecamatan');
        $desa = DB::table('tbl_kelurahan')->where('id', $pengajuan->desa)->value('kelurahan');

        // Ambil alamat detail dari tabel pengajuan
        $alamat_detail = $pengajuan->alamat_detail ?? '';

        // Susun alamat rapi
        $alamat = [];
        if ($alamat_detail) $alamat[] = $alamat_detail;
        if ($desa) $alamat[] = $desa;
        if ($kec) $alamat[] = $kec;
        if ($kab) $alamat[] = $kab;
        if ($prov) $alamat[] = $prov;

        return implode(', ', $alamat);
    }
    public function preview($id)
    {
        $surat_kesehatan = DB::table('surat_kesehatan')->where('id', $id)->first();

        if (!$surat_kesehatan) {
            return redirect()->back()->with('error', 'Surat Kesehatan tidak ditemukan.');
        }

        $pengajuan_surat = PengajuanSurat::find($surat_kesehatan->pengajuan_id);
        $dokter = DB::table('dokter')->where('id', $surat_kesehatan->dokter_id)->first();

        $alamat = $this->getAlamatLengkap($pengajuan_surat);

        return view('admin.surat.kesehatan_preview', compact(
            'surat_kesehatan',
            'pengajuan_surat',
            'dokter',
            'alamat'
        ));
    }
    
}
