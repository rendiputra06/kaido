<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeKrs extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_periode',
        'tgl_mulai',
        'tgl_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function krsMahasiswas(): HasMany
    {
        return $this->hasMany(KrsMahasiswa::class);
    }

    /**
     * Cek apakah periode KRS sedang aktif
     */
    public function isActive(): bool
    {
        return $this->status === 'aktif' &&
            now()->between($this->tgl_mulai, $this->tgl_selesai);
    }

    /**
     * Cek apakah periode KRS sudah dimulai
     */
    public function isStarted(): bool
    {
        return now()->gte($this->tgl_mulai);
    }

    /**
     * Cek apakah periode KRS sudah berakhir
     */
    public function isEnded(): bool
    {
        return now()->gt($this->tgl_selesai);
    }
}
