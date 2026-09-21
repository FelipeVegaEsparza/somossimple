<?php

namespace App\Http\Controllers\Panel\Menu;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], ['name.required' => 'Ingresa el nombre de la categoría.']);

        $business->menuCategories()->create([
            'name' => $validated['name'],
            'position' => $business->menuCategories()->count(),
        ]);

        return back()->with('status', 'Categoría creada.');
    }

    public function destroy(MenuCategory $category): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($category->business_id !== $business->id) {
            abort(404);
        }

        $category->delete();

        return back()->with('status', 'Categoría eliminada.');
    }
}
