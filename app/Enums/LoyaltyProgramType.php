<?php

namespace App\Enums;

enum LoyaltyProgramType: string
{
    case Points = 'points';
    case Visits = 'visits';
    case Stamps = 'stamps';
    case Rewards = 'rewards';

    public function label(): string
    {
        return match ($this) {
            self::Points => 'Puntos',
            self::Visits => 'Visitas',
            self::Stamps => 'Sellos',
            self::Rewards => 'Recompensas',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Points => 'Cada compra acumula puntos.',
            self::Visits => 'Cada visita suma una visita.',
            self::Stamps => 'Cada compra suma un sello.',
            self::Rewards => 'El cliente acumula puntos para canjear recompensas.',
        };
    }

    /**
     * Nombre por defecto de la unidad visible para el negocio y el cliente.
     */
    public function defaultUnitName(): string
    {
        return match ($this) {
            self::Points, self::Rewards => 'puntos',
            self::Visits => 'visitas',
            self::Stamps => 'sellos',
        };
    }

    /**
     * Columna del cliente que acumula este programa.
     */
    public function counter(): string
    {
        return match ($this) {
            self::Points, self::Rewards => 'points',
            self::Visits => 'visits',
            self::Stamps => 'stamps',
        };
    }
}
