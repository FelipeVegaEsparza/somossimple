<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'image_path',
        'active',
        'position',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function isUpcoming(?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now();

        return $this->starts_at->greaterThanOrEqualTo($now);
    }

    public function dateDisplay(): string
    {
        return $this->starts_at->translatedFormat('l d \d\e F · H:i');
    }
}
