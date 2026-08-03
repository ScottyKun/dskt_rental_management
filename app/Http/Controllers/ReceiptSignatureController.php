<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Services\DocumensoService;
use App\Services\PdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ReceiptSignatureController extends Controller
{
    public function __construct(
        protected DocumensoService $documenso,
        protected PdfService $pdfService
    ) {
    }

    // Envoi du recu pour signature (uniquement le gestionnaire/admin qui l'a genere)
    public function send(Receipt $receipt)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'gestionnaire'], true)) {
            abort(403, "Action réservée à un gestionnaire ou un administrateur.");
        }

        $signer = $receipt->generator;
        if (!$signer) {
            return back()->with('error', "Impossible de déterminer le signataire du reçu.");
        }

        try {
            $pdf = $this->pdfService->receiptPdf($receipt);

            $result = $this->documenso->sendForSignature(
                $pdf,
                "Reçu de paiement #{$receipt->receipt_number}",
                [
                    ['email' => $signer->email, 'name' => $signer->name . ' ' . $signer->surname],
                ]
            );

            $receipt->update([
                'documenso_envelope_id' => $result['envelopeId'],
                'signature_status' => 'en_attente',
                'sent_for_signature_at' => now(),
            ]);
        } catch (RuntimeException $e) {
            Log::error('Documenso (recu) : ' . $e->getMessage());
            return back()->with('error', "Erreur lors de l'envoi vers Documenso. Réessayez plus tard.");
        }

        return redirect()->route('receipts.show', $receipt->id)
            ->with('success', 'Reçu envoyé pour signature.');
    }

    // Telechargement du PDF signe uniquement (bloque tant que la signature n'est pas complete)
    public function download(Receipt $receipt)
    {
        $user = Auth::user();
        if ($user->role === 'locataire' && $user->id !== $receipt->tenant_id) {
            abort(403);
        }

        if ($receipt->signature_status !== 'signe' || !$receipt->signed_pdf_path) {
            abort(403, "Le reçu n'a pas encore été signé.");
        }

        return Storage::disk('minio')->download($receipt->signed_pdf_path, "recu-{$receipt->receipt_number}-signe.pdf");
    }
}
