## 1. Retiro de la gestión QR/NFC del panel del dueño

- [x] 1.1 Eliminar del panel del dueño el ítem de menú "QR / NFC", las rutas `panel.qr.*` y el controlador `Panel\QrController` (y su vista), verificando que el panel del dueño carga sin errores y ya no ofrece gestión de QR
- [x] 1.2 Verificar que el dueño conserva solo su URL pública: en dashboard y edición de perfil se muestra `/p/{slug}` sin acciones de QR, cubriendo el escenario "El propietario no gestiona el QR" y "El propietario conserva su URL pública"

## 2. Gestión QR/NFC del administrador

- [x] 2.1 Crear migración `physical_codes` (type, serial único, status available/delivered, business_id, delivered_at) y modelo, verificando que la migración aplica limpio
- [x] 2.2 Crear sección "QR / NFC" en el menú de administración con listado de negocios y ficha por negocio (`Admin\QrNfcController`), verificando el escenario "El administrador accede a la gestión QR/NFC de un negocio"
- [x] 2.3 En la ficha admin: vista previa del QR y descarga (ruta admin con `Content-Disposition`), verificando el escenario "Descargar el QR de un negocio" y que el QR sigue válido ante cambios de contenido
- [x] 2.4 Accesos físicos desde admin: registrar y eliminar etiquetas por negocio, verificando los escenarios de "Administrar los accesos físicos de cada negocio" (incluido que el dueño ya no puede crearlos)
- [x] 2.5 Inventario de códigos físicos: registrar códigos (QR/NFC/QR+NFC) con serial, entregarlos a un negocio y verificar que un código entregado no se reasigna, cubriendo los escenarios de "Inventario de códigos físicos"
- [x] 2.6 Estadísticas de escaneos en la ficha admin (totales QR y NFC vía `AnalyticsService`), verificando el escenario "Ver las estadísticas de escaneos de un negocio"

## 3. Perfil público sin QR

- [x] 3.1 Verificar que ninguna vista pública muestra el QR digital (escenario "El QR no se muestra en el perfil público") y que la ruta de generación `qr/{slug}` sigue operativa para el admin

## 4. Verificación y cierre

- [x] 4.1 Migrar `QrAccessTest` a las nuevas rutas admin y ejecutar la suite completa + `openspec validate`, verificando que no hay regresiones en `qr-nfc`, `analytics` ni en el acceso público
- [ ] 4.2 Recorrer en pantalla (como admin): ficha QR de un negocio, descarga, registro/eliminación de accesos, alta y entrega de un código físico, y estadísticas; confirmar que el dueño ya no ve la sección
