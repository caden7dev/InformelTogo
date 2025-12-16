<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Plateforme Togo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        .notification-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    
    <div class="min-h-screen flex flex-col">
        <!-- Navigation Responsive -->
        <nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo et Nom -->
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-white text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Togo Finance</h1>
                            <p class="text-xs text-gray-500 hidden sm:block">Gestion financière intelligente</p>
                        </div>
                    </div>

                    <!-- Navigation Desktop -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : '' }}">
                            <i class="fas fa-home mr-2"></i>Tableau de bord
                        </a>
                        <a href="{{ route('transactions.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('transactions.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : '' }}">
                            <i class="fas fa-exchange-alt mr-2"></i>Transactions
                        </a>
                        <a href="{{ route('reports.financial') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : '' }}">
                            <i class="fas fa-chart-pie mr-2"></i>Rapports
                        </a>
                        
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('admin.*') ? 'text-purple-600 border-b-2 border-purple-600' : '' }}">
                            <i class="fas fa-crown mr-2"></i>Administration
                        </a>
                        @endif
                    </div>

                    <!-- User Menu Desktop -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button onclick="toggleNotifications()" class="p-2 text-gray-600 hover:text-indigo-600 transition-colors relative">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="notification-dot"></span>
                            </button>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-3 focus:outline-none">
                                <div class="w-10 h-10 border-2 border-indigo-200 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform group-hover:rotate-180"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="p-4 border-b">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="p-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                        <i class="fas fa-user-edit text-gray-400 w-5"></i>
                                        <span>Mon Profil</span>
                                    </a>
                                    <a href="{{ route('profile.settings') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                        <i class="fas fa-cog text-gray-400 w-5"></i>
                                        <span>Paramètres</span>
                                    </a>
                                    <a href="#" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
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

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center space-x-2">
                        <button onclick="toggleMobileMenu()" class="p-2 text-gray-600 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden bg-white border-t border-gray-200 opacity-0 invisible transition-all duration-300 absolute top-16 left-0 right-0 shadow-lg">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                        <i class="fas fa-home w-5"></i>
                        <span>Tableau de bord</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('transactions.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                        <i class="fas fa-exchange-alt w-5"></i>
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('reports.financial') }}" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('reports.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                        <i class="fas fa-chart-pie w-5"></i>
                        <span>Rapports</span>
                    </a>
                    
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:text-purple-600 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('admin.*') ? 'text-purple-600 bg-purple-50' : '' }}">
                        <i class="fas fa-crown w-5"></i>
                        <span>Administration</span>
                    </a>
                    @endif

                    <!-- Mobile User Info -->
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex items-center space-x-3 px-3 py-2">
                            <div class="w-8 h-8 border-2 border-indigo-200 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                <span class="text-white font-semibold text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-user-edit w-5"></i>
                                <span>Mon Profil</span>
                            </a>
                            <a href="{{ route('profile.settings') }}" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 block px-3 py-2 rounded-md text-base font-medium transition-colors">
                                <i class="fas fa-cog w-5"></i>
                                <span>Paramètres</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 w-full text-left text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium transition-colors">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- En-tête de page conditionnel -->
                @if(isset($header) || View::hasSection('header'))
                <header class="bg-white shadow-sm border-b mb-6">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex-1 min-w-0">
                                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                    {{ $header ?? $__env->yieldContent('header') }}
                                </h1>
                                @if(isset($subheader) || View::hasSection('subheader'))
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $subheader ?? $__env->yieldContent('subheader') }}
                                </p>
                                @endif
                            </div>
                            
                            @if(isset($actions) || View::hasSection('actions'))
                            <div class="mt-4 sm:mt-0 sm:ml-4">
                                {{ $actions ?? $__env->yieldContent('actions') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </header>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">Succès</h3>
                            <p class="text-sm text-green-600 mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Erreur</h3>
                            <p class="text-sm text-red-600 mt-1">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                            <ul class="text-sm text-red-600 mt-1 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Main Content -->
                {{ $slot ?? '' }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-500">
                        &copy; {{ date('Y') }} Plateforme Togo Finance. Tous droits réservés.
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        // Toggle Mobile Menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('opacity-0');
            menu.classList.toggle('invisible');
            menu.classList.toggle('opacity-100');
            menu.classList.toggle('visible');
        }

        // Toggle Notifications
        function toggleNotifications() {
            const dropdown = document.getElementById('notifications-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('opacity-0');
                dropdown.classList.toggle('invisible');
                dropdown.classList.toggle('opacity-100');
                dropdown.classList.toggle('visible');
            }
        }

        // Fermer les dropdowns en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            // Mobile menu
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileButton = document.querySelector('[onclick="toggleMobileMenu()"]');
            
            if (mobileMenu && !mobileMenu.contains(event.target) && mobileButton && !mobileButton.contains(event.target)) {
                mobileMenu.classList.add('opacity-0', 'invisible');
                mobileMenu.classList.remove('opacity-100', 'visible');
            }

            // Notifications dropdown
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const notificationsButton = document.querySelector('[onclick="toggleNotifications()"]');
            
            if (notificationsDropdown && !notificationsDropdown.contains(event.target) && notificationsButton && !notificationsButton.contains(event.target)) {
                notificationsDropdown.classList.add('opacity-0', 'invisible');
                notificationsDropdown.classList.remove('opacity-100', 'visible');
            }
        });

        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const flashMessages = document.querySelectorAll('.bg-green-50, .bg-red-50');
                flashMessages.forEach(function(message) {
                    message.style.transition = 'opacity 0.5s ease';
                    message.style.opacity = '0';
                    setTimeout(function() {
                        if (message.parentNode) {
                            message.remove();
                        }
                    }, 500);
                });
            }, 5000);
        });
    </script>
</body>
</html>