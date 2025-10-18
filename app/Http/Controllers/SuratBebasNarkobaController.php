<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuratBebasNarkobaController extends Controller
{
    public function create($pengajuan_id)
    {
        $pengajuan = PengajuanSurat::findOrFail($pengajuan_id);
        $dokter = Dokter::all();

        $alamat = $this->getAlamatLengkap($pengajuan);

        return view('admin.surat.form-bebas-narkoba', compact('pengajuan', 'dokter', 'alamat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan_surat,id',
            'nomor_surat' => 'required|string|max:255',
            'dokter_id' => 'required|exists:dokter,id',
            'fisik_diagnostik' => 'nullable|date',
            'psikiatrik' => 'nullable|date',
            'pemeriksaan_tambahan' => 'nullable|date',
            'penampilan' => 'nullable|string',
            'cara_berjalan' => 'nullable|string',
            'cara_bicara' => 'nullable|string',
            'konjungtiva' => 'nullable|string',
            'bekas_suntikan' => 'nullable|string',
            'tremor' => 'nullable|string',
            'alur_pembicaraan' => 'nullable|string',
            'waham' => 'nullable|string',
            'halusinasi' => 'nullable|string',
            'halusinasi_akustik' => 'nullable|string',
            'halusinasi_visual' => 'nullable|string',
            'halusinasi_lain' => 'nullable|string',
            'cannabis' => 'nullable|string',
            'opiate' => 'nullable|string',
            'metamphetamine' => 'nullable|string',
            'mdma' => 'nullable|string',
            'benzodiazepine' => 'nullable|string',
        ]);

        $pengajuan = PengajuanSurat::findOrFail($validated['pengajuan_id']);
        $alamat = $this->getAlamatLengkap($pengajuan);

        DB::table('surat_bebas_narkoba')->insert(array_merge($validated, [
            'tidak_ada_penyalahgunaan' => $request->has('tidak_ada_penyalahgunaan'),
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return redirect()->route('resume.show', $pengajuan->nik)
            ->with('success', 'Surat Bebas Narkoba berhasil disimpan.');
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
        // Ambil data surat
        $surat = DB::table('surat_bebas_narkoba')->where('id', $id)->first();
        if (!$surat) {
            return redirect()->back()->with('error', 'Data surat tidak ditemukan.');
        }

        // Ambil data pengajuan terkait
        $pengajuan = PengajuanSurat::find($surat->pengajuan_id);
        if (!$pengajuan) {
            return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        // Ambil data dokter
        $dokter = DB::table('dokter')->where('id', $surat->dokter_id)->first();

        // Buat alamat lengkap
        $alamat = $this->getAlamatLengkap($pengajuan);

        // Kirim ke view preview
        return view('admin.surat.bebas_narkoba_preview', compact(
            'surat',
            'pengajuan',
            'dokter',
            'alamat'
        ));
    }
}
