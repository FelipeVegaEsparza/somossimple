<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Business;
use App\Services\AnalyticsService;
use Symfony\Component\HttpFoundation\Response;

class PublicClickController extends Controller
{
    private const ALLOWED = [
        'whatsapp' => AnalyticsEvent::TYPE_CLICK_WHATSAPP,
        'instagram' => AnalyticsEvent::TYPE_CLICK_INSTAGRAM,
        'llamar' => AnalyticsEvent::TYPE_CLICK_PHONE,
    ];

    public function track(string $slug, string $type): Response
    {
        $event = self::ALLOWED[$type] ?? null;

        if ($event === null) {
            return response('', 404);
        }

        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable()) {
            abort(404);
        }

        app(AnalyticsService::class)->record($business, $event);

        return response('', 204);
    }
}
