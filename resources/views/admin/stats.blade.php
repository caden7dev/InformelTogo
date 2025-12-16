<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header Admin -->
    <header class="bg-white shadow-sm border-b">
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
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors">Tableau de bord</a>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-green-600 transition-colors">Utilisateurs</a>
                    <a href="{{ route('admin.transactions.index') }}" class="text-gray-600 hover:text-green-600 transition-colors">Transactions</a>
                    <a href="{{ route('admin.stats') }}" class="text-green-600 font-medium border-b-2 border-green-600 pb-1">Statistiques</a>
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
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </button>
                        
                        <!-- Menu déroulant -->
                        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
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
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Statistiques de la Plateforme</h1>
                <p class="text-gray-600 mt-2">Vue d'ensemble des performances de la plateforme</p>
            </div>
            <div class="flex items-center space-x-2">
                <span id="lastUpdate" class="text-sm text-gray-500">
                    Dernière mise à jour : {{ now()->format('H:i') }}
                </span>
                <button id="refreshStats" class="p-2 text-gray-500 hover:text-green-600 transition-colors">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Statistiques Globales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Utilisateurs</p>
                        <p id="totalUsers" class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Transactions</p>
                        <p id="totalTransactions" class="text-2xl font-bold text-gray-900">{{ number_format($totalTransactions ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Volume Total</p>
                        <p id="totalVolume" class="text-2xl font-bold text-purple-600">
                            @currency($totalVolume ?? 0)
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Solde Global</p>
                        <p id="globalBalance" class="text-2xl font-bold {{ ($globalBalance ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @currency($globalBalance ?? 0)
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-balance-scale text-indigo-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails des Statistiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Statistiques Financières -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Statistiques Financières</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-down text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-900">Total Recettes</p>
                                <p id="totalIncome" class="text-lg font-bold text-green-600">
                                    @currency($totalIncome ?? 0)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-up text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-red-900">Total Dépenses</p>
                                <p id="totalExpense" class="text-lg font-bold text-red-600">
                                    @currency($totalExpense ?? 0)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Moyenne par Transaction</p>
                                <p id="averageTransaction" class="text-lg font-bold text-blue-600">
                                    @currency($averageTransaction ?? 0)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques Utilisateurs -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Statistiques Utilisateurs</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-tie text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-purple-900">Commerçants Actifs</p>
                                <p id="activeMerchants" class="text-lg font-bold text-purple-600">
                                    {{ number_format($activeMerchants ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-orange-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exchange-alt text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-orange-900">Transactions/Utilisateur</p>
                                <p id="transactionsPerUser" class="text-lg font-bold text-orange-600">
                                    {{ number_format($transactionsPerUser ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-indigo-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Volume/Utilisateur</p>
                                <p id="volumePerUser" class="text-lg font-bold text-indigo-600">
                                    @currency($volumePerUser ?? 0)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques Mensuelles -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Activité du Mois en Cours</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl text-white">
                    <p class="text-sm opacity-90">Recettes du Mois</p>
                    <p id="currentMonthIncome" class="text-2xl font-bold mt-2">
                        @currency($currentMonthIncome ?? 0)
                    </p>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-r from-red-500 to-red-600 rounded-2xl text-white">
                    <p class="text-sm opacity-90">Dépenses du Mois</p>
                    <p id="currentMonthExpense" class="text-2xl font-bold mt-2">
                        @currency($currentMonthExpense ?? 0)
                    </p>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl text-white">
                    <p class="text-sm opacity-90">Transactions du Mois</p>
                    <p id="currentMonthTransactions" class="text-2xl font-bold mt-2">
                        {{ number_format($currentMonthTransactions ?? 0) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Message si pas de données -->
        <div id="noDataMessage" class="mt-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6" 
             style="{{ (!isset($totalUsers) || $totalUsers == 0) ? '' : 'display: none;' }}">
            <div class="flex items-center justify-center text-yellow-700">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                <div>
                    <p class="font-medium">Données statistiques non disponibles</p>
                    <p class="text-sm mt-1">Les statistiques apparaîtront ici une fois que les commerçants commenceront à utiliser la plateforme.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification Toast -->
    <div id="notification" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50 hidden">
        <div class="flex items-center">
            <i class="fas fa-sync-alt mr-2"></i>
            <span>Statistiques mises à jour</span>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const refreshBtn = document.getElementById('refreshStats');
        const lastUpdateEl = document.getElementById('lastUpdate');
        const notification = document.getElementById('notification');
        const noDataMessage = document.getElementById('noDataMessage');

        // Fonction pour formater les devises
        function formatCurrency(amount) {
            if (typeof amount !== 'number') return amount;
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Fonction pour mettre à jour un élément avec animation
        function updateElement(elementId, newValue, isCurrency = false) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const oldText = element.textContent;
            let oldValue, newText;
            
            if (isCurrency) {
                oldValue = parseFloat(oldText.replace(/[^\d.-]/g, '') || 0);
                newText = formatCurrency(newValue);
            } else {
                oldValue = parseFloat(oldText.replace(/[^\d]/g, '') || 0);
                newText = new Intl.NumberFormat('fr-FR').format(newValue);
            }
            
            if (oldValue !== newValue) {
                // Animation de comptage
                animateCount(element, oldValue, newValue, isCurrency);
            }
        }

        // Animation de comptage
        function animateCount(element, start, end, isCurrency) {
            const duration = 800;
            const steps = 30;
            const increment = (end - start) / steps;
            let current = start;
            let step = 0;
            
            const timer = setInterval(() => {
                current += increment;
                step++;
                
                if (step >= steps) {
                    current = end;
                    clearInterval(timer);
                }
                
                if (isCurrency) {
                    element.textContent = formatCurrency(current);
                } else {
                    element.textContent = Math.round(current).toLocaleString('fr-FR');
                }
            }, duration / steps);
        }

        // Fonction pour afficher une notification
        function showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            
            notification.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50`;
            notification.querySelector('span').textContent = message;
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.remove('opacity-0', 'translate-y-2');
                notification.classList.add('opacity-100');
            }, 10);
            
            setTimeout(() => {
                notification.classList.remove('opacity-100');
                notification.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => notification.classList.add('hidden'), 300);
            }, 3000);
        }

        // Fonction principale pour mettre à jour les statistiques
        async function updateStats() {
            try {
                refreshBtn.classList.add('fa-spin');
                
                const response = await fetch('/admin/api/stats');
                if (!response.ok) throw new Error('Erreur réseau');
                
                const data = await response.json();
                
                // Mettre à jour les éléments
                updateElement('totalUsers', data.totalUsers || 0);
                updateElement('totalTransactions', data.totalTransactions || 0);
                updateElement('totalVolume', data.totalVolume || 0, true);
                updateElement('globalBalance', data.globalBalance || 0, true);
                updateElement('totalIncome', data.totalIncome || 0, true);
                updateElement('totalExpense', data.totalExpense || 0, true);
                updateElement('averageTransaction', data.averageTransaction || 0, true);
                updateElement('activeMerchants', data.activeMerchants || 0);
                updateElement('transactionsPerUser', data.transactionsPerUser || 0);
                updateElement('volumePerUser', data.volumePerUser || 0, true);
                updateElement('currentMonthIncome', data.currentMonthIncome || 0, true);
                updateElement('currentMonthExpense', data.currentMonthExpense || 0, true);
                updateElement('currentMonthTransactions', data.currentMonthTransactions || 0);
                
                // Mettre à jour l'heure
                const now = new Date();
                lastUpdateEl.textContent = `Dernière mise à jour : ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
                
                // Cacher le message "pas de données" si on a des données
                if (data.totalUsers > 0 && noDataMessage) {
                    noDataMessage.style.display = 'none';
                }
                
                showNotification('Statistiques mises à jour avec succès');
                
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur lors de la mise à jour', 'error');
            } finally {
                refreshBtn.classList.remove('fa-spin');
            }
        }

        // Événements
        refreshBtn.addEventListener('click', updateStats);
        
        // Mettre à jour automatiquement toutes les 30 secondes
        setInterval(updateStats, 30000);
        
        // Mettre à jour quand la page devient visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                updateStats();
            }
        });
        
        // Mettre à jour au chargement de la page (après 1 seconde)
        setTimeout(updateStats, 1000);
    });
    </script>
</body>
</html>