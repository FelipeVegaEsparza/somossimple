# analytics Specification

## Purpose
Entrega estadísticas básicas del negocio para su panel: visitas al perfil, escaneos, accesos y clicks, más el conteo de reservas, como agregados simples por negocio y fáciles de comprender.

## Requirements

### Requirement: Registrar eventos de acceso e interacción

El sistema debe registrar los eventos de acceso e interacción del perfil público de un negocio: visitas al perfil, escaneos de QR, accesos NFC y clicks en los accesos de contacto y redes (como WhatsApp, Instagram o llamar).

#### Scenario: Registro de una visita al perfil
- **WHEN** un visitante abre el perfil público de un negocio
- **THEN** el sistema registra una visita al perfil de ese negocio

#### Scenario: Registro de un escaneo de QR
- **WHEN** un visitante accede al perfil escaneando el QR del negocio
- **THEN** el sistema registra un escaneo de QR para ese negocio

#### Scenario: Registro de un click en contacto o redes
- **WHEN** un visitante hace click en WhatsApp, Instagram o el teléfono desde el perfil público
- **THEN** el sistema registra ese click asociado al negocio

### Requirement: Mostrar estadísticas del negocio

El panel debe mostrar al propietario las estadísticas básicas de su negocio: visitas al perfil, escaneos QR, accesos NFC, clicks y reservas, presentadas de forma simple y comprensible.

#### Scenario: Consultar el resumen de estadísticas
- **WHEN** el propietario consulta las estadísticas de su negocio en el panel
- **THEN** el sistema le muestra los conteos de visitas, escaneos, accesos, clicks y reservas de su negocio

#### Scenario: Conteo de reservas en las estadísticas
- **WHEN** un cliente crea una reserva en el perfil de un negocio
- **THEN** esa reserva se refleja en las estadísticas de reservas del negocio

### Requirement: Aislamiento de estadísticas por negocio

Las estadísticas de un negocio solo deben incluir los eventos de ese negocio; ningún negocio debe ver estadísticas de otro.

#### Scenario: Las estadísticas no mezclan negocios
- **WHEN** el propietario consulta las estadísticas de su negocio
- **THEN** el sistema solo le muestra los eventos registrados para su propio negocio
