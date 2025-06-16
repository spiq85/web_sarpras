<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriBarang extends Migration
{
    public function up()
    {
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('nama_kategori');
            $table->string('prefix_kode', 10)->unique()->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori_barang');
    }
}
