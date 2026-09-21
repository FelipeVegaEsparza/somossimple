<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'image_path',
        'price',
        'duration_minutes',
        'active',
        'position',
    ];

    protected $casts = [
        'price' => 'integer',
        'duration_minutes' => 'integer',
        'active' => 'boolean',
    ];

    public function priceDisplay(): ?string
    {
        return $this->price === null ? null : '$'.number_format($this->price, 0, ',', '.');
    }

    public function durationDisplay(): ?string
    {
        if ($this->duration_minutes === null) {
            return null;
        }

        if ($this->duration_minutes < 60) {
            return $this->duration_minutes.' min';
        }

        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        return $minutes === 0 ? $hours.' h' : $hours.' h '.$minutes.' min';
    }
}
