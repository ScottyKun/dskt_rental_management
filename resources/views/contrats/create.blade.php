@extends('layouts.dashboard')

@section('title', 'Ajouter un Contrat')

@section('dashboard-content')
<div class="max-w-xl mx-auto my-2 sm:my-6">
    <form action="{{ route('contrats.store') }}" method="POST"
          class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg w-full max-w-2xl space-y-5">
        @csrf

        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800">
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

        {{-- Date limite de versement du dépôt --}}
        <div>
            <label class="block mb-1 font-semibold">Dépôt payable au plus tard le</label>
            <input type="date" name="deposit_due_date" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        {{-- Nature du bail --}}
        <div>
            <label class="block mb-1 font-semibold">Nature du bail (usage)</label>
            <input type="text" name="nature_bail" placeholder="ex: habitation, commercial..."
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        {{-- Garant / caution du locataire --}}
        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-700 mb-3">Garant / caution du locataire (optionnel)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-sm font-medium">Nom et prénom</label>
                    <input type="text" name="garant[nom]" class="w-full p-2.5 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">N° CNI</label>
                    <input type="text" name="garant[cni_number]" class="w-full p-2.5 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Téléphone</label>
                    <input type="text" name="garant[telephone]" class="w-full p-2.5 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Email</label>
                    <input type="email" name="garant[email]" class="w-full p-2.5 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Profession</label>
                    <input type="text" name="garant[profession]" class="w-full p-2.5 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block mb-1 text-sm font-medium">Lieu de résidence</label>
                    <input type="text" name="garant[lieu_residence]" class="w-full p-2.5 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Ajouter
        </button>
    </form>
</div>

{{-- Choices.js pour recherche et filtrage --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

{{-- JS pour remplir automatiquement locataire et loyer --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const appartementSelect = new Choices('#appartementSelect', {
        searchEnabled: true,
        placeholder: true,
        placeholderValue: 'Rechercher un appartement',
        shouldSort: false,
        itemSelectText: ''
    });

    const tenantSelect = new Choices('#tenantSelect', {
        searchEnabled: true,
        placeholder: true,
        placeholderValue: 'Rechercher un locataire',
        shouldSort: false,
        itemSelectText: ''
    });

    document.getElementById('appartementSelect').addEventListener('change', function () {
        const selectedOption = this.selectedOptions[0];
        const tenantId = selectedOption ? selectedOption.dataset.tenant : '';
        const rent = selectedOption ? selectedOption.dataset.rent : '';

        if (tenantId) {
            tenantSelect.setChoiceByValue(tenantId);
        } else {
            tenantSelect.clearStore();
        }

        document.getElementById('rentInput').value = rent || '';
    });
});
</script>
@endsection
