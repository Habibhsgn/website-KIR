<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Surat Bebas Narkoba
        Schema::table('surat_bebas_narkoba', function (Blueprint $table) {
            $table->string('nomor_surat')->after('pengajuan_id')->nullable();
        });

        // Surat Kejiwaan
        Schema::table('surat_kejiwaan', function (Blueprint $table) {
            $table->string('nomor_surat')->after('pengajuan_id')->nullable();
        });

        // Surat Kesehatan
        Schema::table('surat_kesehatan', function (Blueprint $table) {
            $table->string('nomor_surat')->after('pengajuan_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('surat_bebas_narkoba', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
        });
        Schema::table('surat_kejiwaan', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
        });
        Schema::table('surat_kesehatan', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
        });
    }
};
