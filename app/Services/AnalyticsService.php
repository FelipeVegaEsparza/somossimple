<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\Business;

class AnalyticsService
{
    public function record(Business $business, string $type): void
    {
        AnalyticsEvent::create([
            'business_id' => $business->id,
            'type' => $type,
        ]);
    }

    public function counts(Business $business): array
    {
        $events = AnalyticsEvent::ofBusiness($business)
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return [
            'visits' => (int) $events->get(AnalyticsEvent::TYPE_PROFILE_VISIT, 0),
            'qr' => (int) $events->get(AnalyticsEvent::TYPE_QR_SCAN, 0),
            'nfc' => (int) $events->get(AnalyticsEvent::TYPE_NFC_TAP, 0),
            'clicks' => (int) $events->get(AnalyticsEvent::TYPE_CLICK_WHATSAPP, 0)
                + (int) $events->get(AnalyticsEvent::TYPE_CLICK_INSTAGRAM, 0)
                + (int) $events->get(AnalyticsEvent::TYPE_CLICK_PHONE, 0),
            'reservations' => (int) $events->get(AnalyticsEvent::TYPE_RESERVATION, 0),
        ];
    }
}
