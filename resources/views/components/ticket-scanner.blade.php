@props(['endpoint', 'event' => null, 'counters' => ['entered' => 0, 'pending' => 0, 'total' => 0]])

<div x-data="ticketScanner(@js($endpoint), @js($event ? (string) $event : ''), @js(csrf_token()), @js($counters))" x-init="init()">
    {{-- Contadores --}}
    <div class="grid grid-cols-3 gap-2 text-center">
        <div class="rounded-xl border border-line bg-surface py-3">
            <p class="text-2xl font-extrabold text-good" x-text="counters.entered">0</p>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-soft">Ingresaron</p>
        </div>
        <div class="rounded-xl border border-line bg-surface py-3">
            <p class="text-2xl font-extrabold text-ink" x-text="counters.pending">0</p>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-soft">Pendientes</p>
        </div>
        <div class="rounded-xl border border-line bg-surface py-3">
            <p class="text-2xl font-extrabold text-ink" x-text="counters.total">0</p>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-soft">Total</p>
        </div>
    </div>

    {{-- Visor de cámara + resultado --}}
    <div class="relative mt-4 overflow-hidden rounded-2xl bg-ink/90" style="aspect-ratio: 1 / 1;">
        <video x-ref="video" class="w-full h-full object-cover" playsinline muted></video>

        <template x-if="!result">
            <div class="pointer-events-none absolute inset-0 grid place-items-center">
                <div class="w-2/3 max-w-xs rounded-2xl border-4 border-white/70" style="aspect-ratio: 1 / 1;"></div>
            </div>
        </template>

        <div x-cloak x-show="result" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-6 text-center text-white" :class="resultColor(result.result)">
            <p class="text-2xl font-extrabold" x-text="resultLabel(result.result)"></p>
            <p class="text-sm" x-text="result.message"></p>
            <template x-if="result.ticket">
                <div class="mt-2 text-sm">
                    <p class="font-semibold" x-text="result.ticket.type + ' · ' + result.ticket.number"></p>
                    <p x-text="result.ticket.holder"></p>
                    <template x-if="result.access">
                        <p class="mt-1 text-xs opacity-90" x-text="'Validada: ' + result.access.at + (result.access.by ? ' · ' + result.access.by : '')"></p>
                    </template>
                </div>
            </template>
        </div>

        <div x-cloak x-show="error && !result" class="absolute inset-x-0 bottom-0 bg-ink/80 px-4 py-2 text-center text-xs text-white/80" x-text="error"></div>
    </div>

    {{-- Entrada manual --}}
    <form class="mt-4 flex gap-2" @submit.prevent="manualSubmit()">
        <input type="text" x-model="manual" class="input h-12" placeholder="Código de entrada (TKT-…) o pega el enlace">
        <button type="submit" class="btn-primary h-12 px-5 shrink-0" :disabled="busy">Validar</button>
    </form>
    <p class="mt-2 text-xs text-ink-faint">Apunta la cámara al QR de la entrada. Tras cada validación vuelve solo al escáner.</p>
</div>

<script>
    function ticketScanner(endpoint, eventId, csrf, counters) {
        return {
            endpoint: endpoint,
            eventId: eventId,
            csrf: csrf,
            counters: counters,
            manual: '',
            busy: false,
            result: null,
            error: '',
            scanning: false,
            detector: null,
            async init() {
                if (! ('BarcodeDetector' in window)) {
                    this.error = 'Este navegador no soporta escaneo con cámara. Ingresa el código manualmente.';
                    return;
                }
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    this.$refs.video.srcObject = stream;
                    await this.$refs.video.play();
                    this.detector = new BarcodeDetector({ formats: ['qr_code'] });
                    this.scanning = true;
                    this.loop();
                } catch (e) {
                    this.error = 'No se pudo abrir la cámara. Puedes validar con el código manual.';
                }
            },
            async loop() {
                if (! this.scanning) return;
                try {
                    const codes = await this.detector.detect(this.$refs.video);
                    if (codes.length && ! this.busy) {
                        await this.submit(codes[0].rawValue);
                    }
                } catch (e) {}
                setTimeout(() => this.loop(), 400);
            },
            async submit(value) {
                if (this.busy || ! value) return;
                this.busy = true;
                try {
                    const res = await fetch(this.endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                        body: JSON.stringify({ token: value, event_id: this.eventId || null }),
                    });
                    this.result = await res.json();
                    if (this.result.counters) this.counters = this.result.counters;
                } catch (e) {
                    this.result = { result: 'invalid', message: 'Error de conexión.' };
                }
                const delay = this.result.result === 'valid' ? 2000 : 3200;
                setTimeout(() => { this.result = null; this.busy = false; }, delay);
            },
            manualSubmit() {
                const value = this.manual.trim();
                if (value) this.submit(value);
                this.manual = '';
            },
            resultLabel(r) {
                return {
                    valid: '✓ ENTRADA VÁLIDA',
                    used: '⚠ ENTRADA YA UTILIZADA',
                    invalid: '✕ ENTRADA NO VÁLIDA',
                    wrong_event: '✕ NO CORRESPONDE A ESTE EVENTO',
                    cancelled: '✕ ENTRADA CANCELADA',
                    refunded: '✕ ENTRADA REEMBOLSADA',
                    unpaid: '✕ ORDEN NO PAGADA',
                }[r] || 'RESULTADO';
            },
            resultColor(r) {
                if (r === 'valid') return 'bg-good';
                if (r === 'used') return 'bg-warn';
                return 'bg-danger';
            },
        };
    }
</script>
