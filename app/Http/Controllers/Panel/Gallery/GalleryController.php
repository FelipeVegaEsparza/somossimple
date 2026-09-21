<?php

namespace App\Http\Controllers\Panel\Gallery;

use App\Http\Controllers\Controller;
use App\Models\BusinessGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $business = auth()->user()->business;

        return view('panel.gallery.index', [
            'business' => $business,
            'images' => $business->gallery()->orderBy('position')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'max:4096'],
        ], [
            'images.required' => 'Selecciona al menos una imagen.',
            'images.*.image' => 'Cada archivo debe ser una imagen.',
        ]);

        foreach ($request->file('images') as $image) {
            $business->gallery()->create([
                'path' => $image->store('perfil', 'public'),
                'position' => $business->gallery()->count(),
            ]);
        }

        return back()->with('status', 'Imágenes agregadas a tu galería.');
    }

    public function destroy(BusinessGallery $image): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($image->business_id !== $business->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('status', 'Imagen eliminada de la galería.');
    }
}
