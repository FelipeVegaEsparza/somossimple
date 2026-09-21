<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\PhysicalCode;
use App\Services\AnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KitResolverController extends Controller
{
    public function show(string $serial): RedirectResponse|View
    {
        $code = PhysicalCode::where('serial', $serial)->first();

        if (! $code) {
            abort(404);
        }

        if ($code->status !== PhysicalCode::STATUS_ACTIVATED || $code->business === null) {
            return view('public.kit-unactivated', ['serial' => $serial]);
        }

        app(AnalyticsService::class)->record($code->business, AnalyticsEvent::TYPE_QR_SCAN);

        return redirect()->route('p.show', $code->business->slug);
    }
}
