<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\ContratDocument;
use App\Services\ContratDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratDocumentController extends Controller
{
    public function __construct(protected ContratDocumentService $documentService)
    {
    }

    // Espace CNI : liste de toutes les pieces jointes par locataire/contrat (admin/gestionnaire)
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'gestionnaire'], true)) {
            abort(403, "Action réservée à un gestionnaire ou un administrateur.");
        }

        $documents = $this->documentService->list(
            $request->query('q'),
            $request->query('status')
        );

        return view('contrats.documents', compact('documents'));
    }

    // Gestionnaire/admin demande une piece au locataire
    public function request(Contrat $contrat)
    {
        $this->authorizeManagement($contrat);

        $this->documentService->request($contrat);

        return redirect()->route('contrats.consult', $contrat->id)
            ->with('success', 'Demande de pièce envoyée au locataire.');
    }

    // Locataire transmet sa piece (CNI)
    public function store(Request $request, Contrat $contrat)
    {
        $user = Auth::user();
        if ($user->role !== 'locataire' || $user->id !== $contrat->tenant_id) {
            abort(403, "Vous ne pouvez pas transmettre de document pour ce contrat.");
        }

        $request->validate([
            // 'mimes' verifie l'extension, 'mimetypes' verifie le contenu reel du fichier (finfo) :
            // les deux sont necessaires, un fichier .pdf renomme depuis un .php echouerait sur mimetypes.
            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'mimetypes:application/pdf,image/jpeg,image/png',
                'max:5120',
            ],
        ]);

        $this->documentService->submit($contrat, $request->file('document'));

        return redirect()->route('contrats.consult', $contrat->id)
            ->with('success', 'Document transmis, en attente de validation par votre gestionnaire.');
    }

    // Gestionnaire/admin valide la piece
    public function validateDoc(Contrat $contrat, ContratDocument $document)
    {
        $this->authorizeManagement($contrat);
        $this->guardBelongsToContrat($contrat, $document);

        $this->documentService->validateDocument($document);

        return redirect()->route('contrats.consult', $contrat->id)
            ->with('success', 'Document validé. Le contrat peut être envoyé pour signature.');
    }

    // Gestionnaire/admin refuse la piece
    public function reject(Request $request, Contrat $contrat, ContratDocument $document)
    {
        $this->authorizeManagement($contrat);
        $this->guardBelongsToContrat($contrat, $document);

        $request->validate(['reason' => 'required|string|max:255']);

        $this->documentService->reject($document, $request->input('reason'));

        return redirect()->route('contrats.consult', $contrat->id)
            ->with('success', 'Document refusé, le locataire a été notifié.');
    }

    // Telechargement (gestionnaire/admin, ou le locataire proprietaire)
    public function download(Contrat $contrat, ContratDocument $document)
    {
        $this->guardBelongsToContrat($contrat, $document);

        $user = Auth::user();
        if ($user->role === 'locataire' && $user->id !== $contrat->tenant_id) {
            abort(403);
        }

        return $this->documentService->download($document);
    }

    // Previsualisation inline (voir sans telecharger), meme regle d'acces que le download
    public function viewInline(Contrat $contrat, ContratDocument $document)
    {
        $this->guardBelongsToContrat($contrat, $document);

        $user = Auth::user();
        if ($user->role === 'locataire' && $user->id !== $contrat->tenant_id) {
            abort(403);
        }

        return $this->documentService->viewInline($document);
    }

    private function authorizeManagement(Contrat $contrat): void
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'gestionnaire'], true)) {
            abort(403, "Action réservée à un gestionnaire ou un administrateur.");
        }
    }

    private function guardBelongsToContrat(Contrat $contrat, ContratDocument $document): void
    {
        if ($document->contrat_id !== $contrat->id) {
            abort(404);
        }
    }
}
