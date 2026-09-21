<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'price_monthly',
    ];

    protected $casts = [
        'price_monthly' => 'integer',
    ];
}
