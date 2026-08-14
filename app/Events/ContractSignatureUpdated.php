<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractSignatureUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $contractId,
        public string $signatureStatus,
        public ?string $signedPdfSha256 = null,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('contrat.' . $this->contractId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'contrat.signature-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'contract_id' => $this->contractId,
            'signature_status' => $this->signatureStatus,
            'signed_pdf_sha256' => $this->signedPdfSha256,
        ];
    }
}