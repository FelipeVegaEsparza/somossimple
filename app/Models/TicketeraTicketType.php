<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketeraTicketType extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'ticketera_ticket_types';

    protected $fillable = [
        'business_id',
        'event_id',
        'name',
        'description',
        'price',
        'stock',
        'sold',
        'sales_start_at',
        'sales_end_at',
        'purchase_limit',
        'is_active',
        'position',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'sold' => 'integer',
        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
        'purchase_limit' => 'integer',
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketeraEvent::class, 'event_id');
    }

    public function remaining(): int
    {
        return max(0, $this->stock - $this->sold);
    }

    public function isSoldOut(): bool
    {
        return $this->remaining() <= 0;
    }

    public function isOnSale(?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now();

        if (! $this->is_active || $this->isSoldOut()) {
            return false;
        }

        if ($this->sales_start_at && $this->sales_start_at->greaterThan($now)) {
            return false;
        }

        if ($this->sales_end_at && $this->sales_end_at->lessThan($now)) {
            return false;
        }

        return true;
    }

    public function priceDisplay(): string
    {
        return $this->price === 0 ? 'Gratis' : '$'.number_format($this->price, 0, ',', '.');
    }
}
