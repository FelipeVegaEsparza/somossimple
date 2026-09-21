<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Redirección de un slug anterior de un negocio al actual (URL estable ante
 * renombrados). Los kits no se ven afectados: apuntan a /k/{serial}.
 */
class SlugAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'old_slug',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
