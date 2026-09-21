<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kit físico vendible (tótem QR + NFC): disponible -> vendido -> activado.
 * El QR y la NFC del kit llevan la URL genérica /k/{serial}.
 */
class PhysicalCode extends Model
{
    use HasFactory;

    public const TYPE_QR = 'qr';

    public const TYPE_NFC = 'nfc';

    public const TYPE_QR_NFC = 'qr_nfc';

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_SOLD = 'sold';

    public const STATUS_ACTIVATED = 'activated';

    public static function statuses(): array
    {
        return [self::STATUS_AVAILABLE, self::STATUS_SOLD, self::STATUS_ACTIVATED];
    }

    protected $fillable = [
        'type',
        'serial',
        'status',
        'business_id',
        'delivered_at',
        'activated_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_NFC => 'NFC',
            self::TYPE_QR_NFC => 'QR + NFC',
            default => 'QR',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SOLD => 'Vendido',
            self::STATUS_ACTIVATED => 'Activado',
            default => 'Disponible',
        };
    }
}
