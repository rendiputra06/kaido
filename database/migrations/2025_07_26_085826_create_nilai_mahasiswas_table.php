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
        Schema::create('nilai_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_detail_id')->constrained('krs_details')->onDelete('cascade');
            $table->foreignId('borang_nilai_id')->constrained('borang_nilais')->onDelete('cascade');
            $table->decimal('nilai', 5, 2); // 0-100
            $table->text('keterangan')->nullable();
            $table->timestamp('terakhir_diubah')->nullable();
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Ensure one grade per krs_detail and borang_nilai
            $table->unique(['krs_detail_id', 'borang_nilai_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswas');
    }
};
