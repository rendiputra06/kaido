<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'kuota',
        'sisa_kuota',
        'mata_kuliah_id',
        'tahun_ajaran_id',
        'dosen_id',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalKuliahs(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class);
    }

    public function krsDetails(): HasMany
    {
        return $this->hasMany(KrsDetail::class);
    }

    public function borangNilais(): HasMany
    {
        return $this->hasMany(BorangNilai::class);
    }
}