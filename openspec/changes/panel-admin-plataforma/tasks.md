## 1. Datos y acceso del administrador

- [x] 1.1 Agregar `users.is_platform_admin` y `businesses.is_paused` (booleans, default false) con migraciones aditivas, verificando que `php artisan migrate:fresh` aplica limpio y no elimina reglas existentes
- [x] 1.2 Marcar el rol en el modelo `User` y crear middleware `EnsurePlatformAdmin` (alias `admin`), verificando que una ruta de prueba `/admin` responde para un admin y rechaza (403) a un propietario sin rol
- [x] 1.3 Crear comando `app:make-admin {email} [--remove]`, verificando que marca/desmarca el flag y que rechaza quitar el último administrador

## 2. Área de administración: listado y ficha

- [x] 2.1 Crear grupo de rutas `/admin` (prefijo protegido por `auth` + middleware `admin`) con su layout y navegación propia, verificando el escenario "Acceso de administrador" y el rechazo a cuentas sin rol
- [x] 2.2 Implementar el listado de negocios con búsqueda por negocio, nombre de cuenta o correo (paginado), verificando el escenario "Listar y buscar negocios del sistema"
- [x] 2.3 Implementar la ficha de negocio (datos de cuenta, datos del negocio, estado de módulos, estado de pausa, URL pública estable), verificando el escenario "Ver la ficha de un negocio" y que el slug se muestra solo como información

## 3. Activación de módulos desde la administración

- [x] 3.1 Implementar el toggle de módulos de pago (catálogo, reservas, clientes, comunicaciones) en la ficha, reutilizando `module_access`, verificando los escenarios "El administrador activa/desactiva un módulo de pago"
- [x] 3.2 Asegurar que el Perfil Digital no tenga opción de desactivarse y que un intento directo no lo desactive, verificando el escenario "El Perfil Digital no se puede desactivar"
- [x] 3.3 Verificar que desactivar un módulo desde la administración conserva sus datos (escenario de conservación de `subscriptions`)

## 4. Pausa y reactivación de cuentas

- [x] 4.1 Implementar pausar/reactivar desde la ficha, verificando que al pausar el perfil público responde 404 y el login del dueño se bloquea con el mensaje de cuenta pausada, y que al reactivar todo vuelve con los datos conservados
- [x] 4.2 Verificar por tests el escenario completo "Pausar y reactivar una cuenta" (sin pérdida de datos y restauración de acceso)

## 5. Edición de cuenta y contraseña

- [x] 5.1 Implementar edición de nombre y correo de la cuenta (validación de correo único), verificando el escenario "Editar los datos de una cuenta"
- [x] 5.2 Implementar restablecimiento de contraseña desde la ficha (nueva contraseña mínima 8, hasheada), verificando el escenario "Restablecer la contraseña de un propietario" (el dueño inicia sesión con la nueva clave)

## 6. Personificación (entrar como negocio)

- [x] 6.1 Implementar "Entrar como este negocio": guardar la sesión del administrador, iniciar sesión como el dueño y mostrar banner de personificación, verificando el escenario "Entrar como un negocio"
- [x] 6.2 Implementar "Volver al panel de administración" que restaura la sesión del administrador, verificando el escenario "Volver a la sesión de administrador"
- [x] 6.3 Aplicar reglas de seguridad (no personificar a sí mismo, no personificar estando ya en personificación, bloquear `/admin` mientras se personifica), verificando con tests que se rechazan

## 7. Verificación y cierre

- [x] 7.1 Ejecutar la suite completa de tests y `openspec validate`, verificando que no hay regresiones en capacidades existentes (accounts, subscriptions, business-profile)
- [ ] 7.2 Recorrer el flujo completo en pantalla: marcar un admin, activar/desactivar módulos, pausar/reactivar, personificar y volver, contrastando el diseño con `docs/directrices-diseno.md`
