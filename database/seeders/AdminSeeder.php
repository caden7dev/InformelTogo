<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $adminExists = User::where('email', 'admin@plateforme-togo.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrateur Plateforme Togo',
                'email' => 'admin@plateforme-togo.com',
                'password' => 'Admin123!', // ⚠️ NE PAS utiliser Hash::make() ici
                'role' => 'admin',
                'region' => 'Lomé',
                'secteur' => 'Administration',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Administrateur créé avec succès!');
            $this->command->info('📧 Email: admin@plateforme-togo.com');
            $this->command->info('🔑 Mot de passe: Admin123!');
        } else {
            $this->command->info('ℹ️ L\'administrateur existe déjà.');
            
            // Optionnel: Réinitialiser le mot de passe
            $admin = User::where('email', 'admin@plateforme-togo.com')->first();
            $admin->password = 'Admin123!'; // ⚠️ Directement le mot de passe en clair
            $admin->save();
            $this->command->info('🔄 Mot de passe réinitialisé.');
        }
    }
}