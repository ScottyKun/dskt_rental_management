@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-6">

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

            {{-- Actions locataire --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('contrats.index') }}" class="...">Voir mes contrats</a>
                <a href="#" class="...">Payer mon loyer</a>
                <a href="#" class="...">Demander un préavis de départ</a>
                <a href="#" class="...">Signaler une panne</a>
                <a href="#" class="...">Voir mes factures</a>
            </div>
        </div>

    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg">
            Vous n'avez pas encore de logement assigné.
        </div>
    @endif

</div>
@endsection
