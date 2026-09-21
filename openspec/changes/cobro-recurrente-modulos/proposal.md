## Why

El producto activa módulos por negocio pero no cobra: la activación es un toggle sin precio ni seguimiento de pagos. Para operar el modelo comercial (Perfil Digital gratis; el resto de pago) el administrador de la plataforma necesita definir cuánto cuesta cada módulo y gestionar manualmente los pagos recurrentes de los negocios, apagando módulos de pago cuando un cliente no está al día.

## What Changes

- **Precios por módulo** definidos por el administrador (precio mensual por cada módulo de pago: catálogo, reservas, clientes, comunicaciones). El Perfil Digital es gratis y no tiene precio.
- **Gestión manual de pagos recurrentes**: el administrador registra pagos por negocio y módulo (monto, fecha) y ve el historial y el estado "al día" / "atrasado" de cada módulo.
- **Regla de impago**: un módulo de pago activo que no está al día (sin pago vigente, tras un período de gracia) se desactiva automáticamente: deja de estar disponible públicamente, sus datos se conservan y se reactiva al registrar un pago.
- El Perfil Digital gratuito nunca se apaga por impago.
- Nueva sección en el área de administración con dos vistas: **Precios** (definir tarifas) y **Pagos** (estado y cobro por negocio/módulo).
- La aplicación de la regla de impago solo se activa para módulos con precio definido: mientras el admin no defina precios, el comportamiento actual (activación por estado, sin cobro) no cambia, para no romper el uso del MVP.
- **Sin pasarela de pago en esta versión**: el cobro lo registra el administrador (manual). Se decide por conversación con el propietario.

## Capabilities

### New Capabilities

- `platform-billing`: configuración comercial y cobro manual recurrente por módulo — precios mensuales por módulo definidos por el admin, registro e historial de pagos por negocio y módulo, cálculo del estado "al día", y la regla de que un módulo de pago sin pago al día (tras período de gracia) deja de estar disponible públicamente conservando sus datos; el Perfil Digital gratuito nunca se apaga.

### Modified Capabilities

No se modifican capacidades existentes: `subscriptions` sigue definiendo la activación por estado; `platform-billing` agrega el plano comercial por encima sin alterar esos contratos.

## Impact

- Datos: nuevas tablas `module_prices` (módulo, precio mensual) y `module_payments` (negocio, módulo, monto, fecha, notas); columna `module_access.activated_at` para calcular la gracia.
- Código: nuevas vistas/controladores del área `/admin` (Precios y Pagos) reutilizando el layout de administración existente; servicio de "al día"/cobertura + comando programado diario que apaga módulos atrasados y conserva datos.
- Comportamiento: una vez definidos precios, un módulo de pago sin pago vigente se apaga solo tras la gracia; la reactivación ocurre al registrar el pago.
