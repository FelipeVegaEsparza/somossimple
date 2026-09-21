<?php

namespace App\Services;

use App\Models\BookingService;
use App\Models\Business;
use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

/**
 * Calcula las horas disponibles de un servicio para una fecha, según el
 * horario semanal del negocio, los días no disponibles y las reservas
 * bloqueantes (pendiente y confirmada).
 *
 * Agenda única por negocio: sin empleados ni recursos (MVP).
 */
class BookingAvailability
{
    public function availableTimes(Business $business, BookingService $service, CarbonImmutable $date): array
    {
        if ($business->bookingDayOffs()->whereDate('date', $date)->exists()) {
            return [];
        }

        $weekHours = $business->bookingWeekHours()
            ->where('day_of_week', $date->dayOfWeekIso - 1)
            ->orderBy('open_time')
            ->get();

        if ($weekHours->isEmpty()) {
            return [];
        }

        $startOfDay = $date->startOfDay();

        $blocked = Reservation::ofBusiness($business)
            ->blocking()
            ->whereBetween('starts_at', [
                $startOfDay->format('Y-m-d 00:00:00'),
                $startOfDay->addDay()->format('Y-m-d 00:00:00'),
            ])
            ->get()
            ->map(fn (Reservation $r) => [
                'start' => $r->starts_at,
                'end' => $r->starts_at->copy()->addMinutes($r->duration_minutes),
            ]);

        $times = [];

        foreach ($weekHours as $range) {
            $open = $startOfDay->setTimeFromTimeString($range->open_time);
            $close = $startOfDay->setTimeFromTimeString($range->close_time);

            if ($close->lessThanOrEqualTo($open)) {
                continue;
            }

            foreach (CarbonPeriod::create($open, '15 minutes', $close)->excludeEndDate() as $slot) {
                $end = $slot->copy()->addMinutes($service->duration_minutes);

                if ($end->greaterThan($close)) {
                    break;
                }

                $overlaps = $blocked->contains(fn ($b) => $slot->lt($b['end']) && $end->gt($b['start']));

                if (! $overlaps) {
                    $times[] = $slot->format('H:i');
                }
            }
        }

        return array_values(array_unique($times));
    }

    public function isFree(Business $business, BookingService $service, Carbon $startsAt): bool
    {
        $date = CarbonImmutable::instance($startsAt)->startOfDay();

        return in_array($startsAt->format('H:i'), $this->availableTimes($business, $service, $date), true);
    }
}
