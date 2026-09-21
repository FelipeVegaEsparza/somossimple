<?php

namespace App\Enums;

enum TicketeraTicketStatus: string
{
    case Issued = 'issued';
    case Used = 'used';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Issued => 'Emitida',
            self::Used => 'Utilizada',
            self::Cancelled => 'Cancelada',
            self::Refunded => 'Reembolsada',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Issued => 'bg-primary-tint text-primary',
            self::Used => 'bg-good/15 text-good',
            self::Cancelled => 'bg-app text-ink-soft',
            self::Refunded => 'bg-red-50 text-danger',
        };
    }
}
