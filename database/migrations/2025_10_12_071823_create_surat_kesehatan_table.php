<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel sudah ada, hapus dulu biar tidak error saat migrate ulang
        Schema::dropIfExists('surat_kesehatan');

        Schema::create('surat_kesehatan', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengajuan_surat
            $table->foreignId('pengajuan_id')
                ->constrained('pengajuan_surat')
                ->onDelete('cascade');

            // Relasi ke dokter
            $table->foreignId('dokter_id')
                ->constrained('dokter')
                ->onDelete('restrict');

            // Data utama
            $table->string('hasil')->comment('Contoh: BAIK dan SEHAT');
            $table->date('tanggal_pemeriksaan');
            $table->text('isi_keterangan')->nullable()->comment('Kalimat otomatis: “Pada Hari ini ...”');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_kesehatan');
    }
};
