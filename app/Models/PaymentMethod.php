<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',   // CASH, CARD
        'label',  // Espèces, Carte
        'is_active'
    ];

    // Relation : un PaymentMethod peut avoir plusieurs paiements
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}