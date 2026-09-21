<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Models\Business;
use App\Models\CatalogItem;
use App\Models\Event;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\Service;
use Illuminate\View\View;

/**
 * Páginas públicas de cada módulo. Se accede a ellas desde las cards del
 * Perfil Digital y pueden abrirse en una pestaña nueva.
 */
class PublicModuleController extends Controller
{
    public function catalog(string $slug): View
    {
        $business = $this->business($slug, Module::Catalog);

        $categories = $business->catalogCategories()
            ->with(['items' => fn ($q) => $q->where('active', true)->orderBy('position')])
            ->orderBy('position')
            ->get()
            ->filter(fn ($c) => $c->items->isNotEmpty());

        $uncategorized = CatalogItem::ofBusiness($business)
            ->whereNull('category_id')
            ->where('active', true)
            ->orderBy('position')
            ->get();

        return view('public.modules.catalog', [
            'business' => $business,
            'categories' => $categories,
            'uncategorized' => $uncategorized,
        ]);
    }

    public function menu(string $slug): View
    {
        $business = $this->business($slug, Module::Menu);

        $featured = MenuItem::ofBusiness($business)
            ->where('featured', true)
            ->where('active', true)
            ->orderBy('position')
            ->get();

        $categories = $business->menuCategories()
            ->with(['items' => fn ($q) => $q->where('active', true)->where('featured', false)->orderBy('position')])
            ->orderBy('position')
            ->get()
            ->filter(fn ($c) => $c->items->isNotEmpty());

        $uncategorized = MenuItem::ofBusiness($business)
            ->whereNull('category_id')
            ->where('featured', false)
            ->where('active', true)
            ->orderBy('position')
            ->get();

        return view('public.modules.menu', [
            'business' => $business,
            'featured' => $featured,
            'categories' => $categories,
            'uncategorized' => $uncategorized,
        ]);
    }

    public function services(string $slug): View
    {
        $business = $this->business($slug, Module::Services);

        return view('public.modules.services', [
            'business' => $business,
            'services' => Service::ofBusiness($business)->where('active', true)->orderBy('position')->get(),
            'reservationsActive' => $business->isModuleActive(Module::Reservations),
        ]);
    }

    public function promotions(string $slug): View
    {
        $business = $this->business($slug, Module::Promotions);

        $promotions = Promotion::ofBusiness($business)
            ->where('active', true)
            ->orderBy('position')
            ->get()
            ->filter(fn ($p) => $p->isLive())
            ->values();

        return view('public.modules.promotions', [
            'business' => $business,
            'promotions' => $promotions,
        ]);
    }

    public function clients(string $slug): View
    {
        $business = $this->business($slug, Module::Clients);

        return view('public.modules.clients', [
            'business' => $business,
        ]);
    }

    public function events(string $slug): View
    {
        $business = $this->business($slug, Module::Events);

        $events = Event::ofBusiness($business)
            ->where('active', true)
            ->orderBy('position')
            ->get()
            ->filter(fn ($e) => $e->isUpcoming())
            ->sortBy('starts_at')
            ->values();

        return view('public.modules.events', [
            'business' => $business,
            'events' => $events,
        ]);
    }

    public function gallery(string $slug): View
    {
        $business = $this->business($slug, Module::Gallery);

        return view('public.modules.gallery', [
            'business' => $business,
            'images' => $business->gallery()->orderBy('position')->get(),
        ]);
    }

    private function business(string $slug, Module $module): Business
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable() || ! $business->isModuleActive($module)) {
            abort(404);
        }

        return $business;
    }
}
