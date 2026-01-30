@extends('layouts.appLimited')

@section('title', 'Ajouter un Paiement')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('payments.store') }}" method="POST" class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-money-bill-wave text-green-500 mr-2"></i>Ajouter un Paiement
        </h2>

        {{-- Locataire --}}
        <div>
            <label class="block mb-1 font-semibold">Locataire</label>
            <select name="tenant_id" id="tenantSelect" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="">Sélectionner un locataire</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" data-manager="{{ $tenant->manager_id ?? '' }}">
                        {{ $tenant->name }} {{ $tenant->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Manager --}}
        <div>
            <label class="block mb-1 font-semibold">Gestionnaire</label>
            <select name="manager_id" id="managerSelect" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="">Sélectionner un gestionnaire</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}">{{ $manager->name }} {{ $manager->surname }}</option>
                @endforeach
            </select>
        </div>

        {{-- Payment Method --}}
        <div>
            <label class="block mb-1 font-semibold">Méthode de paiement</label>
            <select name="payment_method_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}">{{ $method->label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Montant --}}
        <div>
            <label class="block mb-1 font-semibold">Montant</label>
            <input type="number" name="amount" value="{{ old('amount') }}" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

       
        <button type="submit" class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Créer
        </button>
    </form>
</div>

<script>
document.getElementById('tenantSelect').addEventListener('change', function() {
    const managerId = this.selectedOptions[0].dataset.manager;
    if(managerId) {
        document.getElementById('managerSelect').value = managerId;
    }
});
</script>
@endsection