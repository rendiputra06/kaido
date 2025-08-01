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
        Schema::create('matakuliah_prasyarat', function (Blueprint $table) {
            $table->foreignId('matakuliah_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('prasyarat_id')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->primary(['matakuliah_id', 'prasyarat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matakuliah_prasyarat');
    }
};
