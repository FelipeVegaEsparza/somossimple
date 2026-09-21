<?php

namespace App\Services\Loyalty\Wallet;

use App\Models\LoyaltyMember;
use App\Models\LoyaltyWalletCard;

abstract class AbstractWalletProvider implements WalletProvider
{
    /**
     * Clave de configuración bajo loyalty.wallet.{key}.
     */
    abstract protected function key(): string;

    /**
     * Claves que deben estar presentes para considerar la integración lista.
     *
     * @return list<string>
     */
    abstract protected function requiredKeys(): array;

    /**
     * @return array<string, mixed>
     */
    public function config(): array
    {
        return config("loyalty.wallet.{$this->key()}", []);
    }

    public function isConfigured(): bool
    {
        $config = $this->config();

        if (! ($config['enabled'] ?? false)) {
            return false;
        }

        foreach ($this->requiredKeys() as $key) {
            if (empty($config[$key])) {
                return false;
            }
        }

        return true;
    }

    public function generate(LoyaltyMember $member): LoyaltyWalletCard
    {
        $this->ensureConfigured();

        throw new WalletException(
            $this->platform()->label().' está configurado, pero la generación de pases aún no está implementada.'
        );
    }

    public function update(LoyaltyWalletCard $card): LoyaltyWalletCard
    {
        $this->ensureConfigured();

        throw new WalletException('La actualización de pases de '.$this->platform()->label().' aún no está implementada.');
    }

    protected function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new WalletException($this->platform()->label().' aún no está configurado.');
        }
    }
}
