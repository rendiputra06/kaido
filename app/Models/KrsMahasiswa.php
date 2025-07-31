<?php

namespace App\Models;

use App\Enums\KrsStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KrsMahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'periode_krs_id',
        'dosen_pa_id',
        'status',
        'total_sks',
        'catatan_pa',
        'tanggal_submit',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
        'tanggal_approval' => 'datetime',
        'status' => KrsStatusEnum::class,
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function periodeKrs(): BelongsTo
    {
        return $this->belongsTo(PeriodeKrs::class);
    }

    public function dosenPa(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pa_id');
    }

    public function krsDetails(): HasMany
    {
        return $this->hasMany(KrsDetail::class);
    }

    /**
     * Hitung total SKS dari detail KRS
     */
    public function calculateTotalSks(): int
    {
        return $this->krsDetails()
            ->where('status', 'active')
            ->join('kelas', 'krs_details.kelas_id', '=', 'kelas.id')
            ->join('mata_kuliahs', 'kelas.mata_kuliah_id', '=', 'mata_kuliahs.id')
            ->sum('mata_kuliahs.sks');
    }
}

