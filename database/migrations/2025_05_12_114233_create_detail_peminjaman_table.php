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
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id('id_detail_peminjaman');
            $table->unsignedBigInteger('users_id'); // Ensure this is unsignedBigInteger
            $table->unsignedBigInteger('id_barang'); // Ensure this is unsignedBigInteger
            $table->integer('jumlah');
            $table->string('keperluan');
            $table->string('class');
            $table->enum('status', ['dipinjam', 'kembali', 'rejected', 'pending'])->default('dipinjam');
            $table->dateTime('tanggal_pinjam');
            $table->dateTime('tanggal_kembali');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_barang')
                ->references('id_barang')
                ->on('barang')
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
        Schema::dropIfExists('detail_peminjaman');
    }
};
