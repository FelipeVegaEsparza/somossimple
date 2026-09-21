## Purpose

Plano comercial de la plataforma: el administrador define el precio mensual de cada módulo de pago y gestiona manualmente los pagos recurrentes de los negocios, determinando si cada módulo pago está al día y desactivando (sin borrar datos) los módulos de pago cuyos negocios no están al día tras un período de gracia. El Perfil Digital gratuito nunca se apaga.

## ADDED Requirements

### Requirement: Definir el precio mensual de cada módulo

El administrador de la plataforma debe poder definir y actualizar un precio mensual para cada módulo de pago (catálogo, reservas, clientes y comunicaciones). El Perfil Digital es gratuito y no debe tener precio.

#### Scenario: Definir el precio de un módulo
- **WHEN** el administrador define un precio mensual para el módulo de reservas
- **THEN** el sistema guarda el precio y lo muestra en la sección de precios

#### Scenario: Actualizar el precio de un módulo
- **WHEN** el administrador cambia el precio mensual de un módulo existente
- **THEN** el sistema guarda el nuevo precio, que aplica a los pagos futuros

#### Scenario: El Perfil Digital no tiene precio
- **WHEN** el administrador revisa la sección de precios
- **THEN** el Perfil Digital se muestra como gratuito y no permite definirle un precio

### Requirement: Mostrar el estado de pago de cada negocio

El administrador debe poder ver, por negocio y módulo de pago, si el módulo está al día, atrasado o inactivo, junto con su historial de pagos.

#### Scenario: Consultar el estado de pago de un negocio
- **WHEN** el administrador abre la sección de pagos de un negocio
- **THEN** el sistema le muestra el estado de cada módulo de pago y su historial de pagos

### Requirement: Registrar un pago manual por negocio y módulo

El administrador debe poder registrar un pago indicando el negocio, el módulo, el monto y la fecha. Un pago vigente mantiene el módulo al día durante el período que corresponda (por defecto, 30 días desde la fecha del pago).

#### Scenario: Registrar un pago
- **WHEN** el administrador registra un pago de un negocio para el módulo de catálogo con su monto y fecha
- **THEN** el sistema lo guarda en el historial y el módulo queda al día

#### Scenario: Un módulo al día con un pago reciente
- **WHEN** el negocio tiene un pago reciente del módulo y la fecha actual está dentro del período cubierto
- **THEN** el módulo se considera al día y permanece disponible

### Requirement: Apagar módulos de pago sin pago al día

Un módulo de pago con precio definido que está activo pero no está al día debe dejar de estar disponible públicamente después de un período de gracia, sin eliminar sus datos. El módulo debe reactivarse automáticamente cuando el administrador registra un pago vigente.

#### Scenario: Módulo atrasado tras el período de gracia
- **WHEN** un negocio no tiene un pago vigente para un módulo de pago y el período de gracia ya venció
- **THEN** el sistema desactiva ese módulo de pago: deja de estar disponible públicamente y sus datos se conservan

#### Scenario: El período de gracia inicial permite probar sin pagar
- **WHEN** un negocio activa por primera vez un módulo de pago con precio definido y la fecha actual está dentro del período de gracia inicial
- **THEN** el módulo permanece disponible aunque aún no se haya registrado un pago

#### Scenario: Reactivar al registrar un pago
- **WHEN** el administrador registra un pago vigente para un módulo de pago desactivado por impago
- **THEN** el sistema vuelve a activar el módulo y su información continúa disponible

### Requirement: La regla de impago solo aplica a módulos con precio

Mientras un módulo no tenga precio definido, la regla de impago no debe aplicarse: la activación sigue funcionando como hoy, por estado y sin cobro. Esto evita romper el uso actual del producto antes de definir precios.

#### Scenario: Módulo sin precio no se apaga por impago
- **WHEN** un módulo de pago no tiene precio definido y el negocio no registra pagos
- **THEN** el módulo permanece como lo dejó su estado de activación, sin aplicar la regla de impago

### Requirement: El Perfil Digital nunca se apaga por impago

El Perfil Digital es gratuito: la regla de impago no debe afectar jamás su disponibilidad.

#### Scenario: Perfil gratuito activo ante un impago
- **WHEN** un negocio está atrasado en los módulos de pago
- **THEN** su Perfil Digital gratuito continúa disponible con su información, contacto y QR
