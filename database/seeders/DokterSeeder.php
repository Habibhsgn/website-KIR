<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dokter;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
        Dokter::insert([
            [
                'nama' => 'dr. Herlinawati Sitompul',
                'nip' => '19830527 200904 2 007',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Anna Mirah Putri',
                'nip' => '19920112 201903 2 009',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Fikra Milyuni',
                'nip' => '19920521 202012 2 005',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Indah Puspita Putri',
                'nip' => '19910115 201903 2 008',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Nur Wanda Fitri',
                'nip' => '19860607 201705 2 003',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Roslita',
                'nip' => '19800205 200803 2 001',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. S. Natal Pane',
                'nip' => '19751224 200803 1001',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Sakti Ginting',
                'nip' => '19680108 200212 1001',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Sarifianna Pinem',
                'nip' => '19870908 201412 2 001',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Syari Muhammad',
                'nip' => '19940326 202203 1 007',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Tridia Emilda',
                'nip' => '19840911 201103 2 001',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Yunita Evawati Manik',
                'nip' => '19810629 200904 2 002',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Mutia Amiriani',
                'nip' => '19841027 202321 2 001',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Suryati',
                'nip' => '19931105 202421 2 019',
                'jabatan' => 'Dokter Umum',
                'instansi' => 'RSUD Aceh Singkil'
            ],
            [
                'nama' => 'dr. Belli Susandro Pinem, Sp.Kj',
                'nip' => '19890415 201705 1 001',
                'jabatan' => 'Psikiater',
                'instansi' => 'RSUD Aceh Singkil'
            ],
        ]);
    }
}
