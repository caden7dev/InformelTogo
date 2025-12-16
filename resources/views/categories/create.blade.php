@extends('layouts.app')

@section('title', 'Nouvelle Catégorie - Togo Finance')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <a href="{{ route('categories.index') }}" 
           class="text-indigo-600 hover:text-indigo-700 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux catégories
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Nouvelle Catégorie</h1>
        <p class="text-gray-600 mt-2">Créez une nouvelle catégorie pour classer vos transactions</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                           placeholder="Ex: Salaire, Nourriture, Transport...">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="type" 
                                   value="income" 
                                   {{ old('type') == 'income' ? 'checked' : '' }}
                                   required 
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-arrow-down text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Revenu</p>
                                        <p class="text-sm text-gray-500">Argent reçu</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" 
                                   name="type" 
                                   value="expense" 
                                   {{ old('type') == 'expense' ? 'checked' : '' }}
                                   required 
                                   class="sr-only peer">
                            <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-arrow-up text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Dépense</p>
                                        <p class="text-sm text-gray-500">Argent dépensé</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Couleur et Icône -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Couleur -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Couleur
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="color" 
                                   id="color" 
                                   name="color" 
                                   value="{{ old('color', '#6366f1') }}"
                                   class="w-16 h-10 cursor-pointer rounded border border-gray-300">
                            <div class="text-sm text-gray-500">
                                Cliquez pour choisir une couleur
                            </div>
                        </div>
                        @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icône -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Icône
                        </label>
                        <select id="icon" 
                                name="icon" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="tag" {{ old('icon') == 'tag' ? 'selected' : '' }}>🏷️ Tag</option>
                            <option value="shopping-cart" {{ old('icon') == 'shopping-cart' ? 'selected' : '' }}>🛒 Courses</option>
                            <option value="car" {{ old('icon') == 'car' ? 'selected' : '' }}>🚗 Transport</option>
                            <option value="home" {{ old('icon') == 'home' ? 'selected' : '' }}>🏠 Logement</option>
                            <option value="heart" {{ old('icon') == 'heart' ? 'selected' : '' }}>❤️ Santé</option>
                            <option value="graduation-cap" {{ old('icon') == 'graduation-cap' ? 'selected' : '' }}>🎓 Éducation</option>
                            <option value="utensils" {{ old('icon') == 'utensils' ? 'selected' : '' }}>🍴 Restauration</option>
                            <option value="film" {{ old('icon') == 'film' ? 'selected' : '' }}>🎬 Loisirs</option>
                            <option value="wallet" {{ old('icon') == 'wallet' ? 'selected' : '' }}>💰 Portefeuille</option>
                            <option value="briefcase" {{ old('icon') == 'briefcase' ? 'selected' : '' }}>💼 Travail</option>
                        </select>
                        @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Description optionnelle de la catégorie...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('categories.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg shadow hover:shadow-md transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-save"></i>
                        <span>Créer la catégorie</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Prévisualisation dynamique
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('color');
        const iconSelect = document.getElementById('icon');
        
        // Mettre à jour la couleur de l'icône
        colorInput.addEventListener('input', function(e) {
            const preview = document.querySelector('.icon-preview');
            if (preview) {
                preview.style.backgroundColor = e.target.value;
            }
        });
    });
</script>
@endsection