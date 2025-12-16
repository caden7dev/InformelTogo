<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Affiche le formulaire de création d'une nouvelle transaction.
     */
    public function create()
{
    // Récupère toutes les catégories globales + les catégories de l'utilisateur
    $categories = Category::whereNull('user_id') // Catégories globales
        ->orWhere('user_id', auth()->id()) // + Catégories personnelles
        ->orderBy('user_id') // Les globales d'abord
        ->orderBy('name')
        ->get();
    
    return view('transactions.create', compact('categories'));
}
    /**
     * Affiche la liste des transactions
     */
    public function index()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('date_transaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calcul des totaux
        $totalIncome = Transaction::where('user_id', auth()->id())
                            ->where('type', 'income')
                            ->sum('montant');
        
        $totalExpense = Transaction::where('user_id', auth()->id())
                            ->where('type', 'expense')
                            ->sum('montant');
        
        $totalTransactions = Transaction::where('user_id', auth()->id())->count();
        $balance = $totalIncome - $totalExpense;

        // Récupérer les catégories de l'utilisateur pour les filtres
        $categories = Category::where('user_id', auth()->id())
                        ->orWhereNull('user_id')
                        ->get();

        // Pour l'espace commerçant, on n'a pas besoin de la liste des users
        $users = collect(); // Tableau vide

        return view('transactions.index', compact(
            'transactions', 
            'totalIncome', 
            'totalExpense', 
            'totalTransactions',
            'balance',
            'categories',
            'users'
        ));
    }

    /**
     * Gère la validation et l'enregistrement d'une nouvelle transaction.
     */
    public function store(Request $request)
{
    // 1. Validation des données
    $validated = $request->validate([
        'description' => 'required|string|max:500',
        'montant' => 'required|numeric|min:0.01',
        'type' => 'required|in:income,expense',
        'category_id' => 'required|exists:categories,id',
        'date_transaction' => 'required|date|before_or_equal:today'
    ]);

    // Vérifier que la catégorie est globale ou appartient à l'utilisateur
    $category = Category::find($validated['category_id']);
    if ($category->user_id && $category->user_id !== auth()->id()) {
        return redirect()->back()->with('error', 'Catégorie non autorisée.')->withInput();
    }

    // 2. Ajout de l'ID utilisateur
    $validated['user_id'] = auth()->id();

    // 3. Enregistrement de la transaction
    $transaction = Transaction::create($validated);

    // 4. Redirection vers le dashboard du commerçant
    $typeLabel = $transaction->type == 'income' ? 'recette' : 'dépense';
    return redirect()->route('dashboard')->with('success', 
        "{$typeLabel} de " . $this->formatCurrency($transaction->montant) . " enregistrée avec succès!"
    );
}

    /**
     * Affiche le formulaire d'édition d'une transaction
     */
   public function edit(Transaction $transaction)
{
    // Vérifier que l'utilisateur peut modifier cette transaction
    if ($transaction->user_id !== auth()->id()) {
        return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
    }

    $categories = Category::whereNull('user_id') // Globales
        ->orWhere('user_id', auth()->id()) // Personnelles
        ->orderBy('user_id')
        ->orderBy('name')
        ->get();

    return view('transactions.edit', compact('transaction', 'categories'));
}

    /**
     * Met à jour une transaction existante
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Vérifier que l'utilisateur peut modifier cette transaction
        if ($transaction->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'montant' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'date_transaction' => 'required|date|before_or_equal:today'
        ]);

        // Vérifier que la catégorie appartient à l'utilisateur ou est globale
        $category = Category::find($validated['category_id']);
        if ($category->user_id && $category->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Catégorie non autorisée.')->withInput();
        }

        $transaction->update($validated);

        return redirect()->route('dashboard')->with('success', 
            'Transaction de ' . $this->formatCurrency($transaction->montant) . ' modifiée avec succès!'
        );
    }

    /**
     * Supprime une transaction
     */
    public function destroy(Transaction $transaction)
    {
        // Vérifier que l'utilisateur peut supprimer cette transaction
        if ($transaction->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $montant = $transaction->montant;
        $typeLabel = $transaction->type == 'income' ? 'recette' : 'dépense';
        
        $transaction->delete();

        return redirect()->route('dashboard')->with('success', 
            "{$typeLabel} de " . $this->formatCurrency($montant) . " supprimée avec succès!"
        );
    }

    /**
     * Affiche les transactions par type
     */
    public function byType($type)
    {
        if (!in_array($type, ['income', 'expense'])) {
            return redirect()->route('dashboard')->with('error', 'Type de transaction invalide.');
        }

        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->where('type', $type)
            ->orderBy('date_transaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $total = Transaction::where('user_id', auth()->id())
                        ->where('type', $type)
                        ->sum('montant');

        $totalTransactions = $transactions->total();
        $totalIncome = $type === 'income' ? $total : 0;
        $totalExpense = $type === 'expense' ? $total : 0;
        $balance = $totalIncome - $totalExpense;

        return view('transactions.index', compact(
            'transactions', 
            'type', 
            'total',
            'totalTransactions',
            'totalIncome',
            'totalExpense',
            'balance'
        ));
    }

    /**
     * Affiche les transactions par catégorie
     */
    public function byCategory(Category $category)
    {
        // Vérifier que l'utilisateur peut voir cette catégorie
        if ($category->user_id && $category->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->where('category_id', $category->id)
            ->orderBy('date_transaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $total = $transactions->sum('montant');
        $totalTransactions = $transactions->total();
        $totalIncome = $transactions->where('type', 'income')->sum('montant');
        $totalExpense = $transactions->where('type', 'expense')->sum('montant');
        $balance = $totalIncome - $totalExpense;

        return view('transactions.index', compact(
            'transactions', 
            'category', 
            'total',
            'totalTransactions',
            'totalIncome',
            'totalExpense',
            'balance'
        ));
    }

    /**
     * Filtre les transactions par période
     */
public function filter(Request $request)
{
    $query = Transaction::with('category')
        ->where('user_id', auth()->id());

    // ... le reste du code existant ...

    $categories = Category::whereNull('user_id')
        ->orWhere('user_id', auth()->id())
        ->orderBy('user_id')
        ->orderBy('name')
        ->get();

    return view('transactions.index', compact(
        'transactions', 
        'totalIncome', 
        'totalExpense', 
        'totalTransactions',
        'balance',
        'categories'
    ))->with('filters', $request->all());
}
    /**
     * Export des transactions
     */
    public function export(Request $request)
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('date_transaction', 'desc')
            ->get();

        // Pour l'instant, retourner une vue d'export simple
        // Vous pouvez implémenter l'export CSV/Excel plus tard
        return view('transactions.export', compact('transactions'));
    }

    /**
     * Import des transactions
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240'
        ]);

        // Logique d'import basique - à étendre selon vos besoins
        try {
            // Ici vous pouvez utiliser Maatwebsite/Laravel-Excel ou lire le fichier manuellement
            return back()->with('success', 'Import en cours de développement. Fonctionnalité à venir.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    /**
     * Statistiques des transactions
     */
    public function stats(Request $request)
    {
        $user = auth()->user();
        
        // Statistiques générales
        $stats = [
            'total_transactions' => Transaction::where('user_id', $user->id)->count(),
            'total_income' => Transaction::where('user_id', $user->id)->where('type', 'income')->sum('montant'),
            'total_expense' => Transaction::where('user_id', $user->id)->where('type', 'expense')->sum('montant'),
        ];
        
        $stats['balance'] = $stats['total_income'] - $stats['total_expense'];
        
        // Statistiques mensuelles
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $stats['current_month'] = [
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
        ];

        // Si c'est une requête AJAX, retourner JSON
        if ($request->expectsJson()) {
            return response()->json($stats);
        }

        // Sinon, retourner la vue des statistiques
        return view('transactions.stats', compact('stats'));
    }

    /**
     * Récupère les transactions récentes pour l'API
     */
    public function recent()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('date_transaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($transactions);
    }

    /**
     * Formate un montant en FCFA
     */
    private function formatCurrency($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
}