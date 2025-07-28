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
        Schema::create('krs_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_mahasiswa_id')->constrained('krs_mahasiswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->integer('sks');
            $table->enum('status', ['active', 'canceled'])->default('active');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint untuk mencegah duplikasi kelas dalam satu KRS
            $table->unique(['krs_mahasiswa_id', 'kelas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs_details');
    }
};
