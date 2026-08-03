@extends('layouts.dashboard')

@section('title', 'Modifier Paiement')

@section('dashboard-content')
<div class="max-w-xl mx-auto my-2 sm:my-6">
    <form action="{{ route('payments.update', $payment->id) }}" method="POST"
          class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        @method('PUT')

        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-money-bill-wave text-blue-500 mr-2"></i>Modifier Paiement
        </h2>

        {{-- Locataire --}}
        <div>
            <label class="block mb-1 font-semibold">Locataire</label>
            <select name="tenant_id" id="tenantSelect"
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                    required>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}"
                        data-manager="{{ $tenant->manager_id }}"
                        {{ old('tenant_id', $payment->tenant_id) == $tenant->id ? 'selected' : '' }}>
                        {{ $tenant->name }} {{ $tenant->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Gestionnaire --}}
        <div>
            <label class="block mb-1 font-semibold">Gestionnaire</label>
            <select name="manager_id" id="managerSelect"
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                    required>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}"
                        {{ old('manager_id', $payment->manager_id) == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }} {{ $manager->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Méthode de paiement --}}
        <div>
            <label class="block mb-1 font-semibold">Méthode de paiement</label>
            <select name="payment_method_id"
                    class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                    required>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}"
                        {{ old('payment_method_id', $payment->payment_method_id) == $method->id ? 'selected' : '' }}>
                        {{ $method->label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Montant --}}
        <div>
            <label class="block mb-1 font-semibold">Montant</label>
            <input type="number" step="0.01" name="amount"
                   value="{{ old('amount', $payment->amount) }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                   required>
        </div>

        {{-- Bouton --}}
        <button type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Mettre à jour
        </button>
    </form>
</div>

{{-- Choices.js pour recherche et filtrage + auto-selection du gestionnaire --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tenantSelect = new Choices('#tenantSelect', {
        searchEnabled: true, placeholder: true, placeholderValue: 'Rechercher un locataire',
        shouldSort: false, itemSelectText: ''
    });
    const managerSelect = new Choices('#managerSelect', {
        searchEnabled: true, placeholder: true, placeholderValue: '— Gestionnaire associé —',
        shouldSort: false, itemSelectText: ''
    });

    document.getElementById('tenantSelect').addEventListener('change', function () {
        const managerId = this.selectedOptions[0] ? this.selectedOptions[0].dataset.manager : '';
        if (managerId) {
            managerSelect.setChoiceByValue(managerId);
        }
    });
});
</script>
@endsection