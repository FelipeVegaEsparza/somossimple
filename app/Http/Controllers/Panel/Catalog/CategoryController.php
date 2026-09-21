<?php

namespace App\Http\Controllers\Panel\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], ['name.required' => 'Ingresa el nombre de la categoría.']);

        $business->catalogCategories()->create([
            'name' => $validated['name'],
            'position' => $business->catalogCategories()->count(),
        ]);

        return back()->with('status', 'Categoría creada.');
    }

    public function destroy(CatalogCategory $category): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($category->business_id !== $business->id) {
            abort(404);
        }

        $category->delete();

        return back()->with('status', 'Categoría eliminada.');
    }
}
