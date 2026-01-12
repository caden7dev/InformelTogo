<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Objectifs - Togo Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullseye text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Mes Objectifs</h1>
                        <p class="text-xs text-gray-500">Suivez vos progrès financiers</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('goals.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Nouvel objectif</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-home text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Objectifs</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['total_goals'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Objectifs actifs</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['active_goals'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-fire text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Objectifs atteints</p>
                        <p class="text-3xl font-bold mt-2">{{ $stats['completed_goals'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">Taux de réussite</p>
                        <p class="text-3xl font-bold mt-2">{{ number_format($stats['success_rate'] ?? 0, 0) }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression globale -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Progression globale</h2>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Progression moyenne</span>
                        <span class="font-semibold">{{ number_format($overallProgress ?? 0, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-4 rounded-full transition-all duration-500" style="width: {{ min($overallProgress ?? 0, 100) }}%"></div>
                    </div>
                </div>
                <div class="text-4xl">
                    @if($overallProgress >= 75)
                        🎉
                    @elseif($overallProgress >= 50)
                        😊
                    @elseif($overallProgress >= 25)
                        💪
                    @else
                        🚀
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button onclick="switchTab('active')" class="tab-btn active border-b-2 border-indigo-600 py-4 px-1 text-sm font-medium text-indigo-600">
                        Objectifs actifs ({{ $activeGoals->count() }})
                    </button>
                    <button onclick="switchTab('completed')" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Objectifs atteints ({{ $completedGoals->count() }})
                    </button>
                    <button onclick="switchTab('all')" class="tab-btn border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Tous les objectifs ({{ $goals->count() }})
                    </button>
                </nav>
            </div>
        </div>

        <!-- Liste des objectifs actifs -->
        <div id="active-goals" class="tab-content">
            @if($activeGoals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeGoals as $goal)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover-lift">
                    <!-- Header de la carte -->
                    <div class="p-6 border-b" style="background: linear-gradient(135deg, {{ $goal->color ?? '#6366f1' }}15 0%, {{ $goal->color ?? '#6366f1' }}05 100%);">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $goal->color ?? '#6366f1' }}20;">
                                    <i class="fas {{ $goal->icon ?? 'fa-bullseye' }} text-xl" style="color: {{ $goal->color ?? '#6366f1' }};"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $goal->name }}</h3>
                                    <p class="text-xs text-gray-500">
                                        @if($goal->type == 'savings') Épargne
                                        @elseif($goal->type == 'expense') Réduction dépenses
                                        @elseif($goal->type == 'income') Augmentation revenus
                                        @else Personnalisé
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="relative">
                                <button onclick="toggleMenu({{ $goal->id }})" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div id="menu-{{ $goal->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-10">
                                    <a href="{{ route('goals.show', $goal) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-eye mr-2"></i>Voir détails
                                    </a>
                                    <a href="{{ route('goals.edit', $goal) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-edit mr-2"></i>Modifier
                                    </a>
                                    <button onclick="markAsCompleted({{ $goal->id }})" class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-gray-100">
                                        <i class="fas fa-check mr-2"></i>Marquer comme atteint
                                    </button>
                                    <button onclick="deleteGoal({{ $goal->id }})" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>

                        @php
                            $progress = $goal->target_amount > 0 ? min(100, ($goal->current_amount / $goal->target_amount) * 100) : 0;
                        @endphp

                        <!-- Cercle de progression -->
                        <div class="flex justify-center mb-4">
                            <div class="relative w-32 h-32">
                                <svg class="w-32 h-32">
                                    <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="8" fill="none"></circle>
                                    <circle cx="64" cy="64" r="56" stroke="{{ $goal->color ?? '#6366f1' }}" stroke-width="8" fill="none"
                                        stroke-dasharray="{{ 2 * 3.14159 * 56 }}"
                                        stroke-dashoffset="{{ 2 * 3.14159 * 56 * (1 - $progress / 100) }}"
                                        class="progress-ring"
                                        style="transition: stroke-dashoffset 0.5s ease;">
                                    </circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-2xl font-bold" style="color: {{ $goal->color ?? '#6366f1' }};">
                                        {{ number_format($progress, 0) }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($goal->description)
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($goal->description, 80) }}</p>
                        @endif
                    </div>

                    <!-- Corps de la carte -->
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Actuel</span>
                            <span class="font-semibold text-gray-900">{{ number_format($goal->current_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Objectif</span>
                            <span class="font-semibold text-gray-900">{{ number_format($goal->target_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Restant</span>
                            <span class="font-semibold" style="color: {{ $goal->color ?? '#6366f1' }};">{{ number_format(max(0, $goal->target_amount - $goal->current_amount), 0, ',', ' ') }} FCFA</span>
                        </div>

                        @php
                            $daysRemaining = \Carbon\Carbon::parse($goal->deadline)->diffInDays(now());
                        @endphp

                        <div class="pt-3 border-t flex justify-between items-center">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>{{ $daysRemaining }} jours restants</span>
                            </div>
                            @if($progress >= 100)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                Atteint !
                            </span>
                            @elseif($progress >= 75)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                Presque !
                            </span>
                            @elseif($progress >= 50)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                En cours
                            </span>
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                Début
                            </span>
                            @endif
                        </div>

                        <!-- Bouton d'action -->
                        <button onclick="openProgressModal({{ $goal->id }}, '{{ $goal->name }}', {{ $goal->target_amount }}, {{ $goal->current_amount }})" class="w-full mt-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 rounded-lg hover:shadow-lg transition-all flex items-center justify-center space-x-2">
                            <i class="fas fa-plus-circle"></i>
                            <span>Ajouter de la progression</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bullseye text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun objectif actif</h3>
                <p class="text-gray-600 mb-6">Créez votre premier objectif financier pour commencer à suivre vos progrès</p>
                <a href="{{ route('goals.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Créer un objectif</span>
                </a>
            </div>
            @endif
        </div>

        <!-- Liste des objectifs complétés -->
        <div id="completed-goals" class="tab-content hidden">
            @if($completedGoals->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($completedGoals as $goal)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden opacity-75 hover:opacity-100 transition-opacity">
                    <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-trophy text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $goal->name }}</h3>
                                    <p class="text-xs text-green-600 font-medium">Objectif atteint !</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-green-600 mb-2">{{ number_format($goal->target_amount, 0, ',', ' ') }} FCFA</p>
                        <p class="text-sm text-gray-600">Complété le {{ \Carbon\Carbon::parse($goal->updated_at)->format('d/m/Y') }}</p>
                        
                        <div class="mt-4 pt-4 border-t flex space-x-2">
                            <a href="{{ route('goals.show', $goal) }}" class="flex-1 text-center py-2 text-sm bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Voir détails
                            </a>
                            <button onclick="deleteGoal({{ $goal->id }})" class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trophy text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun objectif atteint</h3>
                <p class="text-gray-600">Continuez vos efforts, vous y arriverez !</p>
            </div>
            @endif
        </div>

        <!-- Tous les objectifs -->
        <div id="all-goals" class="tab-content hidden">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objectif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Échéance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($goals as $goal)
                        @php
                            $progress = $goal->target_amount > 0 ? min(100, ($goal->current_amount / $goal->target_amount) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background-color: {{ $goal->color ?? '#6366f1' }}20;">
                                        <i class="fas {{ $goal->icon ?? 'fa-bullseye' }}" style="color: {{ $goal->color ?? '#6366f1' }};"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $goal->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($goal->description, 30) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($goal->type == 'savings') bg-green-100 text-green-800
                                    @elseif($goal->type == 'expense') bg-red-100 text-red-800
                                    @elseif($goal->type == 'income') bg-blue-100 text-blue-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    @if($goal->type == 'savings') Épargne
                                    @elseif($goal->type == 'expense') Dépenses
                                    @elseif($goal->type == 'income') Revenus
                                    @else Personnalisé
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full" style="width: {{ $progress }}%; background-color: {{ $goal->color ?? '#6366f1' }};"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ number_format($progress, 0) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($goal->current_amount, 0, ',', ' ') }} FCFA</div>
                                <div class="text-xs text-gray-500">/ {{ number_format($goal->target_amount, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($goal->deadline)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($goal->completed)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Atteint
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-fire mr-1"></i>Actif
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('goals.show', $goal) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('goals.edit', $goal) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteGoal({{ $goal->id }})" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $goals->links() }}
            </div>
        </div>
    </main>

    <!-- Modal Ajouter Progression -->
    <div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Ajouter de la progression</h3>
                <button onclick="closeProgressModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="progressForm" onsubmit="submitProgress(event)">
                <input type="hidden" id="goalId" name="goal_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Objectif</label>
                    <p id="goalName" class="text-lg font-semibold text-gray-900"></p>
                </div>

                <div class="mb-4">
                    <label for="progressAmount" class="block text-sm font-medium text-gray-700 mb-2">
                        Montant à ajouter <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="progressAmount" name="amount" required min="0" step="0.01"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 50000">
                        <span class="absolute right-4 top-3 text-gray-500">FCFA</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="progressNote" class="block text-sm font-medium text-gray-700 mb-2">
                        Note (optionnel)
                    </label>
                    <textarea id="progressNote" name="note" rows="2"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Ex: Économie du mois de janvier"></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Progression actuelle</span>
                        <span id="currentProgress" class="font-semibold"></span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-600">Après ajout</span>
                        <span id="newProgress" class="font-semibold text-indigo-600"></span>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeProgressModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 transform translate-y-2 opacity-0 transition-all duration-300 hidden z-50">
        <div class="bg-white rounded-lg shadow-xl border px-6 py-4 flex items-center space-x-3">
            <i id="toastIcon" class="fas fa-check-circle text-2xl"></i>
            <div>
                <p id="toastTitle" class="font-semibold"></p>
                <p id="toastMessage" class="text-sm text-gray-600"></p>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Gestion des tabs
        function switchTab(tab) {
            // Mettre à jour les boutons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-indigo-600', 'text-indigo-600');

            // Afficher le contenu correspondant
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            if (tab === 'active') {
                document.getElementById('active-goals').classList.remove('hidden');
            } else if (tab === 'completed') {
                document.getElementById('completed-goals').classList.remove('hidden');
            } else {
                document.getElementById('all-goals').classList.remove('hidden');
            }
        }

        // Toggle menu
        function toggleMenu(goalId) {
            const menu = document.getElementById(`menu-${goalId}`);
            // Fermer tous les autres menus
            document.querySelectorAll('[id^="menu-"]').forEach(m => {
                if (m.id !== `menu-${goalId}`) {
                    m.classList.add('hidden');
                }
            });
            menu.classList.toggle('hidden');
        }

        // Fermer les menus en cliquant ailleurs
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="toggleMenu"]') && !event.target.closest('[id^="menu-"]')) {
                document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Modal de progression
        let currentGoalId = null;
        let currentTargetAmount = 0;
        let currentAmount = 0;

        function openProgressModal(goalId, goalName, targetAmount, currentAmt) {
            currentGoalId = goalId;
            currentTargetAmount = targetAmount;
            currentAmount = currentAmt;
            
            document.getElementById('goalId').value = goalId;
            document.getElementById('goalName').textContent = goalName;
            document.getElementById('currentProgress').textContent = `${currentAmt.toLocaleString('fr-FR')} / ${targetAmount.toLocaleString('fr-FR')} FCFA`;
            document.getElementById('newProgress').textContent = `${currentAmt.toLocaleString('fr-FR')} FCFA`;
            document.getElementById('progressModal').classList.remove('hidden');
            
            // Écouter les changements du montant
            document.getElementById('progressAmount').addEventListener('input', updateProgressPreview);
        }

        function updateProgressPreview() {
            const amount = parseFloat(document.getElementById('progressAmount').value) || 0;
            const newAmount = currentAmount + amount;
            const percentage = (newAmount / currentTargetAmount * 100).toFixed(1);
            document.getElementById('newProgress').textContent = `${newAmount.toLocaleString('fr-FR')} FCFA (${percentage}%)`;
        }

        function closeProgressModal() {
            document.getElementById('progressModal').classList.add('hidden');
            document.getElementById('progressForm').reset();
        }

        // Soumettre la progression
        async function submitProgress(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = {
                amount: parseFloat(formData.get('amount')),
                note: formData.get('note')
            };

            try {
                const response = await fetch(`/goals/${currentGoalId}/add-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Succès !', result.message || 'Progression ajoutée avec succès', 'success');
                    closeProgressModal();
                    
                    // Recharger la page après 1 seconde
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast('Erreur', result.message || 'Une erreur est survenue', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Erreur', 'Impossible d\'ajouter la progression', 'error');
            }
        }

        // Marquer comme complété
        async function markAsCompleted(goalId) {
            if (!confirm('Êtes-vous sûr de vouloir marquer cet objectif comme atteint ?')) {
                return;
            }

            try {
                const response = await fetch(`/goals/${goalId}/mark-completed`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    showToast('Bravo !', 'Objectif marqué comme atteint !', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Erreur', 'Impossible de marquer l\'objectif', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Erreur', 'Une erreur est survenue', 'error');
            }
        }

        // Supprimer un objectif
        async function deleteGoal(goalId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet objectif ? Cette action est irréversible.')) {
                return;
            }

            try {
                const response = await fetch(`/goals/${goalId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    showToast('Supprimé', 'Objectif supprimé avec succès', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Erreur', 'Impossible de supprimer l\'objectif', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Erreur', 'Une erreur est survenue', 'error');
            }
        }

        // Toast notification
        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');

            const config = {
                success: { icon: 'fa-check-circle', color: 'text-green-500' },
                error: { icon: 'fa-times-circle', color: 'text-red-500' },
                warning: { icon: 'fa-exclamation-triangle', color: 'text-yellow-500' },
                info: { icon: 'fa-info-circle', color: 'text-blue-500' }
            };

            const { icon, color } = config[type] || config.success;

            toastIcon.className = `fas ${icon} text-2xl ${color}`;
            toastTitle.textContent = title;
            toastMessage.textContent = message;

            toast.classList.remove('hidden', 'opacity-0', 'translate-y-2');
            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }
    </script>
</body>
</html>