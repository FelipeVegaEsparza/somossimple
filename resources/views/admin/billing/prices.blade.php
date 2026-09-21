@extends('admin.layouts.admin')

@section('title', 'Precios de módulos')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight">Precios de módulos</h1>
        <p class="text-sm text-ink-soft mt-1">Tarifa mensual de cada módulo de pago. El Perfil Digital es gratuito.</p>
    </div>

    <div class="card p-6 max-w-xl">
        <form method="POST" action="{{ route('admin.billing.prices.update') }}" class="space-y-5">
            @csrf

            <div class="flex items-center justify-between py-3 border-b border-line">
                <div>
                    <p class="font-semibold">Perfil Digital</p>
                    <p class="text-sm text-ink-soft">El perfil público del negocio</p>
                </div>
                <span class="badge bg-primary-tint text-primary">Gratis · Siempre activo</span>
            </div>

            @foreach ($paidModules as $module)
                <div class="flex items-center justify-between gap-4 py-3 border-b border-line">
                    <div>
                        <p class="font-semibold">{{ $module->label() }}</p>
                        <p class="text-sm text-ink-soft">Precio mensual en pesos (CLP)</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-ink-soft">$</span>
                        <input type="number" name="prices[{{ $module->value }}]" value="{{ old('prices.'.$module->value, $prices[$module->value] ?? '') }}"
                               min="100" step="1" class="input w-40" placeholder="Ej: 4990">
                    </div>
                    @error('prices.'.$module->value) <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div class="flex items-center justify-between gap-4 py-3 border-b border-line">
                <div>
                    <p class="font-semibold">Kit físico (tótem QR + NFC)</p>
                    <p class="text-sm text-ink-soft">Precio unitario de venta del kit</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-ink-soft">$</span>
                    <input type="number" name="kit_price" value="{{ old('kit_price', $kitPrice ?? '') }}"
                           min="100" step="1" class="input w-40" placeholder="Ej: 14990">
                </div>
                @error('kit_price') <p class="error-msg">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary">Guardar precios</button>
        </form>
    </div>
@endsection
