<?php

namespace App\Services\Loyalty\Wallet;

use App\Enums\LoyaltyWalletPlatform;

class GoogleWalletProvider extends AbstractWalletProvider
{
    protected function key(): string
    {
        return 'google';
    }

    protected function requiredKeys(): array
    {
        return ['issuer_id', 'service_account_path'];
    }

    public function platform(): LoyaltyWalletPlatform
    {
        return LoyaltyWalletPlatform::Google;
    }
}
