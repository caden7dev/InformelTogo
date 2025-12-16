<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = $user->settings ?? [];

        return view('settings.index', compact('user', 'settings'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'currency' => 'required|string|max:10',
            'language' => 'required|string|max:10',
            'date_format' => 'required|string|max:20',
            'timezone' => 'required|string|max:50',
        ]);

        $user->update($validated);

        return back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function preferences()
    {
        $user = Auth::user();
        $preferences = $user->preferences ?? [];

        $themes = ['light', 'dark', 'auto'];
        $dashboardLayouts = ['default', 'compact', 'detailed'];
        $chartTypes = ['line', 'bar', 'pie', 'doughnut'];

        return view('settings.preferences', compact('user', 'preferences', 'themes', 'dashboardLayouts', 'chartTypes'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'theme' => 'required|string|in:light,dark,auto',
            'dashboard_layout' => 'required|string|in:default,compact,detailed',
            'default_chart_type' => 'required|string|in:line,bar,pie,doughnut',
            'show_animations' => 'boolean',
            'compact_mode' => 'boolean',
            'show_tutorials' => 'boolean',
            'auto_refresh' => 'boolean',
            'refresh_interval' => 'integer|min:30|max:300',
        ]);

        $user->preferences = array_merge((array) $user->preferences, $validated);
        $user->save();

        return back()->with('success', 'Préférences mises à jour avec succès.');
    }

    public function notifications()
    {
        $user = Auth::user();
        $notificationSettings = $user->notification_settings ?? [];

        return view('settings.notifications', compact('user', 'notificationSettings'));
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sound_notifications' => 'boolean',
            'transaction_alerts' => 'boolean',
            'budget_alerts' => 'boolean',
            'goal_alerts' => 'boolean',
            'report_reminders' => 'boolean',
            'weekly_summary' => 'boolean',
            'monthly_report' => 'boolean',
        ]);

        $user->notification_settings = array_merge((array) $user->notification_settings, $validated);
        $user->save();

        return back()->with('success', 'Paramètres de notifications mis à jour avec succès.');
    }

    public function security()
    {
        $user = Auth::user();
        $sessions = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('settings.security', compact('user', 'sessions'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function exportData()
    {
        $user = Auth::user();
        
        // Préparer les données pour l'export
        $data = [
            'user' => $user->only(['name', 'email', 'created_at']),
            'transactions' => $user->transactions()->get(),
            'categories' => $user->categories()->get(),
            'budgets' => $user->budgets()->get(),
            'goals' => $user->goals()->get(),
            'exported_at' => now()->toISOString(),
        ];

        $filename = 'togo-finance-export-' . $user->id . '-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|string|in:SUPPRIMER MON COMPTE',
        ]);

        $user = Auth::user();
        
        // Logout l'utilisateur
        Auth::logout();
        
        // Supprimer le compte (soft delete si activé)
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')
            ->with('info', 'Votre compte a été supprimé avec succès.');
    }
}