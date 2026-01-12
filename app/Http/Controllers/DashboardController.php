<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal
     */
    public function index()
    {
        $user = auth()->user();
        
        // Vérification de l'existence des tables
        if (!\Schema::hasTable('transactions') || !\Schema::hasTable('categories')) {
            Log::warning('Tables manquantes pour le dashboard', ['user_id' => $user->id]);
            return view('dashboard', $this->getEmptyDashboardData());
        }

        try {
            // Récupération optimisée des données
            $dashboardData = $this->getDashboardData($user->id);
            
            return view('dashboard', $dashboardData);

        } catch (\Exception $e) {
            Log::error('Erreur dashboard user ' . $user->id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('dashboard', $this->getEmptyDashboardData())
                ->with('error', 'Erreur lors du chargement des données');
        }
       
    $recentGoals = Auth::user()->goals()
        ->where('completed', false)
        ->orderBy('deadline')
        ->limit(3)
        ->get();

    return view('dashboard', compact(
        'totalIncome',
        'totalExpense',
        'balance',
        'transactions',
        'expenseCategories',
        'chartLabels',
        'chartIncome',
        'chartExpense',
        'recentGoals' // Ajouter ceci
    ));
    }

    /**
     * Récupère toutes les données du dashboard de manière optimisée
     */
    private function getDashboardData($userId)
    {
        // Données de base
        $baseData = [
            'totalIncome' => $this->getMonthlyTotal($userId, 'income'),
            'totalExpense' => $this->getMonthlyTotal($userId, 'expense'),
            'balance' => $this->getCurrentBalance($userId),
            'transactions' => $this->getRecentTransactions($userId),
            'chartLabels' => [],
            'chartIncome' => [],
            'chartExpense' => [],
            'expenseCategories' => collect(),
            'quickStats' => $this->getQuickStats($userId)
        ];

        // Données du graphique
        $chartData = $this->getChartData($userId);
        $baseData['chartLabels'] = $chartData['labels'];
        $baseData['chartIncome'] = $chartData['income'];
        $baseData['chartExpense'] = $chartData['expense'];

        // Catégories de dépenses
        $baseData['expenseCategories'] = $this->getExpenseCategories($userId);

        return $baseData;
    }

    /**
     * Récupère le total mensuel par type
     */
    private function getMonthlyTotal($userId, $type)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', $type)
            ->whereYear('date_transaction', now()->year)
            ->whereMonth('date_transaction', now()->month)
            ->sum('montant') ?? 0;
    }

    /**
     * Récupère les transactions récentes
     */
    private function getRecentTransactions($userId, $limit = 10)
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->orderBy('date_transaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Calcule le solde actuel
     */
    private function getCurrentBalance($userId)
    {
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('montant') ?? 0;

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->sum('montant') ?? 0;

        return $totalIncome - $totalExpense;
    }

    /**
     * Génère les données pour le graphique
     */
    private function getChartData($userId)
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->translatedFormat('M');
            
            $incomeData[] = $this->getMonthlyTotalByDate($userId, 'income', $month);
            $expenseData[] = $this->getMonthlyTotalByDate($userId, 'expense', $month);
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    /**
     * Récupère le total pour un mois spécifique
     */
    private function getMonthlyTotalByDate($userId, $type, Carbon $date)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', $type)
            ->whereYear('date_transaction', $date->year)
            ->whereMonth('date_transaction', $date->month)
            ->sum('montant') ?? 0;
    }

    /**
     * Récupère la répartition des dépenses par catégorie
     */
    private function getExpenseCategories($userId)
    {
        $categories = Category::where('type', 'expense')->get();
        
        $categoriesWithTotals = $categories->map(function($category) use ($userId) {
            $total = Transaction::where('user_id', $userId)
                ->where('category_id', $category->id)
                ->whereYear('date_transaction', now()->year)
                ->whereMonth('date_transaction', now()->month)
                ->sum('montant') ?? 0;
                
            return [
                'id' => $category->id,
                'name' => $category->name,
                'total_amount' => $total,
                'color' => $category->color ?? $this->generateColor($category->id),
                'icon' => $category->icon ?? 'fa-folder',
                'percentage' => 0
            ];
        })->where('total_amount', '>', 0)->values();

        // Calcul des pourcentages
        $totalExpense = $categoriesWithTotals->sum('total_amount');
        
        if ($totalExpense > 0) {
            $categoriesWithTotals = $categoriesWithTotals->map(function($category) use ($totalExpense) {
                $category['percentage'] = round(($category['total_amount'] / $totalExpense) * 100, 1);
                return $category;
            });
        }

        return $categoriesWithTotals->sortByDesc('total_amount');
    }

    /**
     * Statistiques rapides pour aujourd'hui
     */
    private function getQuickStats($userId)
    {
        $today = now()->toDateString();

        $todayIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereDate('date_transaction', $today)
            ->sum('montant') ?? 0;

        $todayExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereDate('date_transaction', $today)
            ->sum('montant') ?? 0;

        return [
            'today_income' => $todayIncome,
            'today_expense' => $todayExpense,
            'daily_balance' => $todayIncome - $todayExpense,
            'transactions_count' => Transaction::where('user_id', $userId)
                ->whereDate('date_transaction', $today)
                ->count()
        ];
    }

    /**
     * Génère une couleur basée sur l'ID de la catégorie
     */
    private function generateColor($categoryId)
    {
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF',
            '#FF6384', '#4BC0C0', '#9966FF', '#FF9F40',
            '#36A2EB', '#FFCE56', '#C9CBCF', '#FF6384'
        ];
        
        return $colors[$categoryId % count($colors)];
    }

    /**
     * Données par défaut pour le dashboard
     */
    private function getEmptyDashboardData()
    {
        $emptyChart = $this->getEmptyChartData();
        
        return [
            'totalIncome' => 0,
            'totalExpense' => 0,
            'balance' => 0,
            'transactions' => collect(),
            'chartLabels' => $emptyChart['labels'],
            'chartIncome' => $emptyChart['income'],
            'chartExpense' => $emptyChart['expense'],
            'expenseCategories' => collect(),
            'quickStats' => [
                'today_income' => 0,
                'today_expense' => 0,
                'daily_balance' => 0,
                'transactions_count' => 0
            ]
        ];
    }

    private function getEmptyChartData()
    {
        return [
            'labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            'income' => [0, 0, 0, 0, 0, 0],
            'expense' => [0, 0, 0, 0, 0, 0]
        ];
    }

    // ==================== MÉTHODES API ====================

    /**
     * Endpoint API pour les statistiques rapides
     */
    public function quickStats()
    {
        $user = auth()->user();
        
        try {
            $stats = $this->getQuickStats($user->id);
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('API QuickStats error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'data' => [
                    'today_income' => 0,
                    'today_expense' => 0,
                    'daily_balance' => 0,
                    'transactions_count' => 0
                ]
            ]);
        }
    }

    /**
     * Endpoint API pour les données du graphique
     */
    public function chartData(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', '6months');

        try {
            $chartData = $this->getChartDataForPeriod($user->id, $period);
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
            
        } catch (\Exception $e) {
            Log::error('API ChartData error', [
                'user_id' => $user->id,
                'period' => $period,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'data' => $this->getEmptyChartData()
            ]);
        }
    }

    /**
     * Données du graphique par période
     */
    private function getChartDataForPeriod($userId, $period)
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        switch ($period) {
            case '1year':
                $monthCount = 12;
                break;
            case '3months':
                $monthCount = 3;
                break;
            default: // 6months
                $monthCount = 6;
        }

        for ($i = $monthCount - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->translatedFormat('M');
            
            $incomeData[] = $this->getMonthlyTotalByDate($userId, 'income', $month);
            $expenseData[] = $this->getMonthlyTotalByDate($userId, 'expense', $month);
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    /**
     * Récupère les statistiques mensuelles détaillées
     */
    public function monthlyStats()
    {
        $user = auth()->user();
        
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $stats = [
                'current_month' => [
                    'income' => $this->getMonthlyTotal($user->id, 'income'),
                    'expense' => $this->getMonthlyTotal($user->id, 'expense'),
                    'balance' => $this->getMonthlyTotal($user->id, 'income') - $this->getMonthlyTotal($user->id, 'expense')
                ],
                'previous_month' => [
                    'income' => $this->getMonthlyTotalByDate($user->id, 'income', now()->subMonth()),
                    'expense' => $this->getMonthlyTotalByDate($user->id, 'expense', now()->subMonth()),
                    'balance' => $this->getMonthlyTotalByDate($user->id, 'income', now()->subMonth()) - $this->getMonthlyTotalByDate($user->id, 'expense', now()->subMonth())
                ],
                'yearly_total' => [
                    'income' => Transaction::where('user_id', $user->id)
                        ->where('type', 'income')
                        ->whereYear('date_transaction', $currentYear)
                        ->sum('montant') ?? 0,
                    'expense' => Transaction::where('user_id', $user->id)
                        ->where('type', 'expense')
                        ->whereYear('date_transaction', $currentYear)
                        ->sum('montant') ?? 0
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('API MonthlyStats error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des statistiques',
                'data' => []
            ], 500);
        }
    }

    /**
     * Filtrage des données du dashboard
     */
    public function filter(Request $request)
    {
        $user = auth()->user();
        
        $validator = validator($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'nullable|in:income,expense'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $categoryId = $request->get('category_id');
            $type = $request->get('type');

            $query = Transaction::where('user_id', $user->id);

            if ($startDate && $endDate) {
                $query->whereBetween('date_transaction', [$startDate, $endDate]);
            }

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($type) {
                $query->where('type', $type);
            }

            $filteredData = [
                'transactions' => $query->with('category')
                    ->orderBy('date_transaction', 'desc')
                    ->take(20)
                    ->get(),
                'total_income' => (clone $query)->where('type', 'income')->sum('montant') ?? 0,
                'total_expense' => (clone $query)->where('type', 'expense')->sum('montant') ?? 0,
                'balance' => $this->getCurrentBalance($user->id)
            ];

            return response()->json([
                'success' => true,
                'data' => $filteredData
            ]);

        } catch (\Exception $e) {
            Log::error('Filter error', [
                'user_id' => $user->id,
                'filters' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du filtrage des données'
            ], 500);
        }
    }

    // ==================== MÉTHODES RAPPORTS ====================

    /**
     * Rapport financier détaillé
     */
    public function financialReport()
    {
        $user = auth()->user();
        
        try {
            $data = [
                'totalIncome' => $this->getCurrentBalance($user->id) > 0 ? $this->getMonthlyTotal($user->id, 'income') : 0,
                'totalExpense' => $this->getMonthlyTotal($user->id, 'expense'),
                'balance' => $this->getCurrentBalance($user->id),
                'monthlyTrend' => $this->getMonthlyTrend($user->id),
                'topCategories' => $this->getTopCategories($user->id),
                'period' => now()->translatedFormat('F Y'),
                'yearlySummary' => $this->getYearlySummary($user->id)
            ];
            
            return view('reports.financial', $data);
            
        } catch (\Exception $e) {
            Log::error('Financial report error', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Rapport par catégorie
     */
    public function categoricalReport()
    {
        $user = auth()->user();
        
        try {
            $data = [
                'incomeCategories' => $this->getIncomeCategories($user->id),
                'expenseCategories' => $this->getExpenseCategories($user->id),
                'period' => now()->translatedFormat('F Y')
            ];
            
            return view('reports.categorical', $data);
            
        } catch (\Exception $e) {
            Log::error('Categorical report error', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Rapport mensuel
     */
    public function monthlyReport()
    {
        $user = auth()->user();
        
        try {
            $data = [
                'monthlyData' => $this->getYearlyMonthlyData($user->id),
                'year' => now()->year,
                'annualSummary' => $this->getAnnualSummary($user->id)
            ];
            
            return view('reports.monthly', $data);
            
        } catch (\Exception $e) {
            Log::error('Monthly report error', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Erreur lors de la génération du rapport');
        }
    }

    /**
     * Export PDF
     */
    public function exportPDF()
    {
        $user = auth()->user();
        
        try {
            $data = $this->getDashboardData($user->id);
            $data['export_date'] = now()->format('d/m/Y H:i');
            $data['user'] = $user;
            
            // Pour l'instant on retourne une vue
            // Vous pouvez installer dompdf ou barryvdh/laravel-dompdf plus tard
            return view('exports.dashboard-pdf', $data);
            
        } catch (\Exception $e) {
            Log::error('PDF export error', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Erreur lors de l\'export PDF');
        }
    }

    // ==================== MÉTHODES UTILITAIRES RAPPORTS ====================

    private function getMonthlyTrend($userId)
    {
        $currentMonth = $this->getMonthlyTotal($userId, 'income') - $this->getMonthlyTotal($userId, 'expense');
        $previousMonth = $this->getMonthlyTotalByDate($userId, 'income', now()->subMonth()) - $this->getMonthlyTotalByDate($userId, 'expense', now()->subMonth());
        
        if ($previousMonth == 0) return 0;
        
        return (($currentMonth - $previousMonth) / abs($previousMonth)) * 100;
    }

    private function getTopCategories($userId, $limit = 5)
    {
        return Category::with(['transactions' => function($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->whereYear('date_transaction', now()->year)
                      ->whereMonth('date_transaction', now()->month);
            }])
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'total' => $category->transactions->sum('montant'),
                    'type' => $category->type,
                    'color' => $category->color
                ];
            })
            ->where('total', '>', 0)
            ->sortByDesc('total')
            ->take($limit)
            ->values();
    }

    private function getIncomeCategories($userId)
    {
        return $this->getCategoriesWithTotals($userId, 'income');
    }

    private function getYearlyMonthlyData($userId)
    {
        $data = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(now()->year, $month, 1);
            $data[] = [
                'month' => $date->translatedFormat('M'),
                'income' => $this->getMonthlyTotalByDate($userId, 'income', $date),
                'expense' => $this->getMonthlyTotalByDate($userId, 'expense', $date),
                'balance' => $this->getMonthlyTotalByDate($userId, 'income', $date) - $this->getMonthlyTotalByDate($userId, 'expense', $date)
            ];
        }
        
        return $data;
    }

    private function getCategoriesWithTotals($userId, $type)
    {
        return Category::where('type', $type)
            ->get()
            ->map(function($category) use ($userId, $type) {
                $total = Transaction::where('user_id', $userId)
                    ->where('category_id', $category->id)
                    ->where('type', $type)
                    ->whereYear('date_transaction', now()->year)
                    ->whereMonth('date_transaction', now()->month)
                    ->sum('montant') ?? 0;
                    
                return [
                    'name' => $category->name,
                    'total' => $total,
                    'color' => $category->color,
                    'percentage' => 0
                ];
            })
            ->where('total', '>', 0)
            ->sortByDesc('total')
            ->values();
    }

    private function getYearlySummary($userId)
    {
        $currentYear = now()->year;
        
        return [
            'total_income' => Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('date_transaction', $currentYear)
                ->sum('montant') ?? 0,
            'total_expense' => Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('date_transaction', $currentYear)
                ->sum('montant') ?? 0,
            'average_monthly_income' => Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('date_transaction', $currentYear)
                ->avg('montant') ?? 0,
            'average_monthly_expense' => Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('date_transaction', $currentYear)
                ->avg('montant') ?? 0
        ];
    }

    private function getAnnualSummary($userId)
    {
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;
        
        return [
            'current_year' => [
                'income' => Transaction::where('user_id', $userId)
                    ->where('type', 'income')
                    ->whereYear('date_transaction', $currentYear)
                    ->sum('montant') ?? 0,
                'expense' => Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereYear('date_transaction', $currentYear)
                    ->sum('montant') ?? 0
            ],
            'previous_year' => [
                'income' => Transaction::where('user_id', $userId)
                    ->where('type', 'income')
                    ->whereYear('date_transaction', $previousYear)
                    ->sum('montant') ?? 0,
                'expense' => Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereYear('date_transaction', $previousYear)
                    ->sum('montant') ?? 0
            ]
        ];
    }
}