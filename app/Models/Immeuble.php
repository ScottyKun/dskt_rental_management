<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Immeuble extends Model
{
    use  HasFactory;

    protected $fillable = [
        'name',
        'address',
        'town',
        'description',
        'nb_apartments',
        'nb_available',
        'nb_occupied',
        'status',
        'creator_id',
        'manager_id',
        
    ];

    
    public $timestamps = true;

    public function creator()
    {
        return $this->belongsTo(User::class,'creator_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }

    public function appartements()
    {
        return $this->hasMany(Appartement::class, 'immeuble_id');
    }
}
