<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * El catálogo es único para productos, servicios o menú.
 */
class CatalogCategory extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'position',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'category_id')->orderBy('position');
    }
}
