<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DSKT Rental')</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <meta name="theme-color" content="#ffffff">

    <meta name="mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DSKT Rental">

    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>


<body x-data="{ sidebarOpen: false }" class="bg-gray-100 text-gray-900 h-screen flex flex-col overflow-hidden">

    <!-- HEADER -->
    <header class="bg-white shadow-sm px-4 sm:px-6 py-2 flex justify-between items-center sticky top-0 z-40">
        <!-- Logo + bouton menu mobile -->
        <div class="flex items-center space-x-2 sm:space-x-3">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="md:hidden text-gray-600 hover:text-gray-900 mr-1 text-xl focus:outline-none"
                    aria-label="Ouvrir le menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <i><img src="{{ asset('favicon.ico') }}" alt="" class="w-8 h-8 sm:w-9 sm:h-9 mr-1 sm:mr-3"></i>
            <span class="text-base sm:text-lg font-semibold text-gray-800">DSKT Rental</span>
        </div>

        <!-- Zone droite -->
        <div class="flex items-center space-x-3 sm:space-x-5">
            <!-- Avatar profil -->
            <a href="{{ route('users.show', Auth::user()->id) }}" class="text-blue-500 hover:text-blue-700 transition text-xl sm:text-2xl">
                <i class="fa-solid fa-circle-user"></i>
            </a>

            <!-- Notifications -->
            <livewire:notifications />
            <!-- Déconnexion -->
            <div class="flex items-center space-x-5">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button 
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 
                            transition text-white shadow-md">
                        <i class="fa-solid fa-right-from-bracket text-lg"></i>
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- Notifications dynamiques -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-16 sm:top-5 left-4 right-4 sm:left-auto sm:right-5 bg-green-600 text-white px-4 py-2 rounded shadow-md z-50">
            <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-16 sm:top-5 left-4 right-4 sm:left-auto sm:right-5 bg-red-600 text-white px-4 py-2 rounded shadow-md z-50">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Contenu principal -->
    @yield('content')
    @livewireScripts
    @livewireScriptConfig
</body>
</html>
