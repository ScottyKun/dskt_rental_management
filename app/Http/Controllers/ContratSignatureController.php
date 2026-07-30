<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Services\DocumensoService;
use App\Services\PdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ContratSignatureController extends Controller
{
    public function __construct(
        protected DocumensoService $documenso,
        protected PdfService $pdfService
    ) {
    }

    // Envoi du contrat pour signature bilaterale (gestionnaire/admin puis locataire)
    public function send(Contrat $contrat)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'gestionnaire'], true)) {
            abort(403, "Action réservée à un gestionnaire ou un administrateur.");
        }

        if ($contrat->document_status !== 'valide') {
            return back()->with('error', "La pièce d'identité du locataire doit être validée avant l'envoi en signature.");
        }

        if (!$contrat->tenant || !$contrat->appartement?->immeuble?->manager) {
            return back()->with('error', "Impossible de déterminer les signataires (locataire ou gestionnaire manquant).");
        }

        $manager = $contrat->appartement->immeuble->manager;

        try {
            $pdf = $this->pdfService->contratPdf($contrat);

            $result = $this->documenso->sendForSignature(
                $pdf,
                "Contrat de location #{$contrat->id}",
                [
                    ['email' => $manager->email, 'name' => $manager->name . ' ' . $manager->surname],
                    ['email' => $contrat->tenant->email, 'name' => $contrat->tenant->name . ' ' . $contrat->tenant->surname],
                ]
            );

            $contrat->update([
                'documenso_envelope_id' => $result['envelopeId'],
                'signature_status' => 'en_attente',
                'sent_for_signature_at' => now(),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Documenso (contrat) : ' . $e->getMessage());
            return back()->with('error', "Erreur lors de l'envoi vers Documenso. Réessayez plus tard.");
        }

        return redirect()->route('contrats.consult', $contrat->id)
            ->with('success', 'Contrat envoyé pour signature au bailleur et au locataire.');
    }

    // Telechargement du PDF : le signe si disponible, sinon une version generee a la volee
    public function download(Contrat $contrat)
    {
        $user = Auth::user();
        if ($user->role === 'locataire' && $user->id !== $contrat->tenant_id) {
            abort(403);
        }

        if ($contrat->signature_status === 'signe' && $contrat->signed_pdf_path) {
            return Storage::disk('minio')->download($contrat->signed_pdf_path, "contrat-{$contrat->id}-signe.pdf");
        }

        $pdf = $this->pdfService->contratPdf($contrat);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=contrat-{$contrat->id}.pdf",
        ]);
    }
}
