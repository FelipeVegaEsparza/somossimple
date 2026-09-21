<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePayment extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'module',
        'amount',
        'paid_on',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_on' => 'date',
    ];
}
