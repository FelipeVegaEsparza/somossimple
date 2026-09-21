## Purpose

Gestión de códigos QR/NFC exclusiva del administrador de la plataforma, por negocio: ver y descargar el QR digital, administrar los accesos físicos, llevar un inventario de códigos físicos (QR, NFC o QR+NFC) asignables a negocios, y consultar estadísticas de escaneos. Los dueños de los negocios no tienen acceso a esta gestión.

## ADDED Requirements

### Requirement: Acceso exclusivo del administrador a la gestión QR/NFC

La gestión de QR/NFC de los negocios debe estar disponible únicamente en el área de administración de la plataforma. El administrador debe poder acceder a la gestión QR/NFC de cada negocio desde el listado de negocios.

#### Scenario: El administrador accede a la gestión QR/NFC de un negocio
- **WHEN** el administrador abre la sección QR/NFC y elige un negocio
- **THEN** el sistema le muestra el QR digital, los accesos físicos, el inventario de códigos y las estadísticas de ese negocio

#### Scenario: Un propietario no accede a la gestión
- **WHEN** un propietario de negocio intenta abrir la sección QR/NFC
- **THEN** el sistema no le ofrece la sección, porque la gestión es exclusiva del administrador

### Requirement: Descargar el QR digital de cada negocio

El administrador debe poder descargar el QR digital de cada negocio, apuntando a su URL pública estable.

#### Scenario: Descargar el QR de un negocio
- **WHEN** el administrador descarga el QR digital de un negocio
- **THEN** recibe la imagen del QR que apunta a la URL pública estable del perfil del negocio

### Requirement: Administrar los accesos físicos de cada negocio

El administrador debe poder listar, registrar y eliminar los accesos físicos de cada negocio (por ejemplo, "Mesa 01" o "Recepción"). Todos los accesos apuntan a la misma URL pública del negocio.

#### Scenario: Registrar un acceso físico de un negocio
- **WHEN** el administrador registra un acceso físico para un negocio con una etiqueta
- **THEN** el sistema lo asocia al negocio y lo muestra en su gestión QR/NFC

#### Scenario: Eliminar un acceso físico
- **WHEN** el administrador elimina un acceso físico de un negocio
- **THEN** el sistema lo quita de la gestión del negocio

### Requirement: Inventario de códigos físicos

El administrador debe poder registrar códigos físicos (QR, NFC o QR+NFC) con un serial único y un estado (disponible o entregado). Un código entregado debe asociarse a un único negocio y no puede estar disponible ni asignarse a otro negocio mientras esté entregado.

#### Scenario: Registrar un código físico disponible
- **WHEN** el administrador registra un código físico con su tipo y serial
- **THEN** el sistema lo guarda como disponible en el inventario

#### Scenario: Entregar un código físico a un negocio
- **WHEN** el administrador entrega un código físico disponible a un negocio
- **THEN** el sistema lo marca como entregado y lo asocia a ese negocio

#### Scenario: Un código entregado no se reasigna
- **WHEN** el administrador intenta entregar a otro negocio un código que ya está entregado
- **THEN** el sistema lo impide, porque el código ya pertenece a un negocio

### Requirement: Estadísticas de escaneos por negocio

El administrador debe poder ver estadísticas de acceso de cada negocio: cantidad de escaneos de QR y accesos NFC registrados.

#### Scenario: Ver las estadísticas de escaneos de un negocio
- **WHEN** el administrador consulta la gestión QR/NFC de un negocio
- **THEN** el sistema le muestra el total de escaneos de QR y accesos NFC de ese negocio
