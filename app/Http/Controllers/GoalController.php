<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Auth::user()->goals()
            ->orderBy('deadline')
            ->paginate(10);

        $activeGoals = $goals->where('completed', false);
        $completedGoals = $goals->where('completed', true);

        $totalProgress = $activeGoals->sum('current_amount');
        $totalTarget = $activeGoals->sum('target_amount');
        $overallProgress = $totalTarget > 0 ? ($totalProgress / $totalTarget) * 100 : 0;

        return view('goals.index', compact(
            'goals',
            'activeGoals',
            'completedGoals',
            'overallProgress'
        ));
    }

    public function create()
    {
        $types = [
            'savings' => 'Épargne',
            'debt' => 'Remboursement de dette',
            'investment' => 'Investissement',
            'purchase' => 'Achat',
            'other' => 'Autre'
        ];

        $frequencies = [
            'daily' => 'Quotidien',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'yearly' => 'Annuel',
            'one_time' => 'Unique'
        ];

        return view('goals.create', compact('types', 'frequencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'deadline' => 'required|date|after_or_equal:today',
            'type' => 'required|in:savings,debt,investment,purchase,other',
            'frequency' => 'required|in:daily,weekly,monthly,yearly,one_time',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['current_amount'] = $validated['current_amount'] ?? 0;
        $validated['completed'] = false;

        Goal::create($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Objectif créé avec succès.');
    }

    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);

        $progress = $goal->target_amount > 0 
            ? min(100, ($goal->current_amount / $goal->target_amount) * 100) 
            : 0;
        
        $remaining = max(0, $goal->target_amount - $goal->current_amount);
        
        $daysRemaining = Carbon::parse($goal->deadline)->diffInDays(now());
        $dailyRequired = $daysRemaining > 0 ? $remaining / $daysRemaining : 0;
        
        $monthlyRequired = $remaining / max(1, Carbon::parse($goal->deadline)->diffInMonths(now()));

        return view('goals.show', compact(
            'goal',
            'progress',
            'remaining',
            'daysRemaining',
            'dailyRequired',
            'monthlyRequired'
        ));
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);

        $types = [
            'savings' => 'Épargne',
            'debt' => 'Remboursement de dette',
            'investment' => 'Investissement',
            'purchase' => 'Achat',
            'other' => 'Autre'
        ];

        $frequencies = [
            'daily' => 'Quotidien',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'yearly' => 'Annuel',
            'one_time' => 'Unique'
        ];

        return view('goals.edit', compact('goal', 'types', 'frequencies'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'type' => 'required|in:savings,debt,investment,purchase,other',
            'frequency' => 'required|in:daily,weekly,monthly,yearly,one_time',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'completed' => 'boolean',
        ]);

        // Si le montant actuel atteint ou dépasse la cible, marquer comme complété
        if ($validated['current_amount'] >= $validated['target_amount'] && !$validated['completed']) {
            $validated['completed'] = true;
        }

        $goal->update($validated);

        return redirect()->route('goals.index')
            ->with('success', 'Objectif mis à jour avec succès.');
    }

    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Objectif supprimé avec succès.');
    }

    public function progress(Goal $goal)
    {
        $this->authorize('view', $goal);

        $progress = $goal->target_amount > 0 
            ? min(100, ($goal->current_amount / $goal->target_amount) * 100) 
            : 0;
        
        $remaining = max(0, $goal->target_amount - $goal->current_amount);
        
        $daysRemaining = Carbon::parse($goal->deadline)->diffInDays(now());
        $dailyRequired = $daysRemaining > 0 ? $remaining / $daysRemaining : 0;

        return response()->json([
            'success' => true,
            'progress' => round($progress, 2),
            'current_amount' => $goal->current_amount,
            'target_amount' => $goal->target_amount,
            'remaining' => $remaining,
            'days_remaining' => $daysRemaining,
            'daily_required' => round($dailyRequired, 2),
            'formatted_current' => number_format($goal->current_amount, 0, ',', ' ') . ' FCFA',
            'formatted_target' => number_format($goal->target_amount, 0, ',', ' ') . ' FCFA',
            'formatted_remaining' => number_format($remaining, 0, ',', ' ') . ' FCFA',
        ]);
    }

    public function addProgress(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $goal->current_amount += $request->amount;
        
        // Vérifier si l'objectif est atteint
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->completed = true;
        }

        $goal->save();

        return response()->json([
            'success' => true,
            'message' => 'Progression ajoutée avec succès.',
            'current_amount' => $goal->current_amount,
            'progress' => $goal->target_amount > 0 
                ? min(100, ($goal->current_amount / $goal->target_amount) * 100) 
                : 0,
        ]);
    }
}