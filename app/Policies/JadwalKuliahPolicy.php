<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JadwalKuliah;
use Illuminate\Auth\Access\HandlesAuthorization;

class JadwalKuliahPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa melihat daftar jadwal kuliah
        return $user->can('view_any_jadwal_kuliah');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JadwalKuliah $jadwalKuliah): bool
    {
        // Semua user bisa melihat detail jadwal kuliah
        return $user->can('view_jadwal_kuliah');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin akademik yang bisa membuat jadwal kuliah
        return $user->can('create_jadwal_kuliah');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JadwalKuliah $jadwalKuliah): bool
    {
        // Hanya admin akademik yang bisa mengupdate jadwal kuliah
        return $user->can('update_jadwal_kuliah');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JadwalKuliah $jadwalKuliah): bool
    {
        // Hanya admin akademik yang bisa menghapus jadwal kuliah
        return $user->can('delete_jadwal_kuliah');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JadwalKuliah $jadwalKuliah): bool
    {
        return $user->can('restore_jadwal_kuliah');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JadwalKuliah $jadwalKuliah): bool
    {
        return $user->can('force_delete_jadwal_kuliah');
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReport(User $user): bool
    {
        // Admin akademik dan dosen bisa melihat laporan jadwal
        if ($user->hasRole('dosen')) {
            return true;
        }
        
        return $user->can('view_report_jadwal_kuliah');
    }
}