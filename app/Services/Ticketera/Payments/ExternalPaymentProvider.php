<?php

namespace App\Services\Ticketera\Payments;

use App\Models\TicketeraOrder;

abstract class ExternalPaymentProvider implements PaymentProvider
{
    /**
     * @return list<string>
     */
    abstract protected function requiredKeys(): array;

    public function label(): string
    {
        return (string) config("ticketera.payments.providers.{$this->key()}.label", $this->key());
    }

    /**
     * @return array<string, mixed>
     */
    protected function config(): array
    {
        return config("ticketera.payments.providers.{$this->key()}", []);
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

    public function charge(TicketeraOrder $order): PaymentOutcome
    {
        if (! $this->isConfigured()) {
            throw new PaymentException($this->label().' aún no está configurado.');
        }

        throw new PaymentException($this->label().' está configurado, pero la integración de cobro aún no está implementada.');
    }
}
