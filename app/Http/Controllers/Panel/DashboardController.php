<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Business;
use App\Models\ModulePrice;
use App\Models\PhysicalCode;
use App\Models\Reservation;
use App\Services\AnalyticsService;
use App\Services\BillingService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $business = auth()->user()->business;

        if ($business) {
            $stats = app(AnalyticsService::class)->counts($business);
            $clients = $business->clients()->count();
            $kitsActivos = PhysicalCode::where('business_id', $business->id)
                ->where('status', PhysicalCode::STATUS_ACTIVATED)
                ->count();

            $proximas = Reservation::ofBusiness($business)
                ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(4)
                ->get();

            $weekly = $this->weeklyVisits($business, 7);
            $monthly = $this->weeklyVisits($business, 30);

            $activePaid = collect(Module::cases())
                ->reject(fn ($m) => $m->isFree())
                ->filter(fn ($m) => $business->isModuleActive($m))
                ->values();

            $totalPaid = collect(Module::cases())->reject(fn ($m) => $m->isFree())->count();

            $atrasado = $this->hasPagoAtrasado($business);
        } else {
            $stats = ['visits' => 0, 'qr' => 0, 'nfc' => 0, 'clicks' => 0, 'reservations' => 0];
            $clients = 0;
            $kitsActivos = 0;
            $proximas = collect();
            $weekly = collect();
            $monthly = collect();
            $activePaid = collect();
            $totalPaid = 0;
            $atrasado = false;
        }

        return view('panel.index', [
            'business' => $business,
            'stats' => $stats,
            'clients' => $clients,
            'kitsActivos' => $kitsActivos,
            'proximas' => $proximas,
            'weekly' => $weekly,
            'monthly' => $monthly,
            'weekMax' => $weekly->max('value') ?: 1,
            'monthMax' => $monthly->max('value') ?: 1,
            'activePaid' => $activePaid,
            'totalPaid' => $totalPaid,
            'atrasado' => $atrasado,
        ]);
    }

    private function weeklyVisits(Business $business, int $days): Collection
    {
        return collect(range($days - 1, 0))->map(function (int $i) use ($business) {
            $date = Carbon::today()->subDays($i);

            return [
                'label' => $date->isoFormat('dd'),
                'value' => AnalyticsEvent::ofBusiness($business)
                    ->where('type', AnalyticsEvent::TYPE_PROFILE_VISIT)
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        })->values();
    }

    private function hasPagoAtrasado(Business $business): bool
    {
        if (! ModulePrice::exists()) {
            return false;
        }

        $billing = app(BillingService::class);

        foreach (Module::cases() as $module) {
            if ($module->isFree()) {
                continue;
            }

            if ($business->isModuleActive($module) && $billing->statusFor($business, $module) === 'atrasado') {
                return true;
            }
        }

        return false;
    }
}
