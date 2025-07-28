<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PeriodeKrs;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeriodeKrsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa melihat daftar periode KRS
        return $user->can('view_any_periode_krs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PeriodeKrs $periodeKrs): bool
    {
        // Semua user bisa melihat detail periode KRS
        return $user->can('view_periode_krs');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin akademik yang bisa membuat periode KRS
        return $user->can('create_periode_krs');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PeriodeKrs $periodeKrs): bool
    {
        // Hanya admin akademik yang bisa mengupdate periode KRS
        return $user->can('update_periode_krs');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PeriodeKrs $periodeKrs): bool
    {
        // Hanya admin akademik yang bisa menghapus periode KRS
        return $user->can('delete_periode_krs');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PeriodeKrs $periodeKrs): bool
    {
        return $user->can('restore_periode_krs');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PeriodeKrs $periodeKrs): bool
    {
        return $user->can('force_delete_periode_krs');
    }

    /**
     * Determine whether the user can activate or deactivate the model.
     */
    public function toggleActive(User $user, PeriodeKrs $periodeKrs): bool
    {
        // Hanya admin akademik yang bisa mengaktifkan/nonaktifkan periode KRS
        return $user->can('toggle_active_periode_krs');
    }
}