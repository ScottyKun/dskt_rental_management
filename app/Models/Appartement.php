<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appartement extends Model
{
     protected $fillable = [
        'name',
        'description',
        'type',
        'area',
        'status',
        'rent',
        'immeuble_id',
        'locataire_id',
        
    ];

    
    public $timestamps = true;

    public function immeuble()
    {
        return $this->belongsTo(Immeuble::class, 'immeuble_id');
    }

    public function locataire()
    {
        return $this->belongsTo(User::class, 'locataire_id');
    }


}
