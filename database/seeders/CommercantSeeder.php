<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommercantSeeder extends Seeder
{
    public function run()
    {
        $commercants = [
            [
                'name' => 'ADIKPIYI Yannick',
                'email' => 'yannick@gmail.com',
                'password' => 'yannick1234', // Mot de passe en clair
                'region' => 'Lomé',
                'secteur' => 'Commerce',
            ],
            [
                'name' => 'AHADZI caden', 
                'email' => 'ahadzicaden@gmail.com',
                'password' => 'blandine', // Mot de passe en clair
                'region' => 'Kara',
                'secteur' => 'Restauration',
            ],
            [
                'name' => 'Jean Akakpo',
                'email' => 'jean@gmail.com',
                'password' => 'jean1234', // Mot de passe en clair
                'region' => 'Lomé',
                'secteur' => 'Services',
            ]
        ];

        foreach ($commercants as $commercant) {
            // Vérifier si le commerçant existe déjà
            if (!User::where('email', $commercant['email'])->exists()) {
                User::create([
                    'name' => $commercant['name'],
                    'email' => $commercant['email'],
                    'password' => Hash::make($commercant['password']), // 🔥 CORRECTION ICI
                    'role' => 'commercant',
                    'region' => $commercant['region'],
                    'secteur' => $commercant['secteur'],
                    'email_verified_at' => now(),
                ]);
                
                $this->command->info("✅ Commerçant créé: {$commercant['email']}");
            } else {
                $this->command->info("ℹ️ Commerçant existe déjà: {$commercant['email']}");
            }
        }

        $this->command->info('🎉 Commerçants de test créés avec succès!');
    }
}