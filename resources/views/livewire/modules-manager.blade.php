<div>
    @if (! $hasBusiness)
        <div class="card p-6">
            <p class="text-sm text-ink-soft">Primero crea tu negocio para administrar tus módulos.</p>
            <a href="{{ route('panel.index') }}" class="btn-primary mt-4">Crear negocio</a>
        </div>
    @else
        @if ($notice)
            <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ $notice }}</div>
        @endif

        <div class="card overflow-hidden">
            <ul class="divide-y divide-line">
                @foreach ($modules as $module)
                    <li class="p-5 flex items-start justify-between gap-4" wire:key="module-{{ $module['key'] }}">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h3 class="font-semibold">{{ $module['label'] }}</h3>
                                @if ($module['free'])
                                    <span class="badge bg-primary-tint text-primary">Gratis</span>
                                @else
                                    <span class="badge {{ $module['active'] ? 'bg-[#e6f6ee] text-good' : 'bg-app text-ink-soft' }}">
                                        {{ $module['active'] ? 'Activo' : 'Inactivo' }}
                                    </span>
                                @endif
                                @if ($module['pending'])
                                    <span class="badge bg-[#fff6e0] text-warn">Solicitud pendiente</span>
                                @endif
                            </div>
                            <p class="text-sm text-ink-soft mt-1">{{ $module['description'] }}</p>
                        </div>

                        @if ($module['free'])
                            <span class="badge bg-app text-ink-soft shrink-0">Siempre activo</span>
                        @elseif ($module['pending'])
                            <span class="btn-secondary shrink-0 opacity-60 cursor-not-allowed" aria-disabled="true">Solicitud enviada</span>
                        @elseif ($module['active'])
                            <button type="button" wire:click="solicitar('{{ $module['key'] }}', 'deactivate')" class="btn-secondary shrink-0">
                                Solicitar desactivación
                            </button>
                        @else
                            <button type="button" wire:click="solicitar('{{ $module['key'] }}', 'activate')" class="btn-secondary shrink-0">
                                Solicitar activación
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="mt-3 text-xs text-ink-faint">Los módulos de pago se activan tras confirmación. El Perfil Digital es gratuito y siempre está disponible.</p>
    @endif
</div>
