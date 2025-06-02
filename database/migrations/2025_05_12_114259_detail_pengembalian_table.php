<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_pengembalian', function (Blueprint $table) {
            $table->id('id_detail_pengembalian');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('id_detail_peminjaman');
            $table->unsignedBigInteger('id_peminjaman');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah');
            $table->string('kondisi')->nullable();
            $table->enum('status', ['approve', 'not approve', 'pending'])->default('pending');
            $table->tinyInteger('soft_delete')->default(0);
            $table->dateTime('tanggal_pengembalian');
            $table->string('keterangan')->nullable();
            $table->string('item_image')->nullable();
            $table->timestamps();

            $table->foreign('id_peminjaman')
                ->references('id_peminjaman')
                ->on('peminjaman')
                ->onDelete('cascade');

            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang')
                ->onDelete('cascade');

            $table->foreign('id_detail_peminjaman')
                ->references('id_detail_peminjaman')
                ->on('detail_peminjaman')
                ->onDelete('cascade');

            $table->foreign('users_id')
                ->references('users_id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_returns');
    }
};
