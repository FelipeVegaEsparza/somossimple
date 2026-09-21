<x-module-page :business="$business" title="Novedades" :icon="\App\Enums\Module::Clients->icon()" tagline="Recibe nuestras promociones y novedades">
    <p class="text-sm text-ink-soft">Déjanos tus datos y te avisaremos cuando tengamos promociones, ofertas o novedades.</p>

    @if (session('promo_ok'))
        <p class="mt-4 rounded-xl bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('promo_ok') }}</p>
    @endif

    <form method="POST" action="{{ route('p.voluntary', $business->slug) }}" class="mt-4 space-y-3">
        @csrf
        <div class="grid grid-cols-2 gap-2">
            <input type="text" name="name" required placeholder="Tu nombre" class="input">
            <input type="tel" name="phone" required placeholder="WhatsApp o teléfono" class="input">
        </div>
        <input type="email" name="email" placeholder="Correo (para recibir promociones)" class="input">
        <label class="flex items-start gap-2.5 text-sm text-ink-soft cursor-pointer">
            <input type="checkbox" name="consent" value="1" class="mt-0.5 w-4 h-4 rounded border-line text-primary accent-primary">
            <span>Acepto recibir comunicaciones comerciales por correo. Puedo pedir que me dejen de escribir cuando quiera.</span>
        </label>
        <button type="submit" class="btn-secondary w-full">Registrarme</button>
    </form>
</x-module-page>
