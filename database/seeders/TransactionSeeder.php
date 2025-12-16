<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $categories = Category::all();

        if (!$user || $categories->isEmpty()) {
            $this->command->warn('Aucun utilisateur ou catégorie trouvé. Skipping TransactionSeeder.');
            return;
        }

        $transactions = [
            // Revenus
            ['Salaire du mois', 250000, 'income', 'salaire', 0],
            ['Projet freelance', 75000, 'income', 'freelance', 1],
            ['Dividendes investissements', 15000, 'income', 'dividendes', 7],
            
            // Dépenses
            ['Courses alimentaires', 45000, 'expense', 'alimentation', 0],
            ['Essence voiture', 25000, 'expense', 'transport', 1],
            ['Loyer appartement', 80000, 'expense', 'logement', 2],
            ['Cinéma', 12000, 'expense', 'loisirs', 3],
            ['Consultation médicale', 15000, 'expense', 'sante', 4],
            ['Achat livres', 8000, 'expense', 'education', 5],
            ['Vêtements', 35000, 'expense', 'shopping', 6],
            ['Dîner restaurant', 18000, 'expense', 'restaurant', 7],
        ];

        foreach ($transactions as $transaction) {
            $category = $categories->where('name', $transaction[3])->first();
            
            if ($category) {
                Transaction::create([
                    'description' => $transaction[0],
                    'montant' => $transaction[1],
                    'type' => $transaction[2],
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'date_transaction' => Carbon::now()->subDays(rand(1, 30)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Transactions de test créées avec succès!');
    }
}