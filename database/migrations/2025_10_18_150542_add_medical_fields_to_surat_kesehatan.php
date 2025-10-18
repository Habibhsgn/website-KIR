<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_kesehatan', function (Blueprint $table) {
            $table->integer('tinggi_badan')->nullable()->after('hasil'); // cm
            $table->integer('berat_badan')->nullable()->after('tinggi_badan'); // kg
            $table->string('tensi_darah', 10)->nullable()->after('berat_badan');
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable()->after('tensi_darah');
        });
    }

    public function down(): void
    {
        Schema::table('surat_kesehatan', function (Blueprint $table) {
            $table->dropColumn(['tinggi_badan', 'berat_badan', 'tensi_darah', 'golongan_darah']);
        });
    }
};
