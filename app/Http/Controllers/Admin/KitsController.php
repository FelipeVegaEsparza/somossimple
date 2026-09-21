<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\PhysicalCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stock de kits físicos (tótems QR+NFC) para la venta: alta, listado por
 * estado y venta. La activación la hace el comprador desde su panel.
 */
class KitsController extends Controller
{
    public function index(Request $request): View
    {
        $query = PhysicalCode::query()->with('business');

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($w) use ($q) {
                $w->where('serial', 'like', "%{$q}%")
                    ->orWhereHas('business', fn ($b) => $b->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), PhysicalCode::statuses(), true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('negocio')) {
            $query->where('business_id', (int) $request->input('negocio'));
        }

        return view('admin.kits.index', [
            'kits' => $query->orderByDesc('created_at')->simplePaginate(20)->withQueryString(),
            'counts' => [
                'available' => PhysicalCode::where('status', PhysicalCode::STATUS_AVAILABLE)->count(),
                'sold' => PhysicalCode::where('status', PhysicalCode::STATUS_SOLD)->count(),
                'activated' => PhysicalCode::where('status', PhysicalCode::STATUS_ACTIVATED)->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:qr,nfc,qr_nfc'],
            'serial' => ['nullable', 'string', 'max:120', 'unique:physical_codes,serial'],
        ], [
            'type.required' => 'Selecciona el tipo de kit.',
            'serial.unique' => 'Ese serial ya está registrado.',
        ]);

        $serial = trim($validated['serial'] ?? '');

        if ($serial === '') {
            $serial = $this->generarSerial();
        }

        PhysicalCode::create([
            'type' => $validated['type'],
            'serial' => $serial,
            'status' => PhysicalCode::STATUS_AVAILABLE,
        ]);

        return back()->with('status', 'Kit '.$serial.' registrado en stock.');
    }

    private function generarSerial(): string
    {
        do {
            $serial = 'KIT-'.strtoupper(\Illuminate\Support\Str::random(6));
        } while (PhysicalCode::where('serial', $serial)->exists());

        return $serial;
    }

    public function sell(PhysicalCode $code): RedirectResponse
    {
        if ($code->status !== PhysicalCode::STATUS_AVAILABLE) {
            return back()->with('error', 'El kit '.$code->serial.' no está disponible para vender.');
        }

        $code->update([
            'status' => PhysicalCode::STATUS_SOLD,
            'delivered_at' => now(),
        ]);

        return back()->with('status', 'Kit '.$code->serial.' vendido. El comprador debe activarlo desde su panel.');
    }

    public function qrImage(PhysicalCode $code, Request $request): Response
    {
        $contenido = route('k.show', $code->serial, true);

        $result = (new Builder(
            writer: new SvgWriter(),
            data: $contenido,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 320,
            margin: 10,
        ))->build();

        $headers = ['Content-Type' => 'image/svg+xml'];

        if ($request->boolean('descargar')) {
            $headers['Content-Disposition'] = 'attachment; filename="qr-'.$code->serial.'.svg"';
        }

        return new Response($result->getString(), 200, $headers);
    }

    public function activateFor(Request $request, Business $business): RedirectResponse
    {
        $validated = $request->validate([
            'code_id' => ['required', 'integer', 'exists:physical_codes,id'],
        ], ['code_id.required' => 'Selecciona un kit vendido.']);

        $code = PhysicalCode::findOrFail($validated['code_id']);

        if ($code->status !== PhysicalCode::STATUS_SOLD) {
            return back()->with('error', 'El kit '.$code->serial.' no está vendido o ya fue activado.');
        }

        $code->update([
            'status' => PhysicalCode::STATUS_ACTIVATED,
            'business_id' => $business->id,
            'activated_at' => now(),
        ]);

        return back()->with('status', 'Kit '.$code->serial.' activado para «'.$business->name.'».');
    }
}
