<?php

namespace App\Services\Ticketera\Payments;

/**
 * Resuelve la pasarela de pago activa. Las pasarelas reales se integran sin
 * cambiar el flujo de compra ni el modelo de datos.
 */
class PaymentManager
{
    public function defaultKey(): string
    {
        return (string) config('ticketera.payments.default', 'manual');
    }

    public function provider(?string $key = null): PaymentProvider
    {
        $key = $key ?: $this->defaultKey();

        return match ($key) {
            'webpay' => app(WebpayPaymentProvider::class),
            'mercadopago' => app(MercadoPagoPaymentProvider::class),
            'flow' => app(FlowPaymentProvider::class),
            default => app(ManualPaymentProvider::class),
        };
    }

    /**
     * @return list<array{key: string, label: string, configured: bool}>
     */
    public function providers(): array
    {
        return collect(['manual', 'webpay', 'mercadopago', 'flow'])
            ->map(function (string $key) {
                $provider = $this->provider($key);

                return ['key' => $key, 'label' => $provider->label(), 'configured' => $provider->isConfigured()];
            })
            ->all();
    }

    public function isConfigured(?string $key = null): bool
    {
        return $this->provider($key)->isConfigured();
    }
}
