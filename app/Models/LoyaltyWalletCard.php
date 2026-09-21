<?php

namespace App\Models;

use App\Enums\LoyaltyWalletPlatform;
use App\Enums\LoyaltyWalletStatus;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyWalletCard extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'loyalty_member_id',
        'platform',
        'card_identifier',
        'status',
        'last_synced_at',
        'error_message',
    ];

    protected $casts = [
        'platform' => LoyaltyWalletPlatform::class,
        'status' => LoyaltyWalletStatus::class,
        'last_synced_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(LoyaltyMember::class, 'loyalty_member_id');
    }

    public function markGenerated(?string $identifier = null): self
    {
        $this->update([
            'card_identifier' => $identifier ?? $this->card_identifier,
            'status' => LoyaltyWalletStatus::Generated,
            'last_synced_at' => now(),
            'error_message' => null,
        ]);

        return $this;
    }

    public function markError(string $message): self
    {
        $this->update([
            'status' => LoyaltyWalletStatus::Error,
            'error_message' => $message,
        ]);

        return $this;
    }
}
