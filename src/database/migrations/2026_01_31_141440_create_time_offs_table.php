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
        Schema::create('time_offs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('designer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('start_at');
            $table->date('end_at');

            $table->timestamps();

            $table->index(['designer_id', 'start_at', 'end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_offs');
    }
};
