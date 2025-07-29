<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KomponenNilai;
use Illuminate\Auth\Access\HandlesAuthorization;

class KomponenNilaiPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_komponen::nilai');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KomponenNilai $komponenNilai): bool
    {
        return $user->can('view_komponen::nilai');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_komponen::nilai');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KomponenNilai $komponenNilai): bool
    {
        return $user->can('update_komponen::nilai');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KomponenNilai $komponenNilai): bool
    {
        return $user->can('delete_komponen::nilai');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_komponen::nilai');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, KomponenNilai $komponenNilai): bool
    {
        return $user->can('force_delete_komponen::nilai');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_komponen::nilai');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, KomponenNilai $komponenNilai): bool
    {
        return $user->can('restore_komponen::nilai');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_komponen::nilai');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, KomponenNilai $komponenNilai): bool
    {
        return $user->can('replicate_komponen::nilai');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_komponen::nilai');
    }
}
