## Context

Hoy la gestión de QR/NFC vive en el panel del dueño (`Panel\QrController`, rutas `panel.qr.*`, ítem "QR / NFC" del menú) y en la generación pública del QR (`PublicQrController`, ruta `qr/{slug}`). Este cambio traslada la gestión al administrador de la plataforma (área `/admin` ya existente) y deja al dueño solo su URL. Motivación en `proposal.md`; requisitos en los deltas de `qr-nfc` y `admin-qr-nfc`.

## Goals / Non-Goals

**Goals:**

- Dueño: sin sección/rutas QR/NFC; conserva únicamente la URL pública en su panel.
- Admin: ver y descargar el QR por negocio; administrar accesos físicos; inventario de códigos físicos con entrega a negocio; estadísticas de escaneos.
- QR no visible en el perfil público (ya no se renderiza en ninguna vista pública).

**Non-Goals:**

- Venta/compra de códigos en línea ni checkout (solo registro manual del inventario y su entrega).
- Gestión fina de dispositivos NFC por el dueño (sigue conceptual).
- Estadísticas por acceso individual en esta versión (solo totales QR/NFC por negocio).

## Decisions

### 1. El dueño pierde la sección QR/NFC, conserva la URL

- Se eliminan: ítem de menú, rutas `panel.qr.*`, controlador `Panel\QrController` y su vista.
- La URL pública sigue visible donde hoy se muestra (encabezado del dashboard y página de edición del perfil), sin acciones de gestión.
- El acceso físico (`access_points`) existente se conserva y pasa a administrarse desde el área admin.

### 2. Generación de QR reutilizada

- `PublicQrController` (ruta pública `qr/{slug}`) permanece como generador; el admin la usa para incrustar y descargar el QR. Para descarga con nombre de archivo se agrega una ruta admin que devuelve el SVG con `Content-Disposition: attachment`.
- No hay QR en vistas públicas (nada que quitar).

### 3. Nueva sección admin "QR / NFC"

- Menú lateral de administración: nuevo ítem "QR / NFC" con listado de negocios y ficha por negocio (`Admin\QrNfcController`):
  - QR: vista previa y descarga.
  - Accesos físicos: alta/eliminación de etiquetas.
  - Inventario de códigos físicos.
  - Estadísticas: totales de `qr_scan` y `nfc_tap` vía `AnalyticsService` (datos ya registrados).

### 4. Inventario de códigos físicos

- Tabla `physical_codes`: `type` (qr | nfc | qr_nfc), `serial` único, `status` (available | delivered), `business_id` nullable, `delivered_at`. Solo el admin crea y entrega códigos; un código entregado queda ligado a su negocio (regla de unicidad por serial y por estado).

## Risks / Trade-offs

- **Tests existentes de QR (`QrAccessTest`)** dependen de rutas del dueño → se migran a las rutas admin; la generación pública (`qr.show`) se mantiene y sigue cubierta.
- **Acceso físico entregado vs URL**: entregar un código no cambia la URL (los accesos apuntan a `/p/slug`), por lo que no rompe QR impresos previos.
- **Inventario manual puede divergir de la realidad física** → es gestión administrativa; se asume precisión del operador en esta versión.

## Migration Plan

Migraciones aditivas (`physical_codes`) y migración de rutas/vistas (remoción del panel del dueño); sin cambios destructivos de datos.

## Open Questions

- Venta de códigos y cobro (si se unen con `cobro-recurrente-modulos`): se evaluará después; el inventario queda listo para marcar venta/entrega.
