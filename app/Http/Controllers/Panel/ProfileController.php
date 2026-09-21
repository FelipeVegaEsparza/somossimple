<?php

namespace App\Http\Controllers\Panel;

use App\Enums\ProfileTheme;
use App\Http\Controllers\Controller;
use App\Models\BusinessGallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const SUPPORTED_NETWORKS = ['instagram', 'facebook', 'tiktok', 'youtube', 'linkedin'];

    public function edit(): View
    {
        $business = auth()->user()->business;

        return view('panel.profile.edit', [
            'business' => $business,
            'networks' => self::SUPPORTED_NETWORKS,
            'themes' => ProfileTheme::cases(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $business = auth()->user()->business;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:60'],
            'whatsapp' => ['nullable', 'string', 'max:60'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'url', 'max:500'],
            'opening_hours' => ['nullable', 'string', 'max:500'],
            'theme' => ['nullable', Rule::enum(ProfileTheme::class)],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:4096'],
            'networks' => ['nullable', 'array'],
            'buttons_label' => ['nullable', 'array'],
            'buttons_label.*' => ['nullable', 'string', 'max:255'],
            'buttons_url' => ['nullable', 'array'],
            'buttons_url.*' => ['nullable', 'url', 'max:500'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'contact_email.email' => 'Ingresa un correo válido.',
            'website.url' => 'Ingresa un sitio web válido.',
            'map_url.url' => 'Ingresa una URL de ubicación válida.',
            'logo.image' => 'El logo debe ser una imagen.',
            'cover.image' => 'La portada debe ser una imagen.',
            'gallery.*.image' => 'Las imágenes de la galería deben ser imágenes.',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('perfil', 'public');
        }

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('perfil', 'public');
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $business->gallery()->create([
                    'path' => $image->store('perfil', 'public'),
                    'position' => $business->gallery()->count(),
                ]);
            }
        }

        unset($data['logo'], $data['cover'], $data['gallery'], $data['networks'], $data['buttons_label'], $data['buttons_url']);

        $business->update($data);

        $this->syncLinks($business, $request);

        return redirect()->route('panel.profile.edit')
            ->with('status', 'Tu perfil fue actualizado.');
    }

    public function destroyGalleryImage(Request $request, BusinessGallery $image): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($image->business_id !== $business->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('status', 'Imagen eliminada de la galería.');
    }

    public function destroyLogo(): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($business->logo_path) {
            Storage::disk('public')->delete($business->logo_path);
            $business->update(['logo_path' => null]);
        }

        return back()->with('status', 'Logo eliminado.');
    }

    public function destroyCover(): RedirectResponse
    {
        $business = auth()->user()->business;

        if ($business->cover_path) {
            Storage::disk('public')->delete($business->cover_path);
            $business->update(['cover_path' => null]);
        }

        return back()->with('status', 'Imagen de portada eliminada.');
    }

    private function syncLinks($business, Request $request): void
    {
        $business->profileLinks()->delete();

        $position = 0;

        foreach (self::SUPPORTED_NETWORKS as $network) {
            $url = trim((string) $request->input("networks.$network", ''));

            if ($url !== '') {
                $business->profileLinks()->create([
                    'kind' => 'social',
                    'network' => $network,
                    'url' => $url,
                    'position' => $position++,
                ]);
            }
        }

        $labels = (array) $request->input('buttons_label', []);
        $urls = (array) $request->input('buttons_url', []);

        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            $url = trim((string) ($urls[$i] ?? ''));

            if ($label === '' || $url === '') {
                continue;
            }

            $business->profileLinks()->create([
                'kind' => 'button',
                'label' => $label,
                'url' => $url,
                'position' => $position++,
            ]);
        }
    }
}
