<?php

namespace App\Services\Ticketera;

use App\Enums\TicketeraOrderStatus;
use App\Enums\TicketeraTicketStatus;
use App\Models\TicketeraAccess;
use App\Models\TicketeraEvent;
use App\Models\TicketeraOrder;
use App\Models\TicketeraRefund;
use App\Models\TicketeraTicket;
use App\Models\TicketeraTicketType;
use App\Services\Ticketera\Payments\PaymentManager;
use App\Services\Ticketera\Payments\PaymentOutcome;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Núcleo de la ticketera: numeración, compra con control de stock, emisión de
 * entradas individuales, validación transaccional y reembolsos.
 */
class TicketeraService
{
    public function __construct(private PaymentManager $payments) {}

    public function nextOrderNumber(): string
    {
        $year = now()->year;
        $sequence = TicketeraOrder::whereYear('created_at', $year)->count() + 1;

        do {
            $number = 'ORD-'.$year.'-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            $sequence++;
        } while (TicketeraOrder::where('number', $number)->exists());

        return $number;
    }

    public function nextTicketNumber(): string
    {
        $sequence = TicketeraTicket::count() + 1;

        do {
            $number = 'ENT-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            $sequence++;
        } while (TicketeraTicket::where('number', $number)->exists());

        return $number;
    }

    public function uniqueToken(): string
    {
        do {
            $token = 'TKT-'.Str::upper(Str::random(9));
        } while (TicketeraTicket::where('token', $token)->exists());

        return $token;
    }

    /**
     * Compra completa: reserva stock, cobra y emite entradas. Todo transaccional
     * para evitar sobreventa.
     *
     * @param  array{name: string, lastname?: ?string, email: string, phone?: ?string}  $buyer
     */
    public function checkout(
        TicketeraEvent $event,
        TicketeraTicketType $type,
        int $quantity,
        array $buyer,
        ?string $providerKey = null,
    ): TicketeraOrder {
        return DB::transaction(function () use ($event, $type, $quantity, $buyer, $providerKey) {
            $locked = TicketeraTicketType::whereKey($type->id)->lockForUpdate()->firstOrFail();

            if ($locked->event_id !== $event->id) {
                throw new RuntimeException('El tipo de entrada no corresponde a este evento.');
            }
            if ($event->isCancelled()) {
                throw new RuntimeException('Este evento fue cancelado.');
            }
            if (! $event->isPublished()) {
                throw new RuntimeException('Las ventas de este evento no están abiertas.');
            }
            if (! $locked->isOnSale()) {
                throw new RuntimeException('Ese tipo de entrada no está disponible.');
            }
            if ($quantity < 1) {
                throw new RuntimeException('Elige al menos una entrada.');
            }
            if ($locked->remaining() < $quantity) {
                throw new RuntimeException('No quedan suficientes entradas disponibles.');
            }

            if ($locked->purchase_limit) {
                $already = TicketeraTicket::where('ticket_type_id', $locked->id)
                    ->whereHas('order', fn ($q) => $q->where('buyer_email', $buyer['email'])->where('status', '!=', TicketeraOrderStatus::Cancelled->value))
                    ->count();

                if ($already + $quantity > $locked->purchase_limit) {
                    throw new RuntimeException('Superas el límite de compra por persona para este tipo de entrada.');
                }
            }

            $subtotal = $locked->price * $quantity;
            $commission = (int) round($subtotal * ((float) ($event->commission_rate ?? 0)) / 100);

            $order = TicketeraOrder::create([
                'business_id' => $event->business_id,
                'event_id' => $event->id,
                'number' => $this->nextOrderNumber(),
                'buyer_name' => $buyer['name'],
                'buyer_lastname' => $buyer['lastname'] ?? null,
                'buyer_email' => $buyer['email'],
                'buyer_phone' => $buyer['phone'] ?? null,
                'subtotal' => $subtotal,
                'commission' => $commission,
                'total' => $subtotal,
                'status' => TicketeraOrderStatus::Pending->value,
            ]);

            $outcome = $this->charge($order, $providerKey);

            if ($outcome->isPaid()) {
                $order->update([
                    'status' => TicketeraOrderStatus::Paid->value,
                    'payment_provider' => $providerKey ?: $this->payments->defaultKey(),
                    'payment_reference' => $outcome->reference,
                    'paid_at' => now(),
                ]);

                $locked->increment('sold', $quantity);
                $this->issueTickets($order->fresh(), $locked, $quantity, $buyer);
            } else {
                $order->update([
                    'status' => $outcome->status === 'failed' ? TicketeraOrderStatus::Failed->value : TicketeraOrderStatus::Pending->value,
                    'payment_provider' => $providerKey ?: $this->payments->defaultKey(),
                    'payment_reference' => $outcome->reference,
                ]);
            }

            return $order->fresh(['tickets', 'event']);
        });
    }

    public function charge(TicketeraOrder $order, ?string $providerKey = null): PaymentOutcome
    {
        return $this->payments->provider($providerKey)->charge($order);
    }

    /**
     * @param  array{name: string, lastname?: ?string}  $buyer
     */
    private function issueTickets(TicketeraOrder $order, TicketeraTicketType $type, int $quantity, array $buyer): void
    {
        $holder = trim($buyer['name'].' '.($buyer['lastname'] ?? ''));

        for ($i = 0; $i < $quantity; $i++) {
            TicketeraTicket::create([
                'business_id' => $order->business_id,
                'event_id' => $order->event_id,
                'order_id' => $order->id,
                'ticket_type_id' => $type->id,
                'number' => $this->nextTicketNumber(),
                'token' => $this->uniqueToken(),
                'holder_name' => $holder,
                'price' => $type->price,
                'status' => TicketeraTicketStatus::Issued->value,
                'issued_at' => now(),
            ]);
        }
    }

