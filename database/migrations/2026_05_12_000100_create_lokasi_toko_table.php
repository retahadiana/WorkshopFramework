<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('lokasi_toko', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode')->unique();
            $table->string('nama_toko')->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude', 15, 8)->nullable();
            $table->double('accuracy', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lokasi_toko');
    }
};
