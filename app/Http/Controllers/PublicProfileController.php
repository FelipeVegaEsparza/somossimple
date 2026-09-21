<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Models\AnalyticsEvent;
use App\Models\Business;
use App\Models\SlugAlias;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function legacyRedirect(string $slug): RedirectResponse
    {
        return redirect()->route('p.show', $slug, 301);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $business = Business::where('slug', $slug)->first();

        if (! $business) {
            $alias = SlugAlias::where('old_slug', $slug)->first();

            if ($alias?->business) {
                return redirect()->route('p.show', $alias->business->slug, 301);
            }

            abort(404);
        }

        if (! $business->isPubliclyAvailable()) {
            abort(404);
        }

        app(AnalyticsService::class)->record($business, AnalyticsEvent::TYPE_PROFILE_VISIT);

        // El Perfil Digital es el punto de acceso a los módulos activos. Cada
        // card abre la página pública del módulo en una nueva pestaña. El
        // módulo aparece por estar activo, aunque su contenido esté vacío.
        $iconos = collect(Module::cases())
            ->mapWithKeys(fn (Module $m) => [$m->value => $m->icon()])
            ->all();
        $iconos['social'] = 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244';

        $accesos = [];

        foreach ([
            [Module::Catalog, 'p.catalog', 'Catálogo'],
            [Module::Menu, 'p.menu', 'Menú'],
            [Module::Services, 'p.services', 'Servicios'],
            [Module::Reservations, 'p.reservation', 'Reservas'],
            [Module::Promotions, 'p.promotions', 'Promociones'],
            [Module::Clients, 'p.clients', 'Novedades'],
            [Module::Events, 'p.events', 'Eventos'],
            [Module::Gallery, 'p.gallery', 'Galería'],
            [Module::Loyalty, 'loyalty.public', 'Fidelización'],
        ] as [$module, $route, $label]) {
            if ($business->isModuleActive($module)) {
                $accesos[] = ['label' => $label, 'route' => $route, 'icon' => $module->icon()];
            }
        }

        $reservationsActive = $business->isModuleActive(Module::Reservations);

        $openNow = null;
        if ($reservationsActive) {
            $now = Carbon::now();
            $dow = $now->dayOfWeekIso - 1;
            $openNow = $business->bookingWeekHours()
                ->where('day_of_week', $dow)
                ->get()
                ->contains(fn ($r) => $now->format('H:i:s') >= $r->open_time && $now->format('H:i:s') <= $r->close_time);
        }

        return view('public.profile', [
            'business' => $business,
            'accesos' => $accesos,
            'iconos' => $iconos,
            'reservationsActive' => $reservationsActive,
            'openNow' => $openNow,
        ]);
    }
}
