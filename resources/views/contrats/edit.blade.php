@extends('layouts.appLimited')

@section('title', 'Modifier un Contrat')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100 px-4 py-8">
    <form action="{{ route('contrats.update', $contrat->id) }}" method="POST"
          class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        @method('PUT')

        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-file-contract text-blue-500 mr-2"></i>Modifier un Contrat
        </h2>

        {{-- Appartement --}}
        <div>
            <label class="block mb-1 font-semibold">Appartement</label>
            <select id="appartementSelect" name="appartement_id" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
                <option value="">— Rechercher un appartement —</option>
                @foreach($appartements as $appartement)
                    <option value="{{ $appartement->id }}"
                        data-tenant="{{ $appartement->locataire->id ?? '' }}"
                        data-rent="{{ $appartement->rent ?? '' }}"
                        {{ old('appartement_id', $contrat->appartement_id) == $appartement->id ? 'selected' : '' }}>
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
                    <option value="{{ $locataire->id }}"
                        {{ old('tenant_id', $contrat->tenant_id) == $locataire->id ? 'selected' : '' }}>
                        {{ $locataire->name }} {{ $locataire->surname }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Loyer --}}
        <div>
            <label class="block mb-1 font-semibold">Loyer (CFA)</label>
            <input type="number" id="rentInput" name="rent_amount" value="{{ old('rent_amount', $contrat->rent_amount) }}" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Date début --}}
        <div>
            <label class="block mb-1 font-semibold">Date de début</label>
            <input type="date" name="start_date" value="{{ old('start_date', $contrat->start_date) }}" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Date fin --}}
        <div>
            <label class="block mb-1 font-semibold">Date de fin</label>
            <input type="date" name="end_date" value="{{ old('end_date', $contrat->end_date) }}" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

        {{-- Dépôt --}}
        <div>
            <label class="block mb-1 font-semibold">Caution (CFA)</label>
            <input type="number" name="deposit_amount" value="{{ old('deposit_amount', $contrat->deposit_amount) }}" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none" required>
        </div>

         {{-- Jour de paiement du loyer --}}
        <div>
            <label class="block mb-1 font-semibold">Jour du paiement du loyer</label>
            <input type="number" name="rent_payment_day" value="{{ old('rent_payment_day', $contrat->rent_payment_day) }}" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        {{-- Status --}}
        <div>
            <label class="block mb-1 font-semibold">Status</label>
            <select name="status" class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                @php $status = old('status', $contrat->status); @endphp
                <option value="actif" {{ $status === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="résilié" {{ $status === 'résilié' ? 'selected' : '' }}>Résilié</option>
            </select>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Modifier
        </button>
    </form>
</div>

<script>
document.getElementById('appartementSelect').addEventListener('change', function() {
    const selectedOption = this.selectedOptions[0];
    const tenantId = selectedOption.dataset.tenant;
    const rent = selectedOption.dataset.rent;

    document.getElementById('tenantSelect').value = tenantId || "";
    document.getElementById('rentInput').value = rent || "";
});
</script>
@endsection
