<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agendar · {{ $business->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&family=Sora:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('public._pwa', ['business' => $business])
</head>
<body class="bg-app theme-{{ $business->theme ?: 'clasico' }}">
    <div class="max-w-md mx-auto min-h-screen bg-surface px-5 py-8" x-data="reserva()">
        <a href="{{ route('p.show', $business->slug) }}" class="text-sm font-semibold text-ink-soft hover:text-ink">← Volver a {{ $business->name }}</a>
        <h1 class="mt-4 text-2xl font-extrabold tracking-tight">Agendar hora</h1>
        <p class="mt-1 text-sm text-ink-soft">Elige servicio, fecha y hora. Te contactamos para confirmar.</p>

        @if (session('reserva_ok'))
            <div class="mt-5 rounded-xl bg-primary-tint px-5 py-4 text-sm">
                <p class="font-semibold text-good">Solicitud enviada</p>
                <p class="mt-1 text-ink">Tu reserva de <strong>{{ session('reserva_ok')['service'] }}</strong> quedó en estado <strong>pendiente</strong> para {{ session('reserva_ok')['date'] }} a las {{ session('reserva_ok')['time'] }} hrs.</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-xl bg-red-50 border border-danger/30 px-5 py-4 text-sm text-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('p.reservation.store', $business->slug) }}" class="mt-6">
            @csrf

            <div>
                <h2 class="label">1 · Servicio</h2>
                <div class="space-y-2">
                    @forelse ($services as $service)
                        <label class="flex items-center justify-between gap-3 rounded-xl border border-line px-4 py-3.5 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary-tint">
                            <span>
                                <span class="block font-semibold">{{ $service->name }}</span>
                                <span class="block text-xs text-ink-soft mt-0.5">{{ $service->duration_minutes }} min{{ $service->priceDisplay() ? ' · '.$service->priceDisplay() : '' }}</span>
                            </span>
                            <input type="radio" name="service_id" value="{{ $service->id }}" required
                                   x-model="serviceId" @change="cargarHoras()" class="w-4 h-4 text-primary accent-primary">
                        </label>
                    @empty
                        <p class="text-sm text-ink-soft">Este negocio aún no publica servicios para agendar.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                <h2 class="label">2 · Fecha</h2>
                <input type="date" name="date" x-model="date" :min="hoy" :max="maxDate" @change="cargarHoras()" required class="input">
            </div>

            <div class="mt-6" x-show="date">
                <h2 class="label">3 · Hora</h2>
                <div x-show="cargandoHoras" class="text-sm text-ink-soft">Buscando horas…</div>
                <div x-show="!cargandoHoras && horas.length === 0" class="text-sm text-ink-soft">No hay horas disponibles para esa fecha.</div>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="h in horas" :key="h">
                        <label class="cursor-pointer">
                            <input type="radio" name="time" :value="h" required class="sr-only peer">
                            <span class="block text-center rounded-lg border border-line py-2.5 text-sm font-medium peer-checked:border-primary peer-checked:bg-primary peer-checked:text-white" x-text="h"></span>
                        </label>
                    </template>
                </div>
            </div>

            <div class="mt-6 space-y-4" x-show="serviceId">
                <div>
                    <label for="client_name" class="label">Tu nombre</label>
                    <input id="client_name" type="text" name="client_name" required class="input" placeholder="Nombre y apellido">
                </div>
                <div>
                    <label for="phone" class="label">Teléfono</label>
                    <input id="phone" type="text" name="phone" required class="input" placeholder="+56 9 …">
                </div>
                <div>
                    <label for="email" class="label">Correo (opcional)</label>
                    <input id="email" type="email" name="email" class="input" placeholder="solo si quieres confirmación por correo">
                </div>

                <button type="submit" class="w-full h-13 py-3.5 rounded-xl bg-primary text-white font-semibold">Solicitar hora</button>
            </div>
        </form>
    </div>

    <script>
        function reserva() {
            const hoy = new Date();
            const iso = (d) => d.toISOString().split('T')[0];
            const max = new Date();
            max.setDate(max.getDate() + 30);
            const maxDate = iso(max);

            return {
                serviceId: null,
                date: '',
                horas: [],
                cargandoHoras: false,
                hoy: iso(hoy),
                maxDate,
                async cargarHoras() {
                    if (!this.serviceId || !this.date) return;
                    this.cargandoHoras = true;
                    this.horas = [];
                    const url = @js(route('p.reservation.times', $business->slug)).replace('/horas', '/horas');
                    const res = await fetch(url + '?' + new URLSearchParams({ service_id: this.serviceId, date: this.date }), { headers: { Accept: 'application/json' } });
                    const data = await res.json();
                    this.horas = data.times || [];
                    this.cargandoHoras = false;
                }
            };
        }
    </script>
</body>
</html>
