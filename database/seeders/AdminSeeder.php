<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');

        $gerant = User::create([
            'name' => 'Gérant',
            'email' => 'gerant@example.com',
            'password' => Hash::make('password'),
        ]);

        $gerant->assignRole('gerant');

        $vendeur = User::create([
            'name' => 'Vendeur',
            'email' => 'vendeur@example.com',
            'password' => Hash::make('password'),
        ]);

        $vendeur->assignRole('vendeur');
    }
} 