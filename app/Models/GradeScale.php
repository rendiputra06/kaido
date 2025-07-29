<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_studi_id',
        'nilai_huruf',
        'nilai_indeks',
        'rentang_bawah',
        'rentang_atas',
        'is_aktif',
    ];

    /**
     * Get the program studi that owns the grade scale.
     */
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }
}
