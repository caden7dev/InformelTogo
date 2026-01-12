<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header Admin -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Togo Finance</h1>
                        <p class="text-xs text-gray-500">Administration</p>
                    </div>
                </div>
                
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-green-600 border-b-2 border-green-600 pb-1' : '' }}">Tableau de bord</a>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-green-600 transition-colors {{ request()->routeIs('admin.users.*') ? 'text-green-600 border-b-2 border-green-600 pb-1' : '' }}">Utilisateurs</a>
                    <a href="{{ route('admin.transactions.index') }}" class="text-green-600 font-medium border-b-2 border-green-600 pb-1">Transactions</a>
                    <a href="{{ route('admin.stats') }}" class="text-gray-600 hover:text-green-600 transition-colors {{ request()->routeIs('admin.stats') ? 'text-green-600 border-b-2 border-green-600 pb-1' : '' }}">Statistiques</a>
                </nav>

                <!-- Menu Admin -->
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <button class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-red-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="text-left hidden md:block">
                                <span class="text-gray-700 font-medium block">{{ Auth::user()->name }}</span>
                                <span class="text-gray-500 text-xs block">Administrateur</span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-sm transition-transform group-hover:rotate-180"></i>
                        </button>
                        
                        <!-- Menu déroulant -->
                        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user w-5 text-gray-400 mr-2"></i>
                                    <span>Profil</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-5 text-red-400 mr-2"></i>
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

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- En-tête avec titre et actions -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900">Transactions des Commerçants</h1>
                <p class="text-gray-600 mt-2">Gestion de toutes les transactions de la plateforme</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportToCSV()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-file-export mr-2"></i>
                    Exporter CSV
                </button>
                <button onclick="printTable()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                    <i class="fas fa-print mr-2"></i>
                    Imprimer
                </button>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Transactions</p>
                        <p class="text-2xl font-bold">{{ number_format($totalTransactions ?? 0) }}</p>
                        <p class="text-blue-100 text-xs mt-1 flex items-center">
                            <i class="fas fa-chart-line mr-1"></i>
                            <span>Toutes périodes</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-2xl shadow-lg p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Total Recettes</p>
                        <p class="text-2xl font-bold">
                            {{ number_format($totalIncome ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                        <p class="text-green-100 text-xs mt-1 flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>Revenus totaux</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl shadow-lg p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm">Total Dépenses</p>
                        <p class="text-2xl font-bold">
                            {{ number_format($totalExpense ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                        <p class="text-red-100 text-xs mt-1 flex items-center">
                            <i class="fas fa-arrow-down mr-1"></i>
                            <span>Charges totales</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-2xl shadow-lg p-6 hover-lift">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Solde Global</p>
                        <p class="text-2xl font-bold">
                            {{ number_format($balance ?? 0, 0, ',', ' ') }} FCFA
                        </p>
                        <p class="text-purple-100 text-xs mt-1 flex items-center">
                            <i class="fas fa-balance-scale mr-1"></i>
                            <span>{{ ($balance ?? 0) >= 0 ? 'Bénéfice' : 'Déficit' }}</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres Avancés -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-filter mr-2 text-green-600"></i>
                    Filtres Avancés
                </h2>
                <div class="text-sm text-gray-500">
                    {{ $transactions->total() ?? 0 }} transactions correspondantes
                </div>
            </div>

            <form action="{{ route('admin.transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtre Utilisateur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
                    <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">Tous les utilisateurs</option>
                        @isset($users)
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                
                <!-- Filtre Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">Tous les types</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Recettes</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Dépenses</option>
                    </select>
                </div>
                
                <!-- Filtre Catégorie -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">Toutes les catégories</option>
                        @isset($categories)
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <!-- Filtre Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">Toutes périodes</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.transactions.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Réinitialiser
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>

        <!-- Tableau des transactions -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Liste des Transactions</h2>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                        {{ $transactions->count() }} affichées
                    </span>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                        Page {{ $transactions->currentPage() }}/{{ $transactions->lastPage() }}
                    </span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <span>Utilisateur</span>
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center mr-3">
                                        <span class="text-white text-xs font-semibold">
                                            {{ strtoupper(substr($transaction->user->name ?? '?', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'Utilisateur inconnu' }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->type == 'income')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-arrow-down mr-1 text-xs"></i> Recette
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-arrow-up mr-1 text-xs"></i> Dépense
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($transaction->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $transaction->category->color ?? '#6b7280' }}"></div>
                                    <span class="text-sm text-gray-900">{{ $transaction->category->name ?? 'Catégorie inconnue' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $transaction->date_transaction->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $transaction->date_transaction->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $transaction->description }}">
                                    {{ $transaction->description }}
                                </div>
                                @if($transaction->notes)
                                <div class="text-xs text-gray-500 mt-1 truncate" title="{{ $transaction->notes }}">
                                    <i class="fas fa-sticky-note mr-1"></i>{{ $transaction->notes }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="viewTransaction({{ $transaction->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editTransaction({{ $transaction->id }})" class="text-green-600 hover:text-green-900 transition-colors" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteTransaction({{ $transaction->id }})" class="text-red-600 hover:text-red-900 transition-colors" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-exchange-alt text-5xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium mb-2">Aucune transaction trouvée</p>
                                    <p class="text-sm max-w-md">Aucune transaction ne correspond à vos critères de recherche. Essayez de modifier vos filtres.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700 mb-2 sm:mb-0">
                    Affichage de {{ $transactions->firstItem() }} à {{ $transactions->lastItem() }} sur {{ $transactions->total() }} résultats
                </div>
                <div class="flex justify-end">
                    {{ $transactions->links() }}
                </div>
            </div>
            @endif
        </div>
    </main>

    <script>
        // Fonction d'export CSV
        function exportToCSV() {
            alert('Fonction d\'export CSV à implémenter');
            // Implémentation future pour exporter les données en CSV
        }

        // Fonction d'impression
        function printTable() {
            window.print();
        }

        // Fonctions de gestion des transactions
        function viewTransaction(id) {
            alert('Voir transaction ID: ' + id);
            // Redirection vers la page de détails
            // window.location.href = '/admin/transactions/' + id;
        }

        function editTransaction(id) {
            alert('Modifier transaction ID: ' + id);
            // Redirection vers la page d'édition
            // window.location.href = '/admin/transactions/' + id + '/edit';
        }

        function deleteTransaction(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')) {
                alert('Supprimer transaction ID: ' + id);
                // Implémentation de la suppression AJAX
                // fetch('/admin/transactions/' + id, { method: 'DELETE' })
                // .then(response => location.reload());
            }
        }

        // Recherche en temps réel (optionnel)
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    // Implémentation de la recherche en temps réel
                });
            }
        });

        // Tri des colonnes
        function sortTable(column) {
            alert('Tri par colonne: ' + column);
            // Implémentation du tri des colonnes
        }
    </script>
</body>
</html>