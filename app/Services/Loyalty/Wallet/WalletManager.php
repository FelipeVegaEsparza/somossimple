<?php

namespace App\Services\Loyalty\Wallet;

use App\Enums\LoyaltyWalletPlatform;

/**
 * Resuelve el proveedor de Wallet de cada plataforma. La integración real
 * queda desacoplada: agregar soporte no requiere tocar el módulo de fidelización.
 */
class WalletManager
{
    public function provider(LoyaltyWalletPlatform $platform): WalletProvider
    {
        return match ($platform) {
            LoyaltyWalletPlatform::Apple => app(AppleWalletProvider::class),
            LoyaltyWalletPlatform::Google => app(GoogleWalletProvider::class),
        };
    }

    public function isConfigured(LoyaltyWalletPlatform $platform): bool
    {
        return $this->provider($platform)->isConfigured();
    }
}
