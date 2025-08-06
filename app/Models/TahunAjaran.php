<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected $fillable = [
        'kode',
        'nama',
        'semester',
        'tahun_akademik',
        'tgl_mulai',
        'tgl_selesai',
        'is_active'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($tahunAjaran) {
            // Generate kode if not provided
            if (empty($tahunAjaran->kode) && !empty($tahunAjaran->tgl_mulai) && !empty($tahunAjaran->semester)) {
                $year = $tahunAjaran->tgl_mulai->format('Y');
                $semester = strtolower($tahunAjaran->semester) === 'ganjil' ? '1' : '2';
                $tahunAjaran->kode = $year . $semester;
            }
            
            // Set tahun_akademik based on kode
            $tahunAjaran->tahun_akademik = $tahunAjaran->generateTahunAkademik();
        });

        // Prevent kode from being updated
        static::updating(function ($tahunAjaran) {
            if ($tahunAjaran->isDirty('kode')) {
                $originalKode = $tahunAjaran->getOriginal('kode');
                if ($tahunAjaran->kode !== $originalKode) {
                    $tahunAjaran->kode = $originalKode;
                }
            }
            
            // Update tahun_akademik if kode or semester changes
            if ($tahunAjaran->isDirty(['kode', 'semester'])) {
                $tahunAjaran->tahun_akademik = $tahunAjaran->generateTahunAkademik();
            }
        });
    }
    
    /**
     * Generate tahun_akademik based on kode
     */
    protected function generateTahunAkademik(): string
    {
        if (empty($this->kode) || strlen($this->kode) !== 5) {
            return '';
        }
        
        $year = (int) substr($this->kode, 0, 4);
        $semester = substr($this->kode, 4, 1);
        
        if ($semester === '1') {
            return sprintf('%d/%d', $year, $year + 1);
        }
        
        return sprintf('%d/%d', $year - 1, $year);
    }
}
