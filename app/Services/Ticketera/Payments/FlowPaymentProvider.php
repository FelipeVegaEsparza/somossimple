<?php

namespace App\Services\Ticketera\Payments;

class FlowPaymentProvider extends ExternalPaymentProvider
{
    public function key(): string
    {
        return 'flow';
    }

    protected function requiredKeys(): array
    {
        return ['api_key', 'secret_key'];
    }
}
