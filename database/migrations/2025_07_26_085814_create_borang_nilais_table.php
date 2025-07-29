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
        Schema::create('borang_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilais')->onDelete('cascade');
            $table->decimal('bobot', 5, 2);
            $table->boolean('is_locked')->default(false);
            $table->foreignId('dosen_id')->constrained('dosens')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure unique constraint for kelas_id and komponen_nilai_id
            $table->unique(['kelas_id', 'komponen_nilai_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borang_nilais');
    }
};
