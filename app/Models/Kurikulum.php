<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kurikulum extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function mataKuliahs(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }
}
