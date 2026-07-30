<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\ContratDocument;
use App\Notifications\ContratDocumentRequestedNotification;
use App\Notifications\ContratDocumentReviewedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ContratDocumentService
{
    /**
     * Le gestionnaire/admin demande au locataire de transmettre une pièce (ex: CNI).
     */
    public function request(Contrat $contrat): void
    {
        $contrat->update([
            'document_status' => 'demande',
            'document_requested_at' => now(),
            'document_requested_by' => Auth::id(),
        ]);

        if ($contrat->tenant) {
            $contrat->tenant->notify(new ContratDocumentRequestedNotification($contrat));
        }
    }

    /**
     * Le locataire transmet sa pièce (PDF).
     */
    public function submit(Contrat $contrat, UploadedFile $file, string $type = 'cni'): ContratDocument
    {
        $path = $file->store("contrats/{$contrat->id}/documents", 'minio');

        $document = ContratDocument::create([
            'contrat_id' => $contrat->id,
            'type' => $type,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_by' => Auth::id(),
            'status' => 'en_attente',
        ]);

        $contrat->update(['document_status' => 'soumis']);

        return $document;
    }

    /**
     * Le gestionnaire/admin valide la pièce transmise : le contrat devient signable.
     */
    public function validateDocument(ContratDocument $document): void
    {
        $document->update([
            'status' => 'valide',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'rejection_reason' => null,
        ]);

        $document->contrat->update(['document_status' => 'valide']);

        if ($document->contrat->tenant) {
            $document->contrat->tenant->notify(new ContratDocumentReviewedNotification($document, true));
        }
    }

    /**
     * Le gestionnaire/admin refuse la pièce : le locataire doit en soumettre une nouvelle.
     */
    public function reject(ContratDocument $document, string $reason): void
    {
        $document->update([
            'status' => 'refuse',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $document->contrat->update(['document_status' => 'demande']);

        if ($document->contrat->tenant) {
            $document->contrat->tenant->notify(new ContratDocumentReviewedNotification($document, false));
        }
    }

    public function download(ContratDocument $document)
    {
        if (!Storage::disk('minio')->exists($document->file_path)) {
            throw new RuntimeException('Fichier introuvable.');
        }

        return Storage::disk('minio')->download($document->file_path, $document->original_name);
    }
}
