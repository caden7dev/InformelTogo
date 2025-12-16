<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Plateforme Togo Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .gradient-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-income {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .gradient-expense {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .gradient-balance {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .avatar-ring {
            border: 3px solid;
            border-image: linear-gradient(135deg, #667eea, #764ba2) 1;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .notification-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header avec Navigation -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo et Titre -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Togo Finance</h1>
                        <p class="text-xs text-gray-500">Gestion financière intelligente</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-indigo-600 font-medium border-b-2 border-indigo-600 pb-1">Tableau de bord</a>
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Transactions</a>
                    <a href="{{ route('reports.financial') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Rapports</a>
                    <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Catégories</a>
                    <a href="{{ route('budgets.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Budgets</a>
                </nav>

                <!-- Profil Utilisateur -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button id="notificationButton" class="p-2 text-gray-600 hover:text-indigo-600 transition-colors relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">0</span>
                        </button>
                        
                        <!-- Dropdown Notifications -->
                        <div id="notificationDropdown" class="absolute right-0 top-full mt-2 w-96 bg-white rounded-lg shadow-xl border hidden z-50 max-h-96 overflow-y-auto">
                            <div class="p-4 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Notifications</h3>
                                <div class="flex space-x-2">
                                    <button onclick="notificationSystem.markAllAsRead()" class="text-sm text-indigo-600 hover:text-indigo-700">
                                        Tout marquer comme lu
                                    </button>
                                    <button onclick="notificationSystem.toggleNotifications()" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="notificationList" class="divide-y divide-gray-100">
                                <!-- Les notifications seront chargées dynamiquement -->
                                <div class="py-8 text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto mb-2"></div>
                                    <p class="text-gray-500">Chargement des notifications...</p>
                                </div>
                            </div>
                            <div class="p-3 border-t bg-gray-50">
                                <a href="{{ route('notifications.index') }}" class="text-center block text-sm text-gray-600 hover:text-gray-900">
                                    Voir toutes les notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Paramètres -->
                    <button onclick="openSettingsModal()" class="p-2 text-gray-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-cog text-lg"></i>
                    </button>

                    <!-- Profil Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 avatar-ring rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="p-4 border-b">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-xs text-indigo-600 font-medium mt-1">
                                    {{ Auth::user()->role === 'admin' ? 'Administrateur' : 'Utilisateur' }}
                                </p>
                            </div>
                            <div class="p-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-user text-gray-400 w-5"></i>
                                    <span>Mon Profil</span>
                                </a>
                                
                                <button onclick="openSettingsModal()" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-cog text-gray-400 w-5"></i>
                                    <span>Paramètres</span>
                                </button>
                                
                                <button onclick="notificationSystem.toggleNotifications()" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-bell text-gray-400 w-5"></i>
                                    <span>Notifications</span>
                                    <span id="dropdownBadge" class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                                </button>
                                
                                <a href="{{ route('help.support') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-question-circle text-gray-400 w-5"></i>
                                    <span>Aide & Support</span>
                                </a>
                            </div>
                            <div class="p-2 border-t">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-sign-out-alt text-red-400 w-5"></i>
                                        <span>Déconnexion</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête avec Titre et Bouton -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 space-y-4 lg:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord Financier</h1>
                <p class="text-gray-600 mt-2">Aperçu complet de votre santé financière</p>
            </div>
            <div class="flex space-x-3">
             
                <a href="{{ route('transactions.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center space-x-2 group">
                    <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform duration-300"></i>
                    <span>Nouvelle Transaction</span>
                </a>
            </div>
        </div>

        <!-- Cartes de Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Carte Recettes -->
            <div class="gradient-income text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 font-medium">Total Recettes</p>
                        <p class="text-3xl font-bold mt-2">₣ <span id="total-income">{{ number_format($totalIncome, 0, ',', ' ') }}</span></p>
                        <p class="text-blue-100 text-sm mt-2 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span id="income-trend">Chargement...</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Carte Dépenses -->
            <div class="gradient-expense text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-pink-100 font-medium">Total Dépenses</p>
                        <p class="text-3xl font-bold mt-2">₣ <span id="total-expense">{{ number_format($totalExpense, 0, ',', ' ') }}</span></p>
                        <p class="text-pink-100 text-sm mt-2 flex items-center">
                            <i class="fas fa-arrow-down mr-1"></i>
                            <span id="expense-trend">Chargement...</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Carte Solde -->
            <div class="gradient-balance text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-green-100 font-medium">Solde Net</p>
                        <p class="text-3xl font-bold mt-2">₣ <span id="total-balance">{{ number_format($balance, 0, ',', ' ') }}</span></p>
                        <p class="text-green-100 text-sm mt-2 flex items-center">
                            <i class="fas fa-chart-line mr-1"></i>
                            <span id="balance-status">Équilibre positif</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-scale-balanced text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Carte Transactions -->
            <div class="gradient-card text-white p-6 rounded-2xl shadow-lg hover-lift">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-purple-100 font-medium">Transactions</p>
                        <p class="text-3xl font-bold mt-2" id="transactions-count">{{ $transactions->count() }}</p>
                        <p class="text-purple-100 text-sm mt-2 flex items-center">
                            <i class="fas fa-exchange-alt mr-1"></i>
                            <span id="today-transactions">Chargement...</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Graphique -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Évolution Financière</h3>
                    <div class="flex space-x-2">
                        <button onclick="updateChartPeriod('1month')" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-600 rounded-full period-btn" data-period="1month">Mois</button>
                        <button onclick="updateChartPeriod('3months')" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-full period-btn" data-period="3months">3 mois</button>
                        <button onclick="updateChartPeriod('6months')" class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-full period-btn" data-period="6months">6 mois</button>
                        <button onclick="updateChartPeriod('1year')" class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-full period-btn" data-period="1year">Annuel</button>
                    </div>
                </div>
                <canvas id="financeChart" class="w-full h-64"></canvas>
            </div>

            <!-- Transactions Récentes -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Transactions Récentes</h3>
                    <a href="{{ route('transactions.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center space-x-1">
                        <span>Voir tout</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="space-y-4" id="recent-transactions">
                    @forelse ($transactions->take(5) as $transaction)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center 
                                {{ $transaction->type == 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <i class="fas {{ $transaction->type == 'income' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->description }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $transaction->category->name ?? 'Non catégorisé' }} • 
                                    {{ \Carbon\Carbon::parse($transaction->date_transaction)->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'income' ? '+' : '-' }}₣ {{ number_format($transaction->montant, 0, ',', ' ') }}
                            </p>
                            <div class="flex space-x-2 mt-1">
                                <a href="{{ route('transactions.edit', $transaction) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm" onclick="return confirm('Supprimer cette transaction ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Aucune transaction trouvée</p>
                        <a href="{{ route('transactions.create') }}" class="text-indigo-600 hover:text-indigo-700 text-sm mt-2 inline-block">
                            Créer votre première transaction
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Statistiques supplémentaires -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <!-- Catégories de Dépenses -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Catégories de Dépenses</h3>
                <div class="space-y-3" id="expense-categories">
                    @forelse($expenseCategories as $category)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">{{ $category['name'] }}</span>
                            <span class="text-sm font-medium">{{ $category['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full" style="width: {{ $category['percentage'] }}%; background-color: {{ $category['color'] }}"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            ₣ {{ number_format($category['total_amount'], 0, ',', ' ') }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-chart-pie text-2xl mb-2"></i>
                        <p>Aucune donnée de dépenses ce mois</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Objectifs Mensuels -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Objectifs Mensuels</h3>
                <div class="space-y-4" id="monthly-goals">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Épargne</span>
                            <span class="text-sm font-medium"><span id="savings-percentage">0</span>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 0%" id="savings-bar"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Dépenses</span>
                            <span class="text-sm font-medium"><span id="expenses-percentage">0</span>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 0%" id="expenses-bar"></div>
                        </div>
                    </div>
                    <div class="pt-4">
                        <button onclick="notificationSystem.createGoalNotification()" class="w-full text-center text-sm text-indigo-600 hover:text-indigo-700 py-2">
                            <i class="fas fa-bullseye mr-1"></i>Définir un nouvel objectif
                        </button>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('transactions.export') }}" class="p-3 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors flex flex-col items-center">
                        <i class="fas fa-file-export text-lg mb-1"></i>
                        <span class="text-xs">Exporter</span>
                    </a>
                    <a href="{{ route('reports.financial') }}" class="p-3 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors flex flex-col items-center">
                        <i class="fas fa-chart-pie text-lg mb-1"></i>
                        <span class="text-xs">Rapport</span>
                    </a>
                    <button onclick="showBudgetAlerts()" class="p-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex flex-col items-center">
                        <i class="fas fa-bell text-lg mb-1"></i>
                        <span class="text-xs">Alertes Budget</span>
                    </button>
                    <button onclick="openSettingsModal()" class="p-3 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors flex flex-col items-center">
                        <i class="fas fa-cog text-lg mb-1"></i>
                        <span class="text-xs">Réglages</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal des Paramètres -->
    <div id="settingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Paramètres</h3>
                <button onclick="closeSettingsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thème</label>
                    <select id="themeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="light">Clair</option>
                        <option value="dark">Sombre</option>
                        <option value="auto">Auto</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notifications</label>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Notifications email</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="emailNotifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Notifications push</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="pushNotifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Son des notifications</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="soundNotifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 border-t">
                    <button onclick="saveSettings()" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                        Enregistrer les paramètres
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notificationToast" class="fixed bottom-4 right-4 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50 hidden">
        <div class="flex items-center">
            <i id="toastIcon" class="fas fa-check-circle mr-2"></i>
            <span id="toastMessage">Opération réussie</span>
        </div>
    </div>

    <script>
        // Système de Notifications Avancé
        class TogoNotificationSystem {
            constructor() {
                this.notifications = [];
                this.unreadCount = 0;
                this.pollingInterval = null;
                this.isDropdownOpen = false;
                this.eventListeners = [];
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                this.userId = {{ auth()->check() ? auth()->id() : 0 }};
                this.pollingEnabled = true;
                this.pollingFrequency = 30000; // 30 secondes
                this.soundEnabled = false;
                this.init();
            }
            
            async init() {
                console.log('Initialisation du système de notifications Togo Finance...');
                await this.loadNotifications();
                this.startPolling();
                this.setupEventListeners();
                this.loadSettings();
            }
            
            async loadNotifications() {
                try {
                    // Simulation d'API - À remplacer par votre endpoint réel
                    const mockNotifications = [
                        {
                            id: 1,
                            title: 'Nouvelle transaction enregistrée',
                            message: 'Transaction "Salaire" de 500,000 FCFA ajoutée',
                            created_at: new Date(Date.now() - 5 * 60000).toISOString(),
                            read: false,
                            type: 'success',
                            icon: 'fa-exchange-alt',
                            sender: { name: 'Système' }
                        },
                        {
                            id: 2,
                            title: 'Objectif d\'épargne atteint',
                            message: 'Vous avez atteint 85% de votre objectif mensuel d\'épargne',
                            created_at: new Date(Date.now() - 60 * 60000).toISOString(),
                            read: false,
                            type: 'info',
                            icon: 'fa-trophy',
                            sender: { name: 'Objectifs' }
                        },
                        {
                            id: 3,
                            title: 'Dépassement de budget',
                            message: 'Attention : votre budget "Alimentation" est dépassé de 15%',
                            created_at: new Date(Date.now() - 120 * 60000).toISOString(),
                            read: true,
                            type: 'warning',
                            icon: 'fa-exclamation-triangle',
                            sender: { name: 'Budgets' }
                        },
                        {
                            id: 4,
                            title: 'Rappel de facture',
                            message: 'Facture EDF à payer avant le 15 du mois',
                            created_at: new Date(Date.now() - 24 * 3600000).toISOString(),
                            read: true,
                            type: 'reminder',
                            icon: 'fa-calendar',
                            sender: { name: 'Rappels' }
                        }
                    ];
                    
                    // Si vous avez un backend, utilisez ce code :
                    /*
                    const response = await fetch('/api/notifications', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });
                    
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.notifications = data.notifications.data || data.notifications;
                        this.unreadCount = data.unread_count || this.calculateUnreadCount();
                    } else {
                        throw new Error(data.message || 'Erreur de chargement');
                    }
                    */
                    
                    // Simulation pour le moment
                    this.notifications = mockNotifications;
                    this.unreadCount = this.calculateUnreadCount();
                    this.updateUI();
                    
                } catch (error) {
                    console.error('Erreur chargement notifications:', error);
                    this.showToast('Erreur de chargement des notifications', 'error');
                }
            }
            
            calculateUnreadCount() {
                return this.notifications.filter(n => !n.read).length;
            }
            
            updateUI() {
                // Mettre à jour le badge dans le header
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    if (this.unreadCount > 0) {
                        badge.textContent = this.unreadCount > 9 ? '9+' : this.unreadCount;
                        badge.classList.remove('hidden');
                        if (this.soundEnabled && this.unreadCount > 0) {
                            this.playNotificationSound();
                        }
                    } else {
                        badge.classList.add('hidden');
                    }
                }
                
                // Mettre à jour le badge dans le dropdown
                const dropdownBadge = document.getElementById('dropdownBadge');
                if (dropdownBadge) {
                    if (this.unreadCount > 0) {
                        dropdownBadge.textContent = this.unreadCount > 9 ? '9+' : this.unreadCount;
                        dropdownBadge.classList.remove('hidden');
                    } else {
                        dropdownBadge.classList.add('hidden');
                    }
                }
                
                // Mettre à jour la liste si le dropdown est ouvert
                this.updateNotificationList();
            }
            
            updateNotificationList() {
                const list = document.getElementById('notificationList');
                if (!list) return;
                
                if (this.notifications.length === 0) {
                    list.innerHTML = `
                        <div class="py-8 text-center">
                            <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">Aucune notification</p>
                        </div>
                    `;
                } else {
                    let html = '';
                    this.notifications.forEach(notification => {
                        const timeAgo = this.formatTimeAgo(notification.created_at);
                        const colorClass = this.getColorClass(notification.type);
                        const icon = notification.icon || this.getIconByType(notification.type);
                        
                        html += `
                            <div class="p-3 hover:bg-gray-50 cursor-pointer ${notification.read ? '' : 'bg-blue-50'}" 
                                 onclick="notificationSystem.markAsRead(${notification.id})">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center ${colorClass}">
                                        <i class="fas ${icon} text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">${notification.title}</p>
                                        <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                        <p class="text-xs text-gray-400 mt-1">${notification.sender ? 'De: ' + notification.sender.name + ' • ' : ''}${timeAgo}</p>
                                    </div>
                                    ${!notification.read ? '<span class="w-2 h-2 bg-blue-500 rounded-full mt-2"></span>' : ''}
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `
                        <div class="p-2 border-t">
                            <button onclick="notificationSystem.markAllAsRead()" class="w-full text-center text-sm text-indigo-600 hover:text-indigo-700 py-2">
                                Marquer tout comme lu
                            </button>
                        </div>
                    `;
                    
                    list.innerHTML = html;
                }
            }
            
            async markAsRead(id) {
                try {
                    // Simulation - À remplacer par votre appel API
                    const notification = this.notifications.find(n => n.id === id);
                    if (notification && !notification.read) {
                        notification.read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        this.updateUI();
                        this.showToast('Notification marquée comme lue');
                        
                        // Code pour l'API réelle :
                        /*
                        const response = await fetch(`/api/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                            await this.loadNotifications();
                            this.showToast('Notification marquée comme lue');
                        }
                        */
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('Erreur lors de la mise à jour', 'error');
                }
            }
            
            async markAllAsRead() {
                try {
                    // Marquer toutes comme lues localement
                    this.notifications.forEach(notification => {
                        notification.read = true;
                    });
                    this.unreadCount = 0;
                    this.updateUI();
                    this.showToast('Toutes les notifications marquées comme lues');
                    
                    // Code pour l'API réelle :
                    /*
                    const response = await fetch('/api/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.unreadCount = 0;
                        await this.loadNotifications();
                        this.showToast('Toutes les notifications marquées comme lues');
                    }
                    */
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('Erreur lors de la mise à jour', 'error');
                }
            }
            
            toggleNotifications() {
                const dropdown = document.getElementById('notificationDropdown');
                if (dropdown) {
                    dropdown.classList.toggle('hidden');
                    this.isDropdownOpen = !this.isDropdownOpen;
                    if (this.isDropdownOpen) {
                        this.updateNotificationList();
                    }
                }
            }
            
            startPolling() {
                if (!this.pollingEnabled) return;
                
                // Rafraîchir toutes les 30 secondes
                this.pollingInterval = setInterval(() => {
                    this.loadNotifications();
                }, this.pollingFrequency);
            }
            
            stopPolling() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                    this.pollingInterval = null;
                }
            }
            
            setupEventListeners() {
                // Bouton pour afficher les notifications
                const notificationButton = document.getElementById('notificationButton');
                if (notificationButton) {
                    notificationButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.toggleNotifications();
                    });
                }
                
                // Fermer le dropdown quand on clique ailleurs
                document.addEventListener('click', (event) => {
                    const dropdown = document.getElementById('notificationDropdown');
                    if (dropdown && !dropdown.classList.contains('hidden')) {
                        if (!event.target.closest('#notificationButton') && !event.target.closest('#notificationDropdown')) {
                            dropdown.classList.add('hidden');
                            this.isDropdownOpen = false;
                        }
                    }
                });
                
                // Rafraîchissement manuel avec F5
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'F5') {
                        e.preventDefault();
                        this.loadNotifications();
                    }
                });
            }
            
            formatTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return 'À l\'instant';
                if (seconds < 3600) return `Il y a ${Math.floor(seconds / 60)} min`;
                if (seconds < 86400) return `Il y a ${Math.floor(seconds / 3600)} h`;
                if (seconds < 2592000) return `Il y a ${Math.floor(seconds / 86400)} j`;
                return date.toLocaleDateString('fr-FR');
            }
            
            getColorClass(type) {
                const colors = {
                    'success': 'bg-green-100 text-green-600',
                    'info': 'bg-blue-100 text-blue-600',
                    'warning': 'bg-yellow-100 text-yellow-600',
                    'error': 'bg-red-100 text-red-600',
                    'reminder': 'bg-purple-100 text-purple-600'
                };
                return colors[type] || 'bg-gray-100 text-gray-600';
            }
            
            getIconByType(type) {
                const icons = {
                    'success': 'fa-check-circle',
                    'info': 'fa-info-circle',
                    'warning': 'fa-exclamation-triangle',
                    'error': 'fa-times-circle',
                    'reminder': 'fa-calendar'
                };
                return icons[type] || 'fa-bell';
            }
            
            showToast(message, type = 'success') {
                const toast = document.getElementById('notificationToast');
                const messageEl = document.getElementById('toastMessage');
                const iconEl = document.getElementById('toastIcon');
                
                const config = {
                    success: { bg: 'bg-green-500', icon: 'fa-check-circle' },
                    error: { bg: 'bg-red-500', icon: 'fa-times-circle' },
                    warning: { bg: 'bg-yellow-500', icon: 'fa-exclamation-triangle' },
                    info: { bg: 'bg-blue-500', icon: 'fa-info-circle' }
                };
                
                const { bg, icon } = config[type] || config.success;
                
                toast.className = `fixed bottom-4 right-4 ${bg} text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50`;
                messageEl.textContent = message;
                iconEl.className = `fas ${icon} mr-2`;
                
                toast.classList.remove('hidden');
                
                // Animation d'entrée
                setTimeout(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                    toast.classList.add('opacity-100');
                }, 10);
                
                // Animation de sortie
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.classList.add('hidden'), 300);
                }, 3000);
            }
            
            playNotificationSound() {
                if (!this.soundEnabled) return;
                
                // Créer un son de notification simple
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                    
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.5);
                } catch (e) {
                    console.log('Audio context non supporté');
                }
            }
            
            // Méthodes utilitaires
            testNotification() {
                const testNotif = {
                    id: Date.now(),
                    title: 'Notification de test',
                    message: 'Ceci est une notification de test du système',
                    created_at: new Date().toISOString(),
                    read: false,
                    type: 'info',
                    icon: 'fa-bell',
                    sender: { name: 'Système' }
                };
                
                this.notifications.unshift(testNotif);
                this.unreadCount++;
                this.updateUI();
                this.showToast('Notification de test créée');
            }
            
            createGoalNotification() {
                const goalNotif = {
                    id: Date.now(),
                    title: 'Nouvel objectif défini',
                    message: 'Objectif d\'épargne de 200,000 FCFA défini pour ce mois',
                    created_at: new Date().toISOString(),
                    read: false,
                    type: 'success',
                    icon: 'fa-bullseye',
                    sender: { name: 'Objectifs' }
                };
                
                this.notifications.unshift(goalNotif);
                this.unreadCount++;
                this.updateUI();
                this.showToast('Objectif défini avec succès');
            }
            
            createBudgetAlert() {
                const alertNotif = {
                    id: Date.now(),
                    title: 'Alerte Budget',
                    message: 'Budget "Loisirs" dépassé de 25%. Consultez vos rapports.',
                    created_at: new Date().toISOString(),
                    read: false,
                    type: 'warning',
                    icon: 'fa-exclamation-triangle',
                    sender: { name: 'Budgets' }
                };
                
                this.notifications.unshift(alertNotif);
                this.unreadCount++;
                this.updateUI();
                this.showToast('Alerte budget créée', 'warning');
            }
            
            loadSettings() {
                this.soundEnabled = localStorage.getItem('soundNotifications') !== 'false';
                this.pollingEnabled = localStorage.getItem('pollingEnabled') !== 'false';
                this.pollingFrequency = parseInt(localStorage.getItem('pollingFrequency')) || 30000;
            }
            
            saveSettings() {
                localStorage.setItem('soundNotifications', this.soundEnabled);
                localStorage.setItem('pollingEnabled', this.pollingEnabled);
                localStorage.setItem('pollingFrequency', this.pollingFrequency);
            }
        }

        // Variables globales
        let notificationSystem;
        let financeChart;

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            notificationSystem = new TogoNotificationSystem();
            initChart();
            loadQuickStats();
            loadMonthlyStats();
            setupEventListeners();
            loadUserSettings();
            
            // Mettre à jour les statistiques périodiquement
            setInterval(() => {
                loadQuickStats();
                loadMonthlyStats();
            }, 60000); // Toutes les minutes
        });

        // Initialiser le graphique
        function initChart() {
            const ctx = document.getElementById('financeChart').getContext('2d');
            financeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [
                        {
                            label: 'Recettes',
                            data: {!! json_encode($chartIncome ?? []) !!},
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Dépenses',
                            data: {!! json_encode($chartExpense ?? []) !!},
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ₣${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₣' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Charger les stats rapides
        async function loadQuickStats() {
            try {
                // Simuler le chargement des stats
                const todayCount = Math.floor(Math.random() * 5) + 1;
                document.getElementById('today-transactions').textContent = `${todayCount} aujourd'hui`;
                
                // Mettre à jour les tendances
                updateTrends();
                
            } catch (error) {
                console.error('Erreur chargement stats:', error);
            }
        }

        // Charger les stats mensuelles
        async function loadMonthlyStats() {
            try {
                // Simuler les stats mensuelles
                const savingsPercentage = Math.floor(Math.random() * 100) + 1;
                const expensesPercentage = Math.floor(Math.random() * 100) + 1;
                
                document.getElementById('savings-percentage').textContent = savingsPercentage;
                document.getElementById('expenses-percentage').textContent = expensesPercentage;
                document.getElementById('savings-bar').style.width = savingsPercentage + '%';
                document.getElementById('expenses-bar').style.width = expensesPercentage + '%';
                
            } catch (error) {
                console.error('Erreur chargement stats mensuelles:', error);
            }
        }

        // Mettre à jour les tendances
        function updateTrends() {
            const income = {{ $totalIncome ?? 0 }};
            const expense = {{ $totalExpense ?? 0 }};
            const balance = income - expense;
            
            const incomeTrend = income > 0 ? '+12%' : '+0%';
            const expenseTrend = expense > 0 ? '-8%' : '+0%';
            
            document.getElementById('income-trend').textContent = incomeTrend + ' ce mois';
            document.getElementById('expense-trend').textContent = expenseTrend + ' ce mois';
            document.getElementById('balance-status').textContent = 
                balance >= 0 ? 'Équilibre positif' : 'Déficit';
        }

        // Mettre à jour la période du graphique
        async function updateChartPeriod(period) {
            try {
                // Mettre à jour les boutons actifs
                document.querySelectorAll('.period-btn').forEach(btn => {
                    if (btn.dataset.period === period) {
                        btn.classList.add('bg-indigo-600', 'text-white');
                        btn.classList.remove('text-gray-600', 'hover:bg-gray-100', 'bg-indigo-100', 'text-indigo-600');
                    } else {
                        btn.classList.remove('bg-indigo-600', 'text-white');
                        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
                        if (btn.dataset.period === '1month') {
                            btn.classList.add('bg-indigo-100', 'text-indigo-600');
                        }
                    }
                });

                // Simuler le chargement des données
                // En production, utilisez un appel API :
                // const response = await fetch(`{{ route("api.dashboard.chart-data") }}?period=${period}`);
                // const data = await response.json();
                
                // Simulation
                const labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'];
                const incomeData = [50000, 75000, 60000, 90000, 80000, 95000];
                const expenseData = [30000, 45000, 40000, 55000, 50000, 60000];
                
                financeChart.data.labels = labels;
                financeChart.data.datasets[0].data = incomeData;
                financeChart.data.datasets[1].data = expenseData;
                financeChart.update();
                
                notificationSystem.showToast(`Graphique mis à jour pour ${period}`, 'info');
                
            } catch (error) {
                console.error('Erreur mise à jour graphique:', error);
                notificationSystem.showToast('Erreur lors de la mise à jour du graphique', 'error');
            }
        }

        // Gestion des paramètres
        function openSettingsModal() {
            document.getElementById('settingsModal').classList.remove('hidden');
        }

        function closeSettingsModal() {
            document.getElementById('settingsModal').classList.add('hidden');
        }

        function loadUserSettings() {
            const theme = localStorage.getItem('theme') || 'light';
            const emailNotifications = localStorage.getItem('emailNotifications') !== 'false';
            const pushNotifications = localStorage.getItem('pushNotifications') !== 'false';
            const soundNotifications = localStorage.getItem('soundNotifications') !== 'false';
            
            document.getElementById('themeSelect').value = theme;
            document.getElementById('emailNotifications').checked = emailNotifications;
            document.getElementById('pushNotifications').checked = pushNotifications;
            document.getElementById('soundNotifications').checked = soundNotifications;
            
            applyTheme(theme);
        }

        function saveSettings() {
            const theme = document.getElementById('themeSelect').value;
            const emailNotifications = document.getElementById('emailNotifications').checked;
            const pushNotifications = document.getElementById('pushNotifications').checked;
            const soundNotifications = document.getElementById('soundNotifications').checked;
            
            localStorage.setItem('theme', theme);
            localStorage.setItem('emailNotifications', emailNotifications);
            localStorage.setItem('pushNotifications', pushNotifications);
            localStorage.setItem('soundNotifications', soundNotifications);
            
            applyTheme(theme);
            notificationSystem.soundEnabled = soundNotifications;
            closeSettingsModal();
            notificationSystem.showToast('Paramètres enregistrés avec succès');
        }

        function applyTheme(theme) {
            const body = document.body;
            if (theme === 'dark') {
                body.classList.add('dark');
                body.style.backgroundColor = '#1f2937';
            } else if (theme === 'light') {
                body.classList.remove('dark');
                body.style.backgroundColor = '#f3f4f6';
            } else {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    body.classList.add('dark');
                    body.style.backgroundColor = '#1f2937';
                } else {
                    body.classList.remove('dark');
                    body.style.backgroundColor = '#f3f4f6';
                }
            }
        }

        // Événements
        function setupEventListeners() {
            // Rafraîchissement manuel avec Ctrl+R
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'r') {
                    e.preventDefault();
                    notificationSystem.loadNotifications();
                    loadQuickStats();
                    notificationSystem.showToast('Tableau de bord rafraîchi', 'info');
                }
            });
            
            // Gestion des onglets
            let hidden, visibilityChange;
            if (typeof document.hidden !== "undefined") {
                hidden = "hidden";
                visibilityChange = "visibilitychange";
            } else if (typeof document.msHidden !== "undefined") {
                hidden = "msHidden";
                visibilityChange = "msvisibilitychange";
            } else if (typeof document.webkitHidden !== "undefined") {
                hidden = "webkitHidden";
                visibilityChange = "webkitvisibilitychange";
            }
            
            if (typeof document.addEventListener !== "undefined" && hidden !== undefined) {
                document.addEventListener(visibilityChange, () => {
                    if (!document[hidden]) {
                        notificationSystem.loadNotifications();
                    }
                }, false);
            }
        }

        // Fonctions supplémentaires
        function showCategories() {
            window.location.href = "{{ route('categories.index') }}";
        }

        function showBudgetAlerts() {
            notificationSystem.createBudgetAlert();
        }

        // Export global pour le débogage
        window.notificationSystem = notificationSystem;
        window.showBudgetAlerts = showBudgetAlerts;
        window.updateChartPeriod = updateChartPeriod;
        window.openSettingsModal = openSettingsModal;
        window.closeSettingsModal = closeSettingsModal;
        window.saveSettings = saveSettings;
    </script>
</body>
</html>