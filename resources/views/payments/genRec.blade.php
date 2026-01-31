@extends('layouts.appLimited')

@section('title', 'Générer un reçu')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-gray-100">
    <form method="POST"
          action="{{ route('receipts.generate', $payment->id) }}"
          class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md space-y-5">
        @csrf

        <h2 class="text-3xl font-bold text-center text-gray-800">
            <i class="fa-solid fa-receipt text-blue-600 mr-2"></i>
            Générer un reçu
        </h2>

        {{-- Début de période --}}
        <div>
            <label class="block mb-1 font-semibold text-gray-700">
                Date de début de la période
            </label>
            <input type="date"
                   name="periods[0][period_start]"
                   value="{{ old('periods.0.period_start') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                   required>
        </div>

        {{-- Fin de période --}}
        <div>
            <label class="block mb-1 font-semibold text-gray-700">
                Date de fin de la période
            </label>
            <input type="date"
                   name="periods[0][period_end]"
                   value="{{ old('periods.0.period_end') }}"
                   class="w-full p-3 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
                   required>
        </div>

        {{-- Bouton --}}
        <button type="submit"
                class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fa-solid fa-file-invoice mr-2"></i>
            Générer le reçu
        </button>
    </form>
</div>
@endsection