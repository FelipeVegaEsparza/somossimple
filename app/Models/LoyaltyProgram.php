<?php

namespace App\Models;

use App\Enums\LoyaltyProgramType;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyProgram extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'type',
        'unit_name',
        'earn_amount',
        'earn_units',
        'is_active',
        'card_primary_color',
        'card_secondary_color',
        'card_text_primary',
        'card_text_secondary',
    ];

    protected $casts = [
        'type' => LoyaltyProgramType::class,
        'earn_amount' => 'integer',
        'earn_units' => 'integer',
        'is_active' => 'boolean',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(LoyaltyMember::class, 'business_id', 'business_id');
    }

    public function type(): LoyaltyProgramType
    {
        return $this->type instanceof LoyaltyProgramType ? $this->type : LoyaltyProgramType::Points;
    }
}
