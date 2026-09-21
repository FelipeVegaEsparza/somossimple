<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Imagen de la galería del perfil de un negocio.
 */
class BusinessGallery extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'business_gallery';

    protected $fillable = [
        'business_id',
        'path',
        'position',
    ];
}
