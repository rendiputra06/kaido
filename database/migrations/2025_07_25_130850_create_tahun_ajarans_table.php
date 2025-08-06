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
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 5)->unique(); // Format: YYYYS (S=1 for Ganjil, 2 for Genap)
            $table->string('nama');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_akademik'); // Will be set by the model
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            // Add comment to explain kode format
            $table->comment('kode format: YYYYS (S=1 for Ganjil, 2 for Genap)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajarans');
    }
};
