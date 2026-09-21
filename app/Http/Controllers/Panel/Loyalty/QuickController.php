<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Enums\LoyaltyActivityType;
use App\Models\Business;
use App\Models\LoyaltyMember;
use App\Services\Loyalty\LoyaltyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class QuickController extends LoyaltyController
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $q = trim((string) $request->input('q'));

        return view('panel.loyalty.quick', [
            'business' => $business,
            'program' => $this->program($business),
            'q' => $q,
            'members' => $this->search($business, $q),
            'rewards' => $business->loyaltyRewards()->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request, LoyaltyService $loyalty): RedirectResponse
    {
        $business = $this->business();
        $program = $this->program($business);

        $data = $request->validate([
            'member' => ['required', 'string', 'max:255'],
            'action' => ['required', 'in:points,visit,stamp,redeem'],
            'amount' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'reward_id' => ['nullable', 'integer'],
        ], [
            'member.required' => 'Escribe o escanea el código del cliente.',
        ]);

        $member = $this->findMember($business, $data['member']);

        if (! $member) {
            return back()->with('error', 'No encontramos a ese cliente. Revisa el código o el nombre.');
        }

        if (! $member->isActive()) {
            return back()->with('error', 'El cliente '.$member->name.' está inactivo.');
        }

        switch ($data['action']) {
            case 'points':
                $units = (int) ($data['amount'] ?? $program->earn_units ?: 1);
                $loyalty->register($member, LoyaltyActivityType::EarnPoints, $units, 'Registro rápido', $request->user());
                $message = 'Sumamos '.$units.' '.$program->unit_name.' a '.$member->name.'.';
                break;

            case 'visit':
                $loyalty->register($member, LoyaltyActivityType::EarnVisit, 1, 'Visita registrada', $request->user());
                $message = 'Visita registrada para '.$member->name.'.';
                break;

            case 'stamp':
                $loyalty->register($member, LoyaltyActivityType::EarnStamp, 1, 'Sello registrado', $request->user());
                $message = 'Sello registrado para '.$member->name.'.';
                break;

            default:
                $reward = $business->loyaltyRewards()->where('is_active', true)->find($data['reward_id'] ?? 0);

                if (! $reward) {
                    return back()->with('error', 'Elige una recompensa válida.');
                }

                if (! $loyalty->redeem($member, $reward, $request->user())) {
                    return back()->with('error', 'El cliente no tiene suficientes unidades para «'.$reward->name.'».');
                }

                $message = 'Canjeamos «'.$reward->name.'» para '.$member->name.'.';
        }

        return redirect()->route('panel.loyalty.quick', ['q' => $member->code])->with('status', $message);
    }

    /**
     * Busca por nombre, teléfono, código de cliente o token.
     *
     * @return Collection<int, LoyaltyMember>
     */
    private function search(Business $business, string $q)
    {
        if ($q === '') {
            return collect();
        }

        return $business->loyaltyMembers()
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('token', $q);
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    private function findMember(Business $business, string $value): ?LoyaltyMember
    {
        return $business->loyaltyMembers()
            ->where(function ($w) use ($value) {
                $w->where('code', $value)
                    ->orWhere('token', $value);

                if (is_numeric($value)) {
                    $w->orWhere('id', (int) $value);
                }
            })
            ->first();
    }
}
