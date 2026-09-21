<?php

namespace App\Services\Ticketera\Payments;

class MercadoPagoPaymentProvider extends ExternalPaymentProvider
{
    public function key(): string
    {
        return 'mercadopago';
    }

    protected function requiredKeys(): array
    {
        return ['access_token'];
    }
}
