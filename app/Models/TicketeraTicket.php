<?php

namespace App\Models;

use App\Enums\TicketeraTicketStatus;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketeraTicket extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'ticketera_tickets';

    protected $fillable = [
        'business_id',
        'event_id',
        'order_id',
        'ticket_type_id',
        'number',
        'token',
        'holder_name',
        'price',
        'status',
        'issued_at',
        'used_at',
    ];

    protected $casts = [
        'price' => 'integer',
        'status' => TicketeraTicketStatus::class,
        'issued_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketeraEvent::class, 'event_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(TicketeraOrder::class, 'order_id');
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketeraTicketType::class, 'ticket_type_id');
    }

    public function access(): HasOne
    {
        return $this->hasOne(TicketeraAccess::class, 'ticket_id');
    }

    public function status(): TicketeraTicketStatus
    {
        return $this->status instanceof TicketeraTicketStatus ? $this->status : TicketeraTicketStatus::Issued;
    }

    public function isUsable(): bool
    {
        return $this->status() === TicketeraTicketStatus::Issued;
    }
}
