<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'phone_normalized',
        'email',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ClientTag::class, 'client_tag', 'client_id', 'tag_id');
    }

    public function consents(): HasMany
    {
        return $this->hasMany(ClientConsent::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class)->latest();
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function hasEmailConsent(): bool
    {
        return $this->consents()->where('channel', 'email')->where('granted', true)->exists();
    }
}
