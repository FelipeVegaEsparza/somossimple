<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\ModulePayment;
use App\Services\BillingService;
use Illuminate\View\View;

/**
 * Resumen de los servicios contratados por el negocio, su costo mensual,
 * estado de pago y pagos registrados.
 */
class BillingController extends Controller
{
    public function index(BillingService $billing): View
    {
        $business = auth()->user()->business;
        $moduleAccess = $business->moduleAccess;

        $contracted = [];
        $total = 0;
        $overdue = 0;
        $withoutPrice = 0;

        foreach (Module::cases() as $module) {
            $access = $moduleAccess->firstWhere('module', $module->value);
            $active = (bool) ($access?->active ?? false);

            if (! $module->isFree() && ! $active) {
                continue;
            }

            $price = $module->isFree() ? 0 : $billing->priceFor($module);
            $status = $billing->statusFor($business, $module);

            $contracted[] = [
                'module' => $module,
                'price' => $price,
                'status' => $status,
                'coverage_end' => $module->isFree() ? null : $billing->coverageEndFor($business, $module),
                'activated_at' => $access?->activated_at,
            ];

            if (! $module->isFree()) {
                if ($price === null) {
                    $withoutPrice++;
                } else {
                    $total += $price;
                }

                if ($status === 'atrasado') {
                    $overdue++;
                }
            }
        }

        $available = collect(Module::cases())
            ->reject(fn (Module $module) => $module->isFree())
            ->reject(fn (Module $module) => (bool) ($moduleAccess->firstWhere('module', $module->value)?->active ?? false))
            ->map(fn (Module $module) => ['module' => $module, 'price' => $billing->priceFor($module)])
            ->filter(fn (array $row) => $row['price'] !== null)
            ->values();

        return view('panel.billing.index', [
            'business' => $business,
            'contracted' => collect($contracted),
            'total' => $total,
            'overdue' => $overdue,
            'withoutPrice' => $withoutPrice,
            'available' => $available,
            'payments' => ModulePayment::ofBusiness($business)->orderByDesc('paid_on')->limit(30)->get(),
        ]);
    }
}
