<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lokasi_toko')) return;

        Schema::table('lokasi_toko', function (Blueprint $table) {
            if (!Schema::hasColumn('lokasi_toko', 'alamat')) {
                $table->text('alamat')->nullable()->after('nama_toko');
            }
        });
    }

    public function down()
    {
        Schema::table('lokasi_toko', function (Blueprint $table) {
            if (Schema::hasColumn('lokasi_toko', 'alamat')) {
                $table->dropColumn('alamat');
            }
        });
    }
};
