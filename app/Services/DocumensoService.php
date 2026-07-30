<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DocumensoService
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.documenso.base_url'), '/');
        $this->apiKey = config('services.documenso.api_key');
    }

    protected function http()
    {
        if (!$this->apiKey) {
            throw new RuntimeException("DOCUMENSO_API_KEY n'est pas configurée.");
        }

        return Http::withHeaders(['Authorization' => $this->apiKey])->baseUrl($this->baseUrl . '/api/v2');
    }

    /**
     * Cree une enveloppe a partir d'un PDF (binaire) et l'envoie immediatement aux destinataires.
     *
     * @param string $pdfContent Contenu binaire du PDF
     * @param string $title
     * @param array<int, array{email:string,name:string}> $recipients Ordonnes: index 0 = r1, index 1 = r2...
     * @return array{envelopeId:string}
     */
    public function sendForSignature(string $pdfContent, string $title, array $recipients): array
    {
        $payload = [
            'type' => 'DOCUMENT',
            'title' => $title,
            'recipients' => array_map(fn($r, $i) => [
                'email' => $r['email'],
                'name' => $r['name'],
                'role' => 'SIGNER',
                'signingOrder' => $i + 1,
            ], $recipients, array_keys($recipients)),
        ];

        $response = $this->http()
            ->attach('files', $pdfContent, 'document.pdf', ['Content-Type' => 'application/pdf'])
            ->post('/envelope/create', ['payload' => json_encode($payload)]);

        if ($response->failed()) {
            throw new RuntimeException('Erreur Documenso (create): ' . $response->body());
        }

        $envelopeId = $response->json('id');

        // Distribution immediate aux destinataires (envoi des emails de signature)
        $distribute = $this->http()->post('/envelope/distribute', [
            'envelopeId' => $envelopeId,
        ]);

        if ($distribute->failed()) {
            throw new RuntimeException('Erreur Documenso (distribute): ' . $distribute->body());
        }

        return ['envelopeId' => $envelopeId];
    }

    /**
     * Recupere le PDF signe d'une enveloppe completee, le stocke sur MinIO et calcule
     * son empreinte SHA-256 (preuve d'integrite, independante de Documenso).
     *
     * @return array{path: string, sha256: string}
     */
    public function downloadSignedDocument(string $envelopeId, string $storeAs): array
    {
        $envelope = $this->http()->get("/envelope/{$envelopeId}");

        if ($envelope->failed()) {
            throw new RuntimeException('Erreur Documenso (get envelope): ' . $envelope->body());
        }

        $itemId = $envelope->json('envelopeItems.0.id');

        if (!$itemId) {
            throw new RuntimeException("Impossible de trouver le document de l'enveloppe {$envelopeId}");
        }

        $download = $this->http()->get("/envelope/item/{$itemId}/download");

        if ($download->failed()) {
            throw new RuntimeException('Erreur Documenso (download): ' . $download->body());
        }

        // Selon la version de l'API, la reponse est soit le binaire, soit {"downloadUrl": "..."}
        $contentType = $download->header('Content-Type');
        if (str_contains((string) $contentType, 'application/json')) {
            $url = $download->json('downloadUrl');
            $pdf = Http::get($url)->body();
        } else {
            $pdf = $download->body();
        }

        $sha256 = hash('sha256', $pdf);

        Storage::disk('minio')->put($storeAs, $pdf);

        return ['path' => $storeAs, 'sha256' => $sha256];
    }
}
