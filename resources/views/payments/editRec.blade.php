@extends('layouts.appLimited')

@section('title', 'Modifier Reçu')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form action="{{ route('receipts.update', $receipt->id) }}" method="POST"
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf
        @method('PUT')

        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-receipt text-blue-500 mr-2"></i>Modifier Reçu
        </h2>

        {{-- Numéro du reçu --}}
        <div>
            <label class="block mb-1 font-semibold">Numéro du reçu</label>
            <input type="text" value="{{ $receipt->receipt_number }}"
                   class="w-full p-3 border rounded bg-gray-100" readonly>
        </div>

        {{-- Paiement associé --}}
        <div>
            <label class="block mb-1 font-semibold">Paiement associé</label>
            <input type="text"
                   value="Paiement #{{ $receipt->payment_id }}"
                   class="w-full p-3 border rounded bg-gray-100" readonly>
        </div>

        {{-- Locataire --}}
        <div>
            <label class="block mb-1 font-semibold">Locataire</label>
            <input type="text"
                   value="{{ $receipt->tenant->name }} {{ $receipt->tenant->surname }}"
                   class="w-full p-3 border rounded bg-gray-100" readonly>
        </div>

        {{-- Montant total --}}
        <div>
            <label class="block mb-1 font-semibold">Montant total</label>
            <input type="number" step="0.01" name="total_amount"
                   value="{{ old('total_amount', $receipt->total_amount) }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                   readonly>
        </div>

        {{-- Généré par --}}
        <div>
            <label class="block mb-1 font-semibold">Généré par</label>
            <input type="text"
                   value="{{ $receipt->generatedBy->name }} {{ $receipt->generatedBy->surname }}"
                   class="w-full p-3 border rounded bg-gray-100" readonly>
        </div>

        {{-- Généré le --}}
        <div>
            <label class="block mb-1 font-semibold">Généré le</label>
            <input type="text"
                   value="{{ $receipt->generated_at->format('d/m/Y H:i') }}"
                   class="w-full p-3 border rounded bg-gray-100" readonly>
        </div>

        {{-- Période --}}
        <div>
            <label class="block mb-1 font-semibold">Période</label>
            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="period_start"
                       value="{{ old('period_start', $periods->first()->period_start) }}"
                       class="p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                       required>

                <input type="date" name="period_end"
                       value="{{ old('period_end', $periods->first()->period_end) }}"
                       class="p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                       required>
            </div>
        </div>

        {{-- Bouton --}}
        <button type="submit"
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-paper-plane mr-2"></i>Mettre à jour
        </button>
    </form>
</div>
@endsection