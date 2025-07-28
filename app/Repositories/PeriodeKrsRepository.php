<?php

namespace App\Repositories;

use App\Interfaces\PeriodeKrsRepositoryInterface;
use App\Models\PeriodeKrs;
use Illuminate\Database\Eloquent\Collection;

class PeriodeKrsRepository implements PeriodeKrsRepositoryInterface
{
    /**
     * Mengambil periode KRS yang sedang aktif
     */
    public function getActivePeriod(): ?PeriodeKrs
    {
        return PeriodeKrs::where('status', 'aktif')
            ->where('tgl_mulai', '<=', now())
            ->where('tgl_selesai', '>=', now())
            ->first();
    }

    /**
     * Mengambil semua periode KRS
     */
    public function getAllPeriods(): Collection
    {
        return PeriodeKrs::with('tahunAjaran')->get();
    }

    /**
     * Mengambil periode KRS berdasarkan ID
     */
    public function getPeriodById(int $id): ?PeriodeKrs
    {
        return PeriodeKrs::with('tahunAjaran')->find($id);
    }

    /**
     * Membuat periode KRS baru
     */
    public function createPeriod(array $data): PeriodeKrs
    {
        return PeriodeKrs::create($data);
    }

    /**
     * Memperbarui periode KRS
     */
    public function updatePeriod(int $id, array $data): ?PeriodeKrs
    {
        $period = $this->getPeriodById($id);

        if ($period) {
            $period->update($data);
            return $period;
        }

        return null;
    }

    /**
     * Menghapus periode KRS
     */
    public function deletePeriod(int $id): bool
    {
        $period = $this->getPeriodById($id);

        if ($period) {
            return $period->delete();
        }

        return false;
    }

    /**
     * Mengaktifkan periode KRS (nonaktifkan yang lain)
     */
    public function activatePeriod(int $id): bool
    {
        // Nonaktifkan semua periode yang aktif
        PeriodeKrs::where('status', 'aktif')->update(['status' => 'tidak_aktif']);

        // Aktifkan periode yang dipilih
        $period = $this->getPeriodById($id);
        if ($period) {
            $period->update(['status' => 'aktif']);
            return true;
        }

        return false;
    }
}
