<?php

namespace App\Models;

use App\Enums\LoyaltyActivityType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyActivity extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'loyalty_member_id',
        'reward_id',
        'user_id',
        'type',
        'units',
        'reason',
    ];

    protected $casts = [
        'type' => LoyaltyActivityType::class,
        'units' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(LoyaltyMember::class, 'loyalty_member_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(LoyaltyReward::class, 'reward_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unitsDisplay(): string
    {
        if ($this->units === 0) {
            return '—';
        }

        return ($this->units > 0 ? '+' : '−').abs($this->units);
    }
}
