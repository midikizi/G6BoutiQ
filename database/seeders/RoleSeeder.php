<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Création des rôles
        $adminRole = Role::create(['name' => 'admin']);
        $gerantRole = Role::create(['name' => 'gerant']);
        $vendeurRole = Role::create(['name' => 'vendeur']);

        // Création des permissions
        $permissions = [
            // Gestion des utilisateurs
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Gestion des produits
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Gestion des catégories
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            
            // Gestion des ventes
            'view_sales',
            'create_sales',
            'edit_sales',
            'delete_sales',
            
            // Gestion des stocks
            'view_stocks',
            'create_stocks',
            'edit_stocks',
            'delete_stocks',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Attribution de toutes les permissions à l'admin
        $adminRole->givePermissionTo(Permission::all());

        // Attribution des permissions au gérant (tout sauf gestion utilisateurs)
        $gerantRole->givePermissionTo([
            // Gestion des produits
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Gestion des catégories
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            
            // Gestion des ventes
            'view_sales',
            'create_sales',
            'edit_sales',
            'delete_sales',
            
            // Gestion des stocks
            'view_stocks',
            'create_stocks',
            'edit_stocks',
            'delete_stocks',
        ]);

        // Attribution des permissions au vendeur (ventes uniquement)
        $vendeurRole->givePermissionTo([
            'view_sales',
            'create_sales',
            'edit_sales',
        ]);
    }
} 