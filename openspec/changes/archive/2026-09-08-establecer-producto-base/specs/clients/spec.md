## Purpose

Administra la base de clientes de cada negocio: registro por reserva, registro voluntario o registro manual, con etiquetas, notas, historial básico y un consentimiento de comunicaciones por canal que es independiente de la existencia del cliente.

## ADDED Requirements

### Requirement: Registrar clientes con datos básicos

El propietario debe poder administrar clientes de su negocio, cada uno con nombre, teléfono, correo electrónico y fecha de registro. El teléfono es el dato principal para reconocer a un cliente que ya existe dentro del mismo negocio.

#### Scenario: Registrar un cliente manualmente
- **WHEN** el propietario crea un cliente desde su panel con nombre y teléfono
- **THEN** el sistema lo guarda con su fecha de registro y lo deja visible en su lista de clientes

#### Scenario: Listar y buscar clientes
- **WHEN** el propietario consulta o busca en la lista de clientes de su negocio
- **THEN** el sistema le muestra los clientes del negocio con sus datos principales

### Requirement: Detectar un cliente existente por teléfono

Al registrar un cliente desde una reserva o un registro voluntario, el sistema debe detectar si ya existe un cliente del mismo negocio con el mismo teléfono, normalizado a formato internacional, y vincular la información al cliente existente en lugar de duplicarlo. La coincidencia por teléfono es una detección, no una identidad absoluta.

#### Scenario: Visitante que reserva y ya es cliente
- **WHEN** una persona con un teléfono que coincide con el de un cliente existente del negocio realiza una reserva
- **THEN** el sistema asocia la reserva y la información al cliente existente

#### Scenario: Teléfono sin coincidencia crea un cliente nuevo
- **WHEN** una persona con un teléfono que no coincide con ningún cliente del negocio realiza una reserva o se registra voluntariamente
- **THEN** el sistema crea un cliente nuevo para ese negocio

### Requirement: Registro voluntario con aceptación explícita

El perfil público debe poder ofrecer un registro voluntario ("¿Quieres recibir nuestras promociones?") en el que el visitante ingresa nombre y teléfono o correo electrónico. El registro debe requerir una aceptación explícita para recibir comunicaciones comerciales.

#### Scenario: Registro voluntario con aceptación
- **WHEN** un visitante completa el registro voluntario con su nombre y teléfono y acepta explícitamente recibir comunicaciones comerciales
- **THEN** el sistema crea o actualiza el cliente del negocio y registra su consentimiento para el canal correspondiente

#### Scenario: Registro voluntario sin aceptación
- **WHEN** un visitante entrega sus datos sin aceptar recibir comunicaciones comerciales
- **THEN** el sistema crea o actualiza el cliente del negocio sin registrar consentimiento de comunicaciones comerciales

### Requirement: Etiquetar y anotar clientes

El propietario debe poder asignar etiquetas a sus clientes (como nuevo, frecuente, VIP o inactivo), crear etiquetas personalizadas y agregar notas a cada cliente.

#### Scenario: Asignar una etiqueta a un cliente
- **WHEN** el propietario asigna una etiqueta a un cliente
- **THEN** el sistema la guarda y permite filtrar o identificar clientes por esa etiqueta

#### Scenario: Agregar una nota a un cliente
- **WHEN** el propietario agrega una nota al perfil de un cliente
- **THEN** el sistema la guarda asociada a ese cliente

### Requirement: Consultar el historial de un cliente

El perfil de un cliente debe mostrar su historial básico con el negocio, incluyendo sus reservas, la primera y la última reserva y el total realizado.

#### Scenario: Ver el historial de reservas de un cliente
- **WHEN** el propietario abre el perfil de un cliente
- **THEN** el sistema le muestra el historial de reservas del cliente con sus fechas y total

### Requirement: Consentimiento por canal independiente del cliente

El consentimiento para comunicaciones comerciales debe gestionarse por canal y ser independiente de la existencia del cliente: una persona puede ser cliente sin haber consentido recibir comunicaciones comerciales. En el MVP el canal soportado es el correo electrónico. El propietario debe poder registrar o retirar el consentimiento de un cliente en cada canal.

#### Scenario: Cliente sin consentimiento comercial
- **WHEN** el propietario consulta un cliente que nunca aceptó comunicaciones comerciales
- **THEN** el sistema lo muestra como cliente sin consentimiento para el canal comercial

#### Scenario: Retirar el consentimiento de un cliente
- **WHEN** el propietario retira el consentimiento comercial de un cliente en un canal
- **THEN** el sistema deja de considerar a ese cliente como destinatario comercial de ese canal, conservando su registro de cliente

### Requirement: Aislamiento de clientes por negocio

Un negocio solo debe poder ver, buscar y administrar sus propios clientes; no debe tener acceso a los clientes de otros negocios.

#### Scenario: El negocio no ve clientes ajenos
- **WHEN** el propietario consulta su lista de clientes
- **THEN** el sistema solo le muestra clientes pertenecientes a su propio negocio
