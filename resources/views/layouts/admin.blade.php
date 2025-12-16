<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Plateforme Togo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        .sidebar-collapsed {
            width: 70px;
        }
        .sidebar-expanded {
            width: 260px;
        }
        .main-content-expanded {
            margin-left: 260px;
        }
        .main-content-collapsed {
            margin-left: 70px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar sidebar-expanded fixed inset-y-0 left-0 bg-white shadow-lg z-50 flex flex-col">
        <div class="flex-1">
            <div class="p-4 border-b">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-crown text-white"></i>
                    </div>
                    <div id="sidebar-logo" class="transition-all duration-300">
                        <h1 class="text-lg font-bold text-gray-900">Togo Finance</h1>
                        <p class="text-xs text-gray-500">Panel d'administration</p>
                    </div>
                </div>
            </div>

            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600 border-r-2 border-indigo-600' : '' }}">
                    <i class="fas fa-chart-line w-6 text-center"></i>
                    <span id="sidebar-text" class="transition-all duration-300">Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-600 border-r-2 border-indigo-600' : '' }}">
                    <i class="fas fa-users w-6 text-center"></i>
                    <span id="sidebar-text" class="transition-all duration-300">Utilisateurs</span>
                </a>

                <a href="{{ route('admin.transactions.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors {{ request()->routeIs('admin.transactions') ? 'bg-indigo-50 text-indigo-600 border-r-2 border-indigo-600' : '' }}">
                    <i class="fas fa-exchange-alt w-6 text-center"></i>
                    <span id="sidebar-text" class="transition-all duration-300">Transactions</span>
                </a>

                <a href="{{ route('admin.stats') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors {{ request()->routeIs('admin.stats') ? 'bg-indigo-50 text-indigo-600 border-r-2 border-indigo-600' : '' }}">
                    <i class="fas fa-chart-pie w-6 text-center"></i>
                    <span id="sidebar-text" class="transition-all duration-300">Statistiques</span>
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-arrow-left w-6 text-center"></i>
                    <span id="sidebar-text" class="transition-all duration-300">Retour au site</span>
                </a>
            </nav>
        </div>

        <!-- Bouton Déconnexion en bas -->
        <div class="p-4 border-t mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center space-x-3 w-full px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors group">
                    <i class="fas fa-sign-out-alt text-red-400 group-hover:text-red-600 w-6 text-center"></i>
                    <span id="sidebar-text" class="transition-all duration-300">Déconnexion</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="main-content-expanded transition-all duration-300">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex justify-between items-center px-6 py-4">
                <div class="flex items-center space-x-4">
                    <button id="sidebar-toggle" class="text-gray-600 hover:text-indigo-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-900">
                        @yield('title', 'Administration')
                    </h2>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Bouton Notifications -->
                    <div class="relative">
                        <button id="notificationButton" class="p-2 text-gray-600 hover:text-indigo-600 transition-colors relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                        </button>
                        
                        <!-- Dropdown Notifications -->
                        <div id="notificationDropdown" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border hidden z-50 max-h-96 overflow-y-auto">
                            <div class="p-4 border-b">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                    <button onclick="markAllAsRead()" class="text-sm text-indigo-600 hover:text-indigo-700">
                                        Tout marquer comme lu
                                    </button>
                                </div>
                            </div>
                            <div id="notificationList" class="divide-y divide-gray-100">
                                <!-- Les notifications seront chargées ici -->
                            </div>
                        </div>
                    </div>

                    <!-- Bouton Paramètres -->
                    <button onclick="openSettingsModal()" class="p-2 text-gray-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-cog text-lg"></i>
                    </button>

                    <!-- Profil Utilisateur -->
                    <div class="relative group">
                        <button class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            
                            <div class="text-left hidden md:block">
                                <span class="text-gray-700 font-medium block">{{ Auth::user()->name }}</span>
                                <span class="text-gray-500 text-xs block">{{ Auth::user()->email }}</span>
                            </div>
                            
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <div class="p-4 border-b">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-xs text-indigo-600 font-medium mt-1">Administrateur</p>
                            </div>
                            
                            <div class="p-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-user-edit text-gray-400 w-5"></i>
                                    <span>Modifier le profil</span>
                                </a>
                                
                                <button onclick="openSettingsModal()" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-cog text-gray-400 w-5"></i>
                                    <span>Paramètres</span>
                                </button>
                                
                                <button onclick="showNotifications()" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-bell text-gray-400 w-5"></i>
                                    <span>Notifications</span>
                                    <span id="dropdownBadge" class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                                </button>
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
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
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

    <script>
        // Gestion de la sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarTexts = document.querySelectorAll('#sidebar-text');
        const sidebarLogo = document.getElementById('sidebar-logo');

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-expanded');
            sidebar.classList.toggle('sidebar-collapsed');
            mainContent.classList.toggle('main-content-expanded');
            mainContent.classList.toggle('main-content-collapsed');
            
            sidebarTexts.forEach(text => {
                text.classList.toggle('hidden');
            });
            
            sidebarLogo.classList.toggle('hidden');
        });

        // Système de Notifications
        let notifications = [
            {
                id: 1,
                title: 'Nouvelle transaction',
                message: 'Un commerçant a effectué une transaction de 50 000 F',
                time: 'Il y a 5 min',
                read: false,
                type: 'transaction',
                icon: 'fa-exchange-alt'
            },
            {
                id: 2,
                title: 'Nouvel utilisateur',
                message: 'John Doe s\'est inscrit sur la plateforme',
                time: 'Il y a 1 heure',
                read: false,
                type: 'user',
                icon: 'fa-user-plus'
            },
            {
                id: 3,
                title: 'Mise à jour système',
                message: 'Une nouvelle version est disponible',
                time: 'Il y a 2 jours',
                read: true,
                type: 'system',
                icon: 'fa-cog'
            }
        ];

        let unreadCount = notifications.filter(n => !n.read).length;

        // Initialiser les notifications
        function initNotifications() {
            updateNotificationBadge();
            loadNotifications();
        }

        // Mettre à jour le badge de notifications
        function updateNotificationBadge() {
            const badge = document.getElementById('notificationBadge');
            const dropdownBadge = document.getElementById('dropdownBadge');
            
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            
            if (dropdownBadge) {
                if (unreadCount > 0) {
                    dropdownBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    dropdownBadge.classList.remove('hidden');
                } else {
                    dropdownBadge.classList.add('hidden');
                }
            }
        }

        // Charger les notifications
        function loadNotifications() {
            const notificationList = document.getElementById('notificationList');
            if (!notificationList) return;
            
            if (notifications.length === 0) {
                notificationList.innerHTML = `
                    <div class="py-8 text-center">
                        <i class="fas fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">Aucune notification</p>
                    </div>
                `;
            } else {
                let html = '';
                notifications.forEach(notification => {
                    html += `
                        <div class="p-3 hover:bg-gray-50 cursor-pointer ${notification.read ? '' : 'bg-blue-50'}" 
                             onclick="markAsRead(${notification.id})">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    ${notification.type === 'transaction' ? 'bg-green-100 text-green-600' : 
                                      notification.type === 'user' ? 'bg-blue-100 text-blue-600' : 
                                      'bg-yellow-100 text-yellow-600'}">
                                    <i class="fas ${notification.icon} text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">${notification.title}</p>
                                    <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                    <p class="text-xs text-gray-400 mt-1">${notification.time}</p>
                                </div>
                                ${!notification.read ? '<span class="w-2 h-2 bg-blue-500 rounded-full mt-2"></span>' : ''}
                            </div>
                        </div>
                    `;
                });
                notificationList.innerHTML = html;
            }
        }

        // Afficher/cacher les notifications
        function showNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                // Recharger les notifications à chaque ouverture
                loadNotifications();
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
                // Recharger la liste
                loadNotifications();
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
            loadNotifications();
        }

        // Gestion des paramètres
        function openSettingsModal() {
            document.getElementById('settingsModal').classList.remove('hidden');
        }

        function closeSettingsModal() {
            document.getElementById('settingsModal').classList.add('hidden');
        }

        function loadSettings() {
            const theme = localStorage.getItem('theme') || 'light';
            const emailNotifications = localStorage.getItem('emailNotifications') !== 'false';
            const pushNotifications = localStorage.getItem('pushNotifications') !== 'false';
            
            document.getElementById('themeSelect').value = theme;
            document.getElementById('emailNotifications').checked = emailNotifications;
            document.getElementById('pushNotifications').checked = pushNotifications;
            
            applyTheme(theme);
        }

        function saveSettings() {
            const theme = document.getElementById('themeSelect').value;
            const emailNotifications = document.getElementById('emailNotifications').checked;
            const pushNotifications = document.getElementById('pushNotifications').checked;
            
            localStorage.setItem('theme', theme);
            localStorage.setItem('emailNotifications', emailNotifications);
            localStorage.setItem('pushNotifications', pushNotifications);
            
            applyTheme(theme);
            closeSettingsModal();
            showToast('Paramètres enregistrés avec succès');
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

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const messageEl = document.getElementById('toastMessage');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-2 opacity-0 transition-all duration-300 z-50`;
            messageEl.textContent = message;
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
                toast.classList.add('opacity-100');
            }, 10);
            
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        // Événements
        document.addEventListener('DOMContentLoaded', function() {
            initNotifications();
            loadSettings();
            
            // Bouton notifications
            const notificationButton = document.getElementById('notificationButton');
            if (notificationButton) {
                notificationButton.addEventListener('click', showNotifications);
            }
            
            // Fermer les dropdowns quand on clique ailleurs
            document.addEventListener('click', function(event) {
                const notificationDropdown = document.getElementById('notificationDropdown');
                if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
                    if (!event.target.closest('#notificationButton') && !event.target.closest('#notificationDropdown')) {
                        notificationDropdown.classList.add('hidden');
                    }
                }
                
                const profileDropdown = document.querySelector('.group > .absolute');
                if (profileDropdown && profileDropdown.classList.contains('opacity-100')) {
                    if (!event.target.closest('.group')) {
                        profileDropdown.classList.remove('opacity-100', 'visible');
                        profileDropdown.classList.add('opacity-0', 'invisible');
                    }
                }
            });
        });

        // Ajouter une notification de test (pour démo)
        function addTestNotification() {
            const newNotification = {
                id: notifications.length + 1,
                title: 'Nouveau test',
                message: 'Ceci est une notification de test',
                time: 'À l\'instant',
                read: false,
                type: 'system',
                icon: 'fa-info-circle'
            };
            notifications.unshift(newNotification);
            unreadCount++;
            updateNotificationBadge();
            showToast('Nouvelle notification reçue');
        }
    </script>

    @yield('scripts')
</body>
</html>