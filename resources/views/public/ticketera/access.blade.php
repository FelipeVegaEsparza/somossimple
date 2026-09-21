<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Control de acceso</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app">
    <div class="max-w-md mx-auto min-h-screen bg-surface shadow-card p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-ink-faint">{{ $staff->business?->name }}</p>
        <h1 class="text-2xl font-extrabold tracking-tight">Escanear entrada</h1>
        <p class="mt-1 text-sm text-ink-soft">{{ $staff->name }} · Control de acceso</p>

        @if ($events->isEmpty())
            <div class="mt-6 rounded-xl border border-line bg-app px-4 py-3 text-sm text-ink-soft">No hay eventos activos para validar.</div>
        @else
            <form method="GET" action="{{ route('ticketera.access', $staff->token) }}" class="mt-4">
                <label class="label">Evento</label>
                <select name="event" class="input" onchange="this.form.submit()">
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" @selected($selected?->id === $event->id)>{{ $event->name }}</option>
                    @endforeach
                </select>
            </form>

            <div class="mt-4">
                @if ($selected)
                    <x-ticket-scanner
                        :endpoint="route('ticketera.access.validate', $staff->token)"
                        :event="$selected->id"
                        :counters="$counters"
                    />
                @endif
            </div>
        @endif
    </div>
</body>
</html>
