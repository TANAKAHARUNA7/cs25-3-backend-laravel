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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();

            
            $table->foreignId('designer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('requirement')->nullable();

            $table->date('day');
            $table->time('start_at');
            $table->time('end_at');
            $table->enum('status',['pending', 'confirmed','checked_in','completed','cancelled','no_show'])
                ->default('pending');
            
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            // created_at + updated_at 作成
            $table->timestamps();

            // 重複チェックを速くするためのインデックス
            $table->index(['designer_id', 'day', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
