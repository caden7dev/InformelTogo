@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Détails de la Transaction</h5>
                        <div>
                            <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Montant</th>
                                    <td>
                                        <span class="fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($transaction->amount, 2) }} FCFA
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->type === 'income' ? 'Revenu' : 'Dépense' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Catégorie</th>
                                    <td>
                                        @if($transaction->category)
                                            <span class="badge" style="background-color: {{ $transaction->category->color ?? '#6c757d' }}; color: white;">
                                                {{ $transaction->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucune catégorie</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Date</th>
                                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Modifié le</th>
                                    <td>{{ $transaction->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($transaction->description)
                    <div class="mt-4">
                        <h6>Description</h6>
                        <div class="border p-3 bg-light rounded">
                            {{ $transaction->description }}
                        </div>
                    </div>
                    @endif
                    
                    <!-- Bouton de suppression avec confirmation -->
                    <div class="mt-4">
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer cette transaction
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection