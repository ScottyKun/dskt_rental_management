@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">

    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Bonjour {{ $user->name }} 👋</h1>
        <p class="text-sm text-gray-500">Vue d'ensemble de la plateforme</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Locataires</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['locataires'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Gestionnaires</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['gestionnaires'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Immeubles</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['immeubles'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Contrats actifs</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['contrats_actifs'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Appartements disponibles</p>
            <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $stats['appartements_disponibles'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Appartements occupés</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $stats['appartements_occupes'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Revenus du mois</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ number_format($stats['revenus_du_mois'], 0, ',', ' ') }} CFA</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs sm:text-sm text-gray-500">Paiements en attente</p>
            <p class="text-xl sm:text-2xl font-bold text-amber-600">{{ $stats['paiements_en_attente'] }}</p>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div>
        <h2 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Actions rapides</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <a href="{{ route('immeubles.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-building text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Ajouter un immeuble</span>
            </a>
            <a href="{{ route('appartements.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-door-open text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Ajouter un appartement</span>
            </a>
            <a href="{{ route('users.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-user-plus text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Créer un utilisateur</span>
            </a>
            <a href="{{ route('contrats.create') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-file-contract text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Créer un contrat</span>
            </a>
            <a href="{{ route('payments.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-money-check-dollar text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Voir les paiements</span>
            </a>
            <a href="{{ route('users.index') }}" class="flex items-center gap-3 bg-white rounded-xl shadow p-4 hover:shadow-md hover:bg-blue-50 transition">
                <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                <span class="font-medium text-gray-700">Gérer les utilisateurs</span>
            </a>
        </div>
    </div>

</div>
@endsection
