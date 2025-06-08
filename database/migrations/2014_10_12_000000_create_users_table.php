<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('users_id');
            $table->string('username')->unique();
            $table->string('name')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'user']);
            $table->string('email')->unique()->nullable();
            $table->enum('major',['RPL','TJKT','PSPT','ANIMASI','TE']);
            $table->enum('class',['X','XI','XII']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
