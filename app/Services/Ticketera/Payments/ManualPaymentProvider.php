<?php

namespace App\Services\Ticketera\Payments;

use App\Models\TicketeraOrder;

class ManualPaymentProvider implements PaymentProvider
{
    public function key(): string
    {
        return 'manual';
    }

    public function label(): string
    {
        return (string) config('ticketera.payments.providers.manual.label', 'Pago manual');
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function charge(TicketeraOrder $order): PaymentOutcome
    {
        return new PaymentOutcome('paid', 'MANUAL', 'Pago registrado en modo manual.');
    }
}
