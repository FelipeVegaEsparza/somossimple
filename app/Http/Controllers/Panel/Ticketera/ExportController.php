<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Models\TicketeraEvent;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends TicketeraController
{
    public function orders(TicketeraEvent $event): StreamedResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $rows = $event->orders()->with('tickets')->latest()->get()->map(fn ($order) => [
            $order->number,
            $order->created_at->format('d/m/Y H:i'),
            $order->buyerFullName(),
            $order->buyer_email,
            $order->buyer_phone,
            $order->tickets->count(),
            $order->total,
            $order->status()->label(),
        ]);

        return $this->csv('ordenes-'.$event->slug.'.csv', [
            'Orden', 'Fecha', 'Comprador', 'Correo', 'Teléfono', 'Entradas', 'Total', 'Estado',
        ], $rows);
    }

    public function tickets(TicketeraEvent $event): StreamedResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $rows = $event->tickets()->with(['order', 'ticketType'])->latest()->get()->map(fn ($ticket) => [
            $ticket->number,
            $ticket->token,
            $ticket->holder_name,
            $ticket->ticketType?->name,
            $ticket->price,
            $ticket->status()->label(),
            $ticket->issued_at?->format('d/m/Y H:i'),
            $ticket->used_at?->format('d/m/Y H:i'),
        ]);

        return $this->csv('entradas-'.$event->slug.'.csv', [
            'Número', 'Token', 'Titular', 'Tipo', 'Precio', 'Estado', 'Emitida', 'Usada',
        ], $rows);
    }

    public function accesses(TicketeraEvent $event): StreamedResponse
    {
        $business = $this->business();
        $this->guardEvent($business, $event);

        $rows = $event->accesses()->with(['ticket', 'user', 'staff'])->latest()->get()->map(fn ($access) => [
            $access->created_at->format('d/m/Y H:i'),
            $access->ticket?->number,
            $access->ticket?->holder_name,
            $access->user?->name ?? $access->staff?->name ?? 'Sistema',
        ]);

        return $this->csv('accesos-'.$event->slug.'.csv', [
            'Fecha', 'Entrada', 'Titular', 'Validado por',
        ], $rows);
    }

    /**
     * @param  list<string>  $headers
     * @param  Collection<int, array<int, mixed>>  $rows
     */
    private function csv(string $filename, array $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
