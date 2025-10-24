<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DSKT Rental')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
@livewireStyles
@livewireScripts

<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

    <!-- HEADER -->
    <header class="bg-white shadow-sm px-6 py-2 flex justify-between items-center sticky top-0 z-40">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-building text-blue-500 text-xl"></i>
            <span class="text-lg font-semibold text-gray-800">DSKT Rental</span>
        </div>

        <!-- Zone droite -->
        <div class="flex items-center space-x-5">
            <!-- Avatar profil -->
            <a href="#" class="text-blue-500 hover:text-blue-700 transition text-2xl">
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
             class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-md z-50">
            <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-5 right-5 bg-red-600 text-white px-4 py-2 rounded shadow-md z-50">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Contenu principal -->
    @yield('content')

</body>
</html>
