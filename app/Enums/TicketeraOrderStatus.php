<?php

namespace App\Enums;

enum TicketeraOrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Paid => 'Pagada',
            self::Cancelled => 'Cancelada',
            self::Refunded => 'Reembolsada',
            self::Failed => 'Fallida',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'bg-[#fff6e0] text-warn',
            self::Paid => 'bg-good/15 text-good',
            self::Cancelled => 'bg-app text-ink-soft',
            self::Refunded => 'bg-app text-ink-soft',
            self::Failed => 'bg-red-50 text-danger',
        };
    }
}
