@extends('layouts.admin')

@section('title', 'Rapport financier')

@section('content')

<style>
    .timeline {
        position: relative;
        padding-left: 35px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        width: 4px;
        height: 100%;
        background: #e9ecef;
        border-radius: 10px;
    }

    .transaction {
        position: relative;
        margin-bottom: 25px;
        padding: 20px;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,.05);
    }

    .transaction::before {
        content: '';
        position: absolute;
        left: -29px;
        top: 28px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0d6efd;
    }

    .transaction.income::before { background: #28a745; }
    .transaction.expense::before { background: #dc3545; }

    .amount {
        font-size: 20px;
        font-weight: bold;
    }
</style>

<div class="container-fluid px-4">

    {{-- HEADER --}}
    <div class="mb-4">
        <h2 class="fw-bold">Rapport financier</h2>
        <p class="text-muted">
            Suivi détaillé des transactions du commerçant
        </p>
    </div>

    {{-- STATS --}}
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Revenus totaux</small>
                <h3 class="text-success fw-bold">
                    {{ number_format($totalIncome, 0, ',', ' ') }} FCFA
                </h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Dépenses totales</small>
                <h3 class="text-danger fw-bold">
                    {{ number_format($totalExpense, 0, ',', ' ') }} FCFA
                </h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Bénéfice net</small>
                <h3 class="fw-bold text-primary">
                    {{ number_format($totalIncome - $totalExpense, 0, ',', ' ') }} FCFA
                </h3>
            </div>
        </div>
    </div>

    {{-- TIMELINE DES TRANSACTIONS --}}
    <div class="timeline">

        @forelse($transactions as $transaction)
            <div class="transaction {{ $transaction->type }}">
                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <h6 class="fw-bold mb-1">
                            {{ $transaction->description }}
                        </h6>
                        <small class="text-muted">
                            {{ $transaction->created_at->format('d M Y à H:i') }}
                        </small>
                    </div>

                    <div class="text-end">
                        <div class="amount
                            {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}
                            {{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                        </div>

                        <span class="badge mt-1
                            {{ $transaction->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                            {{ $transaction->type === 'income' ? 'Revenu' : 'Dépense' }}
                        </span>
                    </div>

                </div>
            </div>
        @empty
            <p class="text-muted text-center">
                Aucune transaction enregistrée pour le moment.
            </p>
        @endforelse

    </div>

</div>
@endsection
