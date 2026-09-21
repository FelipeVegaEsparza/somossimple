<?php

namespace App\Services;

use App\Enums\Module;
use App\Models\Business;
use App\Models\ModuleAccess;
use App\Models\ModulePayment;
use App\Models\ModulePrice;
use Carbon\Carbon;

/**
 * Plano comercial manual de la plataforma.
 *
 * Estados por negocio/módulo de pago: al_dia | atrasado | inactivo.
 * Un pago cubre 30 días desde su fecha. La gracia inicial es de 7 días desde
 * la activación. La regla de impago solo aplica a módulos con precio definido;
 * el Perfil Digital nunca se evalúa ni se apaga.
 */
class BillingService
{
    public const COVERAGE_DAYS = 30;

    public const GRACE_DAYS = 7;

    public function priceFor(Module $module): ?int
    {
        return ModulePrice::where('module', $module->value)->value('price_monthly');
    }

    public function hasPrice(Module $module): bool
    {
        return $module->isFree() ? false : $this->priceFor($module) !== null;
    }

    public function coverageEndFor(Business $business, Module $module): ?Carbon
    {
        $last = ModulePayment::ofBusiness($business)
            ->where('module', $module->value)
            ->orderByDesc('paid_on')
            ->first();

        return $last ? $last->paid_on->copy()->addDays(self::COVERAGE_DAYS) : null;
    }

    public function statusFor(Business $business, Module $module, ?Carbon $today = null): string
    {
        if ($module->isFree()) {
            return 'al_dia';
        }

        $access = $business->moduleAccess()->where('module', $module->value)->first();

        if (! $access?->active) {
            return 'inactivo';
        }

        // Sin precio definido: la regla de impago no aplica.
        if (! $this->hasPrice($module)) {
            return 'al_dia';
        }

        $today = $today ?? Carbon::today();

        $coverageEnd = $this->coverageEndFor($business, $module);

        if ($coverageEnd !== null && $coverageEnd->greaterThanOrEqualTo($today)) {
            return 'al_dia';
        }

        $activatedAt = $access->activated_at ?? Carbon::now();

        if ($activatedAt->addDays(self::GRACE_DAYS)->greaterThanOrEqualTo($today)) {
            return 'al_dia';
        }

        return 'atrasado';
    }

    public function registerPayment(Business $business, Module $module, int $amount, string $paidOn, ?string $notes = null): ModulePayment
    {
        $payment = ModulePayment::create([
            'business_id' => $business->id,
            'module' => $module->value,
            'amount' => $amount,
            'paid_on' => $paidOn,
            'notes' => $notes,
        ]);

        // Un pago vigente reactiva el módulo si estaba apagado por impago.
        $access = ModuleAccess::ofBusiness($business)->where('module', $module->value)->first();

        if ($access && ! $access->active) {
            $access->activate();
        }

        return $payment;
    }

    public function applyBilling(): int
    {
        $count = 0;

        $pricedModules = ModulePrice::pluck('module');

        foreach (Module::cases() as $module) {
            if ($module->isFree() || ! $pricedModules->contains($module->value)) {
                continue;
            }

            $activeRows = ModuleAccess::where('module', $module->value)
                ->where('active', true)
                ->with('business')
                ->get();

            foreach ($activeRows as $access) {
                if ($access->business === null) {
                    continue;
                }

                if ($this->statusFor($access->business, $module) === 'atrasado') {
                    $access->deactivate();
                    $count++;
                }
            }
        }

        return $count;
    }
}
