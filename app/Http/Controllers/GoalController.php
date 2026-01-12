<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Category;
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

        // Statistiques pour le dashboard
        $stats = [
            'total_goals' => $goals->count(),
            'active_goals' => $activeGoals->count(),
            'completed_goals' => $completedGoals->count(),
            'success_rate' => $goals->count() > 0 ? ($completedGoals->count() / $goals->count()) * 100 : 0,
        ];

        return view('goals.index', compact(
            'goals',
            'activeGoals',
            'completedGoals',
            'overallProgress',
            'stats'
        ));
    }

  public function create()
{
    // Types d'objectifs
    $types = [
        'savings' => 'Épargne',
        'expense' => 'Réduction dépenses',
        'income' => 'Augmenter revenus',
        'custom' => 'Personnalisé',
    ];

    // Catégories
    $categories = Category::where('user_id', Auth::id())
        ->orWhereNull('user_id')
        ->orderBy('name')
        ->get();

    // Tous les objectifs (pour la tab "all-goals" et pagination)
    $goals = Auth::user()->goals()
        ->orderBy('deadline')
        ->paginate(10);  // Important : paginate() pour la pagination

    // Objectifs actifs (pour la tab "active-goals")
    $activeGoals = Auth::user()->goals()
        ->where('completed', false)
        ->orderBy('deadline')
        ->get();

    // Objectifs complétés (pour la tab "completed-goals")
    $completedGoals = Auth::user()->goals()
        ->where('completed', true)
        ->orderBy('deadline')
        ->get();

    // Calcul de la progression globale
    $totalProgress = $activeGoals->sum('current_amount');
    $totalTarget = $activeGoals->sum('target_amount');
    $overallProgress = $totalTarget > 0 ? ($totalProgress / $totalTarget) * 100 : 0;

    // Statistiques
    $stats = [
        'total_goals' => $goals->total(),
        'active_goals' => $activeGoals->count(),
        'completed_goals' => $completedGoals->count(),
        'success_rate' => $goals->total() > 0 
            ? round(($completedGoals->count() / $goals->total()) * 100) 
            : 0,
    ];

    return view('goals.create', compact(
        'types',
        'categories',
        'goals',           // pour la tab "all-goals" et pagination
        'activeGoals',     // pour la tab "active-goals"
        'completedGoals',  // pour la tab "completed-goals"
        'overallProgress',
        'stats'
    ));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:savings,expense,income,custom',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'nullable|exists:categories,id',
            'enable_notifications' => 'boolean',
            'weekly_reminder' => 'boolean',
            'milestone_alerts' => 'boolean',
        ]);

        // Préparer les données
        $validated['user_id'] = Auth::id();
        $validated['current_amount'] = $validated['current_amount'] ?? 0;
        $validated['completed'] = false;
        
        // Mapper start_date et end_date vers le format attendu
        $validated['deadline'] = $validated['end_date'];
        
        // Déterminer la fréquence basée sur la durée
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $durationInDays = $startDate->diffInDays($endDate);
        
        if ($durationInDays <= 7) {
            $validated['frequency'] = 'daily';
        } elseif ($durationInDays <= 31) {
            $validated['frequency'] = 'weekly';
        } elseif ($durationInDays <= 365) {
            $validated['frequency'] = 'monthly';
        } else {
            $validated['frequency'] = 'yearly';
        }

        // Couleur et icône selon le type
        $typeConfig = [
            'savings' => ['color' => '#10b981', 'icon' => 'fa-piggy-bank'],
            'expense' => ['color' => '#ef4444', 'icon' => 'fa-chart-line'],
            'income' => ['color' => '#3b82f6', 'icon' => 'fa-arrow-trend-up'],
            'custom' => ['color' => '#8b5cf6', 'icon' => 'fa-star'],
        ];

        $validated['color'] = $typeConfig[$validated['type']]['color'];
        $validated['icon'] = $typeConfig[$validated['type']]['icon'];

        // Paramètres de notification
        $notificationSettings = [
            'enabled' => $validated['enable_notifications'] ?? false,
            'weekly_reminder' => $validated['weekly_reminder'] ?? false,
            'milestone_alerts' => $validated['milestone_alerts'] ?? false,
            'alert_threshold' => 80, // Alerter à 80% de l'objectif
        ];
        
        $validated['notification_settings'] = json_encode($notificationSettings);

        // Nettoyer les champs qui ne sont pas dans la table
        unset($validated['enable_notifications']);
        unset($validated['weekly_reminder']);
        unset($validated['milestone_alerts']);
        unset($validated['end_date']);

        Goal::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Objectif créé avec succès ! Vous pouvez suivre votre progression depuis votre tableau de bord.');
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

        // Calculer les jalons
        $milestones = [
            ['percentage' => 25, 'amount' => $goal->target_amount * 0.25, 'reached' => $goal->current_amount >= ($goal->target_amount * 0.25)],
            ['percentage' => 50, 'amount' => $goal->target_amount * 0.50, 'reached' => $goal->current_amount >= ($goal->target_amount * 0.50)],
            ['percentage' => 75, 'amount' => $goal->target_amount * 0.75, 'reached' => $goal->current_amount >= ($goal->target_amount * 0.75)],
            ['percentage' => 100, 'amount' => $goal->target_amount, 'reached' => $goal->current_amount >= $goal->target_amount],
        ];

        return view('goals.show', compact(
            'goal',
            'progress',
            'remaining',
            'daysRemaining',
            'dailyRequired',
            'monthlyRequired',
            'milestones'
        ));
    }

    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);

        $types = [
            'savings' => 'Épargne',
            'expense' => 'Réduction dépenses',
            'income' => 'Augmenter revenus',
            'custom' => 'Personnalisé',
        ];

        $categories = Category::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();

        return view('goals.edit', compact('goal', 'types', 'categories'));
    }

    public function update(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'required|numeric|min:0',
            'deadline' => 'required|date',
            'type' => 'required|in:savings,expense,income,custom',
            'frequency' => 'required|in:daily,weekly,monthly,yearly,one_time',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'completed' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Si le montant actuel atteint ou dépasse la cible, marquer comme complété
        if ($validated['current_amount'] >= $validated['target_amount']) {
            $validated['completed'] = true;
            
            // Créer une notification de succès
            if (!$goal->completed) {
                // Ici vous pouvez créer une notification
                // Notification::create([...])
            }
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
            'note' => 'nullable|string|max:500',
        ]);

        $goal->current_amount += $request->amount;
        
        // Vérifier si l'objectif est atteint
        $wasCompleted = $goal->completed;
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->completed = true;
        }

        $goal->save();

        // Créer une notification si l'objectif vient d'être complété
        if (!$wasCompleted && $goal->completed) {
            // Notification::create([
            //     'user_id' => $goal->user_id,
            //     'title' => 'Objectif atteint !',
            //     'message' => "Félicitations ! Vous avez atteint votre objectif : {$goal->name}",
            //     'type' => 'success',
            // ]);
        }

        // Vérifier les jalons pour les alertes
        $progress = ($goal->current_amount / $goal->target_amount) * 100;
        $milestones = [25, 50, 75, 100];
        
        foreach ($milestones as $milestone) {
            if ($progress >= $milestone && $progress < ($milestone + 10)) {
                // Créer une notification de jalon
                // Notification::create([...])
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Progression ajoutée avec succès.',
            'current_amount' => $goal->current_amount,
            'progress' => $goal->target_amount > 0 
                ? min(100, ($goal->current_amount / $goal->target_amount) * 100) 
                : 0,
            'completed' => $goal->completed,
            'formatted_current' => number_format($goal->current_amount, 0, ',', ' ') . ' FCFA',
        ]);
    }

    /**
     * Récupérer les objectifs actifs pour le dashboard
     */
    public function getActiveGoals()
    {
        $goals = Auth::user()->goals()
            ->where('completed', false)
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'goals' => $goals->map(function($goal) {
                $progress = $goal->target_amount > 0 
                    ? min(100, ($goal->current_amount / $goal->target_amount) * 100) 
                    : 0;
                
                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'type' => $goal->type,
                    'icon' => $goal->icon,
                    'color' => $goal->color,
                    'progress' => round($progress, 2),
                    'current_amount' => number_format($goal->current_amount, 0, ',', ' ') . ' FCFA',
                    'target_amount' => number_format($goal->target_amount, 0, ',', ' ') . ' FCFA',
                    'days_remaining' => Carbon::parse($goal->deadline)->diffInDays(now()),
                ];
            })
        ]);
    }

    /**
     * Marquer un objectif comme complété manuellement
     */
    public function markAsCompleted(Goal $goal)
    {
        $this->authorize('update', $goal);

        $goal->completed = true;
        $goal->current_amount = $goal->target_amount;
        $goal->save();

        return redirect()->back()
            ->with('success', 'Objectif marqué comme complété !');
    }

    /**
     * Réactiver un objectif complété
     */
    public function reactivate(Goal $goal)
    {
        $this->authorize('update', $goal);

        $goal->completed = false;
        $goal->save();

        return redirect()->back()
            ->with('success', 'Objectif réactivé avec succès.');
    }
}