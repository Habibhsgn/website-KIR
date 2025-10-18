<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surat_kesehatan', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('updated_at');
        });

        Schema::table('surat_kejiwaan', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('updated_at');
        });

        Schema::table('surat_bebas_narkoba', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('surat_kesehatan', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });

        Schema::table('surat_kejiwaan', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });

        Schema::table('surat_bebas_narkoba', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
};
