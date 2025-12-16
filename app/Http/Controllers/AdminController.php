<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_merchants' => User::where('role', 'commercant')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_transactions' => Transaction::count(),
            'total_income' => Transaction::where('type', 'income')->sum('montant') ?? 0,
            'total_expense' => Transaction::where('type', 'expense')->sum('montant') ?? 0,
        ];

        $stats['balance'] = $stats['total_income'] - $stats['total_expense'];
        $stats['average_transaction'] = $stats['total_transactions'] > 0 ? 
            ($stats['total_income'] + $stats['total_expense']) / $stats['total_transactions'] : 0;

        // Utilisateurs récents
        $recentUsers = User::latest()->take(5)->get();
        
        // Transactions récentes
        $recentTransactions = Transaction::with(['user', 'category'])->latest()->take(10)->get();

        // Utilisateurs par région
        $usersByRegion = User::select('region', DB::raw('count(*) as count'))
            ->whereNotNull('region')
            ->groupBy('region')
            ->get();

        // Transactions des 6 derniers mois
        $monthlyTransactions = $this->getMonthlyTransactions();

        return view('admin.dashboard', compact(
            'stats', 
            'recentUsers',
            'recentTransactions',
            'usersByRegion', 
            'monthlyTransactions'
        ));
    }

    public function stats()
    {
        // Statistiques détaillées des utilisateurs
        $userStats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();

        // Statistiques des transactions
        $transactionStats = Transaction::select('type', DB::raw('count(*) as count, sum(montant) as total'))
            ->groupBy('type')
            ->get();

        // Top utilisateurs par volume de transactions
        $topUsers = User::withCount('transactions')
            ->with(['transactions' => function($query) {
                $query->select('user_id', 'type', 'montant');
            }])
            ->get()
            ->map(function($user) {
                $user->total_income = $user->transactions->where('type', 'income')->sum('montant') ?? 0;
                $user->total_expense = $user->transactions->where('type', 'expense')->sum('montant') ?? 0;
                $user->balance = $user->total_income - $user->total_expense;
                return $user;
            })
            ->sortByDesc('transactions_count')
            ->take(10);

        return view('admin.stats', compact(
            'userStats', 
            'transactionStats', 
            'topUsers'
        ));
    }

    /**
     * Affiche toutes les transactions (admin)
     */
    public function allTransactions(Request $request)
    {
        $query = Transaction::with(['user', 'category']);

        // Filtrage par type
        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        // Filtrage par utilisateur
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrage par date
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date_transaction', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Filtrage par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $transactions = $query->orderBy('date_transaction', 'desc')->paginate(20);
        
        // Calcul des totaux
        $totalTransactions = Transaction::count();
        $totalIncome = Transaction::where('type', 'income')->sum('montant') ?? 0;
        $totalExpense = Transaction::where('type', 'expense')->sum('montant') ?? 0;
        $balance = $totalIncome - $totalExpense;

        // Données pour les filtres
        $users = User::all(['id', 'name', 'email']);
        $categories = Category::all();

        return view('admin.transactions.index', compact(
            'transactions',
            'totalTransactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'users',
            'categories'
        ));
    }

    /**
     * Affiche le formulaire de création de transaction (admin)
     */
    public function createTransaction()
    {
        $users = User::all(['id', 'name', 'email']);
        $categories = Category::all();
        return view('admin.transactions.create', compact('users', 'categories'));
    }

    /**
     * Enregistre une nouvelle transaction (admin)
     */
    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'date_transaction' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string|max:500'
        ]);

        Transaction::create($validated);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction créée avec succès!');
    }

    /**
     * Affiche le formulaire d'édition de transaction (admin)
     */
    public function editTransaction(Transaction $transaction)
    {
        $users = User::all(['id', 'name', 'email']);
        $categories = Category::all();
        return view('admin.transactions.edit', compact('transaction', 'users', 'categories'));
    }

    /**
     * Met à jour une transaction (admin)
     */
    public function updateTransaction(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense',
            'date_transaction' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction mise à jour avec succès!');
    }

    /**
     * Supprime une transaction (admin)
     */
    public function destroyTransaction(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaction supprimée avec succès!');
    }

    /**
     * Gestion des utilisateurs
     */
    public function users()
    {
        $users = User::withCount('transactions')
            ->select('users.*')
            ->selectRaw('COALESCE((
                SELECT SUM(montant) 
                FROM transactions 
                WHERE transactions.user_id = users.id 
                AND transactions.type = "income"
            ), 0) as total_income')
            ->selectRaw('COALESCE((
                SELECT SUM(montant) 
                FROM transactions 
                WHERE transactions.user_id = users.id 
                AND transactions.type = "expense"
            ), 0) as total_expense')
            ->selectRaw('COALESCE((
                SELECT SUM(montant) 
                FROM transactions 
                WHERE transactions.user_id = users.id 
                AND transactions.type = "income"
            ), 0) - COALESCE((
                SELECT SUM(montant) 
                FROM transactions 
                WHERE transactions.user_id = users.id 
                AND transactions.type = "expense"
            ), 0) as balance')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,commercant',
            'region' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        // Hasher le mot de passe
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now(); // Email vérifié automatiquement

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,commercant',
            'region' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean'
        ]);

        // Ne mettre à jour le mot de passe que si fourni
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès!');
    }

    public function destroyUser(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès!');
    }

    public function reports()
    {
        // Statistiques pour les rapports
        $reportData = [
            'total_users' => User::count(),
            'total_transactions' => Transaction::count(),
            'total_income' => Transaction::where('type', 'income')->sum('montant') ?? 0,
            'total_expense' => Transaction::where('type', 'expense')->sum('montant') ?? 0,
            'users_by_role' => User::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get(),
            'transactions_by_month' => $this->getYearlyTransactionData()
        ];

        return view('admin.reports', compact('reportData'));
    }

    /**
     * Méthodes privées pour les statistiques
     */
    private function getMonthlyTransactions()
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->translatedFormat('M Y');
            
            $incomeData[] = Transaction::where('type', 'income')
                ->whereYear('date_transaction', $month->year)
                ->whereMonth('date_transaction', $month->month)
                ->sum('montant') ?? 0;

            $expenseData[] = Transaction::where('type', 'expense')
                ->whereYear('date_transaction', $month->year)
                ->whereMonth('date_transaction', $month->month)
                ->sum('montant') ?? 0;
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expense' => $expenseData
        ];
    }

    private function getYearlyTransactionData()
    {
        $data = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(now()->year, $month, 1);
            $data[] = [
                'month' => $date->translatedFormat('M'),
                'income' => Transaction::where('type', 'income')
                    ->whereYear('date_transaction', $date->year)
                    ->whereMonth('date_transaction', $date->month)
                    ->sum('montant') ?? 0,
                'expense' => Transaction::where('type', 'expense')
                    ->whereYear('date_transaction', $date->year)
                    ->whereMonth('date_transaction', $date->month)
                    ->sum('montant') ?? 0
            ];
        }

        return $data;
    }

    /**
     * API Methods for AJAX requests
     */
 public function apiStats()
{
    return response()->json([
        'totalUsers' => User::count(),
        'totalTransactions' => Transaction::count(),
        'totalVolume' => Transaction::sum('montant') ?? 0, // Changé de amount à montant
        'globalBalance' => Transaction::where('type', 'income')->sum('montant') - 
                          Transaction::where('type', 'expense')->sum('montant'),
        'totalIncome' => Transaction::where('type', 'income')->sum('montant') ?? 0, // Changé de amount à montant
        'totalExpense' => Transaction::where('type', 'expense')->sum('montant') ?? 0, // Changé de amount à montant
        'averageTransaction' => Transaction::count() > 0 ? 
                               Transaction::avg('montant') : 0, // Changé de amount à montant
        'activeMerchants' => User::where('role', 'commercant')->count(),
        'transactionsPerUser' => User::count() > 0 ? 
                                Transaction::count() / User::count() : 0,
        'volumePerUser' => User::count() > 0 ? 
                          (Transaction::sum('montant') ?? 0) / User::count() : 0, // Changé de amount à montant
        'currentMonthIncome' => Transaction::where('type', 'income')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('montant') ?? 0, // Changé de amount à montant
        'currentMonthExpense' => Transaction::where('type', 'expense')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('montant') ?? 0, // Changé de amount à montant
        'currentMonthTransactions' => Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count(),
    ]);
}
    public function apiTransactions(Request $request)
    {
        $transactions = Transaction::with(['user', 'category'])
            ->latest()
            ->take($request->get('limit', 10))
            ->get();

        return response()->json($transactions);
    }

    /**
 * Afficher la page d'envoi de notifications
 */
public function sendNotification()
{
    $users = User::all(['id', 'name', 'email', 'role']);
    $recentNotifications = Notification::with(['user', 'sender'])
        ->whereHas('sender', function($query) {
            $query->where('id', auth()->id());
        })
        ->latest()
        ->take(10)
        ->get();
    
    return view('admin.notifications.send', compact('users', 'recentNotifications'));
}
}