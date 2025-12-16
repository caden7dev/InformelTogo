<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Auth::user()->alerts()
            ->orderBy('active', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('alerts.index', compact('alerts'));
    }

    public function create()
    {
        $types = [
            'transaction_amount' => 'Montant de transaction',
            'category_spending' => 'Dépenses par catégorie',
            'budget_exceeded' => 'Budget dépassé',
            'goal_progress' => 'Progression d\'objectif',
            'balance_threshold' => 'Seuil de solde',
            'monthly_summary' => 'Résumé mensuel',
        ];

        $conditions = [
            'greater_than' => 'Supérieur à',
            'less_than' => 'Inférieur à',
            'equals' => 'Égal à',
            'greater_than_or_equal' => 'Supérieur ou égal à',
            'less_than_or_equal' => 'Inférieur ou égal à',
            'between' => 'Entre',
        ];

        $channels = [
            'email' => 'Email',
            'notification' => 'Notification',
            'both' => 'Email et Notification',
        ];

        $frequencies = [
            'immediate' => 'Immédiat',
            'daily' => 'Quotidien',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
        ];

        return view('alerts.create', compact('types', 'conditions', 'channels', 'frequencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:transaction_amount,category_spending,budget_exceeded,goal_progress,balance_threshold,monthly_summary',
            'condition' => 'required|in:greater_than,less_than,equals,greater_than_or_equal,less_than_or_equal,between',
            'value' => 'required|numeric',
            'value2' => 'nullable|required_if:condition,between|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'goal_id' => 'nullable|exists:goals,id',
            'channel' => 'required|in:email,notification,both',
            'frequency' => 'required|in:immediate,daily,weekly,monthly',
            'active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['active'] = $validated['active'] ?? true;
        $validated['last_triggered'] = null;

        Alert::create($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte créée avec succès.');
    }

    public function show(Alert $alert)
    {
        $this->authorize('view', $alert);

        $triggerHistory = $alert->triggerHistory()
            ->orderBy('triggered_at', 'desc')
            ->paginate(10);

        return view('alerts.show', compact('alert', 'triggerHistory'));
    }

    public function edit(Alert $alert)
    {
        $this->authorize('update', $alert);

        $types = [
            'transaction_amount' => 'Montant de transaction',
            'category_spending' => 'Dépenses par catégorie',
            'budget_exceeded' => 'Budget dépassé',
            'goal_progress' => 'Progression d\'objectif',
            'balance_threshold' => 'Seuil de solde',
            'monthly_summary' => 'Résumé mensuel',
        ];

        $conditions = [
            'greater_than' => 'Supérieur à',
            'less_than' => 'Inférieur à',
            'equals' => 'Égal à',
            'greater_than_or_equal' => 'Supérieur ou égal à',
            'less_than_or_equal' => 'Inférieur ou égal à',
            'between' => 'Entre',
        ];

        $channels = [
            'email' => 'Email',
            'notification' => 'Notification',
            'both' => 'Email et Notification',
        ];

        $frequencies = [
            'immediate' => 'Immédiat',
            'daily' => 'Quotidien',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
        ];

        $categories = Auth::user()->categories()->get();
        $budgets = Auth::user()->budgets()->get();
        $goals = Auth::user()->goals()->get();

        return view('alerts.edit', compact(
            'alert', 
            'types', 
            'conditions', 
            'channels', 
            'frequencies',
            'categories',
            'budgets',
            'goals'
        ));
    }

    public function update(Request $request, Alert $alert)
    {
        $this->authorize('update', $alert);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:transaction_amount,category_spending,budget_exceeded,goal_progress,balance_threshold,monthly_summary',
            'condition' => 'required|in:greater_than,less_than,equals,greater_than_or_equal,less_than_or_equal,between',
            'value' => 'required|numeric',
            'value2' => 'nullable|required_if:condition,between|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'goal_id' => 'nullable|exists:goals,id',
            'channel' => 'required|in:email,notification,both',
            'frequency' => 'required|in:immediate,daily,weekly,monthly',
            'active' => 'boolean',
        ]);

        $alert->update($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte mise à jour avec succès.');
    }

    public function destroy(Alert $alert)
    {
        $this->authorize('delete', $alert);

        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alerte supprimée avec succès.');
    }

    public function test(Alert $alert)
    {
        $this->authorize('update', $alert);

        // Simuler le déclenchement de l'alerte
        $alert->trigger();

        return back()->with('success', 'Alerte testée avec succès. Vérifiez vos notifications.');
    }

    public function toggle(Alert $alert)
    {
        $this->authorize('update', $alert);

        $alert->active = !$alert->active;
        $alert->save();

        $status = $alert->active ? 'activée' : 'désactivée';

        return back()->with('success', "Alerte {$status} avec succès.");
    }

    // API Methods
    public function checkAlerts(Request $request)
    {
        $user = Auth::user();
        $triggeredAlerts = [];

        $alerts = $user->alerts()
            ->where('active', true)
            ->where('type', 'transaction_amount')
            ->get();

        foreach ($alerts as $alert) {
            // Implémentez la logique de vérification
            // Cette méthode devrait vérifier chaque type d'alerte
        }

        return response()->json([
            'success' => true,
            'triggered_alerts' => $triggeredAlerts,
            'count' => count($triggeredAlerts)
        ]);
    }
}