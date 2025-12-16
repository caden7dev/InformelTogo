<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\TransactionsExport;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function transactions(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        if (!in_array($format, ['csv', 'excel', 'pdf'])) {
            abort(400, 'Format non supporté.');
        }

        $user = Auth::user();
        $date = now()->format('Y-m-d');
        $filename = "transactions-{$user->id}-{$date}";

        switch ($format) {
            case 'csv':
                return Excel::download(new TransactionsExport($user), "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV);
            
            case 'excel':
                return Excel::download(new TransactionsExport($user), "{$filename}.xlsx", \Maatwebsite\Excel\Excel::XLSX);
            
            case 'pdf':
                $transactions = $user->transactions()
                    ->with('category')
                    ->orderBy('date_transaction', 'desc')
                    ->get();
                
                $pdf = PDF::loadView('exports.transactions-pdf', [
                    'transactions' => $transactions,
                    'user' => $user,
                    'date' => $date
                ]);
                
                return $pdf->download("{$filename}.pdf");
        }
    }

    public function transactionsCSV()
    {
        $user = Auth::user();
        $date = now()->format('Y-m-d');
        
        return Excel::download(new TransactionsExport($user), 
            "transactions-{$user->id}-{$date}.csv", 
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function transactionsExcel()
    {
        $user = Auth::user();
        $date = now()->format('Y-m-d');
        
        return Excel::download(new TransactionsExport($user), 
            "transactions-{$user->id}-{$date}.xlsx", 
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function transactionsPDF()
    {
        $user = Auth::user();
        $transactions = $user->transactions()
            ->with('category')
            ->orderBy('date_transaction', 'desc')
            ->get();
        
        $date = now()->format('Y-m-d');
        $filename = "transactions-{$user->id}-{$date}.pdf";
        
        $pdf = PDF::loadView('exports.transactions-pdf', [
            'transactions' => $transactions,
            'user' => $user,
            'date' => $date
        ]);
        
        return $pdf->download($filename);
    }

    public function reportsPDF(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'financial');
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $data = [];
        
        switch ($type) {
            case 'financial':
                $data = $this->getFinancialReportData($user, $period, $startDate, $endDate);
                $view = 'exports.reports.financial-pdf';
                break;
            
            case 'categorical':
                $data = $this->getCategoricalReportData($user, $period, $startDate, $endDate);
                $view = 'exports.reports.categorical-pdf';
                break;
            
            case 'budget':
                $data = $this->getBudgetReportData($user, $period, $startDate, $endDate);
                $view = 'exports.reports.budget-pdf';
                break;
            
            default:
                abort(400, 'Type de rapport non supporté.');
        }
        
        $date = now()->format('Y-m-d');
        $filename = "report-{$type}-{$user->id}-{$date}.pdf";
        
        $pdf = PDF::loadView($view, array_merge($data, [
            'user' => $user,
            'type' => $type,
            'period' => $period,
            'date' => $date
        ]));
        
        return $pdf->download($filename);
    }

    public function budgetPDF(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        
        $budgets = $user->budgets()
            ->where('month', $month)
            ->with('category')
            ->get();
        
        $transactions = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('date_transaction', [
                \Carbon\Carbon::parse($month . '-01')->startOfMonth(),
                \Carbon\Carbon::parse($month . '-01')->endOfMonth()
            ])
            ->with('category')
            ->get();
        
        $date = now()->format('Y-m-d');
        $filename = "budget-{$user->id}-{$month}.pdf";
        
        $pdf = PDF::loadView('exports.budget-pdf', [
            'budgets' => $budgets,
            'transactions' => $transactions,
            'user' => $user,
            'month' => $month,
            'date' => $date
        ]);
        
        return $pdf->download($filename);
    }

    private function getFinancialReportData($user, $period, $startDate, $endDate)
    {
        // Implémentez la logique pour les rapports financiers
        return [
            'income' => 0,
            'expense' => 0,
            'balance' => 0,
            'transactions' => [],
        ];
    }

    private function getCategoricalReportData($user, $period, $startDate, $endDate)
    {
        // Implémentez la logique pour les rapports par catégorie
        return [
            'categories' => [],
            'total_by_category' => [],
        ];
    }

    private function getBudgetReportData($user, $period, $startDate, $endDate)
    {
        // Implémentez la logique pour les rapports de budget
        return [
            'budgets' => [],
            'spent_by_category' => [],
        ];
    }
}