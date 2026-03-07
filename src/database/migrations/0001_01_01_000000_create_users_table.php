<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// users テーブルを作成するマイグレーション
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {   
        // users テーブルを新規作成
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('account')->unique();
            $table->string('password');
            $table->string('user_name', 100);
            $table->enum('role', ['client', 'designer', 'manager'])->default('client');
            $table->enum('gender', ['MEN', 'WOMEN', 'Non_binary']);
            $table->string('phone', 30)->nullable();
            $table->date('birth');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
 
};
