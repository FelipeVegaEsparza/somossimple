<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'description',
        'image_path',
        'price',
        'featured',
        'active',
        'position',
    ];

    protected $casts = [
        'price' => 'integer',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function priceDisplay(): ?string
    {
        return $this->price === null ? null : '$'.number_format($this->price, 0, ',', '.');
    }
}
