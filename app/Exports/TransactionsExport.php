<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        return Transaction::where('user_id', $this->user->id)
            ->with('category')
            ->orderBy('date_transaction', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Catégorie',
            'Description',
            'Montant (FCFA)',
            'Mode de paiement',
            'Statut',
            'Notes'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->date_transaction->format('d/m/Y'),
            $transaction->type == 'income' ? 'Recette' : 'Dépense',
            $transaction->category->name ?? 'Non catégorisé',
            $transaction->description,
            number_format($transaction->montant, 0, ',', ' '),
            $transaction->mode_paiement,
            $transaction->statut,
            $transaction->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}