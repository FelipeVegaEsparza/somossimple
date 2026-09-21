## Why

La gestión de QR/NFC está hoy en manos del dueño de cada negocio (sección "QR / NFC" en su panel). El propietario de la plataforma quiere controlar ese plano: ver, descargar y asociar los códigos QR/NFC de cada cliente, llevar inventario de códigos físicos y ver sus escaneos. El dueño deja de gestionarlos y solo conserva su URL pública.

## What Changes

- Se elimina del panel del dueño la sección "QR / NFC" y sus rutas de gestión (descargar QR, registrar accesos físicos). El dueño conserva únicamente la vista de su URL pública (sin gestión de códigos).
- El administrador de la plataforma gestiona por negocio, en una nueva sección del área `/admin`:
  - Ver y descargar el QR digital del negocio.
  - Listar, registrar y eliminar los accesos físicos del negocio (etiquetas como "Mesa 01").
  - Inventario de códigos físicos: registrar códigos (QR, NFC o QR+NFC) con serial, marcarlos disponibles o entregados a un negocio.
  - Ver estadísticas de escaneos del negocio (QR y NFC).
- El QR digital no se muestra en el perfil público: se descarga/imprime desde el panel de administración.
- El perfil público y el flujo de acceso (URL estable) no cambian.

## Capabilities

### New Capabilities

- `admin-qr-nfc`: gestión de QR/NFC exclusiva del administrador de la plataforma por negocio — visualización y descarga del QR digital, administración de accesos físicos, inventario de códigos físicos (QR/NFC/QR+NFC, serial, estado disponible/entregado) y estadísticas de escaneos; los dueños no tienen acceso a esta gestión.

### Modified Capabilities

- `qr-nfc`: la generación y obtención del QR y la asociación de accesos físicos pasan de manos del propietario del negocio a manos del administrador de la plataforma; se agrega la regla de que el QR no se muestra en el perfil público. La URL pública estable sigue siendo accesible al dueño y no cambia.

## Impact

- Eliminación: sección y rutas `panel/qr*` del dueño, ítem "QR / NFC" del menú del dueño, controlador `Panel\QrController`.
- Datos: `access_points` existentes se conservan y pasan a gestionarse desde administración; nueva tabla `physical_codes` para el inventario.
- Nuevo: sección y controladores bajo `/admin` (menú "QR / NFC"), reutilizando generación de QR existente y las estadísticas ya registradas en `analytics` (escaneos QR/NFC).
- Sin cambio de contrato en `analytics` ni en el acceso público al perfil.
