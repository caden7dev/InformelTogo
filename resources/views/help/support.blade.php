<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aide & Support - Togo Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .gradient-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
                        <i class="fas fa-life-ring text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Togo Finance</h1>
                        <p class="text-xs text-gray-500">Centre d'aide & support</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Tableau de bord</a>
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Transactions</a>
                    <a href="{{ route('help.support') }}" class="text-indigo-600 font-medium border-b-2 border-indigo-600 pb-1">Aide & Support</a>
                    <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Mon Compte</a>
                </nav>

                <!-- Profil Utilisateur -->
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <button class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
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
                            </div>
                            <div class="p-2">
                                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-chart-line text-gray-400 w-5"></i>
                                    <span>Tableau de bord</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fas fa-user text-gray-400 w-5"></i>
                                    <span>Mon Profil</span>
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
        <!-- En-tête -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900">Centre d'Aide & Support</h1>
            <p class="text-gray-600 mt-2">Trouvez des réponses à vos questions ou contactez notre équipe</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- FAQ -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 hover-lift">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Questions Fréquentes (FAQ)</h2>
                    
                    <div class="space-y-4">
                        <div class="border-b border-gray-200 pb-4">
                            <button onclick="toggleFAQ(1)" class="flex justify-between items-center w-full text-left group">
                                <span class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    Comment créer une nouvelle transaction ?
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            </button>
                            <div id="faq-1" class="mt-2 text-gray-600 hidden">
                                <p>Cliquez sur le bouton "Nouvelle Transaction" en haut à droite du tableau de bord. Remplissez le formulaire avec les informations requises (montant, type, catégorie, description) et cliquez sur "Enregistrer".</p>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <button onclick="toggleFAQ(2)" class="flex justify-between items-center w-full text-left group">
                                <span class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    Comment exporter mes données ?
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            </button>
                            <div id="faq-2" class="mt-2 text-gray-600 hidden">
                                <p>Allez dans la section "Actions Rapides" du tableau de bord et cliquez sur "Exporter". Vous pouvez exporter vos transactions au format CSV ou générer un rapport PDF.</p>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <button onclick="toggleFAQ(3)" class="flex justify-between items-center w-full text-left group">
                                <span class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    Comment contacter l'administrateur ?
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            </button>
                            <div id="faq-3" class="mt-2 text-gray-600 hidden">
                                <p>Utilisez le formulaire de contact dans la section "Contact" sur cette page. Vous pouvez également envoyer un email à support@togofinance.tg</p>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <button onclick="toggleFAQ(4)" class="flex justify-between items-center w-full text-left group">
                                <span class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    Comment modifier mon profil ?
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            </button>
                            <div id="faq-4" class="mt-2 text-gray-600 hidden">
                                <p>Cliquez sur votre photo de profil en haut à droite, puis sélectionnez "Mon Profil". Vous pourrez modifier vos informations personnelles et changer votre mot de passe.</p>
                            </div>
                        </div>

                        <div class="pb-4">
                            <button onclick="toggleFAQ(5)" class="flex justify-between items-center w-full text-left group">
                                <span class="font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    Mes données sont-elles sécurisées ?
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                            </button>
                            <div id="faq-5" class="mt-2 text-gray-600 hidden">
                                <p>Oui, toutes vos données sont cryptées et sécurisées. Nous utilisons des protocoles de sécurité avancés pour protéger vos informations financières.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guides & Tutoriels -->
                <div class="bg-white rounded-2xl shadow-lg p-6 hover-lift">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Guides & Tutoriels</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="#" class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 transition-colors hover-lift">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-video text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Guide vidéo de prise en main</p>
                                    <p class="text-sm text-gray-500">Apprenez à utiliser toutes les fonctionnalités</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 transition-colors hover-lift">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-green-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Manuel d'utilisation complet</p>
                                    <p class="text-sm text-gray-500">Documentation détaillée au format PDF</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 transition-colors hover-lift">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Analyser vos finances</p>
                                    <p class="text-sm text-gray-500">Guide d'analyse des rapports financiers</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 transition-colors hover-lift">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Sécurité & Confidentialité</p>
                                    <p class="text-sm text-gray-500">Comment protéger vos données</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-lg p-6 hover-lift">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Contactez notre équipe</h2>
                
                <form id="contactForm" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Votre nom</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sujet</label>
                        <select name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="support">Support technique</option>
                            <option value="feature">Demande de fonctionnalité</option>
                            <option value="bug">Signaler un bug</option>
                            <option value="billing">Facturation</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea name="message" rows="5" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Décrivez votre problème ou votre demande en détail..."
                                  required></textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="urgent" class="mr-2">
                            <span class="text-sm text-gray-700">C'est urgent</span>
                        </label>
                    </div>

                    <button type="button" onclick="submitContactForm()" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 px-4 rounded-lg hover:shadow-lg transition-all duration-300 flex items-center justify-center space-x-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Envoyer le message</span>
                    </button>
                </form>

                <!-- Informations de contact -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contacts directs</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Email de support</p>
                                <p class="text-sm text-gray-600">support@togofinance.tg</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-phone text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Téléphone</p>
                                <p class="text-sm text-gray-600">+228 XX XX XX XX</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 p-3 bg-purple-50 rounded-lg">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Horaires de support</p>
                                <p class="text-sm text-gray-600">Lun-Ven: 8h-18h<br>Sam: 9h-13h</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statut du système -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-sm font-medium text-gray-900">Tous les systèmes fonctionnent</span>
                        </div>
                        <span class="text-xs text-gray-500">Dernière vérification: il y a 5 min</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Supplémentaire -->
        <div class="mt-8 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl p-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4">Vous ne trouvez pas ce que vous cherchez ?</h2>
                <p class="mb-6 opacity-90">Notre équipe est disponible pour vous aider rapidement</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="mailto:support@togofinance.tg" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Écrivez-nous
                    </a>
                    <button onclick="startLiveChat()" class="bg-transparent border-2 border-white px-6 py-3 rounded-lg font-medium hover:bg-white/10 transition-colors">
                        <i class="fas fa-comments mr-2"></i>Chat en direct
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de confirmation -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Message envoyé !</h3>
                <p class="text-gray-600 mb-4">Votre demande a été transmise à notre équipe de support.</p>
                <p class="text-sm text-gray-500 mb-6">Nous vous répondrons dans les plus brefs délais.</p>
                <button onclick="closeConfirmationModal()" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center">
                <p class="text-gray-600">© 2024 Togo Finance. Tous droits réservés.</p>
                <div class="mt-2 flex justify-center space-x-6">
                    <a href="#" class="text-gray-500 hover:text-indigo-600">Conditions d'utilisation</a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600">Politique de confidentialité</a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600">Mentions légales</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // FAQ Toggle
        function toggleFAQ(id) {
            const faq = document.getElementById(`faq-${id}`);
            const icon = faq.previousElementSibling.querySelector('.fa-chevron-down');
            
            faq.classList.toggle('hidden');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        }

        // Soumettre le formulaire de contact
        function submitContactForm() {
            const form = document.getElementById('contactForm');
            const formData = new FormData(form);
            
            // Validation
            const message = formData.get('message');
            if (!message || message.trim() === '') {
                alert('Veuillez saisir un message');
                return;
            }
            
            // Simulation d'envoi (à remplacer par un appel API réel)
            showConfirmationModal();
            
            // Réinitialiser le formulaire
            form.reset();
            form.querySelector('textarea[name="message"]').value = '';
        }

        // Afficher le modal de confirmation
        function showConfirmationModal() {
            document.getElementById('confirmationModal').classList.remove('hidden');
        }

        // Fermer le modal de confirmation
        function closeConfirmationModal() {
            document.getElementById('confirmationModal').classList.add('hidden');
        }

        // Chat en direct (simulation)
        function startLiveChat() {
            alert('Le chat en direct sera bientôt disponible ! En attendant, utilisez le formulaire de contact ou envoyez-nous un email.');
        }

        // Initialiser les FAQs (ouvrir la première)
        document.addEventListener('DOMContentLoaded', function() {
            toggleFAQ(1); // Ouvrir la première FAQ par défaut
        });
    </script>
</body>
</html>