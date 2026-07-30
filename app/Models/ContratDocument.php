<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratDocument extends Model
{
    protected $fillable = [
        'contrat_id',
        'type',
        'file_path',
        'original_name',
        'uploaded_by',
        'status',
        'validated_by',
        'validated_at',
        'rejection_reason',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
