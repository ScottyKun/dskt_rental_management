@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto mt-6">

    <div class="bg-white shadow-lg rounded-xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 mb-4">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-700">
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

        <div class="mt-6 border-t pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-600">
                    Statut de signature :
                    <span @class([
                        'font-semibold',
                        'text-gray-500' => $receipt->signature_status === 'non_envoye',
                        'text-amber-600' => $receipt->signature_status === 'en_attente',
                        'text-green-600' => $receipt->signature_status === 'signe',
                        'text-red-600' => $receipt->signature_status === 'refuse',
                    ])>
                        {{ str_replace('_', ' ', $receipt->signature_status) }}
                    </span>
                </p>
                @if($receipt->signature_status === 'signe' && $receipt->signed_pdf_sha256)
                    <p class="text-xs text-gray-400 break-all mt-1">
                        Empreinte SHA-256 du PDF signé (preuve d'intégrité) : {{ $receipt->signed_pdf_sha256 }}
                    </p>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']) && $receipt->signature_status === 'non_envoye')
                    <form action="{{ route('receipts.signature.send', $receipt->id) }}" method="POST">
                        @csrf
                        <button class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Envoyer pour signature
                        </button>
                    </form>
                @endif

                @if($receipt->signature_status === 'signe')
                    <a href="{{ route('receipts.pdf', $receipt->id) }}"
                       class="inline-block text-center w-full sm:w-auto bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                        Télécharger le reçu (signé)
                    </a>
                @else
                    <button type="button" disabled
                            class="inline-block text-center w-full sm:w-auto bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed">
                        Télécharger (disponible une fois signé)
                    </button>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
