## Context

El MVP opera sin roles: cada cuenta es dueño de un negocio y activa sus propios módulos en `panel/modulos` (ver `subscriptions` spec y `ModulesManager`). No existe un actor de plataforma. Este cambio lo agrega sin alterar los contratos existentes: el administrador gobierna el mismo estado (`module_access`, cuentas, negocios) que ya gobierna el producto. Motivación en `proposal.md`; requisitos en `specs/platform-admin/spec.md`.

## Goals / Non-Goals

**Goals:**

- Un área `/admin` protegida por rol, alcanzable con la misma cuenta (flag en `users`).
- Ficha por negocio con datos de cuenta + estado de módulos + estado de pausa.
- El administrador activa/desactiva módulos de pago reutilizando las reglas de `subscriptions` (Perfil siempre gratis/activo; sin pérdida de datos).
- Pausa/reactivación de cuentas que afecta perfil público y login del dueño, sin borrar datos.
- Edición de cuenta + restablecimiento de contraseña.
- Personificación de un negocio con aviso y retorno seguro a la sesión de administrador.

**Non-Goals:**

- Roles/permisos granulares ni múltiples administradores secundarios (sigue siendo flag simple; evoluciona después).
- Facturación o cobro desde el panel de administración.
- Editar la URL pública (`slug`) del negocio: contradiría la URL estable del acceso físico (§21).
- Panel de administración para el *contenido* de cada negocio fuera de la personificación: si el admin necesita ver clientes/reservas, entra al panel del dueño.

## Decisions

### 1. Rol de administrador como flag en la cuenta (no tabla/guard separado)

- `users.is_platform_admin` (boolean, default false).
- Por qué: el operador es una o pocas personas; duplicar login/guard para un MVP sin roles agrega superficie sin beneficio. Un middleware sobre el prefijo `/admin` da el control.
- Alternativas: tabla `admins` (se descarta: sobre-ingeniería ahora), login separado `/admin/login` (se descarta: doble sistema de credenciales por mantener).
- Alta del primer admin: comando `php artisan app:make-admin {email} [--remove]`. El comando no permite quitar el último administrador para no dejar la plataforma sin acceso.

### 2. Pausa de cuenta como flag en el negocio

- `businesses.is_paused` (boolean, default false). No elimina nada.
- Efectos (comportamiento observable):
  - Perfil público y rutas asociadas (perfil, reserva, QR, registro voluntario, click-tracking) responden 404 mientras la cuenta está pausada.
  - El dueño no puede iniciar sesión: en el login se valida que su negocio no esté pausado y se informa "Tu cuenta está pausada".
  - El administrador puede seguir viendo la ficha y personificar (soporte sobre una cuenta pausada) si lo desea.
- Al reactivar se revierte todo; los datos nunca se tocan.

### 3. Personificación con sesión del administrador

- Mecanismo propio, sin librería externa (evita dependencia nueva para un patrón simple):
  - Al entrar como negocio: se guarda `admin_original_user_id` en sesión y se hace `Auth::login(negocio->account)`.
  - El banner de personificación se muestra en el layout del panel mientras `admin_impersonating` esté en sesión, con botón "Volver al panel de administración" (ruta pública autenticada, no bajo `/admin`).
  - Al volver: se restaura el usuario original por id, se limpia la sesión y se redirige a `/admin`.
- Reglas de seguridad: no se puede personificar si ya hay una personificación activa; no se puede personificar a sí mismo; no se puede entrar a `/admin` mientras se personifica (el panel del negocio es el del dueño).

### 4. Activación de módulos por el administrador

- Misma tabla `module_access` y mismas reglas que el dueño. El admin togglea solo módulos de pago; el módulo Perfil no ofrece toggle (UI sin control + guarda en el handler).

### 5. Edición de cuenta y contraseña

- Nombre/correo: validación estándar (correo único). No se puede editar la URL pública.
- Contraseña: el administrador define una nueva (mínimo 8) que se hashea; el dueño inicia sesión con ella. Alternativa de "enviar enlace de recuperación" se deja como uso del mecanismo existente.

## Risks / Trade-offs

- **Perder el último administrador** → el comando rechaza remover el último admin; documentado.
- **Personificación mal usada / sesión entrecruzada** → reglas de bloqueo (no anidada, no a sí mismo, no `/admin` mientras se personifica) + banner visible siempre; la restauración usa el id original guardado en sesión.
- **Pausa sorpresiva del dueño (sin aviso)** → solo puede originarla el administrador; al intentar login el dueño ve el mensaje de pausa (no "credenciales inválidas", para ser claro).
- **Doble origen de toggles (dueño vs admin)** → no hay conflicto: es el mismo registro; último cambio gana, consistente con el modelo actual.

## Migration Plan

Sin despliegue previo: dos migraciones aditivas (`users.is_platform_admin`, `businesses.is_paused`), comando para marcar el primer admin, y al archivar el cambio las specs pasan a `openspec/specs/platform-admin/`.

## Open Questions

- Ninguna que cambie el alcance: si más adelante se quiere auditoría de acciones del administrador, se agrega como capacidad nueva.
