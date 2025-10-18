<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->renameColumn('nama_ibu', 'nik');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->renameColumn('nik', 'nama_ibu');
        });
    }
};
