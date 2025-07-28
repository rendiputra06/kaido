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
        Schema::create('krs_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('periode_krs_id')->constrained('periode_krs')->cascadeOnDelete();
            $table->foreignId('dosen_pa_id')->constrained('dosens')->cascadeOnDelete();
            $table->integer('total_sks')->default(0);
            $table->integer('max_sks')->default(24);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_pa')->nullable();
            $table->dateTime('tanggal_submit')->nullable();
            $table->dateTime('tanggal_approval')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint untuk memastikan satu mahasiswa hanya memiliki satu KRS per periode
            $table->unique(['mahasiswa_id', 'periode_krs_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs_mahasiswas');
    }
};
