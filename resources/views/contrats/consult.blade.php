@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto bg-white p-4 sm:p-8 rounded-xl shadow space-y-6">
    
    <h1 class="text-xl sm:text-2xl font-bold text-center">CONTRAT DE LOCATION (RÉSIDENCE PRINCIPALE)</h1>

    {{-- 1. Désignation des parties --}}
    <section class="space-y-2">
        <h2 class="font-semibold">1. DÉSIGNATION DES PARTIES</h2>

        <p><strong>Le BAILLEUR (Le propriétaire) :</strong></p>
        <p>Nom et Prénom / Dénomination sociale : {{ $contrat->appartement->immeuble->manager->name ?? '' }} {{ $contrat->appartement->immeuble->manager->surname ?? '' }}</p>
        <p>Adresse : {{ $contrat->appartement->immeuble->manager->address ?? '' }}</p>
        <p class="break-words">Email : {{ $contrat->appartement->immeuble->manager->email ?? '' }}</p>

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

    {{-- Signatures (aperçu, pas les vraies zones Documenso qui sont dans le PDF) --}}
    <section class="space-y-4 mt-6">
        <p>Fait à {{ $contrat->appartement->immeuble->town ?? '' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <div class="flex flex-col sm:flex-row sm:justify-between gap-6 mt-6">
            <div>
                <p>Signature du BAILLEUR :</p>
                @if($contrat->signature_status === 'signe')
                    <p class="mt-8 sm:mt-12 text-green-600 font-medium">
                        <i class="fa-solid fa-signature mr-2"></i>Signé électroniquement
                    </p>
                @else
                    <p class="mt-8 sm:mt-12 border-b w-full sm:w-64"></p>
                @endif
            </div>
            <div>
                <p>Signature du LOCATAIRE :</p>
                @if($contrat->signature_status === 'signe')
                    <p class="mt-8 sm:mt-12 text-green-600 font-medium">
                        <i class="fa-solid fa-signature mr-2"></i>Signé électroniquement
                    </p>
                @else
                    <p class="mt-8 sm:mt-12 border-b w-full sm:w-64"></p>
                @endif
            </div>
        </div>
    </section>

    {{-- Piece jointe (CNI) et signature electronique --}}
    <section class="space-y-4 mt-6 border-t pt-6">
        <h2 class="font-semibold">DOCUMENT D'IDENTITÉ ET SIGNATURE</h2>

        @php $latestDoc = $contrat->latestDocument; @endphp

        @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
            {{-- Vue gestionnaire/admin --}}
            @if($contrat->document_status === 'non_demande')
                <form action="{{ route('contrats.document.request', $contrat->id) }}" method="POST">
                    @csrf
                    <button class="w-full sm:w-auto bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600">
                        Demander la pièce d'identité au locataire
                    </button>
                </form>
            @elseif($contrat->document_status === 'demande')
                <p class="text-amber-600">En attente de transmission par le locataire.</p>
            @elseif($contrat->document_status === 'soumis' && $latestDoc)
                <p class="break-words">Document reçu : {{ $latestDoc->original_name }}
                    <a href="{{ route('contrats.document.download', [$contrat->id, $latestDoc->id]) }}" class="text-blue-600 underline ml-2">télécharger</a>
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('contrats.document.validate', [$contrat->id, $latestDoc->id]) }}" method="POST">
                        @csrf
                        <button class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Valider</button>
                    </form>
                    <form action="{{ route('contrats.document.reject', [$contrat->id, $latestDoc->id]) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="text" name="reason" placeholder="Motif du refus" required
                               class="border rounded px-2 py-2 sm:py-1 text-sm w-full sm:w-auto">
                        <button class="w-full sm:w-auto bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Refuser</button>
                    </form>
                </div>
            @elseif($contrat->document_status === 'valide')
                <p class="text-green-600">✓ Document validé.</p>
            @endif

            @if($contrat->document_status === 'valide' && $contrat->signature_status === 'non_envoye')
                <form action="{{ route('contrats.signature.send', $contrat->id) }}" method="POST" class="mt-3">
                    @csrf
                    <button class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Envoyer pour signature (bailleur + locataire)
                    </button>
                </form>
            @endif
        @else
            {{-- Vue locataire --}}
            @if($contrat->document_status === 'demande' || ($contrat->document_status === 'soumis' && $latestDoc?->status === 'refuse'))
                @if($latestDoc?->status === 'refuse')
                    <p class="text-red-600">Document refusé : {{ $latestDoc->rejection_reason }}</p>
                @endif
                <form action="{{ route('contrats.document.store', $contrat->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row sm:items-center gap-3">
                    @csrf
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required class="text-sm w-full sm:w-auto">
                    <button class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Transmettre ma pièce d'identité</button>
                </form>
            @elseif($contrat->document_status === 'soumis')
                <p class="text-amber-600">Votre document est en cours de vérification.</p>
            @elseif($contrat->document_status === 'valide')
                <p class="text-green-600">✓ Votre document a été validé.</p>
            @endif
        @endif

        {{-- Statut de signature, commun aux deux vues --}}
        <p class="text-sm text-gray-600">
            Statut de signature :
            <span @class([
                'font-semibold',
                'text-gray-500' => $contrat->signature_status === 'non_envoye',
                'text-amber-600' => $contrat->signature_status === 'en_attente',
                'text-green-600' => $contrat->signature_status === 'signe',
                'text-red-600' => $contrat->signature_status === 'refuse',
            ])>
                {{ str_replace('_', ' ', $contrat->signature_status) }}
            </span>
        </p>
        @if($contrat->signature_status === 'signe' && $contrat->signed_pdf_sha256)
            <p class="text-xs text-gray-400 break-all">
                Empreinte SHA-256 du PDF signé (preuve d'intégrité) : {{ $contrat->signed_pdf_sha256 }}
            </p>
        @endif
    </section>

    {{-- Bouton téléchargement --}}
    <div class="mt-6 text-center">
        <a href="{{ route('contrats.pdf', $contrat->id) }}"
           class="inline-block w-full sm:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
           Télécharger le contrat {{ $contrat->signature_status === 'signe' ? '(signé)' : '' }}
        </a>
    </div>

</div>
@endsection
