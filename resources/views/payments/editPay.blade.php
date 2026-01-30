@extends('layouts.appLimited')

@section('title', 'Modifier Paiement')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('payments.update', $payment->id) }}" method="POST"
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        @method('PUT')

        <h2 class="text-3xl font-bold text-center text-gray-800">
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

{{-- Auto-sélection du gestionnaire --}}
<script>
document.getElementById('tenantSelect').addEventListener('change', function () {
    const managerId = this.selectedOptions[0].dataset.manager;
    if (managerId) {
        document.getElementById('managerSelect').value = managerId;
    }
});
</script>
@endsection