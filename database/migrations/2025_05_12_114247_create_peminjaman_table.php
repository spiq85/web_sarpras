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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman'); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('id_detail_peminjaman'); // Ensure this matches the type in `details_borrows`
            $table->enum('status', ['dipinjam', 'rejected', 'pending'])->default('pending');
            $table->string('keperluan')->nullable();
            $table->tinyInteger('soft_delete')->default(0);
            $table->timestamps();

            // Foreign Key Constraints
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
        Schema::dropIfExists('peminjaman');
    }
};
