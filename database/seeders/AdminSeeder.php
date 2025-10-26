<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    // Vérifie si l'admin existe déjà
    $adminEmail = 'admin@dskt.com';

    // Génère un mot de passe aléatoire sécurisé
    $randomPassword = Str::random(12); // 12 caractères aléatoires

    // Crée ou met à jour l'admin avec le mot de passe dès l'insertion
    $admin = User::updateOrCreate(
        ['email' => $adminEmail],
        [
            'name' => 'Admin',
            'surname'=> 'Admin',
            'role' => 'admin',
            'is_validated' => true,
            'must_change_password' => true,
            'password' => Hash::make($randomPassword), // obligatoire pour l'insertion
        ]
    );

    // Affiche le mot de passe uniquement si c'est un nouvel admin
    if ($admin->wasRecentlyCreated) {
        Log::info("Admin par défaut créé : {$adminEmail} | Mot de passe : {$randomPassword}");
    }
}
   
}
