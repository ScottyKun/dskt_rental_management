@extends('layouts.dashboard')

@section('title', 'Ajouter un Paiement')

@section('dashboard-content')
<div class="max-w-xl mx-auto my-2 sm:my-6">
    <form action="{{ route('payments.store') }}" method="POST"
          class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf

        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-money-bill-wave text-blue-600 mr-2"></i>
            Ajouter un Paiement
        </h2>

        {{--  Confirmation création méthode CASH --}}
        @if ($errors->confirmation->has('payment_method'))
            <div class="bg-blue-50 border border-blue-300 text-blue-800 p-4 rounded-lg space-y-3">
                <p class="font-semibold">
                     {{ $errors->confirmation->first('payment_method') }}
                </p>

                <label class="flex items-center space-x-2">
                    <input type="checkbox"
                           name="create_payment_method"
                           value="yes"
                           required
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <span>
                        Oui, créer automatiquement la méthode de paiement
                        <strong>Espèces</strong>
                    </span>
                </label>
            </div>
        @endif

        {{-- Locataire --}}
        <div>
            <label class="block mb-1 font-semibold">Locataire</label>
            <select id="tenantSelect" name="tenant_id" required>
                <option value="">— Rechercher un locataire —</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" data-manager="{{ $tenant->manager_id }}"
                        {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }} {{ $tenant->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Gestionnaire --}}
        <div>
            <label class="block mb-1 font-semibold">Gestionnaire</label>
            <select id="managerSelect" name="manager_id" required>
                <option value="">— Gestionnaire associé —</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}"
                        {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }} {{ $manager->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Méthode de paiement --}}
        <div>
            <label class="block mb-1 font-semibold">Méthode de paiement</label>
            <select id="paymentMethodSelect" name="payment_method_id">
                <option value="">— Sélectionner une méthode —</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}"
                        {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                        {{ $method->label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Motif --}}
        <div>
            <label class="block mb-1 font-semibold">Motif</label>
            <select name="motif" required class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="loyer" {{ old('motif', 'loyer') == 'loyer' ? 'selected' : '' }}>Loyer</option>
                <option value="caution" {{ old('motif') == 'caution' ? 'selected' : '' }}>Caution</option>
                <option value="reparation" {{ old('motif') == 'reparation' ? 'selected' : '' }}>Réparation</option>
                <option value="autre" {{ old('motif') == 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
        </div>

        {{-- Montant --}}
        <div>
            <label class="block mb-1 font-semibold">Montant</label>
            <input type="number" step="0.01" name="amount"
                   value="{{ old('amount') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                   required>
        </div>

        {{-- Bouton --}}
        <button type="submit"
                class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>
            Créer
        </button>
    </form>
</div>

{{-- Choices.js pour recherche et filtrage --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialisation Choices.js
    const tenantSelect = new Choices('#tenantSelect', {
        searchEnabled: true,
        placeholder: true,
        placeholderValue: 'Rechercher un locataire',
        shouldSort: false,
        itemSelectText: ''
    });

    const managerSelect = new Choices('#managerSelect', {
        searchEnabled: true,
        placeholder: true,
        placeholderValue: '— Gestionnaire associé —',
        shouldSort: false,
        itemSelectText: ''
    });

    const paymentMethodSelect = new Choices('#paymentMethodSelect', {
        searchEnabled: true,
        placeholder: true,
        placeholderValue: '— Sélectionner une méthode —',
        shouldSort: false,
        itemSelectText: ''
    });

    // Auto-sélection du manager
    document.querySelector('#tenantSelect').addEventListener('change', function () {
        const selected = this.selectedOptions[0];
        const managerId = selected ? selected.dataset.manager : '';
        if(managerId) {
            managerSelect.setChoiceByValue(managerId);
        } else {
            managerSelect.clearStore();
        }
    });
});
</script>
@endsection