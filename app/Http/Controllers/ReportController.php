<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $user = Auth::user();
        
        // Déterminer les dates en fonction de la période
        list($start, $end) = $this->getDateRange($period, $startDate, $endDate);
        
        // Récupérer les transactions
        $transactions = $user->transactions()
            ->with('category')
            ->whereBetween('date_transaction', [$start, $end])
            ->get();
        
        // Calculer les totaux
        $income = $transactions->where('type', 'income')->sum('montant');
        $expense = $transactions->where('type', 'expense')->sum('montant');
        $balance = $income - $expense;
        
        // Transactions par catégorie
        $incomeByCategory = $transactions->where('type', 'income')
            ->groupBy('category_id')
            ->map(function ($items, $categoryId) {
                return [
                    'category' => $items->first()->category->name ?? 'Non catégorisé',
                    'amount' => $items->sum('montant'),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
            
        $expenseByCategory = $transactions->where('type', 'expense')
            ->groupBy('category_id')
            ->map(function ($items, $categoryId) {
                return [
                    'category' => $items->first()->category->name ?? 'Non catégorisé',
                    'amount' => $items->sum('montant'),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
        
        // Transactions par jour
        $dailyTransactions = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('Y-m-d');
        })->map(function ($items, $date) {
            return [
                'date' => $date,
                'income' => $items->where('type', 'income')->sum('montant'),
                'expense' => $items->where('type', 'expense')->sum('montant'),
                'balance' => $items->where('type', 'income')->sum('montant') - $items->where('type', 'expense')->sum('montant'),
            ];
        })->sortBy('date')->values();
        
        return view('reports.financial', compact(
            'income',
            'expense',
            'balance',
            'incomeByCategory',
            'expenseByCategory',
            'dailyTransactions',
            'period',
            'start',
            'end',
            'transactions'
        ));
    }

    public function categorical(Request $request)
    {
        $period = $request->get('period', 'month');
        $type = $request->get('type', 'expense');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $user = Auth::user();
        
        // Déterminer les dates
        list($start, $end) = $this->getDateRange($period, $startDate, $endDate);
        
        // Récupérer les transactions par catégorie
        $transactions = $user->transactions()
            ->with('category')
            ->where('type', $type)
            ->whereBetween('date_transaction', [$start, $end])
            ->get();
        
        // Grouper par catégorie
        $byCategory = $transactions->groupBy('category_id')->map(function ($items, $categoryId) {
            $category = $items->first()->category;
            $total = $items->sum('montant');
            
            return [
                'id' => $categoryId,
                'name' => $category->name ?? 'Non catégorisé',
                'color' => $category->color ?? '#6B7280',
                'total' => $total,
                'count' => $items->count(),
                'average' => $items->avg('montant'),
                'transactions' => $items,
            ];
        })->sortByDesc('total')->values();
        
        $totalAmount = $byCategory->sum('total');
        
        // Ajouter les pourcentages
        $byCategory = $byCategory->map(function ($category) use ($totalAmount) {
            $category['percentage'] = $totalAmount > 0 ? round(($category['total'] / $totalAmount) * 100, 2) : 0;
            return $category;
        });
        
        return view('reports.categorical', compact(
            'byCategory',
            'totalAmount',
            'type',
            'period',
            'start',
            'end'
        ));
    }

    public function monthly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $user = Auth::user();
        
        // Récupérer les données pour chaque mois
        $monthlyData = collect();
        
        for ($month = 1; $month <= 12; $month++) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();
            
            $transactions = $user->transactions()
                ->whereBetween('date_transaction', [$start, $end])
                ->get();
            
            $income = $transactions->where('type', 'income')->sum('montant');
            $expense = $transactions->where('type', 'expense')->sum('montant');
            $balance = $income - $expense;
            
            $monthlyData->push([
                'month' => $start->format('Y-m'),
                'month_name' => $start->translatedFormat('F'),
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'transaction_count' => $transactions->count(),
            ]);
        }
        
        // Totaux annuels
        $yearlyIncome = $monthlyData->sum('income');
        $yearlyExpense = $monthlyData->sum('expense');
        $yearlyBalance = $yearlyIncome - $yearlyExpense;
        
        // Meilleurs mois
        $bestMonth = $monthlyData->sortByDesc('balance')->first();
        $worstMonth = $monthlyData->sortBy('balance')->first();
        
        return view('reports.monthly', compact(
            'monthlyData',
            'yearlyIncome',
            'yearlyExpense',
            'yearlyBalance',
            'year',
            'bestMonth',
            'worstMonth'
        ));
    }

    public function budget(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $user = Auth::user();
        
        $budgets = $user->budgets()
            ->where('month', $month)
            ->with('category')
            ->get();
        
        // Récupérer les transactions du mois
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = Carbon::parse($month . '-01')->endOfMonth();
        
        $transactions = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('date_transaction', [$start, $end])
            ->with('category')
            ->get();
        
        // Calculer les dépenses par catégorie
        $spentByCategory = $transactions->groupBy('category_id')->map(function ($items, $categoryId) {
            return [
                'category_id' => $categoryId,
                'spent' => $items->sum('montant'),
                'transactions' => $items,
            ];
        });
        
        // Associer les budgets avec les dépenses réelles
        $budgetData = $budgets->map(function ($budget) use ($spentByCategory) {
            $spent = $spentByCategory[$budget->category_id]['spent'] ?? 0;
            $progress = $budget->amount > 0 ? min(100, ($spent / $budget->amount) * 100) : 0;
            $remaining = max(0, $budget->amount - $spent);
            $overspent = max(0, $spent - $budget->amount);
            
            return [
                'budget' => $budget,
                'spent' => $spent,
                'progress' => $progress,
                'remaining' => $remaining,
                'overspent' => $overspent,
                'status' => $spent > $budget->amount ? 'exceeded' : ($progress > 80 ? 'warning' : 'good'),
            ];
        });
        
        $totalBudget = $budgets->sum('amount');
        $totalSpent = $spentByCategory->sum('spent');
        $totalRemaining = max(0, $totalBudget - $totalSpent);
        $totalOverspent = max(0, $totalSpent - $totalBudget);
        
        return view('reports.budget', compact(
            'budgetData',
            'totalBudget',
            'totalSpent',
            'totalRemaining',
            'totalOverspent',
            'month',
            'transactions'
        ));
    }

    public function custom()
    {
        return view('reports.custom');
    }

    public function generateCustom(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:financial,categorical,monthly,budget',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|in:all,income,expense',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'include_details' => 'boolean',
            'group_by' => 'nullable|in:day,week,month,category',
        ]);
        
        $user = Auth::user();
        
        // Construire la requête de base
        $query = $user->transactions()
            ->with('category')
            ->whereBetween('date_transaction', [
                $validated['start_date'],
                $validated['end_date']
            ]);
        
        // Appliquer les filtres
        if ($validated['transaction_type'] && $validated['transaction_type'] != 'all') {
            $query->where('type', $validated['transaction_type']);
        }
        
        if (!empty($validated['category_ids'])) {
            $query->whereIn('category_id', $validated['category_ids']);
        }
        
        if ($validated['min_amount']) {
            $query->where('montant', '>=', $validated['min_amount']);
        }
        
        if ($validated['max_amount']) {
            $query->where('montant', '<=', $validated['max_amount']);
        }
        
        $transactions = $query->get();
        
        // Grouper les données selon le critère
        $groupedData = collect();
        
        if ($validated['group_by']) {
            switch ($validated['group_by']) {
                case 'day':
                    $groupedData = $this->groupByDay($transactions);
                    break;
                case 'week':
                    $groupedData = $this->groupByWeek($transactions);
                    break;
                case 'month':
                    $groupedData = $this->groupByMonth($transactions);
                    break;
                case 'category':
                    $groupedData = $this->groupByCategory($transactions);
                    break;
            }
        }
        
        // Calculer les totaux
        $totalIncome = $transactions->where('type', 'income')->sum('montant');
        $totalExpense = $transactions->where('type', 'expense')->sum('montant');
        $totalBalance = $totalIncome - $totalExpense;
        
        return view('reports.custom-result', array_merge($validated, [
            'transactions' => $transactions,
            'groupedData' => $groupedData,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalBalance' => $totalBalance,
            'transactionCount' => $transactions->count(),
        ]));
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return [Carbon::parse($startDate), Carbon::parse($endDate)];
        }
        
        $now = Carbon::now();
        
        switch ($period) {
            case 'day':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'quarter':
                return [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()];
            case 'year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
        }
    }

    private function groupByDay($transactions)
    {
        return $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('Y-m-d');
        })->map(function ($items, $date) {
            return [
                'date' => $date,
                'income' => $items->where('type', 'income')->sum('montant'),
                'expense' => $items->where('type', 'expense')->sum('montant'),
                'balance' => $items->where('type', 'income')->sum('montant') - $items->where('type', 'expense')->sum('montant'),
                'count' => $items->count(),
            ];
        })->sortBy('date')->values();
    }

    private function groupByWeek($transactions)
    {
        return $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('Y-W');
        })->map(function ($items, $week) {
            $date = Carbon::parse(substr($week, 0, 4) . '-W' . substr($week, 5, 2) . '-1');
            return [
                'week' => $week,
                'week_start' => $date->startOfWeek()->format('Y-m-d'),
                'week_end' => $date->endOfWeek()->format('Y-m-d'),
                'income' => $items->where('type', 'income')->sum('montant'),
                'expense' => $items->where('type', 'expense')->sum('montant'),
                'balance' => $items->where('type', 'income')->sum('montant') - $items->where('type', 'expense')->sum('montant'),
                'count' => $items->count(),
            ];
        })->sortBy('week')->values();
    }

    private function groupByMonth($transactions)
    {
        return $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->date_transaction)->format('Y-m');
        })->map(function ($items, $month) {
            return [
                'month' => $month,
                'month_name' => Carbon::parse($month . '-01')->translatedFormat('F Y'),
                'income' => $items->where('type', 'income')->sum('montant'),
                'expense' => $items->where('type', 'expense')->sum('montant'),
                'balance' => $items->where('type', 'income')->sum('montant') - $items->where('type', 'expense')->sum('montant'),
                'count' => $items->count(),
            ];
        })->sortBy('month')->values();
    }

    private function groupByCategory($transactions)
    {
        return $transactions->groupBy('category_id')->map(function ($items, $categoryId) {
            $category = $items->first()->category;
            return [
                'category_id' => $categoryId,
                'category_name' => $category->name ?? 'Non catégorisé',
                'category_color' => $category->color ?? '#6B7280',
                'income' => $items->where('type', 'income')->sum('montant'),
                'expense' => $items->where('type', 'expense')->sum('montant'),
                'balance' => $items->where('type', 'income')->sum('montant') - $items->where('type', 'expense')->sum('montant'),
                'count' => $items->count(),
            ];
        })->sortByDesc('expense')->values();
    }
}