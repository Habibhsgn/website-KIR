<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    public function provinsi()
    {
        $data = DB::table('tbl_provinsi')
            ->select('id', 'provinsi')
            ->orderBy('provinsi')
            ->get();

        return response()->json($data);
    }

    public function kabupaten($provinsi_id)
    {
        $data = DB::table('tbl_kabkot')
            ->where('provinsi_id', $provinsi_id)
            ->orderBy('kabupaten_kota')
            ->get(['id', 'kabupaten_kota']); // ambil kolom yang dipakai di dropdown

        return response()->json($data);
    }

    public function kecamatan($kabkot_id)
    {
        $data = DB::table('tbl_kecamatan')
            ->where('kabkot_id', $kabkot_id)
            ->orderBy('kecamatan')
            ->get(['id', 'kecamatan']); // ambil field yg dipakai dropdown

        return response()->json($data);
    }

    public function kelurahan($kecamatan_id)
    {
        $data = DB::table('tbl_kelurahan')
            ->where('kecamatan_id', $kecamatan_id)
            ->orderBy('kelurahan')
            ->get(['id', 'kelurahan', 'kd_pos']);
        return response()->json($data);
    }
}
