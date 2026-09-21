<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketeraAccess extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'ticketera_accesses';

    protected $fillable = [
        'business_id',
        'event_id',
        'ticket_id',
        'user_id',
        'staff_id',
        'device',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketeraTicket::class, 'ticket_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(TicketeraEvent::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(TicketeraStaff::class, 'staff_id');
    }
}
