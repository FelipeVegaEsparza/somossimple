<?php

namespace App\Http\Controllers\Panel\Ticketera;

use App\Models\TicketeraStaff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StaffController extends TicketeraController
{
    public function index(): View
    {
        $business = $this->business();

        return view('panel.ticketera.staff', [
            'business' => $business,
            'staff' => $business->ticketeraStaff()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:120'],
        ], [
            'name.required' => 'Ingresa el nombre de la persona.',
        ]);

        TicketeraStaff::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'token' => $this->uniqueToken(),
            'role' => TicketeraStaff::ROLE_ACCESS,
            'is_active' => true,
        ]);

        return back()->with('status', 'Persona agregada. Comparte su enlace de acceso.');
    }

    public function toggle(TicketeraStaff $staff): RedirectResponse
    {
        $business = $this->business();
        abort_unless($staff->business_id === $business->id, 404);

        $staff->update(['is_active' => ! $staff->is_active]);

        return back()->with('status', $staff->is_active ? 'Acceso activado.' : 'Acceso desactivado.');
    }

    public function destroy(TicketeraStaff $staff): RedirectResponse
    {
        $business = $this->business();
        abort_unless($staff->business_id === $business->id, 404);

        $staff->delete();

        return back()->with('status', 'Acceso eliminado.');
    }

    private function uniqueToken(): string
    {
        do {
            $token = Str::lower(Str::random(40));
        } while (TicketeraStaff::where('token', $token)->exists());

        return $token;
    }
}
