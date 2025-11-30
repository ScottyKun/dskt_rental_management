@extends('layouts.appLimited')

@section('title', 'Ajouter un Contrat')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('contrats.store') }}" method="POST"
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf

        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-file-contract text-blue-500 mr-2"></i>Ajouter un Contrat
        </h2>

        {{-- Appartement --}}
        <div>
            <label class="block mb-1 font-semibold">Appartement</label>
            <select id="appartementSelect" name="appartement_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="">— Rechercher un appartement —</option>
                @foreach($appartements as $appartement)
                    <option value="{{ $appartement->id }}"
                        data-tenant="{{ $appartement->locataire->id ?? '' }}"
                        data-rent="{{ $appartement->rent ?? '' }}">
                        {{ $appartement->name }} ({{ $appartement->status }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Locataire --}}
        <div>
            <label class="block mb-1 font-semibold">Locataire</label>
            <select id="tenantSelect" name="tenant_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="">— Rechercher un locataire —</option>
                @foreach($locataires as $locataire)
                    <option value="{{ $locataire->id }}">{{ $locataire->name }} {{ $locataire->surname }}</option>
                @endforeach
            </select>
        </div>

        {{-- Loyer --}}
        <div>
            <label class="block mb-1 font-semibold">Loyer (CFA)</label>
            <input type="number" id="rentInput" name="rent_amount" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Date début --}}
        <div>
            <label class="block mb-1 font-semibold">Date de début</label>
            <input type="date" name="start_date" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Date fin --}}
        <div>
            <label class="block mb-1 font-semibold">Date de fin</label>
            <input type="date" name="end_date" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Jour de paiement du loyer --}}
        <div>
            <label class="block mb-1 font-semibold">Jour du paiement du loyer</label>
            <input type="number" min="1" max="31" name="rent_payment_day"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                   required>
        </div>


        {{-- Dépôt --}}
        <div>
            <label class="block mb-1 font-semibold">Dépôt (CFA)</label>
            <input type="number" name="deposit_amount" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Ajouter
        </button>
    </form>
</div>

{{-- JS pour remplir automatiquement locataire et loyer --}}
<script>
document.getElementById('appartementSelect').addEventListener('change', function() {
    const selectedOption = this.selectedOptions[0];
    const tenantId = selectedOption.dataset.tenant;
    const rent = selectedOption.dataset.rent;

    const tenantSelect = document.getElementById('tenantSelect');
    tenantSelect.value = tenantId || "";

    const rentInput = document.getElementById('rentInput');
    rentInput.value = rent || "";
});
</script>
@endsection
