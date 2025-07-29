<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomponenNilai extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'default_bobot',
        'keterangan',
        'is_aktif'
    ];

    protected $casts = [
        'default_bobot' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    // Relationships
    public function borangNilai()
    {
        return $this->hasMany(BorangNilai::class);
    }
}
