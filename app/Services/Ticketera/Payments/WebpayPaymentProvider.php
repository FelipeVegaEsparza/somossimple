<?php

namespace App\Services\Ticketera\Payments;

class WebpayPaymentProvider extends ExternalPaymentProvider
{
    public function key(): string
    {
        return 'webpay';
    }

    protected function requiredKeys(): array
    {
        return ['commerce_code', 'api_key'];
    }
}
