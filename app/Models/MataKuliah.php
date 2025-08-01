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

    public function kurikulums(): BelongsToMany
    {
        return $this->belongsToMany(Kurikulum::class, 'kurikulum_matakuliah')
            ->withPivot('semester_ditawarkan', 'jenis')
            ->withTimestamps();
    }

    public function kurikulum()
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
            'matakuliah_prasyarat',
            'matakuliah_id',
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
            'matakuliah_prasyarat',
            'prasyarat_id',
            'matakuliah_id'
        );
    }
}
