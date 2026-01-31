@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-700">
                Reçu {{ $receipt->receipt_number }}
            </h2>
            <span class="text-sm font-medium text-gray-500">
                {{ number_format($receipt->total_amount, 2, ',', ' ') }} CFA
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">
                    <span class="font-semibold">Locataire :</span>
                    {{ $receipt->tenant->name }} {{ $receipt->tenant->surname }}
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Paiement associé :</span>
                    #{{ $receipt->payment->id }}
                </p>

                @foreach ($receipt->periods as $period)
                    <p class="text-gray-600">
                        <span class="font-semibold">Période :</span>
                        {{ $period->period_start->format('d/m/Y') }}
                        →
                        {{ $period->period_end->format('d/m/Y') }}
                    </p>
                @endforeach
            </div>

            <div>
                <p class="text-gray-600">
                    <span class="font-semibold">Généré par :</span>
                    {{ $receipt->generator->name }} {{ $receipt->generator->surname }}
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Généré le :</span>
                    {{ $receipt->generated_at->format('d/m/Y H:i') }}
                </p>

                <p class="text-gray-600">
                    <span class="font-semibold">Dernière mise à jour :</span>
                    {{ $receipt->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        
        
    </div>

</div>
@endsection