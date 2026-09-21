<?php

namespace App\Http\Controllers\Panel\Loyalty;

use App\Enums\LoyaltyWalletPlatform;
use App\Enums\LoyaltyWalletStatus;
use App\Models\Business;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyReward;
use App\Models\LoyaltyWalletCard;
use App\Services\Loyalty\LoyaltyService;
use App\Services\Loyalty\Wallet\WalletException;
use App\Services\Loyalty\Wallet\WalletManager;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends LoyaltyController
{
    public function index(Request $request): View
    {
        $business = $this->business();
        $q = trim((string) $request->input('q'));

        $members = $business->loyaltyMembers()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('last_activity_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('panel.loyalty.members.index', [
            'business' => $business,
            'program' => $this->program($business),
            'members' => $members,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        $business = $this->business();

        return view('panel.loyalty.members.create', [
            'business' => $business,
            'program' => $this->program($business),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
        ], [
            'name.required' => 'Ingresa el nombre del cliente.',
            'email.email' => 'Ingresa un correo válido.',
        ]);

        $member = app(LoyaltyService::class)->createMember($business, $validated);

        return redirect()->route('panel.loyalty.members.show', $member)
            ->with('status', 'Cliente registrado con el identificador '.$member->code.'.');
    }

    public function show(LoyaltyMember $member): View
    {
        $business = $this->guard($member);
        $program = $this->program($business);

        return view('panel.loyalty.members.show', [
            'business' => $business,
            'program' => $program,
            'member' => $member,
            'activities' => $member->activities()->with(['reward', 'user'])->paginate(15),
            'availableRewards' => $member->availableRewards(),
            'allRewards' => $business->loyaltyRewards()->where('is_active', true)->get(),
            'walletCards' => $member->walletCards()->get()->keyBy(fn ($card) => $card->platform->value),
            'walletPlatforms' => LoyaltyWalletPlatform::cases(),
            'walletConfigured' => collect(LoyaltyWalletPlatform::cases())
                ->mapWithKeys(fn ($platform) => [$platform->value => app(WalletManager::class)->isConfigured($platform)])
                ->all(),
        ]);
    }

    public function toggleStatus(LoyaltyMember $member): RedirectResponse
    {
        $this->guard($member);

        $member->update([
            'status' => $member->isActive() ? LoyaltyMember::STATUS_INACTIVE : LoyaltyMember::STATUS_ACTIVE,
        ]);

        return back()->with('status', $member->isActive() ? 'Cliente activado.' : 'Cliente desactivado.');
    }

    public function destroy(LoyaltyMember $member): RedirectResponse
    {
        $this->guard($member);

        $name = $member->name;
        $member->delete();

        return redirect()->route('panel.loyalty.members.index')->with('status', 'Cliente «'.$name.'» eliminado.');
    }

    public function qr(LoyaltyMember $member): Response
    {
        $business = $this->guard($member);

        $result = (new Builder(
            writer: new SvgWriter,
            data: route('loyalty.card', [$business->slug, $member->token], true),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 320,
            margin: 10,
        ))->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/svg+xml']);
    }

    public function reward(Request $request, LoyaltyMember $member, LoyaltyReward $reward): RedirectResponse
    {
        $business = $this->guard($member);
        abort_unless($reward->business_id === $business->id, 404);

        $activity = app(LoyaltyService::class)->redeem($member, $reward, $request->user(), 'Canje: '.$reward->name);

        if (! $activity) {
            return back()->with('error', 'El cliente aún no tiene suficientes unidades para «'.$reward->name.'».');
        }

        return back()->with('status', 'Recompensa «'.$reward->name.'» canjeada correctamente.');
    }

    public function wallet(LoyaltyMember $member, string $platform): RedirectResponse
    {
        $business = $this->guard($member);
        $walletPlatform = LoyaltyWalletPlatform::tryFrom($platform);

        abort_if($walletPlatform === null, 404);

        $manager = app(WalletManager::class);
        $card = LoyaltyWalletCard::firstOrCreate(
            ['loyalty_member_id' => $member->id, 'platform' => $walletPlatform->value],
            ['business_id' => $business->id, 'status' => LoyaltyWalletStatus::NotAdded],
        );

        if (! $manager->isConfigured($walletPlatform)) {
            return back()->with('error', $walletPlatform->label().' aún no está configurado.');
        }

        try {
            $manager->provider($walletPlatform)->generate($member);
        } catch (WalletException $e) {
            $card->markError($e->getMessage());

            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Tarjeta enviada a '.$walletPlatform->label().'.');
    }

    /**
     * Asegura que el cliente pertenece al negocio del usuario.
     */
    private function guard(LoyaltyMember $member): Business
    {
        $business = $this->business();

        abort_unless($member->business_id === $business->id, 404);

        return $business;
    }
}
