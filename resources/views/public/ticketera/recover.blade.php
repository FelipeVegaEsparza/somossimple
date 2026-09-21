<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar entradas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-app">
    <div class="max-w-md mx-auto min-h-screen bg-surface shadow-card p-6">
        <h1 class="text-2xl font-extrabold tracking-tight">Recuperar entradas</h1>
        <p class="mt-1 text-sm text-ink-soft">Ingresa el correo con que compraste y el número de compra.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('ticketera.recover.submit') }}" class="mt-5 space-y-4">
            @csrf
            <div>
                <label for="email" class="label">Correo</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input">
            </div>
            <div>
                <label for="number" class="label">Número de compra</label>
                <input id="number" type="text" name="number" value="{{ old('number') }}" required class="input font-mono" placeholder="ORD-2026-000001">
            </div>
            <button type="submit" class="btn-primary w-full h-12">Ver mis entradas</button>
        </form>
    </div>
</body>
</html>
