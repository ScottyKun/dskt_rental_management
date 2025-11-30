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
    ];

    public $timestamps = true;

    //relations
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function appartement()
    {
        return $this->belongsTo(Appartement::class, 'appartement_id');
    }
}