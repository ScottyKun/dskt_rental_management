<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use  HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'is_validated',
        'phone',
        'address',
        'must_change_password',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_validated' => 'boolean',
    ];

    public $timestamps = true;

    public function isValidated()
    {
        return $this->is_validated== true;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function appartement()
    {
        return $this->hasOne(Appartement::class, 'locataire_id');
    }

}
