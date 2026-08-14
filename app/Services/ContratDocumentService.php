<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\ContratDocument;
use App\Notifications\ContratDocumentRequestedNotification;
use App\Notifications\ContratDocumentReviewedNotification;
use App\Repositories\ContratDocumentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ContratDocumentService
{
    public function __construct(protected ContratDocumentRepository $documentRepository)
    {
    }

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
     * Le locataire transmet sa pièce (PDF/image).
     */
    public function submit(Contrat $contrat, UploadedFile $file, string $type = 'cni'): ContratDocument
    {
        $this->assertFileIsSafe($file);

        $path = $file->store("contrats/{$contrat->id}/documents", 'minio');

        $document = $this->documentRepository->create([
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
     * Defense en profondeur, en plus de la validation MIME/extension deja faite au niveau
     * du controleur : on scanne les premiers octets du fichier a la recherche de signatures
     * de code executable (fichier "polyglotte" : ex. une image valide avec du PHP concatene).
     */
    private function assertFileIsSafe(UploadedFile $file): void
    {
        $handle = fopen($file->getRealPath(), 'rb');
        $head = $handle ? fread($handle, 8192) : '';
        if ($handle) {
            fclose($handle);
        }

        $suspiciousSignatures = ['<?php', '<?=', '<%', '<script'];

        foreach ($suspiciousSignatures as $signature) {
            if (stripos($head, $signature) !== false) {
                throw ValidationException::withMessages([
                    'document' => "Ce fichier a été refusé car il ne semble pas être un document valide.",
                ]);
            }
        }
    }

    /**
     * Le gestionnaire/admin valide la pièce transmise : le contrat devient signable.
     */
    public function validateDocument(ContratDocument $document): void
    {
        $this->documentRepository->update($document, [
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
        $this->documentRepository->update($document, [
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

    /**
     * Affichage inline (dans le navigateur, sans forcer le telechargement) -
     * utile pour previsualiser rapidement une CNI sans devoir ouvrir un fichier telecharge.
     */
    public function viewInline(ContratDocument $document)
    {
        if (!Storage::disk('minio')->exists($document->file_path)) {
            throw new RuntimeException('Fichier introuvable.');
        }

        $mimeType = Storage::disk('minio')->mimeType($document->file_path) ?? 'application/octet-stream';

        return Storage::disk('minio')->response($document->file_path, $document->original_name, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
        ]);
    }

    /**
     * Espace CNI : liste des dernieres pieces jointes par contrat, scopee par role,
     * avec recherche par nom de locataire et filtre par statut.
     */
    public function list(?string $term = null, ?string $status = null)
    {
        $user = Auth::user();
        $managerId = $user->role === 'gestionnaire' ? $user->id : null;

        return $this->documentRepository->search($term, $status, $managerId);
    }
}
