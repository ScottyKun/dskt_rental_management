<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Receipt;
use App\Services\DocumensoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumensoWebhookController extends Controller
{
    public function __construct(protected DocumensoService $documenso)
    {
    }

    public function handle(Request $request)
    {
        $secret = config('services.documenso.webhook_secret');
        $received = $request->header('X-Documenso-Secret', '');

        if (!$secret || !hash_equals($secret, (string) $received)) {
            Log::warning('Webhook Documenso rejeté : secret invalide.');
            abort(401);
        }

        $event = $request->input('event');
        $envelopeId = $request->input('payload.envelopeId');

        if (!$envelopeId) {
            return response()->json(['ignored' => true], 200);
        }

        // On cherche l'enveloppe côté contrat, sinon côté reçu
        $contrat = Contrat::where('documenso_envelope_id', $envelopeId)->first();
        $receipt = $contrat ? null : Receipt::where('documenso_envelope_id', $envelopeId)->first();

        $target = $contrat ?? $receipt;

        if (!$target) {
            Log::info("Webhook Documenso : enveloppe {$envelopeId} inconnue, ignorée.");
            return response()->json(['ignored' => true], 200);
        }

        switch ($event) {
            case 'DOCUMENT_COMPLETED':
            case 'document.completed':
                $this->handleCompleted($target, $envelopeId);
                break;

            case 'DOCUMENT_REJECTED':
            case 'document.rejected':
                $target->update(['signature_status' => 'refuse']);
                break;

            default:
                // document.sent / document.opened / document.created : rien à faire de plus
                break;
        }

        return response()->json(['ok' => true]);
    }

    private function handleCompleted(Contrat|Receipt $target, string $envelopeId): void
    {
        $prefix = $target instanceof Contrat ? 'contrats' : 'receipts';
        $storeAs = "{$prefix}/{$target->id}/signed-" . now()->timestamp . '.pdf';

        try {
            $result = $this->documenso->downloadSignedDocument($envelopeId, $storeAs);

            $target->update([
                'signature_status' => 'signe',
                'signed_pdf_path' => $result['path'],
                'signed_pdf_sha256' => $result['sha256'],
            ]);
        } catch (\Throwable $e) {
            Log::error("Erreur téléchargement document signé Documenso ({$envelopeId}) : " . $e->getMessage());
        }
    }
}
