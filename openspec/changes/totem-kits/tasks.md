## 1. Modelo y estados del kit

- [x] 1.1 Ampliar `physical_codes`: estado `available | sold | activated` y columna `activated_at`, migrando sin romper el inventario existente, verificando que la migración aplica limpio y que los códigos previos quedan como disponibles o activados según corresponda
- [x] 1.2 Actualizar `PhysicalCode` con los nuevos estados y helpers, verificando el escenario "Identificar un kit por su serial genérico"

## 2. Venta del kit (admin)

- [x] 2.1 Reemplazar en el admin la acción "Entregar aquí" por "Vender kit" (disponible → vendido, sin asignar negocio; se registra `delivered_at`), verificando los escenarios de "Vender un kit" y que no se vende un kit ya vendido/activado
- [x] 2.2 Mostrar en la ficha QR/NFC del admin el estado del kit y a qué negocio quedó activado, si corresponde

## 3. Activación por el dueño

- [x] 3.1 Crear la sección "Mi kit" en el panel del dueño (ruta `panel/kit`): ver los kits activados del negocio e ingresar el serial para activar, verificando el escenario "El dueño activa su kit correctamente"
- [x] 3.2 Validaciones de activación: serial inexistente, kit no vendido, kit ya activado por otro negocio y kit ya activado por el mismo negocio, verificando los escenarios correspondientes y que la pantalla no ofrece gestión de códigos
- [x] 3.3 Verificar que un negocio puede activar varios kits (escenario "Activar un segundo kit del mismo negocio")

## 4. Resolución pública `/k/{serial}`

- [x] 4.1 Crear la ruta pública `GET /k/{serial}`: redirige (302) al perfil del negocio asociado si está activado, muestra la página "código aún no activado" si no, y responde 404 si el serial no existe, verificando los tres escenarios de "Resolver la URL genérica del kit"
- [x] 4.2 Verificar que la redirección funciona con el negocio sin módulos de pago (perfil gratuito) y no cambia ante cambios de contenido del negocio (escenario "El kit funciona con el perfil gratuito")

## 5. Verificación y cierre

- [x] 5.1 Ejecutar la suite completa y `openspec validate`, verificando que no hay regresiones en `admin-qr-nfc`, `qr-nfc` ni en el acceso público
- [ ] 5.2 Recorrer en pantalla: vender un kit como admin, activarlo como dueño y escanear la URL `/k/{serial}`, confirmando la redirección; contrastar el diseño con `docs/directrices-diseno.md`
