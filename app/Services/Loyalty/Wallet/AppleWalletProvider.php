<?php

namespace App\Services\Loyalty\Wallet;

use App\Enums\LoyaltyWalletPlatform;

class AppleWalletProvider extends AbstractWalletProvider
{
    protected function key(): string
    {
        return 'apple';
    }

    protected function requiredKeys(): array
    {
        return ['pass_type_identifier', 'team_identifier', 'certificate_path'];
    }

    public function platform(): LoyaltyWalletPlatform
    {
        return LoyaltyWalletPlatform::Apple;
    }
}
