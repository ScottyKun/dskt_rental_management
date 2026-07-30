<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 20px; }
        h2 { font-size: 13px; margin-top: 16px; margin-bottom: 6px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        p { margin: 3px 0; line-height: 1.4; }
        ul { margin: 4px 0 4px 20px; padding: 0; }
        .signatures { margin-top: 40px; width: 100%; }
        .signatures td { width: 50%; vertical-align: top; padding-top: 30px; }
        .sig-token { margin-top: 6px; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>CONTRAT DE LOCATION (RÉSIDENCE PRINCIPALE)</h1>

    <h2>1. DÉSIGNATION DES PARTIES</h2>
    <p><strong>Le BAILLEUR (Le propriétaire) :</strong></p>
    <p>Nom et Prénom / Dénomination sociale : {{ $contrat->appartement->immeuble->manager->name ?? '' }} {{ $contrat->appartement->immeuble->manager->surname ?? '' }}</p>
    <p>Adresse : {{ $contrat->appartement->immeuble->manager->address ?? '' }}</p>
    <p>Email : {{ $contrat->appartement->immeuble->manager->email ?? '' }}</p>

    <p><strong>Le LOCATAIRE :</strong></p>
    <p>Nom et Prénom : {{ $contrat->tenant->name ?? '' }} {{ $contrat->tenant->surname ?? '' }}</p>
    <p>Email : {{ $contrat->tenant->email ?? '' }}</p>

    <h2>2. OBJET DU CONTRAT</h2>
    <p>Adresse du logement : {{ $contrat->appartement->immeuble->address ?? '' }}, {{ $contrat->appartement->immeuble->town ?? '' }}</p>
    <p>Type d'habitat : {{ ucfirst($contrat->appartement->type ?? 'Appartement') }}</p>
    <p>Surface habitable : {{ $contrat->appartement->area ?? '' }} m²</p>
    <p>Destination des locaux : Usage d'habitation principale.</p>

    <h2>3. DATE D'EFFET ET DURÉE</h2>
    <p>Date de prise d'effet du bail : {{ $contrat->start_date->format('d/m/Y') }}</p>
    <p>Date de fin : {{ $contrat->end_date->format('d/m/Y') }}</p>

    <h2>4. CONDITIONS FINANCIÈRES</h2>
    <p>Loyer mensuel : {{ number_format($contrat->rent_amount, 2, ',', ' ') }} CFA</p>
    <p>Modalités de paiement : le {{ $contrat->rent_payment_day }} de chaque mois</p>

    <h2>5. DÉPÔT DE GARANTIE</h2>
    <p>Montant : {{ number_format($contrat->deposit_amount, 2, ',', ' ') }} CFA</p>

    <h2>6. OBLIGATIONS DES PARTIES</h2>
    <p><strong>Le BAILLEUR :</strong> Délivrer un logement décent, assurer la jouissance paisible, entretenir les locaux, délivrer les quittances.</p>
    <p><strong>Le LOCATAIRE :</strong> Payer le loyer et charges, user paisiblement, répondre des dégradations, entretien courant, assurance locative.</p>

    <h2>7. CLAUSE RÉSOLUTOIRE</h2>
    <p>En cas de non-paiement ou défaut d'assurance, le bail pourra être résilié de plein droit après mise en demeure.</p>

    <h2>8. ANNEXES OBLIGATOIRES</h2>
    <ul>
        <li>État des lieux d'entrée</li>
        <li>Pièce d'identité du locataire (transmise et validée via la plateforme)</li>
    </ul>

    <p style="margin-top: 20px;">Fait à {{ $contrat->appartement->immeuble->town ?? '' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>

    <table class="signatures">
        <tr>
            <td>
                <p>Signature du BAILLEUR :</p>
                {{-- Documenso detecte ce jeton et place le champ de signature du destinataire n°1 (gestionnaire/admin) --}}
                <p class="sig-token">@verbatim{{signature, r1}}@endverbatim</p>
            </td>
            <td>
                <p>Signature du LOCATAIRE :</p>
                {{-- Destinataire n°2 --}}
                <p class="sig-token">@verbatim{{signature, r2}}@endverbatim</p>
            </td>
        </tr>
    </table>
</body>
</html>
