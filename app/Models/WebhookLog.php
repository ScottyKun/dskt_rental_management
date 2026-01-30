<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'provider',
        'event_type',
        'external_event_id',
        'payload',
        'processed',
        'processed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    // Vérifie si déjà traité
    public function isProcessed(): bool
    {
        return $this->processed;
    }
}
