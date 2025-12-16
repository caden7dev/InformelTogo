<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 py-12 px-4 sm:px-6 lg:px-8">

        <!-- Logo & Titre -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold text-white tracking-wide">Togo Finance</h1>
            <p class="mt-2 text-indigo-100 text-sm">Connexion Commerçant</p>
        </div>

        <!-- Carte de connexion -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Accédez à votre compte</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-red-600" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Adresse Email" class="text-gray-700 font-semibold" />
                    <x-text-input id="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Mot de passe -->
                <div class="mt-6">
                    <x-input-label for="password" value="Mot de passe" class="text-gray-700 font-semibold" />
                    <x-text-input id="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember me / Forgot -->
                <div class="flex justify-between items-center mt-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-900 font-medium" href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Bouton Connexion -->
                <div class="mt-8">
                    <x-primary-button class="w-full justify-center py-3 text-lg font-bold bg-indigo-600 hover:bg-indigo-700 transform hover:scale-105 transition duration-200">
                        Se connecter
                    </x-primary-button>
                </div>

                <!-- Lien vers inscription -->
                <p class="mt-6 text-center text-gray-500 text-sm">
                    Pas encore inscrit ? 
                    <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-800">Créer un compte</a>
                </p>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-6 text-xs text-white text-center max-w-sm">
            * Votre sécurité est notre priorité. Toutes les données sont chiffrées.
        </p>
    </div>
</x-guest-layout>
