<?php

namespace App\Http\Controllers\Panel\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.catalog.index', [
            'business' => $business,
            'categories' => $business->catalogCategories()->withCount(['items'])->orderBy('position')->get(),
            'uncategorizedItems' => CatalogItem::ofBusiness($business)
                ->whereNull('category_id')
                ->orderBy('position')
                ->get(),
            'itemsByCategory' => $business->catalogCategories()->orderBy('position')->with('items')->get(),
        ]);
    }

    public function create(): View
    {
        return view('panel.catalog.form', [
            'business' => auth()->user()->business,
            'item' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($request->filled('category_id') && ! $business->catalogCategories()->whereKey($request->input('category_id'))->exists()) {
            abort(404);
        }

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['position'] = CatalogItem::ofBusiness($business)->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalogo', 'public');
        }

        CatalogItem::create($data);

        return redirect()->route('panel.catalog.index')->with('status', 'Elemento creado.');
    }

    public function edit(CatalogItem $item): View
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        return view('panel.catalog.form', [
            'business' => $business,
            'item' => $item,
        ]);
    }

    public function update(Request $request, CatalogItem $item): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        if ($request->filled('category_id') && ! $business->catalogCategories()->whereKey($request->input('category_id'))->exists()) {
            abort(404);
        }

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalogo', 'public');
        }

        $item->update($data);

        return redirect()->route('panel.catalog.index')->with('status', 'Elemento actualizado.');
    }

    public function toggle(CatalogItem $item): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        $item->update(['active' => ! $item->active]);

        return back()->with('status', $item->active ? 'Elemento activado.' : 'Elemento desactivado.');
    }

    public function destroy(CatalogItem $item): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        $item->delete();

        return back()->with('status', 'Elemento eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:catalog_categories,id'],
            'price_mode' => ['required', 'in:exact,from,none'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Ingresa el nombre del elemento.',
            'price_mode.in' => 'Elige una modalidad de precio válida.',
            'price.numeric' => 'El precio debe ser un número.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
        ]) + ['active' => $request->boolean('active')];
    }
}
