@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-700">
                Paiement ####{{ $payment->id }}
            </h2>
            <span class="text-sm font-medium text-gray-500">
                {{ ucfirst($payment->status ?? 'validé') }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">
                    <span class="font-semibold">Locataire :</span>
                    {{ $payment->tenant->name }} {{ $payment->tenant->surname }}
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Gestionnaire :</span>
                    {{ $payment->manager->name }} {{ $payment->manager->surname }}
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Méthode :</span>
                    {{ $payment->paymentMethod->label }}
                </p>
            </div>

            <div>
                <p class="text-gray-600">
                    <span class="font-semibold">Montant :</span>
                    {{ number_format($payment->amount, 2, ',', ' ') }} CFA
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Créé le :</span>
                    {{ $payment->created_at->format('d/m/Y H:i') }}
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Dernière mise à jour :</span>
                    {{ $payment->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

    </div>

</div>
@endsection