@extends('layouts.app')

@section('content')
<div x-data="{ open: true }" class="flex flex-1 overflow-hidden min-h-0">

    <!-- Overlay mobile (ferme le tiroir au clic en dehors) -->
    <div x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 md:hidden"
         style="display: none;"></div>

    <!-- SIDEBAR -->
    <aside :class="{
                '-translate-x-full': !sidebarOpen,
                'translate-x-0': sidebarOpen,
                'md:w-52': open,
                'md:w-20': !open,
           }"
           class="fixed inset-y-0 left-0 z-50 w-64 md:static md:translate-x-0
                  bg-gray-900 text-white flex flex-col transition-all duration-300 shadow-lg">

        <!-- Bouton menu (desktop: collapse : mobile: fermer le tiroir) -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
            <button @click="open = !open" class="hidden md:block text-gray-300 hover:text-white focus:outline-none">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <span class="md:hidden text-sm font-semibold text-gray-300">Menu</span>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Menu principal -->
        <nav class="flex-1 mt-4 overflow-y-auto" @click="sidebarOpen = false">
            <ul class="space-y-1">
                @php
                    $routes = auth()->user()->role === 'locataire'
                        ? [['Mon logement', 'tenant.logement', 'fa-house']]
                        : [['Accueil', 'dashboard', 'fa-house']];
                @endphp

                @foreach ($routes as [$name, $route, $icon])
                    <li>
                        <a href="{{ route($route) }}"
                           class="flex items-center px-4 py-2 rounded-lg mx-2 
                                  hover:bg-blue-600 hover:text-white transition 
                                  {{ request()->routeIs($route) ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                            <i class="fa-solid {{ $icon }} mr-3 text-lg"></i>
                            <span class="text-sm font-medium md:hidden" x-show="true">{{ $name }}</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">{{ $name }}</span>
                        </a>
                    </li>
                @endforeach
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-chart-line mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Dashboard</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Dashboard</span>
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
                        <span class="text-sm font-medium md:hidden">Messages</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Messages</span>
                    </a>
                </li>
    
                <!-- Admin -->
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('users.index') }}" 
                        class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('users.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-users mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Utilisateurs</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Utilisateurs</span>
                    </a>
                </li>
                @endif

                <!-- Manager -->
                @if(auth()->user()->role === 'gestionnaire')
                <li>
                    <a href="{{ route('manager.index') }}" 
                        class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('manager.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-users mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Locataires</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Locataires</span>
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
                        <span class="text-sm font-medium md:hidden">Immeubles</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Immeubles</span>
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
                        <span class="text-sm font-medium md:hidden">Appartements</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Appartements</span>
                    </a>
                </li>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                    <a href="{{ route('contrats.index') }}" 
                                class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('contrats.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-file-contract mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Contrats</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Contrats</span>
                    </a>
                </li>

                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg mx-2 
                        hover:bg-blue-600 hover:text-white transition text-gray-300">
                        <i class="fa-solid fa-gears mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Services</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Services</span>
                    </a>
                </li>
                @endif

                <!-- Locataire -->
                @if(auth()->user()->role === 'locataire')
                
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">

                <li>
                    <a href="{{ route('contrats.index') }}" 
                        class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('contrats.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-file-contract mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Mes contrats</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Mes contrats</span>
                    </a>
                </li>
                @endif
                <hr class="my-3 border-gray-700 opacity-30">
                <li>
                  <a href="{{ route('payments.index') }}" 
                             class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('payments.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-money-bill-wave mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Paiements</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Paiements</span>
                </a>
                <!-- Séparateur -->
                <hr class="my-3 border-gray-700 opacity-30">
                <a href="{{ route('receipts.index') }}" 
                             class="flex items-center px-4 py-2 rounded-lg mx-2 
                                hover:bg-blue-600 hover:text-white transition 
                                {{ request()->routeIs('receipts.*') ? 'bg-blue-700 text-white' : 'text-gray-300' }}">
                        <i class="fa-solid fa-receipt mr-3 text-lg"></i>
                        <span class="text-sm font-medium md:hidden">Reçus</span>
                            <span class="text-sm font-medium hidden md:inline" x-show="open">Reçus</span>
                </a>
                </li>
                
            </ul>
        </nav>

    </aside>

    <!-- CONTENU PRINCIPAL -->
    <main class="flex-1 bg-gray-50 overflow-y-auto p-4 sm:p-6 w-full">
        @yield('dashboard-content')
    </main>
</div>
@endsection
