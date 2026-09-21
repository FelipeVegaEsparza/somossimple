## Purpose

Permite al negocio crear comunicaciones hacia sus clientes por correo electrónico, seleccionar destinatarios según segmentos y consentimiento, y conservar un historial de los envíos. En el MVP el canal es exclusivamente email.

## ADDED Requirements

### Requirement: Crear una comunicación

El propietario debe poder crear una comunicación con un tipo (promoción, oferta, anuncio, noticia, cambio de horario o comunicación general) y un contenido, para ser enviada por correo electrónico.

#### Scenario: Crear una comunicación comercial
- **WHEN** el propietario crea una promoción con su contenido
- **THEN** el sistema la guarda como una comunicación lista para seleccionar destinatarios y enviar

#### Scenario: Crear una comunicación operacional
- **WHEN** el propietario crea una comunicación de cambio de horario u operacional
- **THEN** el sistema la trata como categoría distinta de las comunicaciones comerciales

### Requirement: Seleccionar destinatarios por segmento

El propietario debe poder dirigir una comunicación a un segmento de sus clientes: todos los que consintieron el canal, clientes nuevos, clientes frecuentes, clientes inactivos, clientes VIP o clientes de una etiqueta determinada.

#### Scenario: Enviar a un segmento por etiqueta
- **WHEN** el propietario selecciona la etiqueta VIP como destinatarios de una comunicación
- **THEN** el sistema resuelve los destinatarios como los clientes del negocio con esa etiqueta

#### Scenario: Destinatarios excluidos sin consentimiento
- **WHEN** el propietario envía una comunicación comercial a un segmento que incluye clientes sin consentimiento del canal
- **THEN** el sistema excluye de los destinatarios a los clientes sin consentimiento para el canal

#### Scenario: Destinatarios sin datos del canal
- **WHEN** un cliente del segmento no tiene correo electrónico registrado
- **THEN** el sistema lo excluye de los destinatarios de la comunicación por email

### Requirement: Consentimiento para comunicaciones comerciales

El sistema solo debe enviar comunicaciones comerciales a los clientes que hayan consentido recibirlas en el canal utilizado. Ser cliente no habilita por sí mismo recibir comunicaciones comerciales.

#### Scenario: Comunicación comercial a cliente sin consentimiento
- **WHEN** el negocio intenta incluir como destinatario comercial a un cliente que no ha consentido el canal
- **THEN** el sistema no envía la comunicación comercial a ese cliente

#### Scenario: Comunicación operacional sin exigir consentimiento comercial
- **WHEN** el negocio envía una comunicación operacional necesaria para un servicio o reserva
- **THEN** el sistema la trata de forma distinta a una comercial y no exige el consentimiento comercial

### Requirement: Registrar el historial de envíos

El sistema debe conservar un historial de las comunicaciones enviadas por el negocio, con su tipo, contenido, segmento o destinatarios y el estado del envío.

#### Scenario: Consultar el historial de comunicaciones
- **WHEN** el propietario consulta el historial de comunicaciones
- **THEN** el sistema le muestra sus comunicaciones con tipo, contenido y estado del envío

#### Scenario: Registro del envío de una comunicación
- **WHEN** el sistema realiza el envío de una comunicación
- **THEN** el envío queda registrado en el historial con su estado
