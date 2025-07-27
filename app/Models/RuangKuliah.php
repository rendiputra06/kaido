<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangKuliah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kode',
        'kapasitas',
    ];
}