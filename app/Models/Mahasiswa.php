<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function dosenPa(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pa_id');
    }

    /**
     * Get all nilai (grades) for this student
     */
    public function riwayatNilai()
    {
        return $this->hasManyThrough(
            \App\Models\NilaiMahasiswa::class, // Target model
            \App\Models\KrsMahasiswa::class,   // Intermediate model
            'mahasiswa_id',                     // Foreign key on KrsMahasiswa table
            'krs_detail_id',                    // Foreign key on NilaiMahasiswa table
            'id',                               // Local key on Mahasiswa table
            'id'                                // Local key on KrsMahasiswa table
        )->join('krs_details', 'krs_mahasiswa_id', '=', 'krs_mahasiswas.id');
    }
}