    /**
     * Extrae el token seguro desde un QR (puede ser token o URL de la entrada).
     */
    public function extractToken(string $raw): string
    {
        $raw = trim($raw);

        if (preg_match('/TKT-[A-Z0-9]+/i', $raw, $matches)) {
            return strtoupper($matches[0]);
        }

        $path = parse_url($raw, PHP_URL_PATH) ?: $raw;
        $segments = array_values(array_filter(explode('/', $path)));
        $last = $segments ? end($segments) : $raw;

        return strtoupper(trim($last));
    }

    /**
     * Validación transaccional de una entrada. Solo una validación concurrente
     * puede resultar exitosa.
     *
     * @return array{result: string, ticket?: TicketeraTicket, event?: TicketeraEvent, access?: TicketeraAccess, message?: string}
     */
    public function validate(
        string $token,
        ?TicketeraEvent $expectedEvent = null,
        ?int $userId = null,
        ?int $staffId = null,
        ?string $device = null,
    ): array {
        $token = $this->extractToken($token);

        return DB::transaction(function () use ($token, $expectedEvent, $userId, $staffId, $device) {
            $ticket = TicketeraTicket::where('token', $token)->lockForUpdate()->first();

            if (! $ticket) {
                return ['result' => 'invalid', 'message' => 'La entrada no existe.'];
            }

            $ticket->load(['event', 'ticketType', 'order']);

            if ($expectedEvent && $ticket->event_id !== $expectedEvent->id) {
                return ['result' => 'wrong_event', 'ticket' => $ticket, 'event' => $ticket->event, 'message' => 'La entrada no corresponde a este evento.'];
            }

            if ($ticket->event?->isCancelled() || $ticket->status() === TicketeraTicketStatus::Cancelled) {
                return ['result' => 'cancelled', 'ticket' => $ticket, 'event' => $ticket->event, 'message' => 'El evento o la entrada fue cancelada.'];
            }

            if ($ticket->status() === TicketeraTicketStatus::Refunded) {
                return ['result' => 'refunded', 'ticket' => $ticket, 'event' => $ticket->event, 'message' => 'La entrada fue reembolsada.'];
            }

            if ($ticket->status() === TicketeraTicketStatus::Used) {
                $ticket->loadMissing('access');

                return ['result' => 'used', 'ticket' => $ticket, 'event' => $ticket->event, 'access' => $ticket->access, 'message' => 'La entrada ya fue utilizada.'];
            }

            if (! $ticket->order?->isPaid()) {
                return ['result' => 'unpaid', 'ticket' => $ticket, 'event' => $ticket->event, 'message' => 'La orden aún no está pagada.'];
            }

            $ticket->update(['status' => TicketeraTicketStatus::Used->value, 'used_at' => now()]);

            $access = TicketeraAccess::create([
                'business_id' => $ticket->business_id,
                'event_id' => $ticket->event_id,
                'ticket_id' => $ticket->id,
                'user_id' => $userId,
                'staff_id' => $staffId,
                'device' => $device,
            ]);

            return ['result' => 'valid', 'ticket' => $ticket->fresh(), 'event' => $ticket->event, 'access' => $access];
        });
    }

    public function refundOrder(TicketeraOrder $order, ?string $reason = null, ?int $amount = null): TicketeraRefund
    {
        return DB::transaction(function () use ($order, $reason, $amount) {
            $order->loadMissing('tickets');

            $refund = TicketeraRefund::create([
                'business_id' => $order->business_id,
                'order_id' => $order->id,
                'reason' => $reason,
                'amount' => $amount ?? $order->total,
                'status' => 'done',
                'refunded_at' => now(),
            ]);

            $order->update(['status' => TicketeraOrderStatus::Refunded->value]);

            foreach ($order->tickets as $ticket) {
                $ticket->update(['status' => TicketeraTicketStatus::Refunded->value]);
                TicketeraTicketType::whereKey($ticket->ticket_type_id)->where('sold', '>', 0)->decrement('sold');
            }

            return $refund;
        });
    }

    public function cancelEvent(TicketeraEvent $event): void
    {
        DB::transaction(function () use ($event) {
            $event->update(['status' => 'cancelled']);
            $event->tickets()->where('status', TicketeraTicketStatus::Issued->value)->update(['status' => TicketeraTicketStatus::Cancelled->value]);
            $event->orders()->whereIn('status', [TicketeraOrderStatus::Pending->value, TicketeraOrderStatus::Paid->value])->update(['status' => TicketeraOrderStatus::Cancelled->value]);
        });
    }

    /**
     * @return array{entered: int, pending: int, total: int}
     */
    public function counters(TicketeraEvent $event): array
    {
        $total = (int) $event->tickets()->whereIn('status', [TicketeraTicketStatus::Issued->value, TicketeraTicketStatus::Used->value])->count();
        $used = (int) $event->tickets()->where('status', TicketeraTicketStatus::Used->value)->count();

        return ['entered' => $used, 'pending' => max(0, $total - $used), 'total' => $total];
    }
}
