@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow space-y-6">
    
    <h1 class="text-2xl font-bold text-center">CONTRAT DE LOCATION (RÉSIDENCE PRINCIPALE)</h1>

    {{-- 1. Désignation des parties --}}
    <section class="space-y-2">
        <h2 class="font-semibold">1. DÉSIGNATION DES PARTIES</h2>

        <p><strong>Le BAILLEUR (Le propriétaire) :</strong></p>
        <p>Nom et Prénom / Dénomination sociale : {{ $contrat->appartement->immeuble->manager->name ?? '' }} {{ $contrat->appartement->immeuble->manager->surname ?? '' }}</p>
        <p>Adresse : {{ $contrat->appartement->immeuble->manager->address ?? '' }}</p>
        <p>Email : {{ $contrat->appartement->immeuble->manager->email ?? '' }}</p>

        <p><strong>Le LOCATAIRE :</strong></p>
        <p>Nom et Prénom : {{ $contrat->tenant->name ?? '' }} {{ $contrat->tenant->surname ?? '' }}</p>
    </section>

    {{-- 2. Objet du contrat --}}
    <section class="space-y-2">
        <h2 class="font-semibold">2. OBJET DU CONTRAT</h2>
        <p>Adresse du logement : {{ $contrat->appartement->immeuble->address ?? '' }}, {{ $contrat->appartement->immeuble->town ?? '' }}</p>
        <p>Type d'habitat : {{ ucfirst($contrat->appartement->type ?? 'Appartement') }}</p>
        <p>Surface habitable : {{ $contrat->appartement->area ?? '' }} m²</p>
        <p>Nombre de pièces principales : {{ $contrat->appartement->description ?? '' }}</p>
        <p>Détail des annexes : {{ $contrat->appartement->description ?? '' }}</p>
        <p>Destination des locaux : Usage d'habitation principale.</p>
    </section>

    {{-- 3. Date d'effet et durée --}}
    <section class="space-y-2">
        <h2 class="font-semibold">3. DATE D'EFFET ET DURÉE</h2>
        <p>Date de prise d'effet du bail : {{ \Carbon\Carbon::parse($contrat->start_date)->format('d/m/Y') }}</p>
        <p>Durée : 
            @if($contrat->appartement->type === 'meublé')
                1 an (reconductible)
            @else
                3 ans (si bailleur personne physique) ou 6 ans (personne morale)
            @endif
        </p>
    </section>

    {{-- 4. Conditions financières --}}
    <section class="space-y-2">
        <h2 class="font-semibold">4. CONDITIONS FINANCIÈRES</h2>
        <p>Loyer mensuel : {{ number_format($contrat->rent_amount,2,',',' ') }} CFA</p>
        <p>Modalités de paiement : Total {{ number_format($contrat->rent_amount,2,',',' ') }} CFA le {{ $contrat->rent_payment_day }} de chaque mois</p>
    </section>

    {{-- 5. Dépôt de garantie --}}
    <section class="space-y-2">
        <h2 class="font-semibold">5. DÉPÔT DE GARANTIE</h2>
        <p>Montant : {{ number_format($contrat->deposit_amount,2,',',' ') }} CFA</p>
    </section>

    {{-- 6. Obligations --}}
    <section class="space-y-2">
        <h2 class="font-semibold">6. OBLIGATIONS DES PARTIES</h2>
        <p><strong>Le BAILLEUR :</strong> Délivrer un logement décent, assurer la jouissance paisible, entretenir les locaux, délivrer les quittances.</p>
        <p><strong>Le LOCATAIRE :</strong> Payer le loyer et charges, user paisiblement, répondre des dégradations, entretien courant, assurance locative.</p>
    </section>

    {{-- 7. Clause résolutoire --}}
    <section>
        <h2 class="font-semibold">7. CLAUSE RÉSOLUTOIRE</h2>
        <p>En cas de non-paiement ou défaut d'assurance, le bail pourra être résilié de plein droit après mise en demeure.</p>
    </section>

    {{-- 8. Annexes obligatoires --}}
    <section>
        <h2 class="font-semibold">8. ANNEXES OBLIGATOIRES</h2>
        <ul class="list-disc ml-6">
            <li>État des lieux d'entrée</li>
            <li>CNI</li>
        </ul>
    </section>

    {{-- Signatures --}}
    <section class="space-y-4 mt-6">
        <p>Fait à {{ $contrat->appartement->immeuble->town ?? '' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <div class="flex justify-between mt-6">
            <div>
                <p>Signature du BAILLEUR :</p>
                <p class="mt-12 border-b w-64"></p>
            </div>
            <div>
                <p>Signature du LOCATAIRE :</p>
                <p class="mt-12 border-b w-64"></p>
            </div>
        </div>
    </section>

    {{-- Bouton téléchargement --}}
    <div class="mt-6 text-center">
        <a href="#" 
           class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
           Télécharger le contrat
        </a>
    </div>

</div>
@endsection
