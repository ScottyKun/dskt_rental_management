@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-4 sm:mt-8 space-y-5">

    {{-- Mini-dashboard personnel --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <x-kpi-card
            label="Loyer"
            :value="$mini['loyer_a_jour'] === null ? '—' : ($mini['loyer_a_jour'] ? 'À jour' : 'En retard')"
            :color="$mini['loyer_a_jour'] === null ? 'gray' : ($mini['loyer_a_jour'] ? 'green' : 'red')" />
        <x-kpi-card
            label="Contrat"
            :value="$mini['jours_restants_contrat'] !== null ? max($mini['jours_restants_contrat'], 0) . ' j restants' : '—'"
            :color="$mini['jours_restants_contrat'] !== null && $mini['jours_restants_contrat'] <= 30 ? 'amber' : 'blue'" />
        <x-kpi-card
            label="Jour de paiement"
            :value="$mini['jour_paiement'] ? 'le ' . $mini['jour_paiement'] . ' du mois' : '—'" />
        <x-kpi-card
            label="Pièce d'identité"
            :value="match($mini['document_status']) {
                'valide' => 'Validée',
                'soumis' => 'En vérification',
                'demande' => 'À transmettre',
                default => '—',
            }"
            :color="$mini['document_status'] === 'valide' ? 'green' : 'amber'" />
    </div>

    @if($appartement)
        {{-- Hero : logement --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-5 sm:px-8 py-6 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <p class="text-blue-100 text-xs uppercase tracking-wide font-medium">Mon logement</p>
                        <h1 class="text-2xl sm:text-3xl font-bold">{{ $appartement->name }}</h1>
                        <p class="text-blue-100 mt-1">
                            <i class="fa-solid fa-location-dot mr-1"></i>
                            {{ $appartement->immeuble->name ?? '' }} — {{ $appartement->immeuble->address ?? '' }} {{ $appartement->immeuble->town ?? '' }}
                        </p>
                    </div>
                    <span class="self-start sm:self-auto bg-white/20 backdrop-blur px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fa-solid fa-circle-check mr-1"></i>{{ ucfirst($appartement->status) }}
                    </span>
                </div>
            </div>

            {{-- Infos en icones --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 p-5 sm:p-8">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                        <i class="fa-solid fa-ruler-combined"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Type & surface</p>
                        <p class="font-medium text-gray-800">{{ ucfirst($appartement->type) }} · {{ number_format($appartement->area, 0) }} m²</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center text-green-600 shrink-0">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Loyer mensuel</p>
                        <p class="font-medium text-gray-800">{{ number_format($appartement->rent, 0, ',', ' ') }} CFA</p>
                    </div>
                </div>

                @if($appartement->description)
                <div class="flex items-start gap-3 sm:col-span-2">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 shrink-0">
                        <i class="fa-solid fa-align-left"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Description</p>
                        <p class="font-medium text-gray-800">{{ $appartement->description }}</p>
                    </div>
                </div>
                @endif

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Gestionnaire</p>
                        <p class="font-medium text-gray-800">{{ $appartement->immeuble->manager->name ?? '—' }} {{ $appartement->immeuble->manager->surname ?? '' }}</p>
                    </div>
                </div>

                @if($mini['contrat'])
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Contrat</p>
                        <p class="font-medium text-gray-800">
                            {{ ucfirst($mini['contrat']->status) }}
                            @if($mini['signature_status'])
                                · signature {{ str_replace('_', ' ', $mini['signature_status']) }}
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-2xl shadow p-5 sm:p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Actions rapides</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                {{-- Payer mon loyer --}}
                <form action="{{ route('payments.sendRequest') }}" method="POST" class="contents">
                    @csrf
                    <button type="submit" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition text-center">
                        <span class="w-11 h-11 rounded-full bg-green-100 text-green-600 flex items-center justify-center group-hover:scale-105 transition">
                            <i class="fa-solid fa-hand-holding-dollar text-lg"></i>
                        </span>
                        <span class="text-sm font-medium text-gray-700">Payer mon loyer</span>
                    </button>
                </form>

                <a href="{{ route('contrats.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition text-center">
                    <span class="w-11 h-11 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-105 transition">
                        <i class="fa-solid fa-file-contract text-lg"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Mes contrats</span>
                </a>

                <a href="{{ route('payments.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition text-center">
                    <span class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Mes paiements</span>
                </a>

                <a href="{{ route('receipts.index') }}" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition text-center">
                    <span class="w-11 h-11 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition">
                        <i class="fa-solid fa-file-invoice text-lg"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Mes factures</span>
                </a>

                <a href="{{ route('messages.request.create') }}" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-amber-200 hover:bg-amber-50 transition text-center">
                    <span class="w-11 h-11 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-105 transition">
                        <i class="fa-solid fa-calendar-days text-lg"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Demander un préavis</span>
                </a>

                <a href="{{ route('messages.request.create') }}" class="group flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-red-200 hover:bg-red-50 transition text-center">
                    <span class="w-11 h-11 rounded-full bg-red-100 text-red-600 flex items-center justify-center group-hover:scale-105 transition">
                        <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Signaler une panne</span>
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow p-8 text-center">
            <i class="fa-solid fa-house-circle-xmark text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Vous n'avez pas encore de logement assigné.</p>
        </div>
    @endif

</div>
@endsection
