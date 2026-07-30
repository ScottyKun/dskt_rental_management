@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 mb-4">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-700">{{ $immeuble->name }}</h2>
            <span class="text-sm font-medium text-gray-500">{{ $immeuble->status === 'actif' ? 'Actif' : 'Inactif' }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600"><span class="font-semibold">Adresse :</span> {{ $immeuble->address }}</p>
                <p class="text-gray-600"><span class="font-semibold">Ville :</span> {{ $immeuble->town }}</p>
                <p class="text-gray-600"><span class="font-semibold">Description :</span> {{ $immeuble->description ?? '—' }}</p>
            </div>

            <div>
                <p class="text-gray-600"><span class="font-semibold">Createur :</span> 
                    {{ $immeuble->creator->name ?? '—' }} {{ $immeuble->creator->surname ?? '' }}</p>
                <p class="text-gray-600"><span class="font-semibold">Gestionnaire :</span> 
                    {{ $immeuble->manager->name ?? '—' }} {{ $immeuble->manager->surname ?? '' }}</p>
                <p class="text-gray-600"><span class="font-semibold">Nombre d'appartements :</span> {{ $immeuble->nb_apartments }}</p>
                <p class="text-gray-600"><span class="font-semibold">Disponibles :</span> {{ $immeuble->nb_available }}</p>
                <p class="text-gray-600"><span class="font-semibold">Occupés :</span> {{ $immeuble->nb_occupied }}</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-2">
            <a href="{{ route('immeubles.edit', $immeuble->id) }}" 
               class="w-full sm:w-auto text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
               <i class="fa-solid fa-pen mr-2"></i>Modifier
            </a>

            <form action="{{ route('immeubles.delete', $immeuble->id) }}" method="POST" 
                  class="w-full sm:w-auto"
                  onsubmit="return confirm('Voulez-vous vraiment supprimer cet immeuble ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fa-solid fa-trash mr-2"></i>Supprimer
                </button>
            </form>

            <a href="{{ route('immeubles.index') }}" 
               class="w-full sm:w-auto text-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
               <i class="fa-solid fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

</div>
@endsection
