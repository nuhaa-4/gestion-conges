<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Gestion Congés') }} - Portail Intranet</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts / Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-between">
        
        <!-- Header / Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Logo / Icon -->
                    <div class="p-2 bg-indigo-600 rounded-lg text-white shadow-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-gray-900">
                        {{ config('app.name', 'Gestion Congés') }}
                    </span>
                </div>

                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Mon Tableau de Bord
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition duration-150">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                    Inscription
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Content -->
                <div class="space-y-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Portail Intranet Entreprise
                    </span>
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-none">
                        Gérez vos congés en <span class="text-indigo-600">toute simplicité</span>.
                    </h1>
                    <p class="text-lg text-gray-600">
                        Soumettez vos demandes de congés (payés, maladie, récupération), déposez vos justificatifs médicaux en ligne, et suivez l'avancement de vos demandes en temps réel.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                Accéder à mon espace
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                Se connecter
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                Créer un compte
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Feature Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <!-- Card 1: Employés -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4 hover:shadow-md transition duration-200">
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl w-12 h-12 flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Espace Salariés</h3>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center">
                                <span class="mr-2 text-indigo-500">✓</span> Formulaire de demande
                            </li>
                            <li class="flex items-center">
                                <span class="mr-2 text-indigo-500">✓</span> Téléversement de justificatifs
                            </li>
                            <li class="flex items-center">
                                <span class="mr-2 text-indigo-500">✓</span> Historique de suivi des dates
                            </li>
                        </ul>
                    </div>

                    <!-- Card 2: Managers -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4 hover:shadow-md transition duration-200">
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl w-12 h-12 flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Espace Manager</h3>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center">
                                <span class="mr-2 text-green-500">✓</span> Validation / Refus en direct
                            </li>
                            <li class="flex items-center">
                                <span class="mr-2 text-green-500">✓</span> Commentaires de validation
                            </li>
                            <li class="flex items-center">
                                <span class="mr-2 text-green-500">✓</span> Statistiques d'absentéisme
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-6 text-center text-sm text-gray-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Gestion Congés') }}. Tous droits réservés. Espace de travail interne.</p>
            </div>
        </footer>

    </body>
</html>
