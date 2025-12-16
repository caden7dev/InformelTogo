<x-guest-layout>
    <!-- Conteneur principal full screen -->
    <div class="h-screen flex flex-col justify-center items-center bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 px-4 sm:px-6 lg:px-8">

        <!-- Logo + Titre -->
        <div class="mb-6 text-center">
            <a href="/">
                <h1 class="text-3xl font-extrabold text-white drop-shadow-lg">Togo Finance</h1>
                <p class="text-sm text-indigo-100 mt-1 drop-shadow-md">Création de compte</p>
            </a>
        </div>

        <!-- Carte d'inscription -->
        <div class="w-full sm:max-w-md px-8 py-8 bg-white rounded-2xl shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Créer votre compte</h2>

            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4 text-red-600" :errors="$errors" />

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nom -->
                <div>
                    <x-input-label for="name" value="Nom complet" class="text-gray-700 font-semibold" />
                    <x-text-input id="name" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm"
                        type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-6">
                    <x-input-label for="email" value="Adresse Email" class="text-gray-700 font-semibold" />
                    <x-text-input id="email" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm"
                        type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Région -->
                <div class="mt-6">
                    <x-input-label for="region" value="Région" class="text-gray-700 font-semibold" />
                    <select id="region" name="region" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm" required>
                        <option value="">Sélectionnez votre région</option>
                        <option value="Lomé" {{ old('region') == 'Lomé' ? 'selected' : '' }}>Lomé</option>
                        <option value="Kara" {{ old('region') == 'Kara' ? 'selected' : '' }}>Kara</option>
                        <option value="Savanes" {{ old('region') == 'Savanes' ? 'selected' : '' }}>Savanes</option>
                        <option value="Centrale" {{ old('region') == 'Centrale' ? 'selected' : '' }}>Centrale</option>
                        <option value="Plateaux" {{ old('region') == 'Plateaux' ? 'selected' : '' }}>Plateaux</option>
                        <option value="Maritime" {{ old('region') == 'Maritime' ? 'selected' : '' }}>Maritime</option>
                    </select>
                    <x-input-error :messages="$errors->get('region')" class="mt-2" />
                </div>

                <!-- Secteur d'activité -->
                <div class="mt-6">
                    <x-input-label for="secteur" value="Secteur d'activité" class="text-gray-700 font-semibold" />
                    <select id="secteur" name="secteur" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm" required>
                        <option value="">Sélectionnez votre secteur</option>
                        <option value="Commerce" {{ old('secteur') == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                        <option value="Restauration" {{ old('secteur') == 'Restauration' ? 'selected' : '' }}>Restauration</option>
                        <option value="Services" {{ old('secteur') == 'Services' ? 'selected' : '' }}>Services</option>
                        <option value="Agriculture" {{ old('secteur') == 'Agriculture' ? 'selected' : '' }}>Agriculture</option>
                        <option value="Artisanat" {{ old('secteur') == 'Artisanat' ? 'selected' : '' }}>Artisanat</option>
                        <option value="Transport" {{ old('secteur') == 'Transport' ? 'selected' : '' }}>Transport</option>
                        <option value="Autre" {{ old('secteur') == 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                    <x-input-error :messages="$errors->get('secteur')" class="mt-2" />
                </div>

                <!-- Mot de passe -->
                <div class="mt-6">
                    <x-input-label for="password" value="Mot de passe" class="text-gray-700 font-semibold" />
                    <x-text-input id="password" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm"
                        type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirmer mot de passe -->
                <div class="mt-6">
                    <x-input-label for="password_confirmation" value="Confirmer le mot de passe" class="text-gray-700 font-semibold" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm"
                        type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Champ caché pour le rôle -->
                <input type="hidden" name="role" value="commercant">

                <!-- Bouton S'inscrire -->
                <div class="flex items-center justify-end mt-8">
                    <x-primary-button class="w-full justify-center bg-indigo-600 hover:bg-indigo-700 py-3 text-lg font-bold transition duration-150">
                        S'inscrire
                    </x-primary-button>
                </div>

                <!-- Lien connexion -->
                <div class="mt-4 text-center">
                    <p class="text-sm text-indigo-300">
                        Déjà inscrit ?
                        <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-800">
                            Se connecter
                        </a>
                    </p>
                </div>

            </form>
        </div>

        <!-- Info sécurité -->
        <p class="mt-6 text-xs text-indigo-100 max-w-sm text-center drop-shadow-md">
            * Vos données sont protégées et chiffrées.
        </p>

    </div>
</x-guest-layout>