<?php

namespace App\Models;

use App\Enums\LoyaltyProgramType;
use App\Models\Concerns\BelongsToBusiness;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'image_path',
        'requirement_type',
        'requirement_units',
        'valid_until',
        'is_active',
        'position',
    ];

    protected $casts = [
        'requirement_type' => LoyaltyProgramType::class,
        'requirement_units' => 'integer',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function isExpired(?Carbon $today = null): bool
    {
        if (! $this->valid_until) {
            return false;
        }

        return $this->valid_until->lessThan($today ?? Carbon::today());
    }

    public function isAvailableFor(LoyaltyMember $member): bool
    {
        if ($this->isExpired() || ! $this->is_active) {
            return false;
        }

        return $member->balanceFor($this->requirement_type) >= $this->requirement_units;
    }

    public function requirementDisplay(): string
    {
        $unit = $this->requirement_type->defaultUnitName();

        return number_format($this->requirement_units, 0, ',', '.').' '.$unit;
    }
}
