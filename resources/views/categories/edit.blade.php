@extends('layouts.app')

@section('title', 'Modifier Catégorie - Togo Finance')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <a href="{{ route('categories.index') }}" 
           class="text-indigo-600 hover:text-indigo-700 mb-4 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux catégories
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Modifier Catégorie</h1>
        <p class="text-gray-600 mt-2">Modifiez les informations de la catégorie</p>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $category->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                                   {{ old('type', $category->type) == 'income' ? 'checked' : '' }}
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
                                   {{ old('type', $category->type) == 'expense' ? 'checked' : '' }}
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
                                   value="{{ old('color', $category->color ?? '#6366f1') }}"
                                   class="w-16 h-10 cursor-pointer rounded border border-gray-300">
                            <div class="text-sm text-gray-500">
                                {{ old('color', $category->color ?? '#6366f1') }}
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
                        <input type="text" 
                               id="icon" 
                               name="icon" 
                               value="{{ old('icon', $category->icon ?? 'tag') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Nom de l'icône FontAwesome">
                        <p class="mt-1 text-xs text-gray-500">
                            Ex: tag, shopping-cart, car, home, heart, etc.
                        </p>
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
                              placeholder="Description optionnelle de la catégorie...">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex justify-between pt-6 border-t">
                    <!-- Bouton Supprimer -->
                    <form action="{{ route('categories.destroy', $category) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center">
                            <i class="fas fa-trash mr-2"></i>
                            Supprimer
                        </button>
                    </form>
                    
                    <!-- Boutons Annuler et Mettre à jour -->
                    <div class="flex space-x-4">
                        <a href="{{ route('categories.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg shadow hover:shadow-md transition-all duration-300 flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Mettre à jour</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection