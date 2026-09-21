<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\LoyaltyProgram;
use App\Services\Loyalty\LoyaltyService;

abstract class LoyaltyController extends Controller
{
    protected function business(): Business
    {
        $business = auth()->user()->business;

        abort_unless($business && $business->isModuleActive(Module::Loyalty), 404);

        return $business;
    }

    protected function program(Business $business): LoyaltyProgram
    {
        return app(LoyaltyService::class)->program($business);
    }
}
