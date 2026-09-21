## Purpose

Gestiona la activación de los módulos del producto por negocio: el módulo gratuito de perfil siempre activo, los módulos de pago activables de forma independiente, la determinación de módulos activos y la conservación de sus datos al desactivarse o expirar.

## ADDED Requirements

### Requirement: Módulos del producto y estado de activación

El producto debe contar con un conjunto fijo de módulos: el perfil digital, gratuito y siempre activo, y los módulos de pago catálogo, reservas, clientes y comunicaciones, cada uno con un estado de activación propio por negocio. El sistema debe poder determinar qué módulos están activos para un negocio.

#### Scenario: Estado inicial de un negocio nuevo
- **WHEN** un negocio se crea en la plataforma
- **THEN** su perfil digital está activo de forma gratuita y los módulos de catálogo, reservas, clientes y comunicaciones están inactivos

#### Scenario: Consultar los módulos activos de un negocio
- **WHEN** el propietario consulta sus módulos en el panel
- **THEN** el sistema le muestra el estado de activación de cada módulo de su negocio

### Requirement: Activar y desactivar módulos desde el panel

El propietario debe poder activar y desactivar los módulos de pago de su negocio desde el panel. La interfaz debe dejar claro qué módulo está activo y cuál no. En el MVP no existe facturación ni pasarela de pago: la activación se gestiona mediante el estado del módulo.

#### Scenario: Activar el módulo de reservas
- **WHEN** el propietario activa el módulo de reservas de su negocio
- **THEN** el sistema lo marca como activo y las funcionalidades de reservas quedan disponibles

#### Scenario: Desactivar un módulo de pago
- **WHEN** el propietario desactiva el módulo de catálogo de su negocio
- **THEN** el sistema lo marca como inactivo y sus funcionalidades dejan de estar disponibles

### Requirement: Reflejar la activación en la disponibilidad pública

Cuando un módulo está inactivo, sus funcionalidades no deben aparecer en el perfil público ni estar disponibles para los visitantes. El perfil gratuito debe seguir funcionando aunque todos los módulos de pago estén inactivos.

#### Scenario: Módulo inactivo no disponible públicamente
- **WHEN** un negocio tiene inactivo el módulo de reservas
- **THEN** los visitantes no ven la opción de agendar en el perfil del negocio

#### Scenario: Perfil gratuito activo sin módulos de pago
- **WHEN** un negocio no tiene activo ningún módulo de pago
- **THEN** su perfil gratuito con información, contacto, redes, ubicación y QR continúa disponible

### Requirement: Conservar los datos al desactivar o expirar un módulo

Al desactivar un módulo o al terminar su vigencia, los datos de ese módulo no deben eliminarse automáticamente. Si el módulo se vuelve a activar, su información debe continuar disponible.

#### Scenario: Datos conservados tras desactivar un módulo
- **WHEN** el propietario desactiva el módulo de reservas de una barbería que tenía servicios y reservas
- **THEN** el botón de agendar deja de estar disponible, pero los servicios y reservas almacenados no se eliminan

#### Scenario: Reactivar un módulo conserva su información
- **WHEN** el propietario reactiva un módulo previamente desactivado o expirado
- **THEN** el sistema restablece la disponibilidad del módulo y su información continúa disponible
