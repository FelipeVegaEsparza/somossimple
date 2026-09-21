@extends('admin.layouts.admin')

@section('title', $business->name)

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
        <div>
            <p class="text-sm text-ink-soft">Ficha de negocio</p>
            <h1 class="text-3xl font-bold tracking-tight">{{ $business->name }}</h1>
            <p class="text-sm text-ink-soft mt-1 font-mono">/{{ $business->slug }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.business.index') }}" class="btn-ghost">← Volver</a>
            <span class="badge {{ $business->is_paused ? 'bg-red-50 text-danger' : 'bg-[#e6f6ee] text-good' }}">
                {{ $business->is_paused ? 'Pausado' : 'Activo' }}
            </span>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-line bg-primary-tint px-4 py-3 text-sm text-ink">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-danger/30 bg-red-50 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">URL pública</h2>
                <p class="text-sm text-ink-soft mt-0.5">Raíz de la plataforma. Los kits apuntan a /k/{serial}, así que cambiar la URL no los afecta; la URL anterior seguirá redirigiendo.</p>

                <form method="POST" action="{{ route('admin.business.slug', $business) }}" class="mt-3 flex gap-2">
                    @csrf
                    <span class="input inline-flex items-center w-auto px-3 text-ink-soft">/</span>
                    <input type="text" name="slug" value="{{ $business->slug }}" class="input" required maxlength="60">
                    <button type="submit" class="btn-secondary shrink-0">Guardar URL</button>
                </form>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Módulos</h2>
                <p class="text-sm text-ink-soft mt-0.5">Perfil Digital: gratis y siempre activo. Los demás se activan por separado.</p>

                <ul class="mt-4 divide-y divide-line">
                    <li class="py-3 flex items-center justify-between gap-3">
                        <span class="text-sm font-medium">Perfil Digital</span>
                        <span class="badge bg-primary-tint text-primary shrink-0">Gratis · Siempre activo</span>
                    </li>
                    @foreach ($paidModules as $module)
                        @php($access = $business->moduleAccess->firstWhere('module', $module->value))
                        <li class="py-3 flex items-center justify-between gap-3">
                            <span class="text-sm font-medium">{{ $module->label() }}</span>
                            <form method="POST" action="{{ route('admin.business.module', [$business, $module->value]) }}">
                                @csrf
                                <button type="submit" class="text-sm font-semibold {{ $access?->active ? 'text-danger' : 'text-primary' }}">
                                    {{ $access?->active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Cuenta</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-soft">Nombre</dt><dd class="font-medium">{{ $business->account->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Correo</dt><dd class="font-medium">{{ $business->account->email }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Creado</dt><dd class="font-medium">{{ $business->created_at->translatedFormat('d/m/Y') }}</dd></div>
                </dl>

                <h3 class="mt-6 text-sm font-semibold">Editar datos de la cuenta</h3>
                <form method="POST" action="{{ route('admin.business.account', $business) }}" class="mt-3 space-y-3">
                    @csrf
                    <input type="text" name="name" value="{{ $business->account->name }}" required class="input">
                    <input type="email" name="email" value="{{ $business->account->email }}" required class="input">
                    <button type="submit" class="btn-secondary">Guardar cuenta</button>
                </form>

                <h3 class="mt-6 text-sm font-semibold">Restablecer contraseña</h3>
                <form method="POST" action="{{ route('admin.business.password', $business) }}" class="mt-3 space-y-3">
                    @csrf
                    <input type="password" name="password" placeholder="Contraseña nueva (mín. 8)" class="input" required>
                    <input type="password" name="password_confirmation" placeholder="Repite la contraseña" class="input" required>
                    <button type="submit" class="btn-secondary">Restablecer contraseña</button>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-6">
                <h2 class="text-lg font-semibold">Acciones</h2>
                <div class="mt-4 space-y-3">
                    <form method="POST" action="{{ route('admin.business.pause', $business) }}" onsubmit="return confirm('¿{{ $business->is_paused ? 'Reactivar' : 'Pausar' }} la cuenta de «{{ $business->name }}»? Al {{ $business->is_paused ? 'reactivar' : 'pausar' }} no se elimina ningún dato.')">
                        @csrf
                        <button type="submit" class="{{ $business->is_paused ? 'btn-primary' : 'btn-secondary' }} w-full">
                            {{ $business->is_paused ? 'Reactivar cuenta' : 'Pausar cuenta' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.business.impersonate', $business) }}" onsubmit="return confirm('¿Entrar como el propietario de «{{ $business->name }}»?')">
                        @csrf
                        <button type="submit" class="btn-primary w-full">Entrar como este negocio</button>
                    </form>
                </div>
                <p class="mt-4 text-xs text-ink-faint">Al personificar actúas como el dueño; podrás volver al panel de administración desde el aviso visible.</p>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Activar un kit para este negocio</h2>
                <p class="text-sm text-ink-soft mt-0.5">Si el cliente te entregó el serial o quieres dejarlo listo tú mismo.</p>

                @if ($kitsSold->isEmpty())
                    <p class="mt-3 text-xs text-ink-faint">No hay kits vendidos sin activar. Véndelos desde la sección Kits.</p>
                @else
                    <form method="POST" action="{{ route('admin.kits.activate', $business) }}" class="mt-3 flex gap-2">
                        @csrf
                        <select name="code_id" class="input" required>
                            @foreach ($kitsSold as $kit)
                                <option value="{{ $kit->id }}">{{ $kit->serial }} · {{ $kit->typeLabel() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-secondary shrink-0">Activar kit</button>
                    </form>
                @endif
            </div>

            <div class="card p-6 border-danger/30">
                <h2 class="text-lg font-semibold text-danger">Eliminar cuenta</h2>
                <p class="text-sm text-ink-soft mt-1">Borra la cuenta, el negocio y todos sus datos (clientes, reservas, catálogo, etc.). No se puede deshacer. Los kits vuelven al inventario como vendidos.</p>
                <form method="POST" action="{{ route('admin.business.destroy', $business) }}" class="mt-4" onsubmit="return confirm('¿Eliminar definitivamente la cuenta de {{ $business->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary w-full text-danger border-danger/40 hover:border-danger">Eliminar cuenta y datos</button>
                </form>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Página pública</h2>
                <a href="{{ route('p.show', $business->slug) }}" target="_blank" rel="noopener" class="btn-secondary mt-3 w-full">Ver perfil público</a>
                <p class="mt-3 text-xs text-ink-faint">La URL pública es estable y no se modifica desde la administración: el QR/NFC sigue apuntando al mismo perfil.</p>
            </div>

            <div class="card p-6">
                <h2 class="text-lg font-semibold">Escaneos de kits</h2>
                <div class="mt-3 rounded-xl bg-app p-4">
                    <p class="text-2xl font-bold">{{ number_format($scansQr) }}</p>
                    <p class="text-xs text-ink-soft">Escaneos QR desde sus kits</p>
                </div>
            </div>
        </div>
    </div>
@endsection
