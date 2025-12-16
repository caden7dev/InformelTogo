<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Togo Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Togo Finance</h1>
                        <p class="text-xs text-gray-500">Paramètres</p>
                    </div>
                </div>
                
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors">Tableau de bord</a>
                    <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-green-600 transition-colors">Profil</a>
                    <a href="{{ route('profile.settings') }}" class="text-green-600 font-medium border-b-2 border-green-600 pb-1">Paramètres</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Paramètres de l'Application</h1>
            <p class="text-gray-600 mt-2">Gérez les préférences et configurations de votre compte</p>
        </div>

        <!-- Messages de statut -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <div>
                    <p class="text-red-800 font-medium">Erreurs de validation :</p>
                    <ul class="text-red-700 text-sm mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Navigation latérale -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-6">
                    <nav class="space-y-2">
                        <a href="#preferences" class="flex items-center space-x-3 px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-sliders-h w-5 text-gray-400"></i>
                            <span>Préférences</span>
                        </a>
                        <a href="#notifications" class="flex items-center space-x-3 px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-bell w-5 text-gray-400"></i>
                            <span>Notifications</span>
                        </a>
                        <a href="#securite" class="flex items-center space-x-3 px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-shield-alt w-5 text-gray-400"></i>
                            <span>Sécurité</span>
                        </a>
                        <a href="#compte" class="flex items-center space-x-3 px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-user-cog w-5 text-gray-400"></i>
                            <span>Compte</span>
                        </a>
                        <a href="#export" class="flex items-center space-x-3 px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-file-export w-5 text-gray-400"></i>
                            <span>Export de données</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Section Préférences -->
                <div id="preferences" class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-sliders-h mr-3 text-green-600"></i>
                        Préférences Générales
                    </h2>

                    <form class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Devise -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-money-bill-wave mr-2 text-gray-400"></i>
                                    Devise par défaut
                                </label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="XOF" selected>Franc CFA (FCFA)</option>
                                    <option value="EUR">Euro (€)</option>
                                    <option value="USD">Dollar US ($)</option>
                                </select>
                            </div>

                            <!-- Format de date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                    Format de date
                                </label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="fr" selected>JJ/MM/AAAA</option>
                                    <option value="en">MM/JJ/AAAA</option>
                                </select>
                            </div>

                            <!-- Fuseau horaire -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-globe mr-2 text-gray-400"></i>
                                    Fuseau horaire
                                </label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="Africa/Lome" selected>Lomé (UTC+0)</option>
                                    <option value="Europe/Paris">Paris (UTC+1)</option>
                                </select>
                            </div>

                            <!-- Langue -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-language mr-2 text-gray-400"></i>
                                    Langue
                                </label>
                                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="fr" selected>Français</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                        </div>

                        <!-- Paramètres d'affichage -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Affichage</h3>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                    <span class="ml-3 text-sm text-gray-700">Afficher les graphiques animés</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                    <span class="ml-3 text-sm text-gray-700">Mode sombre automatique</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-3 text-sm text-gray-700">Résumé quotidien par email</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer les préférences
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Section Notifications -->
                <div id="notifications" class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-bell mr-3 text-blue-600"></i>
                        Paramètres de Notifications
                    </h2>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Notifications Email -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Notifications Email</h3>
                                <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Alertes de solde</span>
                                        <p class="text-xs text-gray-500">Recevoir des alertes quand le solde est bas</p>
                                    </div>
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                </label>
                                <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Rapports hebdomadaires</span>
                                        <p class="text-xs text-gray-500">Résumé de vos activités de la semaine</p>
                                    </div>
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                </label>
                            </div>

                            <!-- Notifications Application -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Notifications Application</h3>
                                <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Nouvelles fonctionnalités</span>
                                        <p class="text-xs text-gray-500">Alertes sur les nouvelles mises à jour</p>
                                    </div>
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                </label>
                                <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Conseils financiers</span>
                                        <p class="text-xs text-gray-500">Suggestions pour optimiser vos finances</p>
                                    </div>
                                    <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" checked>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-200">
                            <button type="button" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Mettre à jour les notifications
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Sécurité -->
                <div id="securite" class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-shield-alt mr-3 text-red-600"></i>
                        Sécurité et Connexion
                    </h2>

                    <div class="space-y-6">
                        <!-- Session active -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Session active</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Cette session</p>
                                    <p class="text-xs text-gray-500">Connecté depuis {{ now()->format('d/m/Y à H:i') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Actif
                                </span>
                            </div>
                        </div>

                        <!-- Authentification à deux facteurs -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Authentification à deux facteurs</h3>
                                    <p class="text-sm text-gray-500">Améliorez la sécurité de votre compte</p>
                                </div>
                                <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                    Activer
                                </button>
                            </div>
                        </div>

                        <!-- Journal d'activité -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Journal d'activité récente</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span>Connexion réussie</span>
                                    <span class="text-gray-500">{{ now()->subMinutes(15)->format('H:i') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span>Modification du profil</span>
                                    <span class="text-gray-500">{{ now()->subDays(1)->format('d/m H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Export de données -->
                <div id="export" class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-file-export mr-3 text-purple-600"></i>
                        Export de Données
                    </h2>

                    <div class="space-y-6">
                        <p class="text-gray-600">Téléchargez une copie de vos données pour sauvegarde ou analyse.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors text-center">
                                <i class="fas fa-file-csv text-2xl text-green-600 mb-2"></i>
                                <p class="font-medium text-gray-900">Export CSV</p>
                                <p class="text-xs text-gray-500">Format tableur</p>
                            </button>
                            
                            <button class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors text-center">
                                <i class="fas fa-file-pdf text-2xl text-red-600 mb-2"></i>
                                <p class="font-medium text-gray-900">Export PDF</p>
                                <p class="text-xs text-gray-500">Rapport imprimable</p>
                            </button>
                            
                            <button class="p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors text-center">
                                <i class="fas fa-file-excel text-2xl text-green-600 mb-2"></i>
                                <p class="font-medium text-gray-900">Export Excel</p>
                                <p class="text-xs text-gray-500">Format avancé</p>
                            </button>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                <div>
                                    <p class="text-yellow-800 font-medium">Attention</p>
                                    <p class="text-yellow-700 text-sm">L'export peut prendre plusieurs minutes selon la quantité de données.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Navigation fluide entre les sections
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="#"]');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Animation des sections
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Appliquer l'animation aux sections
            const sections = document.querySelectorAll('.bg-white.rounded-2xl');
            sections.forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'all 0.6s ease-out';
                observer.observe(section);
            });
        });
    </script>
</body>
</html>