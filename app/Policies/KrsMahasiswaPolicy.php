<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KrsMahasiswa;
use Illuminate\Auth\Access\HandlesAuthorization;

class KrsMahasiswaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_krs_mahasiswa');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        // Mahasiswa hanya bisa melihat KRS miliknya sendiri
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krsMahasiswa->mahasiswa_id;
        }
        
        // Dosen hanya bisa melihat KRS mahasiswa bimbingannya
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $krsMahasiswa->dosen_pa_id;
        }
        
        // Admin akademik bisa melihat semua KRS
        return $user->can('view_krs_mahasiswa');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya mahasiswa yang bisa membuat KRS
        if ($user->hasRole('mahasiswa')) {
            return true;
        }
        
        // Admin akademik juga bisa membuat KRS untuk mahasiswa
        return $user->can('create_krs_mahasiswa');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        // Mahasiswa hanya bisa mengupdate KRS miliknya sendiri dan hanya jika statusnya draft
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krsMahasiswa->mahasiswa_id && 
                   $krsMahasiswa->status === 'draft';
        }
        
        // Dosen PA hanya bisa mengupdate KRS mahasiswa bimbingannya untuk approval
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $krsMahasiswa->dosen_pa_id && 
                   $krsMahasiswa->status === 'submitted';
        }
        
        // Admin akademik bisa mengupdate semua KRS
        return $user->can('update_krs_mahasiswa');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        // Hanya admin akademik yang bisa menghapus KRS
        return $user->can('delete_krs_mahasiswa');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        return $user->can('restore_krs_mahasiswa');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        return $user->can('force_delete_krs_mahasiswa');
    }

    /**
     * Determine whether the user can submit the KRS.
     */
    public function submit(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        // Mahasiswa hanya bisa submit KRS miliknya sendiri dan hanya jika statusnya draft
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krsMahasiswa->mahasiswa_id && 
                   $krsMahasiswa->status === 'draft';
        }
        
        return false;
    }

    /**
     * Determine whether the user can approve or reject the KRS.
     */
    public function approveOrReject(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        // Dosen PA hanya bisa approve/reject KRS mahasiswa bimbingannya dan hanya jika statusnya submitted
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $krsMahasiswa->dosen_pa_id && 
                   $krsMahasiswa->status === 'submitted';
        }
        
        // Admin akademik juga bisa approve/reject KRS
        return $user->can('approve_krs_mahasiswa');
    }

    /**
     * Determine whether the user can reset the KRS status.
     */
    public function resetStatus(User $user, KrsMahasiswa $krsMahasiswa): bool
    {
        // Hanya admin akademik yang bisa mereset status KRS
        return $user->can('reset_status_krs_mahasiswa');
    }
}