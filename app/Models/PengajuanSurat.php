<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use HasFactory; // Memastikan trait HasFactory digunakan jika Anda menggunakannya

    protected $table = 'pengajuan_surat';

    protected $fillable = [
        'nama', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'pendidikan', 'provinsi', 'kabupaten', 'kecamatan', 'desa',
        'pekerjaan', 'status', 'agama', 'no_hp', 'email', 'keperluan',
        'alamat_detail', 'jenis_surat', 'total_harga', 
        
        // FIELD UTAMA
        'midtrans_order_id', 
        'payment_status',
        
        // <-- FIELD KRITIS UNTUK SISTEM ANTRIAN HARUS DITAMBAHKAN -->
        'nomor_antrian', 
        'tanggal_kuota',
        // FIELD KRITIS UNTUK SISTEM ANTRIAN HARUS DITAMBAHKAN -->
        'midtrans_response_raw', // Tambahkan jika ada di tabel
    ];

    protected $casts = [
        'jenis_surat' => 'array',
        'tanggal_lahir' => 'date', // Pastikan field tanggal di-cast
        'tanggal_kuota' => 'date', // KRITIS: Cast field baru sebagai tanggal
    ];
}
