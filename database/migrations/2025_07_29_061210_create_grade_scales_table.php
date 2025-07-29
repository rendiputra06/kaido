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
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studis')->onDelete('cascade');
            $table->string('nilai_huruf', 5);
            $table->decimal('nilai_indeks', 3, 2);
            $table->decimal('rentang_bawah', 5, 2);
            $table->decimal('rentang_atas', 5, 2);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->unique(['program_studi_id', 'nilai_huruf']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};
