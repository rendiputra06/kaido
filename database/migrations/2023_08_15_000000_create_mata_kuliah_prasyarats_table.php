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
        Schema::create('mata_kuliah_prasyarats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('prasyarat_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi prasyarat
            $table->unique(['mata_kuliah_id', 'prasyarat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_prasyarats');
    }
};