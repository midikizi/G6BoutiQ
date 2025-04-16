<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vente;
use Illuminate\Auth\Access\HandlesAuthorization;

class VentePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'gestionnaire', 'vendeur']);
    }

    public function view(User $user, Vente $vente): bool
    {
        return $user->hasAnyRole(['admin', 'gestionnaire', 'vendeur']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'vendeur']);
    }

    public function update(User $user, Vente $vente): bool
    {
        return $user->hasAnyRole(['admin', 'vendeur']);
    }

    public function delete(User $user, Vente $vente): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Vente $vente): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Vente $vente): bool
    {
        return $user->hasRole('admin');
    }
} 