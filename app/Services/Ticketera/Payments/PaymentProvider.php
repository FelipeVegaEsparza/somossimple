<?php

namespace App\Services\Ticketera\Payments;

use App\Models\TicketeraOrder;

interface PaymentProvider
{
    public function key(): string;

    public function label(): string;

    public function isConfigured(): bool;

    /**
     * @throws PaymentException cuando la pasarela no está configurada.
     */
    public function charge(TicketeraOrder $order): PaymentOutcome;
}
