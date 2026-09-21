# reservations Specification

## Purpose
Permite que un visitante solicite una hora al negocio desde su perfil público y que el propietario administre sus reservas en una agenda única, con estados simples y cálculo de disponibilidad según horario, duración del servicio y reservas existentes.

## Requirements

### Requirement: Configurar servicios reservables

El propietario debe poder configurar, dentro del módulo de reservas, los servicios que sus clientes pueden reservar. Cada servicio reservable debe tener nombre, duración y precio. El módulo de reservas no debe depender de que el módulo de catálogo esté activo.

#### Scenario: Crear un servicio reservable
- **WHEN** el propietario crea un servicio reservable con nombre, duración y precio
- **THEN** el sistema lo guarda y queda disponible para ser seleccionado al agendar

#### Scenario: Reservas sin catálogo activo
- **WHEN** un negocio tiene el módulo de reservas activo pero el de catálogo no
- **THEN** el sistema le permite configurar y ofrecer servicios reservables igualmente

### Requirement: Configurar horarios de atención

El propietario debe poder definir los días de atención y los horarios en los que su negocio recibe reservas, así como marcar días puntuales como no disponibles.

#### Scenario: Definir días y horarios de atención
- **WHEN** el propietario configura los días de la semana y los horarios en que atiende
- **THEN** el sistema usa esa configuración para calcular las horas disponibles

#### Scenario: Marcar un día como no disponible
- **WHEN** el propietario marca un día puntual como no disponible
- **THEN** el sistema no ofrece horas reservables en ese día

### Requirement: Solicitar una reserva

Un visitante debe poder solicitar una reserva desde el perfil público seleccionando un servicio reservable, una fecha, una hora disponible e ingresando su nombre y teléfono; el correo electrónico debe ser opcional.

#### Scenario: Solicitud de reserva completada
- **WHEN** un visitante selecciona un servicio, elige una fecha con horas disponibles, escoge una hora, ingresa su nombre y teléfono y confirma
- **THEN** el sistema crea la reserva en estado pendiente y confirma la solicitud al visitante

#### Scenario: Solicitud sin correo electrónico
- **WHEN** un visitante completa una solicitud de reserva sin indicar correo electrónico
- **THEN** el sistema permite completar la solicitud de todos modos

### Requirement: Calcular disponibilidad de horas

La disponibilidad de un servicio en una fecha debe calcularse a partir del horario de atención del negocio, la duración del servicio y las reservas existentes. Las reservas en estado pendiente o confirmada deben ocupar el horario correspondiente y no ofrecerse nuevamente.

#### Scenario: Hora ocupada por una reserva bloqueante
- **WHEN** una reserva pendiente o confirmada ocupa un horario y un visitante consulta horas para esa fecha y servicio
- **THEN** el sistema no ofrece las horas que colisionan con esa reserva

#### Scenario: Hora liberada por una reserva cancelada
- **WHEN** una reserva previamente bloqueante pasa a estado cancelado o completado
- **THEN** el sistema vuelve a ofrecer el horario que esa reserva ocupaba

#### Scenario: Sin atención en la fecha consultada
- **WHEN** un visitante consulta horas en una fecha en que el negocio no atiende o está marcada como no disponible
- **THEN** el sistema informa que no hay horarios disponibles en esa fecha

### Requirement: Gestionar estados de las reservas

Cada reserva debe tener un estado entre pendiente, confirmada, cancelada y completada. El propietario debe poder cambiar el estado de una reserva desde su panel.

#### Scenario: El propietario confirma una reserva
- **WHEN** el propietario confirma una reserva pendiente
- **THEN** la reserva pasa a estado confirmada

#### Scenario: El propietario cancela una reserva
- **WHEN** el propietario cancela una reserva
- **THEN** la reserva pasa a estado cancelada y su horario queda disponible nuevamente

### Requirement: Consultar la agenda de reservas

El propietario debe poder consultar las reservas de su negocio organizadas por día o por semana, viendo para cada una el horario, el cliente, el servicio y el estado. El MVP debe contemplar una única agenda por negocio, sin empleados ni calendarios externos.

#### Scenario: Consultar las reservas del día
- **WHEN** el propietario consulta la agenda de un día
- **THEN** el sistema le muestra las reservas de ese día con su horario, cliente, servicio y estado

#### Scenario: Consultar las reservas de la semana
- **WHEN** el propietario consulta la agenda de una semana
- **THEN** el sistema le muestra las reservas de los días de esa semana con su horario, cliente, servicio y estado
