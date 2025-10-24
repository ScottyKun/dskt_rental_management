@extends('layouts.appLimited')

@section('title', 'Ajouter un Appartement')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('appartements.store') }}" method="POST"
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf

        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-building text-blue-500 mr-2"></i>Ajouter un Appartement
        </h2>

        {{-- Nom --}}
        <div>
            <label class="block mb-1 font-semibold">Nom</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Description --}}
        <div>
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description"
                      class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">{{ old('description') }}</textarea>
        </div>

        {{-- Type --}}
        <div>
            <label class="block mb-1 font-semibold">Type</label>
            <input type="text" name="type" value="{{ old('type') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Surface --}}
        <div>
            <label class="block mb-1 font-semibold">Surface (m²)</label>
            <input type="number" name="area" value="{{ old('area') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Loyer --}}
        <div>
            <label class="block mb-1 font-semibold">Loyer (CFA)</label>
            <input type="number"  name="rent" value="{{ old('rent') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Statut --}}
        <div>
            <label class="block mb-1 font-semibold">Statut</label>
            <select name="status" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="disponible" selected>Disponible</option>
                <option value="occupe">Occupé</option>
                <option value="en_renovation">En rénovation</option>
            </select>
        </div>

        {{-- Immeuble --}}
        <div>
            <label class="block mb-1 font-semibold">Immeuble</label>
            <select name="immeuble_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                @foreach($immeubles as $immeuble)
                    <option value="{{ $immeuble->id }}">{{ $immeuble->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Locataire --}}
        <div>
            <label class="block mb-1 font-semibold">Locataire</label>
            <select name="locataire_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">— Non attribué —</option>
                @foreach($locataires as $locataire)
                    <option value="{{ $locataire->id }}">{{ $locataire->name }} {{ $locataire->surname }}</option>
                @endforeach
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
