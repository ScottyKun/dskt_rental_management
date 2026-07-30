<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'rent_amount',
        'rent_payment_day',
        'deposit_amount',
        'status',
        'tenant_id',
        'appartement_id',
        'documenso_envelope_id',
        'signature_status',
        'signed_pdf_path',
        'signed_pdf_sha256',
        'sent_for_signature_at',
        'document_status',
        'document_requested_at',
        'document_requested_by',
    ];

    public $timestamps = true;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'sent_for_signature_at' => 'datetime',
        'document_requested_at' => 'datetime',
    ];

    //relations
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function appartement()
    {
        return $this->belongsTo(Appartement::class, 'appartement_id');
    }

    public function documents()
    {
        return $this->hasMany(ContratDocument::class);
    }

    // Le document (CNI) le plus recent soumis par le locataire
    public function latestDocument()
    {
        return $this->hasOne(ContratDocument::class)->latestOfMany();
    }

    public function documentRequestedBy()
    {
        return $this->belongsTo(User::class, 'document_requested_by');
    }
}
