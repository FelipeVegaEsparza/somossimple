<?php

namespace App\Http\Controllers;

use App\Enums\Module;
use App\Models\Business;
use App\Models\LoyaltyProgram;
use App\Services\Loyalty\LoyaltyService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Página pública de fidelización de cada negocio: el cliente se registra,
 * obtiene su tarjeta digital y su código QR.
 */
class PublicLoyaltyController extends Controller
{
    public function show(string $slug): View
    {
        $business = $this->business($slug);

        return view('public.loyalty.show', [
            'business' => $business,
            'program' => $this->program($business),
            'rewards' => $business->loyaltyRewards()->where('is_active', true)->get(),
        ]);
    }

    public function registerForm(string $slug): View
    {
        $business = $this->business($slug);

        return view('public.loyalty.register', [
            'business' => $business,
            'program' => $this->program($business),
        ]);
    }

    public function register(string $slug, Request $request, LoyaltyService $loyalty): RedirectResponse
    {
        $business = $this->business($slug);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
        ], [
            'name.required' => 'Ingresa tu nombre.',
            'email.email' => 'Ingresa un correo válido.',
        ]);

        $member = $loyalty->createMember($business, $validated);

        return redirect()->route('loyalty.card', [$business->slug, $member->token])
            ->with('loyalty_ok', '¡Bienvenido! Esta es tu tarjeta digital.');
    }

    public function identify(string $slug, Request $request): RedirectResponse
    {
        $business = $this->business($slug);

        $value = trim((string) $request->input('identifier'));

        $member = $business->loyaltyMembers()
            ->where(function ($w) use ($value) {
                $w->where('code', $value)
                    ->orWhere('token', $value)
                    ->orWhere('phone', $value)
                    ->orWhere('email', $value);
            })
            ->first();

        if (! $member) {
            return back()->withErrors(['identifier' => 'No encontramos una tarjeta con ese dato.'])->withInput();
        }

        return redirect()->route('loyalty.card', [$business->slug, $member->token]);
    }

    public function card(string $slug, string $token): View
    {
        $business = $this->business($slug);
        $member = $business->loyaltyMembers()->where('token', $token)->firstOrFail();

        return view('public.loyalty.card', [
            'business' => $business,
            'program' => $this->program($business),
            'member' => $member,
            'availableRewards' => $member->availableRewards(),
            'rewards' => $business->loyaltyRewards()->where('is_active', true)->get(),
        ]);
    }

    public function qr(string $slug, string $token): Response
    {
        $business = $this->business($slug);
        $member = $business->loyaltyMembers()->where('token', $token)->firstOrFail();

        $result = (new Builder(
            writer: new SvgWriter,
            data: route('loyalty.card', [$business->slug, $member->token], true),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 320,
            margin: 8,
        ))->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/svg+xml']);
    }

    private function business(string $slug): Business
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        abort_unless($business->isPubliclyAvailable() && $business->isModuleActive(Module::Loyalty), 404);

        return $business;
    }

    private function program(Business $business): LoyaltyProgram
    {
        return app(LoyaltyService::class)->program($business);
    }
}
