@extends('layouts.panel')

@section('title', 'Configuración de fidelización')

@section('content')
    <div class="mb-5">
        <p class="text-sm text-ink-soft">Fidelización</p>
        <h1 class="text-3xl font-bold tracking-tight">Configuración del programa</h1>
        <p class="text-sm text-ink-soft mt-1">Define cómo tus clientes acumulan y qué pueden obtener.</p>
    </div>

    @include('panel.loyalty._nav')

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('panel.loyalty.program.update') }}" class="max-w-2xl space-y-4"
          x-data="{ type: @js(old('type', $program->type->value)) }">
        @csrf
        @method('PUT')

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Datos del programa</h2>

            <div class="mt-5 space-y-4">
                <div>
                    <label for="name" class="label">Nombre del programa</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $program->name) }}" required class="input @error('name') input-error @enderror" placeholder="Club Amigos Café Patagonia">
                    @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="label">Descripción</label>
                    <textarea id="description" name="description" rows="3" class="input h-auto py-3" placeholder="Explica brevemente cómo funciona tu programa.">{{ old('description', $program->description) }}</textarea>
                </div>

                <div>
                    <label class="label">Tipo de programa</label>
                    <div class="grid sm:grid-cols-2 gap-2">
                        @foreach ($types as $type)
                            <label>
                                <input type="radio" name="type" value="{{ $type->value }}" x-model="type" class="sr-only peer">
                                <span class="block rounded-xl border border-line p-3 cursor-pointer peer-checked:border-primary peer-checked:bg-primary-tint">
                                    <span class="block font-semibold text-sm">{{ $type->label() }}</span>
                                    <span class="block text-xs text-ink-soft mt-0.5">{{ $type->description() }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('type') <p class="error-msg">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="unit_name" class="label">Nombre de la unidad</label>
                    <input id="unit_name" type="text" name="unit_name" value="{{ old('unit_name', $program->unit_name) }}" required class="input @error('unit_name') input-error @enderror" placeholder="puntos, visitas, sellos">
                    @error('unit_name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Reglas</h2>

            <template x-if="type === 'points' || type === 'rewards'">
                <div class="mt-5 grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="earn_amount" class="label">Monto que suma (CLP)</label>
                        <input id="earn_amount" type="number" name="earn_amount" min="1" value="{{ old('earn_amount', $program->earn_amount ?? 1000) }}" class="input">
                    </div>
                    <div>
                        <label for="earn_units" class="label">Unidades que entrega</label>
                        <input id="earn_units" type="number" name="earn_units" min="1" value="{{ old('earn_units', $program->earn_units ?? 10) }}" class="input">
                    </div>
                    <p class="sm:col-span-2 text-sm text-ink-soft">Ejemplo: $1.000 de compra = 10 puntos.</p>
                </div>
            </template>

            <template x-if="type === 'visits' || type === 'stamps'">
                <div class="mt-5">
                    <p class="text-sm text-ink-soft" x-text="type === 'visits' ? 'Cada visita registrada suma 1 unidad.' : 'Cada compra suma 1 sello.'"></p>
                    <input type="hidden" name="earn_units" value="1">
                </div>
            </template>

            @error('earn_units') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="card p-6">
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active)) class="w-4 h-4 rounded border-line text-primary focus:ring-primary/30">
                <span class="text-sm font-medium">Programa activo</span>
            </label>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Guardar configuración</button>
        </div>
    </form>
@endsection
