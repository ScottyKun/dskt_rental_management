<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptPeriod extends Model
{
    protected $fillable = [
        'receipt_id',
        'period_start',
        'period_end'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}