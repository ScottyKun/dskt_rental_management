@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-4xl mx-auto bg-white p-4 sm:p-8 rounded-xl shadow space-y-6">

    <div class="text-center">
        <h1 class="text-xl sm:text-2xl font-bold">CONTRAT DE BAIL</h1>
        <p class="text-sm text-gray-500 mt-1">N° {{ $contrat->numero }}</p>
    </div>

    {{-- Objet et parties --}}
    <section class="space-y-2">
        <h2 class="font-semibold">OBJET ET PARTIES</h2>
        <p>Nature du bail : Ceci est un contrat de bail à usage {{ $contrat->nature_bail ?? "d'habitation" }}</p>

        @php $manager = $contrat->appartement->immeuble->manager ?? null; @endphp
        <p class="mt-3"><strong>Le BAILLEUR :</strong></p>
        <p>Nom et Prénom : {{ $manager->name ?? '' }} {{ $manager->surname ?? '' }}</p>
        <p>CNI N° : {{ $manager->cni_number ?? '—' }}</p>
        <p>Téléphone : {{ $manager->phone ?? '—' }}</p>
        <p class="break-words">Email : {{ $manager->email ?? '' }}</p>
        <p>Lieu de résidence : {{ $manager->address ?? '—' }}</p>

        <p class="mt-3"><strong>Le LOCATAIRE :</strong></p>
        <p>Nom et Prénom : {{ $contrat->tenant->name ?? '' }} {{ $contrat->tenant->surname ?? '' }}</p>
        <p>CNI N° : {{ $contrat->tenant->cni_number ?? '—' }}</p>
        <p>Téléphone : {{ $contrat->tenant->phone ?? '—' }}</p>
        <p>Lieu de résidence : {{ $contrat->tenant->address ?? '—' }}</p>
        <p>Profession : {{ $contrat->tenant->profession ?? '—' }}</p>

        @if($contrat->garant)
            <p class="mt-3"><strong>Le GARANT du locataire :</strong></p>
            <p>Nom et Prénom : {{ $contrat->garant->nom }}</p>
            <p>CNI N° : {{ $contrat->garant->cni_number ?? '—' }}</p>
            <p>Téléphone : {{ $contrat->garant->telephone ?? '—' }}</p>
            <p>Email : {{ $contrat->garant->email ?? '—' }}</p>
            <p>Lieu de résidence : {{ $contrat->garant->lieu_residence ?? '—' }}</p>
            <p>Profession : {{ $contrat->garant->profession ?? '—' }}</p>
        @endif
    </section>

    {{-- Objet du contrat --}}
    <section class="space-y-2">
        <h2 class="font-semibold">OBJET DU CONTRAT</h2>
        <p>Adresse du logement : {{ $contrat->appartement->immeuble->address ?? '' }}, {{ $contrat->appartement->immeuble->town ?? '' }}</p>
        <p>Type d'habitat : {{ ucfirst($contrat->appartement->type ?? 'Appartement') }}</p>
        <p>Surface habitable : {{ $contrat->appartement->area ?? '' }} m²</p>
        <p>Désignation des locaux : {{ $contrat->appartement->description ?? '—' }}</p>
        <p>Destination des locaux : Usage d'habitation principale.</p>
    </section>

    {{-- Durée --}}
    <section class="space-y-2">
        <h2 class="font-semibold">DATE D'EFFET ET DURÉE</h2>
        <p>Date de prise d'effet du bail : {{ \Carbon\Carbon::parse($contrat->start_date)->format('d/m/Y') }}</p>
        <p>Date de fin : {{ \Carbon\Carbon::parse($contrat->end_date)->format('d/m/Y') }}</p>
    </section>

    {{-- Conditions financières --}}
    <section class="space-y-2">
        <h2 class="font-semibold">CONDITIONS FINANCIÈRES</h2>
        <p>Loyer mensuel : {{ number_format($contrat->rent_amount,2,',',' ') }} CFA (hors charges)</p>
        <p>Modalités de paiement : payable d'avance le {{ $contrat->rent_payment_day }} de chaque mois la 1ère année, puis chaque deux mois après la première année.</p>
    </section>

    {{-- Dépôt de garantie --}}
    <section class="space-y-2">
        <h2 class="font-semibold">DÉPÔT DE GARANTIE</h2>
        <p>Montant : {{ number_format($contrat->deposit_amount,2,',',' ') }} CFA</p>
        <p>Payable au plus tard le : {{ optional($contrat->deposit_due_date)->format('d/m/Y') ?? '—' }}</p>
    </section>

    {{-- Obligations --}}
    <section class="space-y-2">
        <h2 class="font-semibold">OBLIGATIONS DES PARTIES</h2>
        <p><strong>Le BAILLEUR :</strong> Délivrer un logement décent, assurer la jouissance paisible, entretenir les locaux, délivrer les quittances.</p>
        <p><strong>Le LOCATAIRE :</strong> Payer le loyer et charges, user paisiblement, répondre des dégradations, entretien courant, assurance locative.</p>
    </section>

    {{-- Clause résolutoire --}}
    <section>
        <h2 class="font-semibold">CLAUSE RÉSOLUTOIRE</h2>
        <p>En cas de non-paiement ou défaut d'assurance, le bail pourra être résilié de plein droit après mise en demeure.</p>
    </section>

    {{-- Annexes --}}
    <section>
        <h2 class="font-semibold">ANNEXES OBLIGATOIRES</h2>
        <ul class="list-disc ml-6">
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
        @if($contrat->garant)
            <p class="text-sm text-gray-500">Le garant du locataire : {{ $contrat->garant->nom }}</p>
        @endif
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
                    <a href="{{ route('contrats.document.view', [$contrat->id, $latestDoc->id]) }}" target="_blank" class="text-blue-600 underline ml-2">voir</a>
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

        {{-- Statut de signature, commun aux deux vues (mis a jour en direct via Reverb) --}}
        <p class="text-sm text-gray-600">
            Statut de signature :
            <span id="signature-status-label" @class([
                'font-semibold',
                'text-gray-500' => $contrat->signature_status === 'non_envoye',
                'text-amber-600' => $contrat->signature_status === 'en_attente',
                'text-green-600' => $contrat->signature_status === 'signe',
                'text-red-600' => $contrat->signature_status === 'refuse',
            ])>
                {{ str_replace('_', ' ', $contrat->signature_status) }}
            </span>
        </p>
        <p id="signature-hash" class="text-xs text-gray-400 break-all {{ $contrat->signature_status === 'signe' && $contrat->signed_pdf_sha256 ? '' : 'hidden' }}">
            Empreinte SHA-256 du PDF signé (preuve d'intégrité) : <span id="signature-hash-value">{{ $contrat->signed_pdf_sha256 }}</span>
        </p>
    </section>

    {{-- Bouton téléchargement (mis a jour en direct via Reverb) --}}
    <div class="mt-6 text-center" id="download-zone">
        @if($contrat->signature_status === 'signe')
            <a href="{{ route('contrats.pdf', $contrat->id) }}"
               class="inline-block w-full sm:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
               Télécharger le contrat (signé)
            </a>
        @else
            <button type="button" disabled
                    class="inline-block w-full sm:w-auto bg-gray-300 text-gray-500 px-6 py-2 rounded-lg cursor-not-allowed">
               Télécharger le contrat (disponible une fois signé)
            </button>
        @endif
    </div>

</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', function () {
    const waitForEcho = setInterval(function () {
        if (!window.Echo) {
            return;
        }

        clearInterval(waitForEcho);

        window.Echo
            .private('contrat.{{ $contrat->id }}')
            .listen('.contrat.signature-updated', function (data) {

                console.log('Signature du contrat mise à jour :', data);

                const label = document.getElementById('signature-status-label');
                const hashBlock = document.getElementById('signature-hash');
                const hashValue = document.getElementById('signature-hash-value');
                const downloadZone = document.getElementById('download-zone');

                const statusLabels = {
                    'non_envoye': 'non envoyé',
                    'en_attente': 'en attente',
                    'signe': 'signé',
                    'refuse': 'refusé',
                };

                const statusColors = {
                    'non_envoye': 'text-gray-500',
                    'en_attente': 'text-amber-600',
                    'signe': 'text-green-600',
                    'refuse': 'text-red-600',
                };

                if (label) {
                    label.textContent =
                        statusLabels[data.signature_status] ?? data.signature_status;

                    label.className =
                        'font-semibold ' +
                        (statusColors[data.signature_status] ?? '');
                }

                if (data.signature_status === 'signe') {

                    if (data.signed_pdf_sha256 && hashValue) {
                        hashValue.textContent = data.signed_pdf_sha256;
                        hashBlock?.classList.remove('hidden');
                    }

                    if (downloadZone) {
                        downloadZone.innerHTML = `
                            <a href="{{ route('contrats.pdf', $contrat->id) }}"
                               class="inline-block w-full sm:w-auto bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                                Télécharger le contrat (signé)
                            </a>
                        `;
                    }
                }
            });

    }, 200);
});
</script>
@endauth
@endsection