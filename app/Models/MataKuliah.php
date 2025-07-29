<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }
    
    /**
     * Mendapatkan mata kuliah yang menjadi prasyarat untuk mata kuliah ini
     */
    public function prasyarats(): BelongsToMany
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'mata_kuliah_prasyarats',
            'mata_kuliah_id',
            'prasyarat_id'
        );
    }
    
    /**
     * Mendapatkan mata kuliah yang memiliki prasyarat mata kuliah ini
     */
    public function mataKuliahLanjutan(): BelongsToMany
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'mata_kuliah_prasyarats',
            'prasyarat_id',
            'mata_kuliah_id'
        );
    }
}
