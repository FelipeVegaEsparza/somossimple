<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Communication;
use App\Services\CommunicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunicationsController extends Controller
{
    private function businessOrFail(): Business
    {
        $business = auth()->user()->business;

        if (! $business->isModuleActive(Module::Communications)) {
            abort(403, 'Activa el módulo de Comunicaciones para usar esta sección.');
        }

        return $business;
    }

    public function index(): View
    {
        $business = $this->businessOrFail();

        return view('panel.communications.index', [
            'business' => $business,
            'communications' => $business->communications()->withCount('sends')->orderByDesc('created_at')->get(),
            'typeLabels' => Communication::TYPES,
        ]);
    }

    public function create(): View
    {
        $this->businessOrFail();

        return view('panel.communications.create', [
            'typeLabels' => Communication::typeOptions(),
            'tags' => auth()->user()->business->clientTags()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->businessOrFail();

        $validated = $request->validate([
            'type' => ['required', 'in:'.implode(',', Communication::TYPES)],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'audience' => ['required', 'in:all,tag'],
            'tag_name' => ['nullable', 'required_if:audience,tag', 'string', 'max:255'],
        ], [
            'type.required' => 'Elige el tipo de comunicación.',
            'subject.required' => 'Ingresa el asunto.',
            'body.required' => 'Escribe el contenido.',
            'audience.required' => 'Selecciona los destinatarios.',
            'tag_name.required_if' => 'Elige una etiqueta para los destinatarios.',
        ]);

        $communication = $business->communications()->create([
            'type' => $validated['type'],
            'is_commercial' => $request->boolean('is_commercial'),
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'audience' => $validated['audience'],
            'tag_name' => $validated['tag_name'] ?? null,
            'status' => Communication::STATUS_DRAFT,
        ]);

        $recipients = app(CommunicationService::class)->resolveRecipients($communication);

        return redirect()->route('panel.communications.index')
            ->with('status', 'Comunicación creada en borrador. Lista para '.$recipients->count().' destinatarios.');
    }

    public function send(Communication $communication): RedirectResponse
    {
        $business = $this->businessOrFail();

        if ($communication->business_id !== $business->id) {
            abort(404);
        }

        $count = app(CommunicationService::class)->send($communication);

        return back()->with('status', 'Comunicación enviada a '.$count.' destinatarios.');
    }
}
