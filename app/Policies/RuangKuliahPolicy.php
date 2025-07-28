<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RuangKuliah;
use Illuminate\Auth\Access\HandlesAuthorization;

class RuangKuliahPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_ruang_kuliah');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RuangKuliah $ruangKuliah): bool
    {
        return $user->can('view_ruang_kuliah');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin akademik yang bisa membuat ruang kuliah
        return $user->can('create_ruang_kuliah');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RuangKuliah $ruangKuliah): bool
    {
        // Hanya admin akademik yang bisa mengupdate ruang kuliah
        return $user->can('update_ruang_kuliah');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RuangKuliah $ruangKuliah): bool
    {
        // Hanya admin akademik yang bisa menghapus ruang kuliah
        return $user->can('delete_ruang_kuliah');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RuangKuliah $ruangKuliah): bool
    {
        return $user->can('restore_ruang_kuliah');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RuangKuliah $ruangKuliah): bool
    {
        return $user->can('force_delete_ruang_kuliah');
    }
}