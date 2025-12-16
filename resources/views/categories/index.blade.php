@extends('layouts.app')

@section('title', 'Catégories - Togo Finance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Catégories</h1>
            <p class="text-gray-600 mt-2">Gérez vos catégories de revenus et dépenses</p>
        </div>
        <a href="{{ route('categories.create') }}" 
           class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center space-x-2 group">
            <i class="fas fa-plus-circle group-hover:rotate-90 transition-transform duration-300"></i>
            <span>Nouvelle Catégorie</span>
        </a>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('categories.index') }}" 
               class="px-4 py-2 rounded-lg {{ request()->routeIs('categories.index') ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Toutes
            </a>
            <a href="{{ route('categories.byType', 'income') }}" 
               class="px-4 py-2 rounded-lg {{ request()->is('categories/type/income') ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-arrow-down mr-1"></i> Revenus
            </a>
            <a href="{{ route('categories.byType', 'expense') }}" 
               class="px-4 py-2 rounded-lg {{ request()->is('categories/type/expense') ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-arrow-up mr-1"></i> Dépenses
            </a>
        </div>
    </div>

    <!-- Liste des catégories -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @if($categories->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" 
                                     style="background-color: {{ $category->color ?? '#6366f1' }}">
                                    <i class="fas fa-{{ $category->icon ?? 'tag' }} text-white"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $category->description ?? 'Aucune description' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $category->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->type == 'income' ? 'Revenu' : 'Dépense' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full border border-gray-300 mr-2" 
                                     style="background-color: {{ $category->color ?? '#6366f1' }}"></div>
                                <span class="text-sm text-gray-900">{{ $category->color ?? '#6366f1' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->transactions_count ?? 0 }} transaction(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('categories.edit', $category) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3" 
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-12 text-center">
            <i class="fas fa-tags text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 mb-2">Aucune catégorie trouvée</p>
            <a href="{{ route('categories.create') }}" 
               class="text-indigo-600 hover:text-indigo-700 text-sm inline-flex items-center">
                <i class="fas fa-plus mr-1"></i>
                Créer votre première catégorie
            </a>
        </div>
        @endif
    </div>

    <!-- Pagination (si nécessaire) -->
    @if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator && $categories->hasPages())
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection