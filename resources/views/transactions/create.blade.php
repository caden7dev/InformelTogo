<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Transaction - Plateforme Togo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header Commerçant -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo et Titre -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-store text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Togo Finance</h1>
                        <p class="text-xs text-gray-500">Espace Commerçant</p>
                    </div>
                </div>

                <!-- Navigation Commerçant -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-green-600 transition-colors">Tableau de bord</a>
                    <a href="{{ route('transactions.index') }}" class="text-green-600 font-medium border-b-2 border-green-600 pb-1">Transactions</a>
                    <a href="{{ route('reports.financial') }}" class="text-gray-600 hover:text-green-600 transition-colors">Rapports</a>
                </nav>

                <!-- Profil Commerçant -->
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <button class="flex items-center space-x-3 focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="text-left hidden md:block">
                                <span class="text-gray-700 font-medium block">{{ Auth::user()->name }}</span>
                                <span class="text-gray-500 text-xs block">{{ Auth::user()->email }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </button>
                        
                        <!-- Menu déroulant -->
                        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <!-- Profil -->
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-user-circle w-5 text-gray-400 mr-2"></i>
                                    <span>Mon Profil</span>
                                </a>
                                
                                <!-- Séparateur -->
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                <!-- Déconnexion -->
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
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <!-- En-tête -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle Transaction</h1>
                <p class="text-gray-600 mt-2">Ajoutez une nouvelle transaction financière</p>
            </div>

            <!-- Formulaire -->
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type de Transaction -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de Transaction *
                        </label>
                        <select name="type" id="type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">Sélectionnez un type</option>
                            <option value="income">💰 Recette (Entrée d'argent)</option>
                            <option value="expense">💸 Dépense (Sortie d'argent)</option>
                        </select>
                        @error('type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Montant -->
                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">
                            Montant (FCFA) *
                        </label>
                        <div class="relative">
                            <input type="number" name="montant" id="montant" step="0.01" min="0.01" required
                                   placeholder="0.00"
                                   class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">FCFA</span>
                            </div>
                        </div>
                        @error('montant')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie - Utilise les catégories réelles de la base -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Catégorie *
                        </label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">Sélectionnez une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    @if($category->type == 'income') 💰 @else 💸 @endif
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="date_transaction" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de la Transaction *
                        </label>
                        <input type="date" name="date_transaction" id="date_transaction" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        @error('date_transaction')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description détaillée *
                        </label>
                        <textarea name="description" id="description" required rows="3"
                                  placeholder="Décrivez cette transaction en détail. Ex: Vente de 10 unités de produit X à M. Diallo, Achat de matériel de bureau pour le stock..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"></textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer la Transaction</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Aide -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <div>
                    <h3 class="font-medium text-blue-900">Guide du commerçant</h3>
                    <ul class="text-blue-700 text-sm mt-2 space-y-1">
                        <li>• <strong>Recette</strong> : Toutes vos entrées d'argent (ventes, services, etc.)</li>
                        <li>• <strong>Dépense</strong> : Toutes vos sorties d'argent (achats, charges, salaires)</li>
                        <li>• Choisissez une catégorie précise pour un meilleur suivi</li>
                        <li>• Une description détaillée facilite le suivi et les rapports</li>
                        <li>• Tous les montants sont exprimés en <strong>Francs CFA (FCFA)</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Filtrage des catégories selon le type
        document.getElementById('type').addEventListener('change', function() {
            const selectedType = this.value;
            const categorySelect = document.getElementById('category_id');
            const options = categorySelect.options;
            
            // Afficher/masquer les options selon le type
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const isIncome = option.textContent.includes('💰');
                const isExpense = option.textContent.includes('💸');
                
                if (option.value === '') {
                    option.style.display = ''; // Garder l'option vide
                } else if (selectedType === '') {
                    option.style.display = ''; // Afficher tout si aucun type
                } else if (selectedType === 'income' && isIncome) {
                    option.style.display = '';
                } else if (selectedType === 'expense' && isExpense) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                    // Désélectionner si l'option était sélectionnée
                    if (option.selected) {
                        categorySelect.value = '';
                    }
                }
            }
        });

        // Déclencher le changement au chargement
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('type').dispatchEvent(new Event('change'));
            
            // Formatage automatique du montant
            const montantInput = document.getElementById('montant');
            montantInput.addEventListener('blur', function() {
                if (this.value) {
                    // Formater avec 2 décimales
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
            
            // Validation du montant minimum
            montantInput.addEventListener('input', function() {
                if (this.value && parseFloat(this.value) < 0.01) {
                    this.setCustomValidity('Le montant doit être d\'au moins 0.01 FCFA');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>