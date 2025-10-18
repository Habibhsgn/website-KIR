<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratKeteranganKejiwaanController extends Controller
{
    public function create($pengajuan_id)
    {
        $pengajuan = PengajuanSurat::findOrFail($pengajuan_id);
        $dokter = Dokter::all();

        $alamat = $this->getAlamatLengkap($pengajuan);
        // ✅ Pastikan view sesuai nama file yang sekarang
        return view('admin.surat.form-keterangan-kejiwaan', compact('pengajuan', 'dokter', 'alamat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan_surat,id',
            'dokter_id' => 'required|exists:dokter,id',
            'nomor_surat' => 'required|string|max:255', // tambahkan validasi nomor surat
            'hasil' => 'required|string',
            'tanggal_pemeriksaan' => 'required|date',
        ]);

        $pengajuan = PengajuanSurat::findOrFail($validated['pengajuan_id']);

        // Ambil alamat lengkap
        $alamat = $this->getAlamatLengkap($pengajuan);

        // Simpan ke tabel surat_kejiwaan
        DB::table('surat_kejiwaan')->insert([
            'pengajuan_id' => $validated['pengajuan_id'],
            'dokter_id' => $validated['dokter_id'],
            'nomor_surat' => $validated['nomor_surat'], // simpan nomor surat
            'hasil' => $validated['hasil'],
            'tanggal_pemeriksaan' => $validated['tanggal_pemeriksaan'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update status pengajuan jadi “terisi”
        $pengajuan->update(['status_surat' => 'terisi']);

        return redirect()->route('resume.show', $pengajuan->nik)
            ->with('success', 'Surat Keterangan Kejiwaan berhasil disimpan.');
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
        $surat_kejiwaan = DB::table('surat_kejiwaan')->where('id', $id)->first();

        if (!$surat_kejiwaan) {
            return redirect()->back()->with('error', 'Surat Kejiwaan tidak ditemukan.');
        }

        $pengajuan_surat = PengajuanSurat::find($surat_kejiwaan->pengajuan_id);
        $dokter = DB::table('dokter')->where('id', $surat_kejiwaan->dokter_id)->first();
        $alamat = $this->getAlamatLengkap($pengajuan_surat);

        return view('admin.surat.kejiwaan_preview', compact(
            'surat_kejiwaan',
            'pengajuan_surat',
            'dokter',
            'alamat'
        ));
    }
}
