<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            // Kolom untuk menyimpan nomor antrian yang dialokasikan (misalnya, 1, 2, 3...)
            $table->unsignedSmallInteger('nomor_antrian')->nullable()->after('payment_status');
            
            // Kolom untuk menyimpan tanggal kuota pemeriksaan/kunjungan yang dialokasikan
            $table->date('tanggal_kuota')->nullable()->after('nomor_antrian');
            
            // Opsional: Kolom untuk menyimpan pesan respons Midtrans (Sudah ada di versi sebelumnya)
            // $table->text('midtrans_response_raw')->nullable()->after('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropColumn(['nomor_antrian', 'tanggal_kuota']);
        });
    }
};
