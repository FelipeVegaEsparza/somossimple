<?php

namespace App\Enums;

enum LoyaltyWalletStatus: string
{
    case NotAdded = 'not_added';
    case Generated = 'generated';
    case Active = 'active';
    case UpdatePending = 'update_pending';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::NotAdded => 'No agregada',
            self::Generated => 'Generada',
            self::Active => 'Activa',
            self::UpdatePending => 'Actualización pendiente',
            self::Error => 'Error',
        };
    }
}
