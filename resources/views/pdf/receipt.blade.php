<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 20px; }
        p { margin: 4px 0; line-height: 1.5; }
        .amount { text-align: center; font-size: 20px; font-weight: bold; margin: 20px 0; }
        .sig-token { margin-top: 40px; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>REÇU DE PAIEMENT N° {{ $receipt->receipt_number }}</h1>

    <p><strong>Locataire :</strong> {{ $receipt->tenant->name }} {{ $receipt->tenant->surname }}</p>
    <p><strong>Généré par :</strong> {{ $receipt->generator->name }} {{ $receipt->generator->surname }}</p>
    <p><strong>Date de génération :</strong> {{ $receipt->generated_at->format('d/m/Y H:i') }}</p>

    @foreach ($receipt->periods as $period)
        <p><strong>Période couverte :</strong> {{ $period->period_start->format('d/m/Y') }} → {{ $period->period_end->format('d/m/Y') }}</p>
    @endforeach

    <div class="amount">{{ number_format($receipt->total_amount, 2, ',', ' ') }} CFA</div>

    <p>Le présent reçu atteste de la réception du paiement mentionné ci-dessus pour le compte du locataire désigné.</p>

    <p>Signature (gestionnaire/administrateur) :</p>
    {{-- Documenso detecte ce jeton et place le champ de signature du destinataire n°1 (seul signataire) --}}
    <p class="sig-token">@verbatim{{signature, r1}}@endverbatim</p>
</body>
</html>
