@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-8">

    @if($appartement)
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-700">{{ $appartement->name }}</h2>
                <span class="text-sm font-medium text-gray-500">
                    {{ ucfirst($appartement->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Infos principales --}}
                <div>
                    <p class="text-gray-600"><span class="font-semibold">Description :</span> {{ $appartement->description ?? '—' }}</p>
                    <p class="text-gray-600"><span class="font-semibold">Type :</span> {{ $appartement->type ?? '—' }}</p>
                    <p class="text-gray-600"><span class="font-semibold">Surface :</span> {{ $appartement->area }} m²</p>
                    <p class="text-gray-600"><span class="font-semibold">Loyer :</span> 
                        {{ number_format($appartement->rent, 2, ',', ' ') }} CFA
                    </p>
                </div>

                {{-- Immeuble --}}
                <div>
                    <p class="text-gray-600"><span class="font-semibold">Immeuble :</span> {{ $appartement->immeuble->name ?? '—' }}</p>
                    <p class="text-gray-600"><span class="font-semibold">Localisation :</span> 
                        {{ ($appartement->immeuble->address ?? '') . ' ' . ($appartement->immeuble->town ?? '') }}
                    </p>
                    <p class="text-gray-600"><span class="font-semibold">Gestionnaire :</span> 
                        {{ ($appartement->immeuble->manager->surname ?? '') . ' '. ($appartement->immeuble->manager->name ?? '') }}
                    </p>
                </div>

                {{-- Statut contrat --}}
                <div>
                    <p class="text-gray-600"><span class="font-semibold">Statut contrat :</span> 
                        {{ $appartement->contratActif->status ?? '— Aucun contrat —' }}
                    </p>
                </div>
            </div>
            <hr class="my-6">
            {{-- Actions locataire --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                <!-- Voir mes contrats -->
                <a href="{{ route('contrats.index') }}"
                class="flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg text-center transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-6h13m-2 0V5H6v6H2l10 10 10-10h-5z"/>
                    </svg>
                    Mes contrats
                </a>

                <!-- Payer mon loyer -->
                <a href="{{ route('payments.create') }}"
                class="flex items-center justify-center bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg text-center transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>
                    Payer mon loyer
                </a>

                <!-- Demander un préavis de départ -->
                <a href="{{ route('messages.request.create') }}"
                class="flex items-center justify-center bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg text-center transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Demander un préavis
                </a>

                <!-- Signaler une panne -->
                <a href="{{ route('messages.request.create') }}"
                class="flex items-center justify-center bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg text-center transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-12.728 12.728"/>
                    </svg>
                    Signaler une panne
                </a>

                <!-- Voir mes factures -->
                <a href="{{ route('payments.index') }}"
                class="flex items-center justify-center bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg text-center transition duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h18v18H3z"/>
                    </svg>
                    Mes factures
                </a>

            </div>
        </div>

    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
            Vous n'avez pas encore de logement assigné.
        </div>
    @endif

</div>
@endsection
