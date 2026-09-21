<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationSend extends Model
{
    use HasFactory;

    protected $fillable = [
        'communication_id',
        'client_id',
        'email',
        'status',
    ];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
