@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<!-- En-tête -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrateur</h1>
    <p class="text-gray-600 mt-2">Vue d'ensemble de la plateforme</p>
</div>

<!-- Cartes de Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Utilisateurs -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-600 font-medium">Total Utilisateurs</p>
                <p class="text-3xl font-bold mt-2 text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                <p class="text-green-600 text-sm mt-2 flex items-center">
                    <i class="fas fa-users mr-1"></i>
                    Tous utilisateurs
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Commerçants -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-600 font-medium">Commerçants</p>
                <p class="text-3xl font-bold mt-2 text-gray-900">{{ $stats['total_merchants'] ?? 0 }}</p>
                <p class="text-green-600 text-sm mt-2 flex items-center">
                    <i class="fas fa-store mr-1"></i>
                    Utilisateurs actifs
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-purple-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-600 font-medium">Transactions</p>
                <p class="text-3xl font-bold mt-2 text-gray-900">{{ $stats['total_transactions'] ?? 0 }}</p>
                <p class="text-purple-600 text-sm mt-2 flex items-center">
                    <i class="fas fa-exchange-alt mr-1"></i>
                    Total opérations
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-exchange-alt text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Volume Financier -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-orange-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-600 font-medium">Volume Financier</p>
                <p class="text-3xl font-bold mt-2 text-gray-900">₣ {{ number_format(($stats['total_income'] ?? 0) + ($stats['total_expense'] ?? 0), 0, ',', ' ') }}</p>
                <p class="text-orange-600 text-sm mt-2 flex items-center">
                    <i class="fas fa-chart-line mr-1"></i>
                    Chiffre d'affaires
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Graphique Transactions -->
    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des Transactions</h3>
        <canvas id="transactionsChart" class="w-full h-64"></canvas>
    </div>

    <!-- Utilisateurs par Région -->
    <div class="bg-white p-6 rounded-2xl shadow-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisateurs par Type</h3>
        <div class="space-y-4">
            @php
                $userTypes = [
                    ['type' => 'Commerçants', 'count' => $stats['total_merchants'] ?? 0, 'color' => 'bg-green-500'],
                    ['type' => 'Administrateurs', 'count' => $stats['total_admins'] ?? 0, 'color' => 'bg-purple-500'],
                    ['type' => 'Utilisateurs', 'count' => ($stats['total_users'] ?? 0) - (($stats['total_merchants'] ?? 0) + ($stats['total_admins'] ?? 0)), 'color' => 'bg-blue-500']
                ];
            @endphp
            @foreach($userTypes as $userType)
                @if($userType['count'] > 0)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">{{ $userType['type'] }}</span>
                        <span class="text-sm font-medium">{{ $userType['count'] }} utilisateurs</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $percentage = ($userType['count'] / ($stats['total_users'] ?? 1)) * 100;
                        @endphp
                        <div class="{{ $userType['color'] }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Derniers Utilisateurs -->
