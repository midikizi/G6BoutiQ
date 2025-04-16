<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Produit;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProduitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'gestionnaire', 'vendeur']);
    }

    public function view(User $user, Produit $produit): bool
    {
        return $user->hasAnyRole(['admin', 'gestionnaire', 'vendeur']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'gestionnaire']);
    }

    public function update(User $user, Produit $produit): bool
    {
        return $user->hasAnyRole(['admin', 'gestionnaire']);
    }

    public function delete(User $user, Produit $produit): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Produit $produit): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Produit $produit): bool
    {
        return $user->hasRole('admin');
    }
} 