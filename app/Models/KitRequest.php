<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Solicitud de kit desde la landing: llega al administrador para contactar
 * y concretar la venta (sin checkout online).
 */
class KitRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_name',
        'whatsapp',
        'email',
        'quantity',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];
}
