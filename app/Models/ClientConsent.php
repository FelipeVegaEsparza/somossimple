<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientConsent extends Model
{
    use HasFactory;

    public const CHANNEL_EMAIL = 'email';

    public const SOURCE_RESERVATION = 'reservation';

    public const SOURCE_VOLUNTARY = 'voluntary';

    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'client_id',
        'channel',
        'granted',
        'source',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
