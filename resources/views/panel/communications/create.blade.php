@extends('layouts.panel')

@section('title', 'Nueva comunicación')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Nueva comunicación</h1>
                <p class="text-sm text-ink-soft mt-1">Se guarda en borrador y verás cuántos clientes la recibirán antes de enviar.</p>
            </div>
            <a href="{{ route('panel.communications.index') }}" class="btn-ghost">Volver</a>
        </div>

        <form method="POST" action="{{ route('panel.communications.store') }}" class="space-y-4">
            @csrf

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Contenido</h2>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="type" class="label">Tipo</label>
                        <select id="type" name="type" class="input" required>
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subject" class="label">Asunto</label>
                        <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required class="input">
                        @error('subject') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="body" class="label">Contenido</label>
                        <textarea id="body" name="body" rows="7" required class="input h-auto py-3">{{ old('body') }}</textarea>
                        @error('body') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_commercial" value="1" @checked(old('is_commercial', true)) class="mt-0.5 w-4 h-4 rounded border-line text-primary accent-primary">
                        <span class="text-sm">
                            <span class="font-medium">Comunicación comercial</span>
                            <span class="block text-ink-soft">Si es comercial, solo llega a clientes que consintieron recibir promociones. Desmarca para avisos operacionales (ej. cambios de horario).</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Destinatarios</h2>
                <div class="mt-5 space-y-3">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="audience" value="all" @checked(old('audience', 'all') === 'all') class="w-4 h-4 text-primary accent-primary">
                        <span class="text-sm font-medium">Todos los clientes con consentimiento</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="radio" name="audience" value="tag" @checked(old('audience') === 'tag') class="w-4 h-4 text-primary accent-primary">
                        <span class="text-sm font-medium">Clientes con una etiqueta</span>
                    </label>
                    <div>
                        <label for="tag_name" class="label">Etiqueta</label>
                        <input list="tags" id="tag_name" name="tag_name" value="{{ old('tag_name') }}" class="input" placeholder="Ej: VIP">
                        <datalist id="tags">
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->name }}">
                            @endforeach
                        </datalist>
                        @error('tag_name') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Crear borrador</button>
            </div>
        </form>
    </div>
@endsection
