@extends('layouts.appLimited')

@section('title', 'Ajouter un Immeuble')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('immeubles.store') }}" method="POST"
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-building text-blue-500 mr-2"></i>Ajouter un Immeuble
        </h2>

        {{-- Nom --}}
        <div>
            <label class="block mb-1 font-semibold">Nom</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Adresse --}}
        <div>
            <label class="block mb-1 font-semibold">Adresse</label>
            <input type="text" name="address" value="{{ old('address') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Ville --}}
        <div>
            <label class="block mb-1 font-semibold">Ville</label>
            <input type="text" name="town" value="{{ old('town') }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Description --}}
        <div>
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description') }}</textarea>
        </div>

        {{-- Gestionnaire --}}
        <div>
            <label class="block mb-1 font-semibold">Gestionnaire</label>
            <select name="manager_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}">{{ $manager->name }} {{ $manager->surname }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nombre d'appartements --}}
        <div>
            <label class="block mb-1 font-semibold">Nombre d'appartements</label>
            <input type="number" name="nb_apartments" value="{{ old('nb_apartments') ?? 0 }}"
                class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" min="0">
        </div>

        {{-- Status --}}
        <div>
            <label class="block mb-1 font-semibold">Statut</label>
            <select name="status" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="actif" selected>Actif</option>
                <option value="inactif">Inactif</option>
            </select>
        </div>

        {{-- Bouton --}}
        <button type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Ajouter
        </button>
    </form>
</div>
@endsection
