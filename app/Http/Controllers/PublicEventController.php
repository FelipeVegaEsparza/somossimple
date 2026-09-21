<?php

namespace App\Http\Controllers;

use App\Enums\TicketeraEventStatus;
use App\Models\TicketeraEvent;
use App\Models\TicketeraOrder;
use App\Models\TicketeraTicket;
use App\Models\TicketeraTicketType;
use App\Services\Ticketera\Payments\PaymentException;
use App\Services\Ticketera\TicketeraService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Página pública del evento y compra de entradas sin necesidad de cuenta.
 */
class PublicEventController extends Controller
{
    public function show(string $slug): View
    {
        $event = $this->event($slug);

        return view('public.ticketera.event', [
            'event' => $event,
            'ticketTypes' => $event->ticketTypes()->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request, string $slug, TicketeraService $service): RedirectResponse
    {
        $event = $this->event($slug);

        $validated = $request->validate([
            'ticket_type_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'name' => ['required', 'string', 'max:80'],
            'lastname' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
        ], [
            'ticket_type_id.required' => 'Elige un tipo de entrada.',
            'quantity.required' => 'Elige la cantidad.',
            'name.required' => 'Ingresa tu nombre.',
            'email.required' => 'Ingresa tu correo para enviarte la entrada.',
            'email.email' => 'Ingresa un correo válido.',
        ]);

        $type = TicketeraTicketType::where('event_id', $event->id)->find($validated['ticket_type_id']);

        if (! $type) {
            return back()->withInput()->withErrors(['ticket_type_id' => 'Ese tipo de entrada no existe.']);
        }

        try {
            $order = $service->checkout($event, $type, $validated['quantity'], [
                'name' => $validated['name'],
                'lastname' => $validated['lastname'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);
        } catch (PaymentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('ticketera.order', [$event->slug, $order->number])
            ->with('checkout_email', $order->buyer_email);
    }

    public function order(Request $request, string $slug, string $number): View|RedirectResponse
    {
        $event = $this->event($slug);
        $order = $event->orders()->where('number', $number)->with(['tickets.ticketType', 'event'])->firstOrFail();

        $email = (string) $request->query('email', '');

        if ($email !== '' && strcasecmp($email, $order->buyer_email) !== 0) {
            abort(404);
        }

        return view('public.ticketera.order', [
            'event' => $event,
            'order' => $order,
            'email' => $email,
        ]);
    }

    public function ticket(string $token): View
    {
        $ticket = TicketeraTicket::where('token', strtoupper(trim($token)))->with(['event', 'ticketType', 'order'])->firstOrFail();

        return view('public.ticketera.ticket', [
            'ticket' => $ticket,
            'event' => $ticket->event,
        ]);
    }

    public function qr(string $token): Response
    {
        $ticket = TicketeraTicket::where('token', strtoupper(trim($token)))->firstOrFail();

        $result = (new Builder(
            writer: new SvgWriter,
            data: route('ticketera.ticket', $ticket->token, true),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 320,
            margin: 8,
        ))->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function recoverForm(): View
    {
        return view('public.ticketera.recover');
    }

    public function recover(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'number' => ['required', 'string', 'max:40'],
        ], [
            'email.required' => 'Ingresa tu correo.',
            'number.required' => 'Ingresa el número de compra (ORD-...).',
        ]);

        $order = TicketeraOrder::where('number', strtoupper(trim($validated['number'])))
            ->where('buyer_email', $validated['email'])
            ->with('event')
            ->first();

        if (! $order) {
            return back()->withInput()->withErrors(['number' => 'No encontramos una compra con esos datos.']);
        }

        return redirect()->route('ticketera.order', [
            'slug' => $order->event->slug,
            'number' => $order->number,
            'email' => $order->buyer_email,
        ]);
    }

    private function event(string $slug): TicketeraEvent
    {
        $event = TicketeraEvent::where('slug', $slug)->firstOrFail();

        abort_unless(in_array($event->status(), [TicketeraEventStatus::Published, TicketeraEventStatus::Paused], true), 404);

        return $event;
    }
}
