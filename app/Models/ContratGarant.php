<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratGarant extends Model
{
    protected $fillable = [
        'contrat_id',
        'nom',
        'cni_number',
        'telephone',
        'email',
        'lieu_residence',
        'profession',
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }
}
