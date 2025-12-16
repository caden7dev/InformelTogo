<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BudgetController extends Controller
{
    /**
     * Afficher la liste des budgets
     */
  public function index()
{
    $budgets = Budget::forUser(Auth::id())
        ->with('category')
        ->orderBy('start_date', 'desc') // Retirez orderBy('is_active', 'desc')
        ->get();
        
    return view('budgets.index', compact('budgets'));
}
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $categories = Category::forUser(Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
            
        return view('budgets.create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau budget
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'period' => 'required|in:monthly,quarterly,yearly,custom',
            'amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'has_alert' => 'boolean',
            'alert_threshold' => 'nullable|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['current_amount'] = 0;
        
        // Calculer la date de fin si non spécifiée
        if (empty($validated['end_date']) && $validated['period'] !== 'custom') {
            $validated['end_date'] = $this->calculateEndDate($validated['start_date'], $validated['period']);
        }

        Budget::create($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget créé avec succès.');
    }

    /**
     * Afficher un budget spécifique
     */
    public function show(Budget $budget)
    {
        // Vérifier les permissions
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $statistics = $budget->getStatistics();
        $transactions = $budget->transactions()->latest()->paginate(10);

        return view('budgets.show', compact('budget', 'statistics', 'transactions'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Budget $budget)
    {
        // Vérifier les permissions
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::forUser(Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    /**
     * Mettre à jour un budget
     */
    public function update(Request $request, Budget $budget)
    {
        // Vérifier les permissions
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'period' => 'required|in:monthly,quarterly,yearly,custom',
            'amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'has_alert' => 'boolean',
            'alert_threshold' => 'nullable|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        // Recalculer le montant actuel si la catégorie ou les dates changent
        $categoryChanged = $budget->category_id != $validated['category_id'];
        $datesChanged = $budget->start_date != $validated['start_date'] || $budget->end_date != $validated['end_date'];
        
        if ($categoryChanged || $datesChanged) {
            $validated['current_amount'] = 0;
        }

        // Calculer la date de fin si non spécifiée
        if (empty($validated['end_date']) && $validated['period'] !== 'custom') {
            $validated['end_date'] = $this->calculateEndDate($validated['start_date'], $validated['period']);
        }

        $budget->update($validated);

        // Mettre à jour le montant actuel
        $budget->updateCurrentAmount();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget mis à jour avec succès.');
    }

    /**
     * Supprimer un budget
     */
    public function destroy(Budget $budget)
    {
        // Vérifier les permissions
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget supprimé avec succès.');
    }

    /**
     * Afficher la progression d'un budget
     */
    public function progress(Budget $budget)
    {
        // Vérifier les permissions
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $statistics = $budget->getStatistics();
        
        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Réinitialiser un budget pour une nouvelle période
     */
    public function reset(Budget $budget)
    {
        // Vérifier les permissions
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        if ($budget->resetForNewPeriod()) {
            return redirect()->route('budgets.index')
                ->with('success', 'Budget réinitialisé pour la nouvelle période.');
        }

        return redirect()->route('budgets.index')
            ->with('error', 'Impossible de réinitialiser ce budget.');
    }

    /**
     * Calculer la date de fin en fonction de la période
     */
    private function calculateEndDate($startDate, $period)
    {
        $start = Carbon::parse($startDate);

        switch ($period) {
            case 'monthly':
                return $start->copy()->endOfMonth();
            case 'quarterly':
                return $start->copy()->addMonths(3)->subDay();
            case 'yearly':
                return $start->copy()->addYear()->subDay();
            default:
                return null;
        }
    }
}