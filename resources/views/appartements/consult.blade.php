@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-6">

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
                <p class="text-gray-600"><span class="font-semibold">Loyer :</span> {{ number_format($appartement->rent, 2, ',', ' ') }} CFA</p>
            </div>

            {{-- Immeuble et locataire --}}
            <div>
                <p class="text-gray-600"><span class="font-semibold">Immeuble :</span> {{ $appartement->immeuble->name ?? '—' }}</p>
                <p class="text-gray-600"><span class="font-semibold">Locataire :</span> 
                    {{ $appartement->locataire ? $appartement->locataire->name . ' ' . $appartement->locataire->surname : '— Non attribué —' }}
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex space-x-2">
            <a href="{{ route('appartements.edit', $appartement->id) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
               <i class="fa-solid fa-pen mr-2"></i>Modifier
            </a>

            <form action="{{ route('appartements.destroy', $appartement->id) }}" method="POST" 
                  onsubmit="return confirm('Voulez-vous vraiment supprimer cet appartement ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fa-solid fa-trash mr-2"></i>Supprimer
                </button>
            </form>

            <a href="{{ route('appartements.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
               <i class="fa-solid fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

</div>
@endsection
