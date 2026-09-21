<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Client;
use App\Models\ClientConsent;
use App\Models\ClientNote;
use App\Models\ClientTag;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientsController extends Controller
{
    private function businessOrFail(): Business
    {
        $business = auth()->user()->business;

        if (! $business->isModuleActive(Module::Clients)) {
            abort(403, 'Activa el módulo de Clientes para usar esta sección.');
        }

        return $business;
    }

    public function index(Request $request): View
    {
        $business = $this->businessOrFail();
        $query = Client::ofBusiness($business)->withCount('reservations');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
        }

        return view('panel.clients.index', [
            'business' => $business,
            'clients' => $query->orderByDesc('created_at')->simplePaginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $this->businessOrFail();

        return view('panel.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->businessOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'name.required' => 'Ingresa el nombre del cliente.',
            'email.email' => 'Ingresa un correo válido.',
        ]);

        $service = app(ClientService::class);
        $client = $service->resolve($business, $validated['name'], $validated['phone'], $validated['email'] ?? null);
        $service->ensureConsent($client, ClientConsent::CHANNEL_EMAIL, false, ClientConsent::SOURCE_MANUAL);

        return redirect()->route('panel.clients.show', $client)
            ->with('status', 'Cliente registrado.');
    }

    public function show(Client $client): View
    {
        $business = $this->businessOrFail();

        if ($client->business_id !== $business->id) {
            abort(404);
        }

        $client->load(['tags', 'notes', 'reservations' => fn ($q) => $q->orderByDesc('starts_at'), 'consents']);
        $availableTags = $business->clientTags()->orderBy('name')->get();

        return view('panel.clients.show', [
            'business' => $business,
            'client' => $client,
            'availableTags' => $availableTags,
            'firstReservation' => $client->reservations()->orderBy('starts_at')->first(),
            'totalReservations' => $client->reservations()->count(),
        ]);
    }

    public function storeNote(Request $request, Client $client): RedirectResponse
    {
        $this->businessOrFail();

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ], ['note.required' => 'Escribe una nota.']);

        $client->notes()->create(['note' => $validated['note']]);

        return back()->with('status', 'Nota agregada.');
    }

    public function destroyNote(ClientNote $note): RedirectResponse
    {
        $business = $this->businessOrFail();

        if ($note->client->business_id !== $business->id) {
            abort(404);
        }

        $note->delete();

        return back()->with('status', 'Nota eliminada.');
    }

    public function attachTag(Request $request, Client $client): RedirectResponse
    {
        $business = $this->businessOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tag = ClientTag::ofBusiness($business)->firstOrCreate([
            'business_id' => $business->id,
            'name' => trim($validated['name']),
        ]);

        $client->tags()->syncWithoutDetaching([$tag->id]);

        return back()->with('status', 'Etiqueta asignada.');
    }

    public function detachTag(Client $client, ClientTag $tag): RedirectResponse
    {
        $business = $this->businessOrFail();

        if ($client->business_id !== $business->id || $tag->business_id !== $business->id) {
            abort(404);
        }

        $client->tags()->detach($tag->id);

        return back()->with('status', 'Etiqueta quitada.');
    }

    public function updateConsent(Request $request, Client $client): RedirectResponse
    {
        $this->businessOrFail();

        $validated = $request->validate([
            'granted' => ['required', 'boolean'],
        ]);

        app(ClientService::class)->ensureConsent(
            $client,
            ClientConsent::CHANNEL_EMAIL,
            $validated['granted'],
            ClientConsent::SOURCE_MANUAL,
        );

        return back()->with('status', 'Consentimiento actualizado.');
    }
}
