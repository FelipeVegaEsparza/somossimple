@php($items = [
    ['panel.loyalty.index', 'Dashboard', 'panel.loyalty.index'],
    ['panel.loyalty.quick', 'Registrar', 'panel.loyalty.quick'],
    ['panel.loyalty.members.index', 'Clientes', 'panel.loyalty.members.*'],
    ['panel.loyalty.rewards.index', 'Recompensas', 'panel.loyalty.rewards.*'],
    ['panel.loyalty.activities.index', 'Actividad', 'panel.loyalty.activities.*'],
    ['panel.loyalty.card', 'Tarjeta', 'panel.loyalty.card'],
    ['panel.loyalty.program', 'Configuración', 'panel.loyalty.program'],
])

<div class="mb-6 flex flex-wrap gap-2 border-b border-line pb-3">
    @foreach ($items as $item)
        @php($active = request()->routeIs($item[2]))
        <a href="{{ route($item[0]) }}"
           class="px-3.5 h-9 inline-flex items-center rounded-lg text-sm font-semibold {{ $active ? 'bg-primary-tint text-primary' : 'text-ink-soft hover:bg-app hover:text-ink' }}">
            {{ $item[1] }}
        </a>
    @endforeach
</div>
