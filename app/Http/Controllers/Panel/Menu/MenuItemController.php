<?php

namespace App\Http\Controllers\Panel\Menu;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.menu.index', [
            'business' => $business,
            'categories' => $business->menuCategories()->withCount(['items'])->orderBy('position')->get(),
            'uncategorizedItems' => MenuItem::ofBusiness($business)
                ->whereNull('category_id')
                ->orderBy('position')
                ->get(),
            'itemsByCategory' => $business->menuCategories()->orderBy('position')->with('items')->get(),
        ]);
    }

    public function create(): View
    {
        return view('panel.menu.form', [
            'business' => auth()->user()->business,
            'item' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($request->filled('category_id') && ! $business->menuCategories()->whereKey($request->input('category_id'))->exists()) {
            abort(404);
        }

        $data = $this->validated($request);
        $data['business_id'] = $business->id;
        $data['position'] = MenuItem::ofBusiness($business)->count();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('panel.menu.index')->with('status', 'Plato creado.');
    }

    public function edit(MenuItem $item): View
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        return view('panel.menu.form', [
            'business' => $business,
            'item' => $item,
        ]);
    }

    public function update(Request $request, MenuItem $item): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        if ($request->filled('category_id') && ! $business->menuCategories()->whereKey($request->input('category_id'))->exists()) {
            abort(404);
        }

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu', 'public');
        }

        $item->update($data);

        return redirect()->route('panel.menu.index')->with('status', 'Plato actualizado.');
    }

    public function toggle(MenuItem $item): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        $item->update(['active' => ! $item->active]);

        return back()->with('status', $item->active ? 'Plato activado.' : 'Plato desactivado.');
    }

    public function destroy(MenuItem $item): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($item->business_id !== $business->id) {
            abort(404);
        }

        $item->delete();

        return back()->with('status', 'Plato eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:menu_categories,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'featured' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required' => 'Ingresa el nombre del plato.',
            'price.numeric' => 'El precio debe ser un número.',
            'image.image' => 'La imagen debe ser un archivo de imagen.',
        ]) + [
            'featured' => $request->boolean('featured'),
            'active' => $request->boolean('active'),
        ];
    }
}
