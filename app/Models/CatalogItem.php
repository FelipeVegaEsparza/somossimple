<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogItem extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'description',
        'image_path',
        'price_mode',
        'price',
        'active',
        'position',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'integer',
    ];

    public const MODE_EXACT = 'exact';

    public const MODE_FROM = 'from';

    public const MODE_NONE = 'none';

    public function category(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class);
    }

    public function priceDisplay(): string
    {
        if ($this->price_mode === self::MODE_EXACT && $this->price !== null) {
            return '$'.number_format($this->price, 0, ',', '.');
        }

        if ($this->price_mode === self::MODE_FROM && $this->price !== null) {
            return 'Desde $'.number_format($this->price, 0, ',', '.');
        }

        return 'Consultar precio';
    }
}
