<?php

namespace App\Models;

use App\Enums\LoyaltyProgramType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class LoyaltyMember extends Model
{
    use BelongsToBusiness, HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'business_id',
        'code',
        'token',
        'name',
        'phone',
        'email',
        'points',
        'visits',
        'stamps',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'visits' => 'integer',
        'stamps' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'business_id', 'business_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LoyaltyActivity::class)->latest();
    }

    public function walletCards(): HasMany
    {
        return $this->hasMany(LoyaltyWalletCard::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Valor acumulado en la columna que corresponde al tipo de programa.
     */
    public function balanceFor(LoyaltyProgramType $type): int
    {
        return (int) $this->{$type->counter()};
    }

    /**
     * Recompensas activas y vigentes que el cliente ya puede canjear.
     *
     * @return Collection<int, LoyaltyReward>
     */
    public function availableRewards()
    {
        return LoyaltyReward::ofBusiness($this->business)
            ->where('is_active', true)
            ->get()
            ->filter(fn (LoyaltyReward $reward) => $reward->isAvailableFor($this))
            ->values();
    }

    /**
     * Unidades que le faltan para la próxima recompensa (null si ya tiene alguna).
     */
    public function unitsToNextReward(): ?int
    {
        $reward = LoyaltyReward::ofBusiness($this->business)
            ->where('is_active', true)
            ->get()
            ->filter(fn (LoyaltyReward $reward) => ! $reward->isAvailableFor($this))
            ->sortBy('requirement_units')
            ->first();

        if (! $reward) {
            return null;
        }

        return max(0, $reward->requirement_units - $this->balanceFor($reward->requirement_type));
    }
}
