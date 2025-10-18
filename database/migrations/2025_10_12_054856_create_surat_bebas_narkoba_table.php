<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_bebas_narkoba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_surat')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('dokter')->onDelete('cascade');

            // Tanggal pemeriksaan
            $table->datetime('fisik_diagnostik')->nullable();
            $table->datetime('psikiatrik')->nullable();
            $table->datetime('pemeriksaan_tambahan')->nullable();

            // Pemeriksaan Fisik
            $table->string('penampilan')->nullable();
            $table->string('cara_berjalan')->nullable();
            $table->string('cara_bicara')->nullable();
            $table->string('konjungtiva')->nullable();
            $table->string('bekas_suntikan')->nullable();
            $table->string('tremor')->nullable();

            // Pemeriksaan Psikiatrik
            $table->string('alur_pembicaraan')->nullable();
            $table->string('waham')->nullable();
            $table->string('halusinasi')->nullable();
            $table->string('halusinasi_akustik')->nullable();
            $table->string('halusinasi_visual')->nullable();
            $table->string('halusinasi_lain')->nullable();

            // Pemeriksaan Tambahan
            $table->string('cannabis')->nullable();
            $table->string('opiate')->nullable();
            $table->string('metamphetamine')->nullable();
            $table->string('mdma')->nullable();
            $table->string('benzodiazepine')->nullable();

            // Kesimpulan umum
            $table->boolean('tidak_ada_penyalahgunaan')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_bebas_narkoba');
    }
};
