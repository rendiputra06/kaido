<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NilaiMahasiswa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'krs_detail_id',
        'borang_nilai_id',
        'nilai',
        'keterangan',
        'terakhir_diubah',
        'diubah_oleh',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'terakhir_diubah' => 'datetime',
    ];

    // Relationships
    public function krsDetail()
    {
        return $this->belongsTo(KrsDetail::class);
    }

    public function borangNilai()
    {
        return $this->belongsTo(BorangNilai::class);
    }

    public function diubahOleh()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    // Scopes
    public function scopeForKelas($query, $kelasId)
    {
        return $query->whereHas('krsDetail', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
    }

    public function scopeForMahasiswa($query, $mahasiswaId)
    {
        return $query->whereHas('krsDetail.krs', function($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        });
    }

    // Helpers
    public function getNilaiHurufAttribute(): ?string
    {
        if ($this->nilai === null) {
            return null;
        }

        if ($this->nilai >= 80) return 'A';
        if ($this->nilai >= 70) return 'B';
        if ($this->nilai >= 60) return 'C';
        if ($this->nilai >= 50) return 'D';
        return 'E';
    }

    public function getBobotNilaiAttribute(): ?float
    {
        if ($this->nilai === null) {
            return null;
        }

        return match($this->nilai_huruf) {
            'A' => 4.0,
            'B' => 3.0,
            'C' => 2.0,
            'D' => 1.0,
            default => 0.0,
        };
    }

    // Events
    protected static function booted()
    {
        static::saving(function ($nilai) {
            $nilai->terakhir_diubah = now();
            $nilai->diubah_oleh = auth()->id();
        });
    }
}
