<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'payment_id',
        'tenant_id',
        'total_amount',
        'generated_by',
        'generated_at',
        'documenso_envelope_id',
        'signature_status',
        'signed_pdf_path',
        'signed_pdf_sha256',
        'sent_for_signature_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'sent_for_signature_at' => 'datetime',
    ];

    // Relation avec le paiement
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Le locataire
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // L'utilisateur qui a généré le reçu (admin ou gestionnaire)
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Les périodes associées (multi-mois)
    public function periods()
    {
        return $this->hasMany(ReceiptPeriod::class);
    }
}
