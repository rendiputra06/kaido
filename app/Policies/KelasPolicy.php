<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Auth\Access\HandlesAuthorization;

class KelasPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa melihat daftar kelas
        return $user->can('view_any_kelas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kelas $kelas): bool
    {
        // Semua user bisa melihat detail kelas
        return $user->can('view_kelas');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin akademik yang bisa membuat kelas
        return $user->can('create_kelas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kelas $kelas): bool
    {
        // Dosen pengampu bisa mengupdate kelas yang diampu
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $kelas->dosen_id;
        }
        
        // Admin akademik bisa mengupdate semua kelas
        return $user->can('update_kelas');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kelas $kelas): bool
    {
        // Hanya admin akademik yang bisa menghapus kelas
        return $user->can('delete_kelas');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kelas $kelas): bool
    {
        return $user->can('restore_kelas');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kelas $kelas): bool
    {
        return $user->can('force_delete_kelas');
    }

    /**
     * Determine whether the user can view the class roster.
     */
    public function viewRoster(User $user, Kelas $kelas): bool
    {
        // Dosen pengampu bisa melihat daftar mahasiswa di kelas yang diampu
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $kelas->dosen_id;
        }
        
        // Admin akademik bisa melihat daftar mahasiswa di semua kelas
        return $user->can('view_roster_kelas');
    }
}