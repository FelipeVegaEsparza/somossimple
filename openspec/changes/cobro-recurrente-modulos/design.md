## Context

La activación de módulos hoy es solo estado (`module_access`, ver `subscriptions`), sin dinero. Este cambio agrega el plano comercial manual por encima, operado por el administrador de la plataforma (área `/admin` ya construida en `panel-admin-plataforma`). Motivación en `proposal.md`; requisitos en `specs/platform-billing/spec.md`.

## Goals / Non-Goals

**Goals:**

- Precios mensuales por módulo de pago gestionados por el admin (Perfil gratis).
- Registro manual e historial de pagos por negocio/módulo.
- Determinación de "al día" con cobertura por pago y período de gracia inicial.
- Regla de impago que apaga módulos de pago (no el perfil gratuito) conservando datos.
- Sin pasarela: cobro registrado por el admin.

**Non-Goals:**

- Pasarela de pago, débito automático, webhooks (decisión explícita del propietario para esta versión).
- Descuentos/precios especiales por cliente (por ahora precio global por módulo).
- Facturación/boletas, IVA u obligaciones tributarias.
- Notificaciones automáticas al cliente por impago (fuera de alcance; el admin opera).

## Decisions

### 1. Tabla de precios por módulo

- `module_prices`: fila por módulo de pago (`module` + `price_monthly`). Perfil no tiene fila: se muestra "Gratis".
- El precio se lee al momento de evaluar o registrar pagos (precio vigente); los pagos históricos conservan su `amount` como snapshot.
- Puerto comercial: la **regla de impago solo aplica a módulos con precio definido**. Sin precios, el producto se comporta como el MVP actual (toggle por estado), lo que mantiene verdes los flujos y tests existentes hasta que el admin defina tarifas.

### 2. Pagos y cobertura

- `module_payments`: `business_id`, `module`, `amount`, `paid_on`, `notes`.
- Cobertura de un pago: 30 días desde `paid_on`. El último pago define la cobertura vigente.
- Estado por negocio/módulo:
  - `al día`: existe pago cuyo `paid_on + 30d >= hoy`, o (nunca pagado) dentro del período de gracia inicial desde la activación.
  - `atrasado`: hubo pago en el pasado cuya cobertura venció, o venció la gracia inicial.
  - `inactivo`: el módulo no está activo.

### 3. Gracia inicial y desactivación

- `module_access.activated_at` registra cuándo el módulo pasó a activo la primera vez (se setea al activar).
- Gracia inicial: 7 días desde `activated_at` (sin pago, el módulo sigue disponible para probar).
- Después de la gracia o de vencer la cobertura, un comando diario (`app:apply-billing`) evalúa cada módulo pago con precio definido y activo que no esté al día → lo desactiva (`module_access.active = false`). Solo el perfil queda intacto.
- Al registrar un pago vigente, el sistema reactiva el módulo (si el negocio lo tenía antes activo se deduce de que el módulo fue activado; si estaba apagado por impago, se reactiva).
- Los datos del módulo jamás se tocan.

### 4. UI en el área `/admin`

- Dos vistas nuevas en el menú lateral de administración: **Precios** (editar tarifas por módulo) y **Pagos** (listado de negocios con badge de estado por módulo; ficha por negocio para registrar pago y ver historial). Se reutiliza el layout `admin/layouts/admin.blade.php`.

## Risks / Trade-offs

- **Interpretación de "al día" distinta entre admin y dueño** → single source: servicio `BillingService` que consulta `module_payments` y `module_access.activated_at`; dashboard/admin y comando usan la misma lógica.
- **Apagado sorpresivo** → solo tras vencer gracia/cobertura y solo con precio definido; no borra datos y se revierte al registrar pago.
- **Registros de pago duplicados/erróneos** → el admin ve historial y puede corregir (se deja margen en la UI de ficha para quitar/editar un pago como mejora posterior; no en esta versión).
- **Tests existentes de toggles** → intactos porque no hay precios definidos en esos escenarios (regla inactiva).

## Migration Plan

Migraciones aditivas (`module_prices`, `module_payments`, `module_access.activated_at`), comando diario registrado en el scheduler, sin cambios destructivos.

## Open Questions

- Notificación al cliente sobre impago/renovación: se decide dejar fuera (el admin comunica como quiera); si se quiere, futuro uso del módulo Comunicaciones.
