<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalKuota = 5;

        $kuotaTerpakai = DB::table('pengajuan_surat')
            ->whereDate('created_at', $today)
            ->count();

        $sisaKuota = max($totalKuota - $kuotaTerpakai, 0);

        $suratSehat = DB::table('pengajuan_surat')
            ->whereDate('created_at', $today)
            ->where('jenis_surat', 'like', '%keterangan_sehat%')
            ->count();

        $suratKejiwaan = DB::table('pengajuan_surat')
            ->whereDate('created_at', $today)
            ->where('jenis_surat', 'like', '%kejiwaan%')
            ->count();

        $suratNarkoba = DB::table('pengajuan_surat')
            ->whereDate('created_at', $today)
            ->where('jenis_surat', 'like', '%bebas_narkoba%')
            ->count();

        return view('admin.dashboard', compact(
            'totalKuota',
            'kuotaTerpakai',
            'sisaKuota',
            'suratSehat',
            'suratKejiwaan',
            'suratNarkoba'
        ));
    }

    // ✅ Tambahkan method baru untuk halaman Data Pengajuan
    public function dataPengajuan()
    {
        $dataPengajuan = DB::table('pengajuan_surat')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) {
                // Decode JSON string jadi array PHP
                $decoded = json_decode($item->jenis_surat, true);

                // Pastikan hasilnya array, bukan null/string
                $item->jenis_surat_array = is_array($decoded)
                    ? $decoded
                    : [$item->jenis_surat];

                return $item;
            });

        return view('admin.data-pengajuan', compact('dataPengajuan'));
    }

    
}
