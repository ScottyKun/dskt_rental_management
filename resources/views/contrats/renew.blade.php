@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-lg mx-auto my-6 bg-white p-6 rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-4">Renouveler le contrat</h2>

    <form method="POST" action="{{ route('contrats.renew', $contrat->id) }}">
        @csrf

        {{-- Appartement (readonly) --}}
        <div>
            <label class="font-semibold">Appartement</label>
            <input type="text" class="w-full p-3 border rounded bg-gray-100"
                   value="{{ $appartement->name }}" readonly>
        </div>

        {{-- Locataire --}}
        <div class="mt-3">
            <label class="font-semibold">Locataire</label>
            <input type="text" class="w-full p-3 border rounded bg-gray-100"
                   value="{{ $tenant->name }} {{ $tenant->surname }}" readonly>
        </div>

        {{-- Nouveau loyer --}}
        <div class="mt-3">
            <label class="font-semibold">Loyer (CFA)</label>
            <input type="number" name="rent_amount" required class="w-full p-3 border rounded">
        </div>

        {{-- Jour de paiement --}}
        <div class="mt-3">
            <label class="font-semibold">Jour de paiement</label>
            <input type="number" name="rent_payment_day" required min="1" max="31"
                   class="w-full p-3 border rounded">
        </div>

        {{-- Nouveau dépôt --}}
        <div class="mt-3">
            <label class="font-semibold">Caution (CFA)</label>
            <input type="number" name="deposit_amount" required class="w-full p-3 border rounded">
        </div>

        {{-- Dates --}}
        <div class="mt-3">
            <label class="font-semibold">Nouvelle date de début</label>
            <input type="date" name="start_date" required class="w-full p-3 border rounded">
        </div>

        <div class="mt-3">
            <label class="font-semibold">Nouvelle date de fin</label>
            <input type="date" name="end_date" required class="w-full p-3 border rounded">
        </div>

        <button class="mt-5 w-full bg-blue-600 text-white py-3 rounded-lg">
            Renouveler le contrat
        </button>
    </form>
</div>
@endsection
