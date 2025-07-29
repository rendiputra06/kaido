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
        Schema::create('nilai_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('krs_detail_id')->constrained('krs_details')->cascadeOnDelete();
            $table->float('nilai_angka', 5, 2);
            $table->string('nilai_huruf', 2);
            $table->float('bobot', 3, 2);
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi nilai
            $table->unique(['mahasiswa_id', 'krs_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_akhirs');
    }
};
