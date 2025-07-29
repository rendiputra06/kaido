<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiAkhir extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    
    /**
     * Relasi ke KrsDetail
     */
    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KrsDetail::class);
    }
    
    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
