<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorangNilai extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kelas_id',
        'komponen_nilai_id',
        'bobot',
        'is_locked',
        'dosen_id',
        'keterangan'
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'is_locked' => 'boolean',
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function komponenNilai()
    {
        return $this->belongsTo(KomponenNilai::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function nilaiMahasiswa()
    {
        return $this->hasMany(NilaiMahasiswa::class);
    }

    // Lock the borang nilai to prevent further changes
    public function lock()
    {
        $this->update(['is_locked' => true]);
    }

    // Unlock the borang nilai to allow changes
    public function unlock()
    {
        $this->update(['is_locked' => false]);
    }

    // Check if the borang is locked
    public function isLocked(): bool
    {
        return $this->is_locked;
    }
}
