@php($items = [
    ['panel.ticketera.index', 'Dashboard', 'panel.ticketera.index'],
    ['panel.ticketera.events.index', 'Eventos', 'panel.ticketera.events.*'],
    ['panel.ticketera.orders.index', 'Órdenes', 'panel.ticketera.orders.*'],
    ['panel.ticketera.tickets.index', 'Entradas', 'panel.ticketera.tickets.*'],
    ['panel.ticketera.access', 'Control de acceso', 'panel.ticketera.access'],
    ['panel.ticketera.staff.index', 'Personal', 'panel.ticketera.staff.*'],
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
