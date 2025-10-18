<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::rename('surat_keterangan_sehat', 'surat_kejiwaan');
    }

    public function down(): void
    {
        Schema::rename('surat_kejiwaan', 'surat_keterangan_sehat');
    }
};
