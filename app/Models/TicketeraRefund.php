<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketeraRefund extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'ticketera_refunds';

    protected $fillable = [
        'business_id',
        'order_id',
        'reason',
        'amount',
        'status',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(TicketeraOrder::class, 'order_id');
    }
}
