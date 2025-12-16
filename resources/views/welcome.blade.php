<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Togo Financement, Gestion Simplifiée</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .hero-section {
            background: linear-gradient(to right, #6366f1, #8b5cf6, #ec4899);
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            color: white;
        }
        .cta-button {
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        header.fixed-header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
        }
    </style>
</head>
<body class="antialiased font-sans">

<header class="bg-white shadow-md p-4 flex justify-between items-center fixed-header">
    <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-700">Togo Finance</a>
    <nav class="space-x-4">
        <!-- Header fixe avec seulement connexion et inscription -->
        <a href="{{ route('login') }}" class="font-semibold text-gray-700 hover:text-indigo-600 py-2 px-4 transition duration-150">Se connecter</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="font-semibold text-white bg-indigo-600 hover:bg-indigo-700 py-2 px-4 rounded-lg transition duration-150">S'inscrire</a>
        @endif
    </nav>
</header>

<section class="hero-section">
    <div class="max-w-5xl mx-auto px-4">
        <h1 class="text-6xl font-extrabold mb-4 tracking-tight">
            Gérez Vos Finances, Simplifiez Votre Commerce 🇹🇬
        </h1>
        <p class="text-xl mb-10 max-w-3xl mx-auto">
            La plateforme web simple, sécurisée et accessible, conçue pour les commerçants du secteur informel au Togo.
        </p>

        <!-- Bouton Commencer Gratuitement fixe vers l'inscription -->
        <a href="{{ route('register') }}" 
           class="cta-button inline-block bg-indigo-600 text-white text-xl font-bold py-4 px-10 rounded-full shadow-lg hover:bg-indigo-700 transition duration-300">
            Commencer Gratuitement
        </a>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-12">
            Accédez à vos outils de gestion en un clic
        </h2>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="bg-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-t-4 border-indigo-500">
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">Enregistrement Rapide</h3>
                <p class="text-gray-600 mb-6">Enregistrez vos recettes et dépenses en moins de 30 secondes.</p>
                <a href="{{ route('login') }}" 
                   class="inline-block cta-button bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg transition duration-150 shadow-md">
                    Ajouter une Transaction
                </a>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-t-4 border-green-500">
                <h3 class="text-2xl font-semibold text-gray-900 mb-3">Bilans et Rapports</h3>
                <p class="text-gray-600 mb-6">Visualisez votre solde, bénéfice net et historique financier.</p>
                <a href="{{ route('login') }}" 
                   class="inline-block cta-button bg-green-600 text-white font-bold py-3 px-8 rounded-lg transition duration-150 shadow-md">
                    Voir mon Bilan
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-indigo-50">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-4xl font-bold text-gray-800 text-center mb-12">Pourquoi choisir Togo Finance ?</h2>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center p-4">
                <div class="text-4xl text-indigo-600 mb-3">✨</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Simplicité d'Utilisation</h3>
                <p class="text-gray-600">Interface pensée pour le secteur informel, sans complexité comptable.</p>
            </div>

            <div class="text-center p-4">
                <div class="text-4xl text-indigo-600 mb-3">🔒</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Sécurité et Confidentialité</h3>
                <p class="text-gray-600">Vos données sont chiffrées et confidentielles. Vous seul y avez accès.</p>
            </div>

            <div class="text-center p-4">
                <div class="text-4xl text-indigo-600 mb-3">📱</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">100% Mobile Compatible</h3>
                <p class="text-gray-600">Suivez vos finances où que vous soyez, sur ordinateur ou smartphone.</p>
            </div>
        </div>
    </div>
</section>

<footer class="bg-gray-800 text-white py-8">
    <div class="max-w-5xl mx-auto px-4 text-center">
        <p class="mb-4">&copy; {{ date('Y') }} Togo Finance. Tous droits réservés.</p>
        <div class="space-x-4 text-sm">
            <a href="#" class="hover:text-indigo-400">Conditions d'Utilisation</a>
            <a href="#" class="hover:text-indigo-400">Politique de Confidentialité</a>
            <a href="mailto:contact@togofinance.com" class="hover:text-indigo-400">Contact</a>
        </div>
    </div>
</footer>

</body>
</html>
