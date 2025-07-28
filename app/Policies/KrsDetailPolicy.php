<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KrsDetail;
use Illuminate\Auth\Access\HandlesAuthorization;

class KrsDetailPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_krs_detail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KrsDetail $krsDetail): bool
    {
        // Mahasiswa hanya bisa melihat detail KRS miliknya sendiri
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krsDetail->krsMahasiswa->mahasiswa_id;
        }
        
        // Dosen hanya bisa melihat detail KRS mahasiswa bimbingannya
        if ($user->hasRole('dosen')) {
            return $user->dosen->id === $krsDetail->krsMahasiswa->dosen_pa_id;
        }
        
        // Admin akademik bisa melihat semua detail KRS
        return $user->can('view_krs_detail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya mahasiswa yang bisa menambahkan detail KRS
        if ($user->hasRole('mahasiswa')) {
            return true;
        }
        
        // Admin akademik juga bisa menambahkan detail KRS
        return $user->can('create_krs_detail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KrsDetail $krsDetail): bool
    {
        // Mahasiswa hanya bisa mengupdate detail KRS miliknya sendiri dan hanya jika status KRS draft
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krsDetail->krsMahasiswa->mahasiswa_id && 
                   $krsDetail->krsMahasiswa->status === 'draft';
        }
        
        // Admin akademik bisa mengupdate semua detail KRS
        return $user->can('update_krs_detail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KrsDetail $krsDetail): bool
    {
        // Mahasiswa hanya bisa menghapus detail KRS miliknya sendiri dan hanya jika status KRS draft
        if ($user->hasRole('mahasiswa')) {
            return $user->mahasiswa->id === $krsDetail->krsMahasiswa->mahasiswa_id && 
                   $krsDetail->krsMahasiswa->status === 'draft';
        }
        
        // Admin akademik bisa menghapus semua detail KRS
        return $user->can('delete_krs_detail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, KrsDetail $krsDetail): bool
    {
        return $user->can('restore_krs_detail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, KrsDetail $krsDetail): bool
    {
        return $user->can('force_delete_krs_detail');
    }
}