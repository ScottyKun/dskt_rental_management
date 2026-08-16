<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Receipt;
use App\Events\ContractSignatureUpdated;
use App\Repositories\UserRepository;
use App\Services\DocumensoService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumensoWebhookController extends Controller
{
    public function __construct(
        protected DocumensoService $documenso,
        protected MessageService $messageService,
        protected UserRepository $userRepository
    ) {
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
                if ($target instanceof Contrat) {
                    try {
                        event(new ContractSignatureUpdated($target->id, 'refuse'));
                    } catch (\Throwable $e) {
                        Log::warning('Diffusion Reverb (signature refusée) échouée, ignorée : ' . $e->getMessage());
                    }
                    $this->notifySignatureUpdate($target, false);
                }
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

            if ($target instanceof Contrat) {
                try {
                    event(new ContractSignatureUpdated($target->id, 'signe', $result['sha256']));
                } catch (\Throwable $e) {
                    Log::warning('Diffusion Reverb (signature complétée) échouée, ignorée : ' . $e->getMessage());
                }
                $this->notifySignatureUpdate($target, true);
            }
        } catch (\Throwable $e) {
            Log::error("Erreur téléchargement document signé Documenso ({$envelopeId}) : " . $e->getMessage());
        }
    }

    /**
     * Notification in-app (centre de notifications + temps reel + push) au locataire,
     * au gestionnaire de l'immeuble et aux admins, quand un contrat est signe ou refuse.
     */
    private function notifySignatureUpdate(Contrat $contrat, bool $signed): void
    {
        $tenant = $contrat->tenant;
        if (!$tenant) {
            return;
        }

        $title = $signed ? 'Contrat signé' : 'Signature refusée';
        $content = $signed
            ? "Le contrat n°{$contrat->numero} a été signé électroniquement par toutes les parties."
            : "La signature électronique du contrat n°{$contrat->numero} a été refusée.";

        $recipients = $this->userRepository->getAdminsAndTenantManager($tenant)->push($tenant);

        foreach ($recipients->unique('id') as $recipient) {
            $this->messageService->notifyInApp($recipient->id, null, $title, $content);
        }
    }
}