<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\ModulePayment;
use App\Models\ModulePrice;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagosController extends Controller
{
    public function index(Request $request): View
    {
        $query = Business::query()->with(['account', 'moduleAccess']);

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('businesses.name', 'like', "%{$q}%")
                    ->orWhereHas('account', fn ($a) => $a->where('email', 'like', "%{$q}%"));
            });
        }

        return view('admin.billing.pagos.index', [
            'negocios' => $query->orderByDesc('businesses.created_at')->simplePaginate(15)->withQueryString(),
            'paidModules' => collect(Module::cases())->reject(fn ($m) => $m->isFree())->values(),
            'billing' => app(BillingService::class),
            'preciosDefinidos' => ModulePrice::exists(),
        ]);
    }

    public function show(Business $business): View
    {
        $billing = app(BillingService::class);
        $paidModules = collect(Module::cases())->reject(fn ($m) => $m->isFree())->values();

        $business->load(['moduleAccess', 'account']);

        $estados = $paidModules->mapWithKeys(function (Module $module) use ($business, $billing) {
            $access = $business->moduleAccess->firstWhere('module', $module->value);

            return [$module->value => [
                'status' => $billing->statusFor($business, $module),
                'price' => $billing->priceFor($module),
                'lastPayment' => ModulePayment::ofBusiness($business)->where('module', $module->value)->orderByDesc('paid_on')->first(),
                'active' => $access?->active ?? false,
            ]];
        });

        return view('admin.billing.pagos.show', [
            'business' => $business,
            'paidModules' => $paidModules,
            'estados' => $estados,
            'billing' => $billing,
            'pagos' => ModulePayment::ofBusiness($business)->with('business')->orderByDesc('paid_on')->get(),
        ]);
    }

    public function store(Request $request, Business $business): RedirectResponse
    {
        $module = Module::tryFrom($request->input('module'));

        if ($module === null || $module->isFree()) {
            abort(422, 'Módulo no válido para el cobro.');
        }

        $validated = $request->validate([
            'module' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:100'],
            'paid_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'amount.required' => 'Ingresa el monto del pago.',
            'amount.integer' => 'El monto debe ser un número en CLP.',
            'paid_on.required' => 'Ingresa la fecha del pago.',
        ]);

        app(BillingService::class)->registerPayment(
            $business,
            $module,
            (int) $validated['amount'],
            $validated['paid_on'],
            $validated['notes'] ?? null,
        );

        return back()->with('status', 'Pago registrado para '.$module->label().'. El módulo queda al día.');
    }
}