<div class="bg-white p-6 rounded-2xl shadow-lg mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Derniers Utilisateurs Inscrits</h3>
        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center space-x-1">
            <span>Voir tout</span>
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Utilisateur</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Email</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Rôle</th>
                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Inscription</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($recentUsers ?? []) as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white text-xs font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                            {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 
                               ($user->role == 'commercant' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                        Aucun utilisateur trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Transactions Récentes -->
<div class="bg-white p-6 rounded-2xl shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Transactions Récentes</h3>
        <a href="{{ route('admin.transactions.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center space-x-1">
            <span>Voir tout</span>
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    <div class="space-y-4">
        @forelse(($recentTransactions ?? []) as $transaction)
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center 
                    {{ $transaction->type == 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    <i class="fas {{ $transaction->type == 'income' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $transaction->description }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $transaction->user->name ?? 'Utilisateur inconnu' }} • 
                        {{ $transaction->category->name ?? 'Non catégorisé' }} • 
                        {{ $transaction->date_transaction->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-semibold {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type == 'income' ? '+' : '-' }}₣ {{ number_format($transaction->montant, 0, ',', ' ') }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-exchange-alt text-4xl mb-3 text-gray-300"></i>
            <p>Aucune transaction récente</p>
        </div>
        @endforelse
    </div>
</div>

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
<div id="notificationToast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50 hidden">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toastMessage">Opération réussie</span>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des transactions
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('transactionsChart');
        if (ctx) {
            const transactionsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($monthlyTransactions['labels'] ?? []),
                    datasets: [
                        {
                            label: 'Recettes',
                            data: @json($monthlyTransactions['income'] ?? []),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Dépenses',
                            data: @json($monthlyTransactions['expense'] ?? []),
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
                            ticks: {
                                callback: function(value) {
                                    return '₣' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialiser les paramètres
        loadSettings();
    });

    // Gestion des notifications
    let notifications = [
        {
            id: 1,
            title: 'Nouvelle transaction',
            message: 'Un commerçant a effectué une transaction de 50 000 F',
            time: 'Il y a 5 min',
            read: false,
            type: 'transaction'
        },
        {
            id: 2,
            title: 'Nouvel utilisateur',
            message: 'John Doe s\'est inscrit sur la plateforme',
            time: 'Il y a 1 heure',
            read: false,
            type: 'user'
        },
        {
            id: 3,
            title: 'Mise à jour système',
            message: 'Une nouvelle version est disponible',
            time: 'Il y a 2 jours',
            read: true,
            type: 'system'
        }
    ];

    let unreadCount = notifications.filter(n => !n.read).length;

    // Mettre à jour le badge de notifications
    function updateNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    // Afficher les notifications
    function showNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            // Construire le contenu des notifications
            let html = '';
            
            if (notifications.length === 0) {
                html = `
                    <div class="py-8 text-center">
                        <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">Aucune notification</p>
                    </div>
                `;
            } else {
                notifications.forEach(notification => {
                    html += `
                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer ${notification.read ? '' : 'bg-blue-50'}" 
                             onclick="markAsRead(${notification.id})">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    ${notification.type === 'transaction' ? 'bg-green-100 text-green-600' : 
                                      notification.type === 'user' ? 'bg-blue-100 text-blue-600' : 
                                      'bg-yellow-100 text-yellow-600'}">
                                    <i class="fas 
                                        ${notification.type === 'transaction' ? 'fa-exchange-alt' : 
                                          notification.type === 'user' ? 'fa-user-plus' : 
                                          'fa-cog'} text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">${notification.title}</p>
                                    <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                    <p class="text-xs text-gray-400 mt-1">${notification.time}</p>
                                </div>
                                ${!notification.read ? '<span class="w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                            </div>
                        </div>
                    `;
                });
                
                html += `
                    <div class="p-2 border-t">
                        <button onclick="markAllAsRead()" class="w-full text-center text-sm text-indigo-600 hover:text-indigo-700 py-2">
                            Marquer tout comme lu
                        </button>
                    </div>
                `;
            }
            
            dropdown.innerHTML = html;
            dropdown.classList.toggle('hidden');
        }
    }

    // Marquer une notification comme lue
    function markAsRead(id) {
        const notification = notifications.find(n => n.id === id);
        if (notification && !notification.read) {
            notification.read = true;
            unreadCount--;
            updateNotificationBadge();
            showToast('Notification marquée comme lue');
        }
    }

    // Marquer toutes les notifications comme lues
    function markAllAsRead() {
        notifications.forEach(notification => {
            if (!notification.read) {
                notification.read = true;
            }
        });
        unreadCount = 0;
        updateNotificationBadge();
        showToast('Toutes les notifications marquées comme lues');
        showNotifications(); // Rafraîchir l'affichage
    }

    // Gestion des paramètres
    function openSettingsModal() {
        document.getElementById('settingsModal').classList.remove('hidden');
    }

    function closeSettingsModal() {
        document.getElementById('settingsModal').classList.add('hidden');
    }

    function loadSettings() {
        // Charger les paramètres depuis localStorage
        const theme = localStorage.getItem('theme') || 'light';
        const emailNotifications = localStorage.getItem('emailNotifications') !== 'false';
        const pushNotifications = localStorage.getItem('pushNotifications') !== 'false';
        
        document.getElementById('themeSelect').value = theme;
        document.getElementById('emailNotifications').checked = emailNotifications;
        document.getElementById('pushNotifications').checked = pushNotifications;
        
        // Appliquer le thème
        applyTheme(theme);
    }

    function saveSettings() {
        const theme = document.getElementById('themeSelect').value;
        const emailNotifications = document.getElementById('emailNotifications').checked;
        const pushNotifications = document.getElementById('pushNotifications').checked;
        
        // Sauvegarder dans localStorage
        localStorage.setItem('theme', theme);
        localStorage.setItem('emailNotifications', emailNotifications);
        localStorage.setItem('pushNotifications', pushNotifications);
        
        // Appliquer le thème
        applyTheme(theme);
        
        // Fermer le modal et afficher une notification
        closeSettingsModal();
        showToast('Paramètres enregistrés avec succès');
    }

    function applyTheme(theme) {
        const body = document.body;
        
        if (theme === 'dark') {
            body.classList.add('dark');
        } else if (theme === 'light') {
            body.classList.remove('dark');
        } else {
            // Auto - utiliser la préférence système
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                body.classList.add('dark');
            } else {
                body.classList.remove('dark');
            }
        }
    }

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('notificationToast');
        const messageEl = document.getElementById('toastMessage');
        
        // Définir la couleur selon le type
        const bgColor = type === 'success' ? 'bg-green-500' : 
                       type === 'error' ? 'bg-red-500' : 
                       type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
        
        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50`;
        messageEl.textContent = message;
        toast.classList.remove('hidden');
        
        // Animation d'entrée
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-y-2');
            toast.classList.add('opacity-100');
        }, 10);
        
        // Animation de sortie après 3 secondes
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
    }

    // Fermer les dropdowns quand on clique ailleurs
    document.addEventListener('click', function(event) {
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
            if (!event.target.closest('#notificationButton') && !event.target.closest('#notificationDropdown')) {
                notificationDropdown.classList.add('hidden');
            }
        }
    });

    // Mettre à jour le badge au chargement
    updateNotificationBadge();
</script>
@endsection