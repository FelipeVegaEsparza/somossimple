## Context

El inventario `physical_codes` (del cambio `admin-qr-nfc`, ya codificado) registra códigos físicos con `type/serial/status (available|delivered)/business_id/delivered_at`. Hoy el admin "entrega" un código asignándolo a un negocio. Este cambio convierte eso en un producto vendible con autoactivación del dueño y resolución de URL genérica. Motivación y requisitos en `proposal.md` y `specs/physical-kits/spec.md`.

## Goals / Non-Goals

**Goals:**

- Kit (tótem QR+NFC) como código único vendible: disponible → vendido → activado.
- Venta registrada por el admin sin asignar negocio; activación por el dueño con su serial.
- URL genérica `/k/{serial}` impresa en el kit; resolución server-side al perfil asociado (o página "no activado").
- Independencia del kit respecto a módulos de pago (perfil gratuito §31).

**Non-Goals:**

- Precio/cobro del kit y pasarela (costo único §22; se integrará después, probablemente sobre el plano comercial manual).
- Transferencia de kits entre negocios, devoluciones, baja de kits.
- Compra online en esta versión.
- Escritura física de tags NFC (se asume configurados en producción con la URL `/k/{serial}`).

## Decisions

### 1. Estados del kit sobre `physical_codes`

- Se amplía `status` a `available | sold | activated` y se agrega `activated_at` (nullable).
- `business_id` y `delivered_at` se usan así: `delivered_at` = momento de la venta; `business_id` = solo cuando se activa.
- La venta (admin) valida `status === available`; la activación (dueño) valida `status === sold`.

### 2. Venta vs activación (dos actores)

```
admin:  disponible --> vendido   (registra venta; sin negocio)
dueño:  vendido    --> activado  (ingresa serial; se liga a su negocio)
```

- El flujo "Entregar aquí" del admin del cambio anterior se reemplaza por "Vender kit" (sin asignar negocio). El inventario del admin muestra el estado y a qué negocio quedó activado, si aplica.

### 3. Activación en el panel del dueño

- Nueva ruta autenticada `panel/kit` con una vista simple: estado de los kits activados del negocio + un campo para ingresar el serial.
- Reglas: serial debe existir, estar `sold`, y no estar activado por otro negocio. Si el negocio ya tiene ese serial, se informa como ya activado (idempotente).
- No expone gestión de códigos (coherente con `admin-qr-nfc`: el dueño no administra QR/NFC).

### 4. Resolución `/k/{serial}`

- Ruta pública `GET /k/{serial}`:
  - serial activado con negocio → `302` a `route('p.show', business.slug)` (el perfil público aplica sus propias reglas, incluida pausa).
  - serial existente no activado → vista "código aún no activado" (HTTP 200).
  - serial inexistente → 404.
- Esta URL es el contenido del QR y de la NFC impresos; no cambia si el negocio cambia contenido (principio §21).

## Risks / Trade-offs

- **Kit comprado por quien no lo activa** (venta a persona equivocada) → el serial viaja físico; la activación queda bajo control del que tiene el kit. Mitigación operacional (el admin vende a la cuenta correcta).
- **Compatibilidad con `admin-qr-nfc` en curso** → el cambio se aplica/archiva después de `admin-qr-nfc`; la spec de `physical-kits` complementa, no contradice, y el inventario evoluciona en código.
- **Espera de resolución del kit** al perfil: requiere servidor online (igual que los perfiles).

## Migration Plan

Migración aditiva sobre `physical_codes` (estados + `activated_at`), rutas nuevas, sin cambios destructivos.

## Open Questions

- Precio de los kits y su registro de venta en dinero: fuera de esta versión; se une después al plano de pagos/cobros.
