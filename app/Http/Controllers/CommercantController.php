<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommercantController extends Controller
{
    /**
     * Dashboard spécifique au commerçant
     */
    public function dashboard()
    {
        $user = Auth::user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Statistiques du mois en cours
        $monthlyIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereYear('date_transaction', $currentYear)
            ->whereMonth('date_transaction', $currentMonth)
            ->sum('montant');

        $monthlyExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereYear('date_transaction', $currentYear)
            ->whereMonth('date_transaction', $currentMonth)
            ->sum('montant');

        // Transactions récentes
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('date_transaction', 'desc')
            ->take(10)
            ->get();

        // Catégories de dépenses du mois
        $expenseCategories = Category::where('type', 'expense')
            ->with(['transactions' => function($query) use ($user, $currentYear, $currentMonth) {
                $query->where('user_id', $user->id)
                    ->whereYear('date_transaction', $currentYear)
                    ->whereMonth('date_transaction', $currentMonth);
            }])
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'amount' => $category->transactions->sum('montant'),
                    'color' => $category->color,
                    'percentage' => 0
                ];
            })
            ->where('amount', '>', 0)
            ->sortByDesc('amount')
            ->values();

        // Calcul des pourcentages pour les catégories
        $totalExpenses = $expenseCategories->sum('amount');
        if ($totalExpenses > 0) {
            $expenseCategories = $expenseCategories->map(function($category) use ($totalExpenses) {
                $category['percentage'] = round(($category['amount'] / $totalExpenses) * 100, 1);
                return $category;
            });
        }

        // Données pour le graphique (6 derniers mois)
        $chartData = $this->getChartData($user->id);

        return view('commercant.dashboard', compact(
            'monthlyIncome',
            'monthlyExpense',
            'recentTransactions',
            'expenseCategories',
            'chartData'
        ));
    }

    /**
     * Liste des transactions du commerçant
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('date_transaction', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->where('date_transaction', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date_transaction', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate(20);
        $categories = Category::all();

        // Statistiques des filtres appliqués
        $filterStats = [
            'total_income' => (clone $query)->where('type', 'income')->sum('montant'),
            'total_expense' => (clone $query)->where('type', 'expense')->sum('montant'),
            'count' => $transactions->total()
        ];

        return view('commercant.transactions.index', compact(
            'transactions', 
            'categories', 
            'filterStats'
        ));
    }

    /**
     * Afficher le formulaire de création d'une transaction
     */
    public function createTransaction()
    {
        $categories = Category::all();
        return view('commercant.transactions.create', compact('categories'));
    }

    /**
     * Enregistrer une nouvelle transaction
     */
    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'date_transaction' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $validated['user_id'] = Auth::id();

        Transaction::create($validated);

        return redirect()->route('commercant.transactions.index')
            ->with('success', 'Transaction créée avec succès!');
    }

    /**
     * Afficher le formulaire d'édition d'une transaction
     */
    public function editTransaction(Transaction $transaction)
    {
        // Vérifier que la transaction appartient au commerçant
        $this->authorize('update', $transaction);

        $categories = Category::all();
        return view('commercant.transactions.edit', compact('transaction', 'categories'));
    }

    /**
     * Mettre à jour une transaction
     */
    public function updateTransaction(Request $request, Transaction $transaction)
    {
        // Vérifier que la transaction appartient au commerçant
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'date_transaction' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $transaction->update($validated);

        return redirect()->route('commercant.transactions.index')
            ->with('success', 'Transaction mise à jour avec succès!');
    }

    /**
     * Supprimer une transaction
     */
    public function destroyTransaction(Transaction $transaction)
    {
        // Vérifier que la transaction appartient au commerçant
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->route('commercant.transactions.index')
            ->with('success', 'Transaction supprimée avec succès!');
    }

    /**
     * Afficher les détails d'une transaction
     */
    public function showTransaction(Transaction $transaction)
    {
        // Vérifier que la transaction appartient au commerçant
        $this->authorize('view', $transaction);

        return view('commercant.transactions.show', compact('transaction'));
    }

    /**
     * Statistiques détaillées du commerçant
     */
    public function stats(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'month'); // month, quarter, year

        $stats = $this->getDetailedStats($user->id, $period);

        return view('commercant.stats', compact('stats', 'period'));
    }

    /**
     * Rapports personnalisés du commerçant
     */
    public function reports()
    {
        $user = Auth::user();
        
        $reportData = [
            'monthly_comparison' => $this->getMonthlyComparison($user->id),
            'category_analysis' => $this->getCategoryAnalysis($user->id),
            'yearly_summary' => $this->getYearlySummary($user->id)
        ];

        return view('commercant.reports', compact('reportData'));
    }

    /**
     * Export des données du commerçant
     */
    public function exportTransactions(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::where('user_id', $user->id)
            ->with('category')
            ->orderBy('date_transaction', 'desc');

        // Appliquer les mêmes filtres que dans l'index
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_transaction', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->get();

        // Générer le CSV
        $fileName = 'transactions_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, ['Date', 'Description', 'Catégorie', 'Type', 'Montant', 'Notes']);
            
            // Données
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date_transaction->format('d/m/Y'),
                    $transaction->description,
                    $transaction->category->name,
                    $transaction->type == 'income' ? 'Revenu' : 'Dépense',
                    $transaction->montant,
                    $transaction->notes ?? ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tableau de bord rapide (widgets)
     */
    public function quickStats()
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'today' => [
                'income' => Transaction::where('user_id', $user->id)
                    ->where('type', 'income')
                    ->whereDate('date_transaction', $today)
                    ->sum('montant'),
                'expense' => Transaction::where('user_id', $user->id)
                    ->where('type', 'expense')
                    ->whereDate('date_transaction', $today)
                    ->sum('montant'),
                'count' => Transaction::where('user_id', $user->id)
                    ->whereDate('date_transaction', $today)
                    ->count()
            ],
            'month' => [
                'income' => Transaction::where('user_id', $user->id)
                    ->where('type', 'income')
                    ->whereYear('date_transaction', $currentYear)
                    ->whereMonth('date_transaction', $currentMonth)
                    ->sum('montant'),
                'expense' => Transaction::where('user_id', $user->id)
                    ->where('type', 'expense')
                    ->whereYear('date_transaction', $currentYear)
                    ->whereMonth('date_transaction', $currentMonth)
                    ->sum('montant')
            ],
            'balance' => $this->calculateBalance($user->id)
        ];

        return response()->json($stats);
    }

    // ==================== MÉTHODES PRIVÉES ====================

    /**
     * Génère les données pour le graphique
     */
    private function getChartData($userId)
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            
            $incomeData[] = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('date_transaction', $date->year)
                ->whereMonth('date_transaction', $date->month)
                ->sum('montant');
            
            $expenseData[] = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('date_transaction', $date->year)
                ->whereMonth('date_transaction', $date->month)
                ->sum('montant');
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    /**
     * Calcule le solde total
     */
    private function calculateBalance($userId)
    {
        $income = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('montant');
        
        $expense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->sum('montant');

        return $income - $expense;
    }

    /**
     * Statistiques détaillées
     */
    private function getDetailedStats($userId, $period)
    {
        $currentDate = now();
        
        switch ($period) {
            case 'year':
                $startDate = $currentDate->copy()->subYear();
                break;
            case 'quarter':
                $startDate = $currentDate->copy()->subMonths(3);
                break;
            default: // month
                $startDate = $currentDate->copy()->subMonth();
        }

        return [
            'period' => $period,
            'current' => [
                'income' => Transaction::where('user_id', $userId)
                    ->where('type', 'income')
                    ->whereBetween('date_transaction', [$startDate, $currentDate])
                    ->sum('montant'),
                'expense' => Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereBetween('date_transaction', [$startDate, $currentDate])
                    ->sum('montant'),
                'transactions' => Transaction::where('user_id', $userId)
                    ->whereBetween('date_transaction', [$startDate, $currentDate])
                    ->count()
            ],
            'previous' => [
                'income' => Transaction::where('user_id', $userId)
                    ->where('type', 'income')
                    ->whereBetween('date_transaction', [
                        $startDate->copy()->subYear(), 
                        $currentDate->copy()->subYear()
                    ])
                    ->sum('montant'),
                'expense' => Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereBetween('date_transaction', [
                        $startDate->copy()->subYear(), 
                        $currentDate->copy()->subYear()
                    ])
                    ->sum('montant')
            ]
        ];
    }

    /**
     * Comparaison mensuelle
     */
    private function getMonthlyComparison($userId)
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = [
                'month' => $date->translatedFormat('M Y'),
                'income' => Transaction::where('user_id', $userId)
                    ->where('type', 'income')
                    ->whereYear('date_transaction', $date->year)
                    ->whereMonth('date_transaction', $date->month)
                    ->sum('montant'),
                'expense' => Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereYear('date_transaction', $date->year)
                    ->whereMonth('date_transaction', $date->month)
                    ->sum('montant')
            ];
        }

        return $data;
    }

    /**
     * Analyse par catégorie
     */
    private function getCategoryAnalysis($userId)
    {
        $currentYear = now()->year;

        return Category::with(['transactions' => function($query) use ($userId, $currentYear) {
                $query->where('user_id', $userId)
                    ->whereYear('date_transaction', $currentYear);
            }])
            ->get()
            ->map(function($category) {
                $total = $category->transactions->sum('montant');
                return [
                    'name' => $category->name,
                    'type' => $category->type,
                    'total' => $total,
                    'count' => $category->transactions->count(),
                    'color' => $category->color
                ];
            })
            ->where('total', '>', 0)
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Résumé annuel
     */
    private function getYearlySummary($userId)
    {
        $currentYear = now()->year;

        return [
            'total_income' => Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('date_transaction', $currentYear)
                ->sum('montant'),
            'total_expense' => Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('date_transaction', $currentYear)
                ->sum('montant'),
            'average_monthly_income' => Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('date_transaction', $currentYear)
                ->avg('montant'),
            'average_monthly_expense' => Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('date_transaction', $currentYear)
                ->avg('montant'),
            'most_used_category' => $this->getMostUsedCategory($userId, $currentYear)
        ];
    }

    /**
     * Catégorie la plus utilisée
     */
    private function getMostUsedCategory($userId, $year)
    {
        $category = Category::withCount(['transactions' => function($query) use ($userId, $year) {
                $query->where('user_id', $userId)
                    ->whereYear('date_transaction', $year);
            }])
            ->orderBy('transactions_count', 'desc')
            ->first();

        return $category ? [
            'name' => $category->name,
            'count' => $category->transactions_count,
            'color' => $category->color
        ] : null;
    }
}