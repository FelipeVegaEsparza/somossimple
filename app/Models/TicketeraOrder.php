<?php

namespace App\Models;

use App\Enums\TicketeraOrderStatus;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketeraOrder extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'ticketera_orders';

    protected $fillable = [
        'business_id',
        'event_id',
        'number',
        'buyer_name',
        'buyer_lastname',
        'buyer_email',
        'buyer_phone',
        'subtotal',
        'commission',
        'total',
        'status',
        'payment_provider',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'commission' => 'integer',
        'total' => 'integer',
        'status' => TicketeraOrderStatus::class,
        'paid_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketeraEvent::class, 'event_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TicketeraTicket::class, 'order_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(TicketeraRefund::class, 'order_id');
    }

    public function status(): TicketeraOrderStatus
    {
        return $this->status instanceof TicketeraOrderStatus ? $this->status : TicketeraOrderStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->status() === TicketeraOrderStatus::Paid;
    }

    public function buyerFullName(): string
    {
        return trim($this->buyer_name.' '.($this->buyer_lastname ?? ''));
    }
}
