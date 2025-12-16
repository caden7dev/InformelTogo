@extends('layouts.app')

@section('title', 'Budgets - Togo Finance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Budgets</h1>
            <p class="text-gray-600 mt-2">Planifiez et suivez vos dépenses</p>
        </div>
        <a href="{{ route('budgets.create') }}" 
           class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center space-x-2 group">
            <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform duration-300"></i>
            <span>Nouveau Budget</span>
        </a>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('budgets.index') }}" 
               class="px-4 py-2 rounded-lg {{ request()->routeIs('budgets.index') ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Tous
            </a>
            <a href="{{ route('budgets.index', ['filter' => 'active']) }}" 
               class="px-4 py-2 rounded-lg {{ request('filter') == 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Actifs
            </a>
            <a href="{{ route('budgets.index', ['filter' => 'inactive']) }}" 
               class="px-4 py-2 rounded-lg {{ request('filter') == 'inactive' ? 'bg-gray-100 text-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Inactifs
            </a>
            <a href="{{ route('budgets.index', ['filter' => 'over_budget']) }}" 
               class="px-4 py-2 rounded-lg {{ request('filter') == 'over_budget' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Dépassés
            </a>
        </div>
    </div>

    <!-- Liste des budgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($budgets as $budget)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border {{ $budget->is_over_budget ? 'border-red-300' : ($budget->progress_percentage >= 80 ? 'border-yellow-300' : 'border-green-300') }}">
            <!-- En-tête -->
            <div class="p-6 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $budget->name }}</h3>
                            @if(!$budget->is_active)
                            <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">
                                Inactif
                            </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            @if($budget->category)
                            <i class="fas fa-tag mr-1"></i>{{ $budget->category->name }}
                            @else
                            <i class="fas fa-layer-group mr-1"></i>Toutes catégories
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-gray-900">
                            ₣ {{ number_format($budget->amount, 0, ',', ' ') }}
                        </span>
                        <p class="text-sm text-gray-500">{{ $budget->period }}</p>
                    </div>
                </div>
            </div>

            <!-- Progression -->
            <div class="p-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Progression</span>
                    <span class="text-sm font-bold {{ $budget->is_over_budget ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $budget->progress_percentage }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full {{ $budget->is_over_budget ? 'bg-red-500' : ($budget->progress_percentage >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                         style="width: {{ min($budget->progress_percentage, 100) }}%"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-500 mt-2">
                    <span>Dépensé: ₣ {{ number_format($budget->current_amount, 0, ',', ' ') }}</span>
                    <span>Restant: ₣ {{ number_format($budget->remaining_amount, 0, ',', ' ') }}</span>
                </div>
            </div>

            <!-- Période et statut -->
            <div class="p-6 bg-gray-50">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Période</p>
                        <p class="text-sm font-medium">
                            {{ $budget->start_date->format('d/m/Y') }}
                            @if($budget->end_date)
                            - {{ $budget->end_date->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jours restants</p>
                        <p class="text-sm font-medium {{ $budget->days_remaining < 7 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $budget->days_remaining }} jours
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('budgets.show', $budget) }}" 
                       class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> Détails
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('budgets.edit', $budget) }}" 
                           class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('budgets.destroy', $budget) }}" 
                              method="POST" 
                              onsubmit="return confirm('Supprimer ce budget ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-chart-pie text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 mb-2">Aucun budget créé</p>
                <p class="text-sm text-gray-400 mb-4">Créez votre premier budget pour suivre vos dépenses</p>
                <a href="{{ route('budgets.create') }}" 
                   class="text-indigo-600 hover:text-indigo-700 text-sm font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-1"></i>
                    Créer un budget
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Statistiques globales -->
    @if($budgets->count() > 0)
    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques globales</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Total budget</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    ₣ {{ number_format($budgets->sum('amount'), 0, ',', ' ') }}
                </p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Total dépensé</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    ₣ {{ number_format($budgets->sum('current_amount'), 0, ',', ' ') }}
                </p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Budgets dépassés</p>
                <p class="text-2xl font-bold text-red-600 mt-1">
                    {{ $budgets->where('is_over_budget', true)->count() }}
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection