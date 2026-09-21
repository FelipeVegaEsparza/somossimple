<?php

namespace App\Enums;

enum TicketeraEventStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Paused = 'paused';
    case Finished = 'finished';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Published => 'Publicado',
            self::Paused => 'Pausado',
            self::Finished => 'Finalizado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Draft => 'bg-app text-ink-soft',
            self::Published => 'bg-good/15 text-good',
            self::Paused => 'bg-[#fff6e0] text-warn',
            self::Finished => 'bg-app text-ink-soft',
            self::Cancelled => 'bg-red-50 text-danger',
        };
    }
}
