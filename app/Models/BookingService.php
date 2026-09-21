<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingService extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'duration_minutes',
        'price',
        'active',
        'position',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'integer',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'service_id');
    }

    public function priceDisplay(): ?string
    {
        return $this->price === null ? null : '$'.number_format($this->price, 0, ',', '.');
    }
}
