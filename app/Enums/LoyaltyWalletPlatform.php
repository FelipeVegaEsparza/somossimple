<?php

namespace App\Enums;

enum LoyaltyWalletPlatform: string
{
    case Apple = 'apple';
    case Google = 'google';

    public function label(): string
    {
        return match ($this) {
            self::Apple => 'Apple Wallet',
            self::Google => 'Google Wallet',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Apple => 'Apple',
            self::Google => 'Google',
        };
    }
}
