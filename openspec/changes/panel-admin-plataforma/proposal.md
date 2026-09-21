## Why

Como operador de la plataforma necesito gestionar las cuentas de los negocios desde un solo lugar: hoy cada dueño activa sus propios módulos en su panel y no existe forma de administrar usuarios, módulos o accesos desde la plataforma. Esto impide operar el modelo comercial (solo el Perfil Digital es gratis; el resto se activa por separado) y dar soporte.

## What Changes

- Se introduce un **administrador de la plataforma**: una cuenta normal marcada con rol de administrador (mismo login), que accede a un área protegida `/admin` inaccesible para cuentas sin ese rol.
- Nueva sección de administración que permite:
  - Listar y buscar cuentas/negocios, y abrir la ficha de cada uno.
  - **Activar/desactivar los módulos de pago** (catálogo, reservas, clientes, comunicaciones) de cada negocio. El Perfil Digital es gratis y **siempre activo**: el administrador no puede desactivarlo.
  - **Pausar/reactivar cuentas**: al pausar, el perfil público del negocio deja de estar disponible y el dueño no puede iniciar sesión; no se elimina ningún dato. Al reactivar, todo vuelve.
  - **Editar datos de la cuenta** (nombre, correo) y **restablecer la contraseña** del dueño.
  - **Personificar (impostar)** a cada negocio: entrar a su panel actuando como el dueño (con aviso visible y botón "Volver al panel de administración").
- La URL pública (`slug`) de los negocios no es editable por el administrador: el acceso físico QR/NFC apunta a una URL estable (principio §21 del documento maestro).
- Se agrega un comando de consola para marcar/desmarcar una cuenta como administrador de la plataforma.
- **Sin cambios de contrato en las capacidades existentes** (`accounts`, `subscriptions`, `business-profile`): las reglas de activación, conservación de datos y gating del perfil público se reutilizan; el administrador es un actor adicional sobre el mismo estado.

## Capabilities

### New Capabilities

- `platform-admin`: área de administración de la plataforma — acceso protegido por rol, listado y ficha de cuentas/negocios, activación de módulos de pago, pausa/reactivación de cuentas, edición de cuenta y reset de contraseña, y personificación de un negocio con retorno seguro a la sesión de administrador.

### Modified Capabilities

No se modifican capacidades existentes: el cambio agrega un nuevo actor y un nuevo panel; las reglas de comportamiento de cuentas, suscripciones y perfil público permanecen iguales.

## Impact

- Datos: nuevas columnas `users.is_platform_admin` y `businesses.is_paused` (sin romper el modelo por negocio ni la regla de no pérdida de datos).
- Autenticación: mismo login; middleware que protege el prefijo `/admin`; mecanismo de personificación basado en sesión con restauración del usuario administrador.
- Comportamiento existente afectado solo al pausar: el perfil público deja de servirse y el login del dueño se bloquea mientras dure la pausa.
- Público: nuevos controladores/vistas bajo `app/Http/Controllers/Admin/` y `resources/views/admin/`, navegación separada del panel de negocios.
