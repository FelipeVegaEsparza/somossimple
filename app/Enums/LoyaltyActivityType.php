<?php

namespace App\Enums;

enum LoyaltyActivityType: string
{
    case EarnPoints = 'earn_points';
    case RedeemPoints = 'redeem_points';
    case EarnVisit = 'earn_visit';
    case EarnStamp = 'earn_stamp';
    case RewardEarned = 'reward_earned';
    case RewardRedeemed = 'reward_redeemed';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::EarnPoints => 'Puntos sumados',
            self::RedeemPoints => 'Puntos canjeados',
            self::EarnVisit => 'Visita registrada',
            self::EarnStamp => 'Sello registrado',
            self::RewardEarned => 'Recompensa obtenida',
            self::RewardRedeemed => 'Recompensa canjeada',
            self::Adjustment => 'Ajuste manual',
        };
    }

    public function isCredit(): bool
    {
        return in_array($this, [self::EarnPoints, self::EarnVisit, self::EarnStamp], true);
    }

    /**
     * Unidad que afecta esta operación en la ficha del cliente.
     */
    public function counter(): ?string
    {
        return match ($this) {
            self::EarnPoints, self::RedeemPoints, self::Adjustment => 'points',
            self::EarnVisit => 'visits',
            self::EarnStamp => 'stamps',
            self::RewardEarned, self::RewardRedeemed => null,
        };
    }
}
