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
            $table->foreignId('krs_detail_id')->constrained('krs_details')->cascadeOnDelete();
            $table->decimal('nilai_angka', 5, 2);
            $table->string('nilai_huruf', 2);
            $table->decimal('bobot_nilai', 3, 2);
            $table->boolean('is_final')->default(false);
            $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint to prevent duplicate final grades
            $table->unique(['krs_detail_id']);
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
