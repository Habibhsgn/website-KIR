<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->string('midtrans_order_id')->unique()->nullable()->after('total_harga');
            $table->string('payment_status')->default('DRAFT')->after('midtrans_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropColumn('midtrans_order_id');
            $table->dropColumn('payment_status');
        });
    }
};
