<?php

namespace App\Services\Loyalty\Wallet;

use App\Enums\LoyaltyWalletPlatform;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyWalletCard;

interface WalletProvider
{
    public function platform(): LoyaltyWalletPlatform;

    /**
     * ¿Están configuradas las credenciales necesarias?
     */
    public function isConfigured(): bool;

    /**
     * Genera la tarjeta del cliente en la plataforma.
     *
     * @throws WalletException cuando la integración no está configurada.
     */
    public function generate(LoyaltyMember $member): LoyaltyWalletCard;

    /**
     * Actualiza una tarjeta existente (por ejemplo, al cambiar los puntos).
     *
     * @throws WalletException cuando la integración no está configurada.
     */
    public function update(LoyaltyWalletCard $card): LoyaltyWalletCard;
}
