<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NilaiAkhir extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'krs_detail_id',
        'nilai_angka',
        'nilai_huruf',
        'bobot_nilai',
        'is_final',
        'finalized_by',
        'finalized_at',
        'catatan'
    ];
    
    protected $casts = [
        'nilai_angka' => 'decimal:2',
        'bobot_nilai' => 'decimal:2',
        'is_final' => 'boolean',
        'finalized_at' => 'datetime',
    ];
    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    // Relationships
    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KrsDetail::class)->withTrashed();
    }
    
    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
    
    // Scopes
    public function scopeFinalized($query)
    {
        return $query->where('is_final', true);
    }
    
    public function scopeForMahasiswa($query, $mahasiswaId)
    {
        return $query->whereHas('krsDetail.krs', function($q) use ($mahasiswaId) {
            $q->where('mahasiswa_id', $mahasiswaId);
        });
    }
    
    public function scopeForKelas($query, $kelasId)
    {
        return $query->whereHas('krsDetail', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
    }
    
    public function scopeForSemester($query, $semester)
    {
        return $query->whereHas('krsDetail.krs', function($q) use ($semester) {
            $q->where('semester', $semester);
        });
    }
    
    // Helpers
    public function finalize(int $userId, ?string $catatan = null): bool
    {
        return $this->update([
            'is_final' => true,
            'finalized_by' => $userId,
            'finalized_at' => now(),
            'catatan' => $catatan ?? $this->catatan,
        ]);
    }
    
    public function unfinalize(): bool
    {
        return $this->update([
            'is_final' => false,
            'finalized_by' => null,
            'finalized_at' => null,
        ]);
    }
    
    // Events
    protected static function booted()
    {
        static::creating(function ($nilaiAkhir) {
            // Ensure we have the latest calculated values
            if ($nilaiAkhir->isDirty(['nilai_angka', 'nilai_huruf', 'bobot_nilai'])) {
                $nilaiAkhir->is_final = false;
                $nilaiAkhir->finalized_by = null;
                $nilaiAkhir->finalized_at = null;
            }
        });
        
        static::updating(function ($nilaiAkhir) {
            // If any grade-related fields are changed, unfinalize the grade
            if ($nilaiAkhir->isDirty(['nilai_angka', 'nilai_huruf', 'bobot_nilai'])) {
                $nilaiAkhir->is_final = false;
                $nilaiAkhir->finalized_by = null;
                $nilaiAkhir->finalized_at = null;
            }
        });
    }
}
