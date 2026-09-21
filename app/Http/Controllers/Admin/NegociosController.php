<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\ModuleAccess;
use App\Models\ModuleActivationRequest;
use App\Models\SlugAlias;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class NegociosController extends Controller
{
    public function index(): View
    {
        $paidModules = collect(Module::cases())->reject(fn ($m) => $m->isFree())->pluck('value');
        $billing = app(\App\Services\BillingService::class);

        $atrasados = collect();
        if (\App\Models\ModulePrice::exists()) {
            $atrasados = Business::with(['moduleAccess'])->get()->flatMap(function (Business $business) use ($billing) {
                return collect(Module::cases())
                    ->reject(fn ($m) => $m->isFree())
                    ->filter(fn ($m) => $business->isModuleActive($m) && $billing->statusFor($business, $m) === 'atrasado')
                    ->map(fn ($m) => ['business' => $business, 'module' => $m]);
            })->take(6)->values();
        }

        $topEscaneos = \App\Models\AnalyticsEvent::query()
            ->where('type', \App\Models\AnalyticsEvent::TYPE_QR_SCAN)
            ->selectRaw('business_id, count(*) as total')
            ->groupBy('business_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->load('business');

        $platformWeekly = collect(range(6, 0))->map(function (int $i) {
            $date = \Carbon\Carbon::today()->subDays($i);

            return [
                'label' => $date->isoFormat('dd'),
                'visits' => \App\Models\AnalyticsEvent::where('type', \App\Models\AnalyticsEvent::TYPE_PROFILE_VISIT)->whereDate('created_at', $date)->count(),
                'scans' => \App\Models\AnalyticsEvent::where('type', \App\Models\AnalyticsEvent::TYPE_QR_SCAN)->whereDate('created_at', $date)->count(),
            ];
        })->values();

        return view('admin.dashboard', [
            'total' => Business::count(),
            'pausados' => Business::where('is_paused', true)->count(),
            'activos' => Business::where('is_paused', false)->count(),
            'modulosActivos' => ModuleAccess::whereIn('module', $paidModules)->where('active', true)->count(),
            'recientes' => Business::with('account')->orderByDesc('businesses.created_at')->limit(5)->get(),
            'activaciones' => ModuleActivationRequest::where('status', 'pending')
                ->with(['business.account'])
                ->orderByDesc('created_at')
                ->get(),
            'solicitudesKits' => \App\Models\KitRequest::where('status', 'nueva')->count(),
            'kits' => [
                'available' => \App\Models\PhysicalCode::where('status', \App\Models\PhysicalCode::STATUS_AVAILABLE)->count(),
                'sold' => \App\Models\PhysicalCode::where('status', \App\Models\PhysicalCode::STATUS_SOLD)->count(),
                'activated' => \App\Models\PhysicalCode::where('status', \App\Models\PhysicalCode::STATUS_ACTIVATED)->count(),
            ],
            'atrasados' => $atrasados,
            'topEscaneos' => $topEscaneos,
            'platformWeekly' => $platformWeekly,
            'platformMax' => max(1, (int) $platformWeekly->max('visits'), (int) $platformWeekly->max('scans')),
        ]);
    }

    public function listNegocios(Request $request): View
    {
        $query = Business::query()->with('account');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('businesses.name', 'like', "%{$q}%")
                    ->orWhereHas('account', function ($a) use ($q) {
                        $a->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        return view('admin.negocios.index', [
            'negocios' => $query->orderByDesc('businesses.created_at')->simplePaginate(20)->withQueryString(),
            'total' => Business::count(),
            'pausados' => Business::where('is_paused', true)->count(),
        ]);
    }

    public function show(Business $business): View
    {
        $business->load(['account', 'moduleAccess']);
        $stats = app(\App\Services\AnalyticsService::class)->counts($business);

        return view('admin.negocios.show', [
            'business' => $business,
            'scansQr' => $stats['qr'],
            'kitsSold' => \App\Models\PhysicalCode::where('status', \App\Models\PhysicalCode::STATUS_SOLD)
                ->whereNull('business_id')
                ->orderBy('serial')
                ->get(),
            'paidModules' => collect(Module::cases())->reject(fn ($m) => $m->isFree())->values(),
        ]);
    }

    public function toggleModule(Business $business, string $module): RedirectResponse
    {
        $moduleEnum = Module::tryFrom($module);

        if ($moduleEnum === null || $moduleEnum->isFree()) {
            return back()->with('error', 'El Perfil Digital es gratuito y no se puede desactivar.');
        }

        $access = ModuleAccess::ofBusiness($business)->firstOrCreate(
            ['business_id' => $business->id, 'module' => $moduleEnum->value],
            ['active' => false],
        );

        if ($access->active) {
            $access->deactivate();
        } else {
            $access->activate();
        }

        ModuleActivationRequest::ofBusiness($business)->where('module', $moduleEnum->value)->delete();

        return back()->with('status', 'Módulo '.$moduleEnum->label().($access->active ? ' activado.' : ' desactivado.'));
    }

    public function togglePause(Business $business): RedirectResponse
    {
        $business->update(['is_paused' => ! $business->is_paused]);

        $estado = $business->is_paused ? 'pausada' : 'reactivada';

        return back()->with('status', 'Cuenta de «'.$business->name.'» '.$estado.'. Ningún dato fue eliminado.');
    }

    public function updateAccount(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$business->account_id],
        ], [
            'name.required' => 'Ingresa el nombre de la cuenta.',
            'email.required' => 'Ingresa el correo de la cuenta.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Ese correo ya está en uso por otra cuenta.',
        ]);

        $business->account->update($validated);

        return back()->with('status', 'Datos de la cuenta actualizados.');
    }

    public function resetPassword(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Ingresa la contraseña nueva.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $business->account->update(['password' => Hash::make($validated['password'])]);

        return back()->with('status', 'Contraseña restablecida. El propietario debe usar la nueva contraseña.');
    }

    public function updateSlug(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ], [
            'slug.required' => 'Ingresa la URL.',
            'slug.regex' => 'Usa solo minúsculas, números y guiones.',
        ]);

        $newSlug = $validated['slug'];

        if (! Business::slugAvailable($newSlug, $business->id)) {
            return back()->with('error', 'Esa URL ya está en uso o está reservada por la plataforma.');
        }

        if ($newSlug !== $business->slug) {
            SlugAlias::create([
                'business_id' => $business->id,
                'old_slug' => $business->slug,
            ]);

            $business->update(['slug' => $newSlug]);
        }

        return back()->with('status', 'URL actualizada a «'.$newSlug.'». Los kits no se ven afectados y la URL anterior sigue redirigiendo.');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $account = $business->account;

        if ($account?->is_platform_admin) {
            return back()->with('error', 'No puedes eliminar la cuenta de un administrador de la plataforma.');
        }

        // Los kits físicos no se eliminan: vuelven a quedar vendidos y sin negocio.
        \App\Models\PhysicalCode::where('business_id', $business->id)->update([
            'business_id' => null,
            'status' => \App\Models\PhysicalCode::STATUS_SOLD,
            'activated_at' => null,
        ]);

        $nombre = $business->name;

        $account?->delete();

        return redirect()->route('admin.business.index')
            ->with('status', 'La cuenta de «'.$nombre.'» y sus datos fueron eliminados. Los kits volvieron al inventario como vendidos.');
    }

    public function impersonate(Business $business): RedirectResponse
    {
        if (session()->has('admin_original_user_id')) {
            abort(403, 'Ya estás personificando una cuenta.');
        }

        $admin = auth()->user();

        if ($admin->is($business->account)) {
            abort(403, 'No puedes personificar tu propia cuenta.');
        }

        session(['admin_original_user_id' => $admin->id]);

        Auth::login($business->account);

        return redirect()->route('panel.index')
            ->with('status', 'Estás actuando como el propietario de «'.$business->name.'». Cuando termines, vuelve al panel de administración.');
    }

    public function leaveImpersonation(): RedirectResponse
    {
        $originalId = session('admin_original_user_id');

        if (! $originalId) {
            return redirect()->route('admin.index');
        }

        $admin = User::find($originalId);

        session()->forget('admin_original_user_id');

        if ($admin) {
            Auth::login($admin);
        }

        return redirect()->route('admin.index')->with('status', 'Volviste al panel de administración.');
    }
}
