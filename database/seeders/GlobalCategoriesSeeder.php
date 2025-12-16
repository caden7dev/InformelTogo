<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalCategoriesSeeder extends Seeder
{
    public function run()
    {
        $globalCategories = [
            // Recettes (income)
            ['name' => 'Ventes produits', 'type' => 'income', 'color' => '#10B981'],
            ['name' => 'Services', 'type' => 'income', 'color' => '#059669'],
            ['name' => 'Autres revenus', 'type' => 'income', 'color' => '#047857'],
            
            // Dépenses (expense)
            ['name' => 'Achats stock', 'type' => 'expense', 'color' => '#EF4444'],
            ['name' => 'Loyer local', 'type' => 'expense', 'color' => '#DC2626'],
            ['name' => 'Salaires', 'type' => 'expense', 'color' => '#B91C1C'],
            ['name' => 'Électricité', 'type' => 'expense', 'color' => '#991B1B'],
            ['name' => 'Eau', 'type' => 'expense', 'color' => '#7F1D1D'],
            ['name' => 'Internet/Téléphone', 'type' => 'expense', 'color' => '#F59E0B'],
            ['name' => 'Transport', 'type' => 'expense', 'color' => '#D97706'],
            ['name' => 'Marketing', 'type' => 'expense', 'color' => '#B45309'],
            ['name' => 'Entretien', 'type' => 'expense', 'color' => '#92400E'],
            ['name' => 'Impôts', 'type' => 'expense', 'color' => '#78350F'],
            ['name' => 'Autres dépenses', 'type' => 'expense', 'color' => '#57534E'],
        ];

        foreach ($globalCategories as $category) {
            // Vérifier si la catégorie globale existe déjà
            $exists = Category::where('name', $category['name'])
                ->whereNull('user_id')
                ->where('type', $category['type'])
                ->exists();

            if (!$exists) {
                Category::create([
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'color' => $category['color'],
                    'user_id' => null, // Explicitement null pour les catégories globales
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Catégories globales créées avec succès!');
    }
}