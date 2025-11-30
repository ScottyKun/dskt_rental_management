@extends('layouts.dashboard')

@section('dashboard-content')

<div class="max-w-3xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-lg p-8">

        <!-- Titre -->
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            Profil de {{ $user->name }} {{ $user->surname }}
        </h2>

        <!-- Informations utilisateur -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Nom -->
            <div>
                <p class="text-sm text-gray-500">Nom</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->name }}</p>
            </div>

            <!-- Prénom -->
            <div>
                <p class="text-sm text-gray-500">Prénom</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->surname }}</p>
            </div>

            <!-- Email -->
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->email }}</p>
            </div>

            <!-- Téléphone -->
            <div>
                <p class="text-sm text-gray-500">Téléphone</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->phone ?? '—' }}</p>
            </div>

            <!-- Adresse -->
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Adresse</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->address ?? '—' }}</p>
            </div>

            <!-- Rôle -->
            <div>
                <p class="text-sm text-gray-500">Rôle</p>
                <p class="text-lg font-semibold text-gray-800 capitalize">
                    {{ $user->role }}
                </p>
            </div>

            <!-- État -->
            <div>
                <p class="text-sm text-gray-500">Statut</p>

                @if($user->is_validated)
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                        Actif
                    </span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full">
                        Inactif
                    </span>
                @endif
            </div>

            <!-- Manager -->
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Manager</p>
                @if($user->manager)
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $user->manager->name }} {{ $user->manager->surname }}
                    </p>
                @else
                    <p class="text-lg font-semibold text-gray-800">Aucun manager</p>
                @endif
            </div>

        </div>

        <!-- Bouton Modifier -->
        <div class="mt-8 text-right">
            <a href="{{ route('users.edit', $user->id) }}"
               class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Modifier mes informations
            </a>
        </div>
    </div>
</div>

@endsection
