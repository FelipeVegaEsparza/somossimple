<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Enums\TicketeraEventStatus;
use App\Models\TicketeraAccess;
use App\Models\TicketeraEvent;
use App\Models\TicketeraTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessController extends TicketeraController
{
    public function index(Request $request): View
    {
        $business = $this->business();

        $events = $business->ticketeraEvents()
            ->whereIn('status', [TicketeraEventStatus::Published->value, TicketeraEventStatus::Paused->value])
            ->orderBy('starts_at')
            ->get();

        $selected = $request->filled('event')
            ? $events->firstWhere('id', $request->integer('event'))
            : $events->first();

        $staff = $business->ticketeraStaff()->where('is_active', true)->first();

        return view('panel.ticketera.access', [
            'business' => $business,
            'events' => $events,
            'selected' => $selected,
            'counters' => $selected ? $this->service()->counters($selected) : ['entered' => 0, 'pending' => 0, 'total' => 0],
            'staffLink' => $staff ? route('ticketera.access', $staff->token) : null,
        ]);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $business = $this->business();

        $validated = $request->validate([
            'token' => ['required', 'string', 'max:2000'],
            'event_id' => ['nullable', 'integer'],
        ]);

        $event = null;
        if (! empty($validated['event_id'])) {
            $event = TicketeraEvent::where('business_id', $business->id)->find($validated['event_id']);
        }

        $result = $this->service()->validate(
            $validated['token'],
            $event,
            $request->user()?->id,
            null,
            $request->userAgent(),
        );

        return response()->json($this->present($result));
    }

    /**
     * @param  array{result: string, ticket?: TicketeraTicket, event?: TicketeraEvent, access?: TicketeraAccess, message?: string}  $result
     * @return array<string, mixed>
     */
    private function present(array $result): array
    {
        $ticket = $result['ticket'] ?? null;
        $event = $result['event'] ?? null;

        return [
            'result' => $result['result'],
            'message' => $result['message'] ?? null,
            'ticket' => $ticket ? [
                'number' => $ticket->number,
                'type' => $ticket->ticketType?->name,
                'holder' => $ticket->holder_name,
                'used_at' => $ticket->used_at?->format('d/m/Y H:i'),
            ] : null,
            'event' => $event ? ['name' => $event->name, 'starts_at' => $event->starts_at->format('d/m/Y H:i')] : null,
            'access' => isset($result['access']) && $result['access'] ? [
                'at' => $result['access']->created_at->format('d/m/Y H:i'),
                'by' => $result['access']->user?->name ?? $result['access']->staff?->name,
            ] : null,
        ];
    }
}
