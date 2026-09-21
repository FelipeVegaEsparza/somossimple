<x-module-page :business="$business" title="Registrarme" :icon="\App\Enums\Module::Loyalty->icon()" :tagline="$program->name">
    <p class="text-sm text-ink-soft">Déjanos tus datos y recibe tu tarjeta digital de fidelización. Solo pedimos lo necesario.</p>

    <form method="POST" action="{{ route('loyalty.register', $business->slug) }}" class="mt-5 space-y-3">
        @csrf

        <div>
            <label for="name" class="label">Tu nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input @error('name') input-error @enderror">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="label">Teléfono / WhatsApp (opcional)</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="input" placeholder="+56 9 …">
        </div>

        <div>
            <label for="email" class="label">Correo (opcional)</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="input">
            @error('email') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full h-12">Crear mi tarjeta</button>
    </form>

    <p class="mt-4 text-center text-xs text-ink-faint">Tus datos quedan guardados de forma segura y los usamos solo para tu tarjeta.</p>
</x-module-page>
