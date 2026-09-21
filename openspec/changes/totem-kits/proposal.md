## Why

La gestión de QR/NFC ya vive en el admin (inventario `physical_codes`), pero un tótem es un **producto físico vendible**: un kit impreso por adelantado con QR + etiqueta NFC que un negocio compra y debe poder activar y asociar a su propio perfil digital. Hoy no existe venta, ni autoactivación del cliente, ni una URL que resuelva un código pre-impreso al perfil del negocio que lo compró.

## What Changes

- **Ciclo de vida del kit** sobre `physical_codes`: `disponible → vendido → activado`. Un kit = un serial (tipo `qr_nfc`); el QR y la NFC del kit llevan la misma URL genérica `plataforma/k/{serial}`, impresa en producción (no contiene la URL del negocio).
- **Venta (admin)**: el administrador registra la venta/entrega de un kit (lo pasa de disponible a vendido). La venta no asigna negocio.
- **Activación (cliente)**: el dueño, desde una pantalla simple de su panel ("Mi kit"), ingresa el serial del kit que compró; el sistema lo valida y lo asocia a su negocio. La pantalla no ofrece gestión de códigos (sigue siendo exclusiva del admin).
- **Resolución `/k/{serial}`**: al escanear/apoyar el kit, la plataforma resuelve el serial y redirige (302) al perfil público del negocio asociado. Si el serial no está activado, muestra una página neutral "código aún no activado". Serial inexistente → 404.
- **El kit nunca depende del contenido ni de los módulos**: la redirección funciona mientras el negocio tenga su perfil gratuito (§31); apagar módulos de pago no afecta al kit. Un negocio puede activar varios kits (Mesa 01, Mesa 02…).
- Evoluciona el inventario del cambio `admin-qr-nfc` (que hoy asigna el código a un negocio directamente) hacia venta + activación por el cliente.

## Capabilities

### New Capabilities

- `physical-kits`: ciclo de vida comercial y de activación de los kits físicos (tótems QR+NFC) — estados disponible/vendido/activado, venta registrada por el administrador, autoactivación del dueño con su serial, y resolución pública de la URL genérica `/k/{serial}` que redirige al perfil asociado (o muestra estado no activado); el kit funciona con el perfil gratuito y es independiente de los módulos de pago.

### Modified Capabilities

No se modifican capacidades existentes en spec: la capacidad `admin-qr-nfc` (inventario/gestión admin) queda como está en spec y este cambio la complementa; al archivar en orden ambas quedan consistentes.

## Impact

- Datos: `physical_codes` amplía su estado a `available | sold | activated` y suma `activated_at`; `business_id` se setea en la activación (no en la venta).
- Rutas: pública `GET /k/{serial}` (resolución/redirect); del dueño `panel/kit` (activación); del admin, la venta de un kit disponible dentro de la sección QR/NFC existente.
- Código: ajuste del flujo "entregar aquí" del admin (pasaba a asignar negocio) para que sea "vender kit" sin asignar; nueva pantalla del dueño; controlador de resolución.
