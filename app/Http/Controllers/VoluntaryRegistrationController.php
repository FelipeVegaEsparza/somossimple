<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Models\Business;
use App\Models\ClientConsent;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoluntaryRegistrationController extends Controller
{
    public function store(string $slug, Request $request): RedirectResponse
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable() || ! $business->isModuleActive(Module::Clients)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
            'consent' => ['nullable'],
        ], [
            'name.required' => 'Ingresa tu nombre.',
            'phone.required' => 'Ingresa tu teléfono o WhatsApp.',
        ]);

        $email = $validated['email'] ?? null;
        $granted = $request->boolean('consent') && $email !== null;

        $client = app(ClientService::class)->resolve($business, $validated['name'], $validated['phone'], $email);
        app(ClientService::class)->ensureConsent($client, ClientConsent::CHANNEL_EMAIL, $granted, ClientConsent::SOURCE_VOLUNTARY);

        return redirect()->route('p.clients', $business->slug)
            ->with('promo_ok', $granted
                ? '¡Listo! Te avisaremos de nuestras promociones.'
                : 'Gracias por registrarte. Si más adelante quieres recibir promociones, avísanos.');
    }
}
