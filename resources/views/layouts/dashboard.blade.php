@extends('layouts.app')

@section('content')
<div x-data="{ open: true }" class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside :class="open ? 'w-52' : 'w-20'" 
           class="bg-gray-900 text-white flex flex-col transition-all duration-300 shadow-lg">

        <!-- Bouton menu -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
            <button @click="open = !open" class="text-gray-300 hover:text-white focus:outline-none">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
        </div>

        <!-- Menu principal -->
        <nav class="flex-1 mt-4 overflow-y-auto">
            <ul class="space-y-1">
                @php
                    $routes = [
                        ['Accueil', 'dashboard', 'fa-house'],
                    ];
                @endphp

                @foreach ($routes as [$name, $route, $icon])
                    <li>
                        <a href="{{ route($route) }}"
                           class="flex items-center px-4 py-2 rounded-lg mx-2 
                                  hover:bg-blue-600 hover:text-white transition 
                                  {{ request()->routeIs($route) ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                            <i class="fa-solid {{ $icon }} mr-3 text-lg"></i>
                            <span x-show="open" class="text-sm font-medium">{{ $name }}</span>
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-chart-line mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Dashboard</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <li>
                    <a href="{{ route('messages.index') }}" 
                            class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('messages.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-envelope mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Messages</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <!-- Admin -->
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('users.index') }}" 
                        class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('users.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-users mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Utilisateurs</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-money-bill-wave mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Paiements</span>
                    </a>
                </li>
                @endif
                
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <!-- Gestionnaire -->
                @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
                <li>
                    <a href="{{ route('immeubles.index') }}" 
                        class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('immeubles.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-building mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Immeubles</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                    <a href="{{ route('appartements.index') }}" 
                            class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('appartements.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-house-chimney mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Appartements</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-file-contract mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Contrats</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-gears mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Services</span>
                    </a>
                </li>
                @endif
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <!-- Locataire -->
                @if(auth()->user()->role === 'locataire')
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-house mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Mon logement</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-file-contract mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Mes contrats</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-money-bill-wave mr-3 text-lg"></i>
                        <span x-show="open" class="text-sm font-medium">Mes paiements</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>

    </aside>

    <!-- CONTENU PRINCIPAL -->
    <main class="flex-1 bg-gray-50 overflow-y-auto p-6">
        @yield('dashboard-content')
    </main>
</div>
@endsection
