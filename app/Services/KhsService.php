<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\KrsMahasiswa;
use App\Models\NilaiAkhir;
use Illuminate\Support\Collection;

class KhsService
{
    /**
     * Hitung IPK (Indeks Prestasi Kumulatif) mahasiswa
     */
    public function calculateGPA(int $mahasiswaId): array
    {
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        
        // Ambil semua nilai akhir yang sudah final
        $nilaiAkhir = NilaiAkhir::with(['krsDetail.kelas.mataKuliah'])
            ->whereHas('krsDetail.krs', function($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId)
                      ->where('status', 'approved');
            })
            ->where('is_final', true)
            ->get();

        if ($nilaiAkhir->isEmpty()) {
            return [
                'ipk' => 0.00,
                'total_sks' => 0,
                'total_mutu' => 0.00,
                'detail' => []
            ];
        }

        $totalSks = 0;
        $totalMutu = 0.00;
        $detail = [];

        foreach ($nilaiAkhir as $nilai) {
            $krsDetail = $nilai->krsDetail;
            $kelas = $krsDetail->kelas;
            $mataKuliah = $kelas->mataKuliah;
            
            $sks = $mataKuliah->sks;
            $nilaiHuruf = $nilai->nilai_huruf;
            $bobot = $this->convertGradeToPoint($nilaiHuruf);
            $mutu = $sks * $bobot;

            $totalSks += $sks;
            $totalMutu += $mutu;

            $detail[] = [
                'kode_mk' => $mataKuliah->kode_mk,
                'nama_mk' => $mataKuliah->nama_mk,
                'sks' => $sks,
                'nilai_huruf' => $nilaiHuruf,
                'bobot' => $bobot,
                'mutu' => $mutu,
                'semester' => $krsDetail->krs->semester,
                'tahun_ajaran' => $krsDetail->krs->tahunAjaran->nama,
            ];
        }

        $ipk = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

        return [
            'ipk' => $ipk,
            'total_sks' => $totalSks,
            'total_mutu' => $totalMutu,
            'detail' => $detail
        ];
    }

    /**
     * Hitung IPS (Indeks Prestasi Semester) untuk semester tertentu
     */
    public function calculateIPS(int $mahasiswaId, int $semester): array
    {
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        
        // Ambil nilai akhir untuk semester tertentu
        $nilaiAkhir = NilaiAkhir::with(['krsDetail.kelas.mataKuliah'])
            ->whereHas('krsDetail.krs', function($query) use ($mahasiswaId, $semester) {
                $query->where('mahasiswa_id', $mahasiswaId)
                      ->where('semester', $semester)
                      ->where('status', 'approved');
            })
            ->where('is_final', true)
            ->get();

        if ($nilaiAkhir->isEmpty()) {
            return [
                'ips' => 0.00,
                'total_sks' => 0,
                'total_mutu' => 0.00,
                'detail' => []
            ];
        }

        $totalSks = 0;
        $totalMutu = 0.00;
        $detail = [];

        foreach ($nilaiAkhir as $nilai) {
            $krsDetail = $nilai->krsDetail;
            $kelas = $krsDetail->kelas;
            $mataKuliah = $kelas->mataKuliah;
            
            $sks = $mataKuliah->sks;
            $nilaiHuruf = $nilai->nilai_huruf;
            $bobot = $this->convertGradeToPoint($nilaiHuruf);
            $mutu = $sks * $bobot;

            $totalSks += $sks;
            $totalMutu += $mutu;

            $detail[] = [
                'kode_mk' => $mataKuliah->kode_mk,
                'nama_mk' => $mataKuliah->nama_mk,
                'sks' => $sks,
                'nilai_huruf' => $nilaiHuruf,
                'bobot' => $bobot,
                'mutu' => $mutu,
            ];
        }

        $ips = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.00;

        return [
            'ips' => $ips,
            'total_sks' => $totalSks,
            'total_mutu' => $totalMutu,
            'detail' => $detail
        ];
    }

    /**
     * Konversi nilai huruf ke bobot nilai
     */
    private function convertGradeToPoint(string $grade): float
    {
        return match(strtoupper($grade)) {
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D' => 1.0,
            'E' => 0.0,
            default => 0.0,
        };
    }

    /**
     * Ambil riwayat KHS per semester
     */
    public function getKhsHistory(int $mahasiswaId): Collection
    {
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);
        
        return KrsMahasiswa::with(['krsDetails.kelas.mataKuliah', 'tahunAjaran'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'approved')
            ->orderBy('semester', 'asc')
            ->get()
            ->map(function ($krs) use ($mahasiswaId) {
                $ips = $this->calculateIPS($mahasiswaId, $krs->semester);
                return [
                    'semester' => $krs->semester,
                    'tahun_ajaran' => $krs->tahunAjaran->nama,
                    'ips' => $ips['ips'],
                    'total_sks' => $ips['total_sks'],
                    'total_mutu' => $ips['total_mutu'],
                    'mata_kuliah' => $ips['detail']
                ];
            });
    }

    /**
     * Generate transkrip nilai sementara
     */
    public function generateTranscript(int $mahasiswaId): array
    {
        $gpa = $this->calculateGPA($mahasiswaId);
        $history = $this->getKhsHistory($mahasiswaId);
        
        return [
            'mahasiswa' => Mahasiswa::with(['user', 'programStudi'])->find($mahasiswaId),
            'ipk' => $gpa['ipk'],
            'total_sks' => $gpa['total_sks'],
            'total_mutu' => $gpa['total_mutu'],
            'riwayat_semester' => $history
        ];
    }
}