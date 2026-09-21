<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\TicketeraEvent;
use App\Services\Ticketera\TicketeraService;

abstract class TicketeraController extends Controller
{
    protected function business(): Business
    {
        $business = auth()->user()->business;

        abort_unless($business && $business->isModuleActive(Module::Ticketera), 404);

        return $business;
    }

    protected function service(): TicketeraService
    {
        return app(TicketeraService::class);
    }

    protected function guardEvent(Business $business, TicketeraEvent $event): TicketeraEvent
    {
        abort_unless($event->business_id === $business->id, 404);

        return $event;
    }
}
