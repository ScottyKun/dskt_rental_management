<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'tenant_id',
        'manager_id',
        'payment_method_id',
        'amount',
        'currency',
        'status',
        'external_reference',
        'paid_at'
    ];

    // Le locataire qui paye
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    // Le gestionnaire qui encaisse (nullable pour paiement carte)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Méthode de paiement (espèce, carte, etc.)
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Reçu(s) associés
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    // Scope : paiements confirmés
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'CONFIRMED');
    }

    // Vérifie si paiement confirmé
    public function isConfirmed(): bool
    {
        return $this->status === 'CONFIRMED';
    }
}