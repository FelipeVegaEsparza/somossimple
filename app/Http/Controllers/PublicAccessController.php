<?php

namespace App\Http\Controllers;

use App\Enums\TicketeraEventStatus;
use App\Models\TicketeraEvent;
use App\Models\TicketeraStaff;
use App\Services\Ticketera\TicketeraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Modo control de acceso para personal con enlace propio (sin acceso al panel).
 */
class PublicAccessController extends Controller
{
    public function scanner(string $token): View
    {
        $staff = $this->staff($token);

        $events = $staff->business->ticketeraEvents()
            ->whereIn('status', [TicketeraEventStatus::Published->value, TicketeraEventStatus::Paused->value])
            ->orderBy('starts_at')
            ->get();

        $selected = $events->first();

        return view('public.ticketera.access', [
            'staff' => $staff,
            'events' => $events,
            'selected' => $selected,
            'counters' => $selected ? app(TicketeraService::class)->counters($selected) : ['entered' => 0, 'pending' => 0, 'total' => 0],
        ]);
    }

    public function validateToken(Request $request, string $token): JsonResponse
    {
        $staff = $this->staff($token);

        $data = $request->validate([
            'token' => ['required', 'string', 'max:2000'],
            'event_id' => ['nullable', 'integer'],
        ]);

        $event = null;
        if (! empty($data['event_id'])) {
            $event = TicketeraEvent::where('business_id', $staff->business_id)->find($data['event_id']);
        }

        $service = app(TicketeraService::class);
        $result = $service->validate($data['token'], $event, null, $staff->id, $request->userAgent());

        $counterEvent = $event ?? ($result['event'] ?? null);
        $counters = $counterEvent && $counterEvent->business_id === $staff->business_id
            ? $service->counters($counterEvent)
            : ['entered' => 0, 'pending' => 0, 'total' => 0];

        $ticket = $result['ticket'] ?? null;
        $access = $result['access'] ?? null;

        return response()->json([
            'result' => $result['result'],
            'message' => $result['message'] ?? null,
            'ticket' => $ticket ? [
                'number' => $ticket->number,
                'type' => $ticket->ticketType?->name,
                'holder' => $ticket->holder_name,
                'used_at' => $ticket->used_at?->format('d/m/Y H:i'),
            ] : null,
            'event' => isset($result['event']) && $result['event'] ? ['name' => $result['event']->name] : null,
            'access' => $access ? [
                'at' => $access->created_at->format('d/m/Y H:i'),
                'by' => $access->staff?->name ?? $access->user?->name,
            ] : null,
            'counters' => $counters,
        ]);
    }

    private function staff(string $token): TicketeraStaff
    {
        $staff = TicketeraStaff::where('token', $token)->firstOrFail();

        abort_unless($staff->isActive(), 404);

        return $staff;
    }
}
