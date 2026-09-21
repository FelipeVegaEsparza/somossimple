<?php

namespace App\Services\Ticketera\Payments;

class PaymentOutcome
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $reference = null,
        public readonly ?string $message = null,
    ) {}

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
