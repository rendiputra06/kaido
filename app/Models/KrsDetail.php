<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KrsDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'krs_mahasiswa_id',
        'kelas_id',
        'sks',
        'status',
        'keterangan',
    ];

    public function krsMahasiswa(): BelongsTo
    {
        return $this->belongsTo(KrsMahasiswa::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Cek apakah detail KRS aktif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Cek apakah detail KRS dibatalkan
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Ambil SKS dari mata kuliah kelas
     */
    public function getSksFromMataKuliah(): int
    {
        return $this->kelas->mataKuliah->sks ?? 0;
    }
}
