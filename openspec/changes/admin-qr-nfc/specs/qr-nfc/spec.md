## MODIFIED Requirements

### Requirement: Generar y obtener el QR digital del perfil

El sistema debe generar un código QR digital por negocio que apunte a la URL pública estable de su perfil. El QR digital se obtiene desde el panel de administración de la plataforma; el propietario del negocio no gestiona ni descarga el QR. El QR no se muestra en el perfil público.

#### Scenario: Descargar el QR digital
- **WHEN** el administrador de la plataforma solicita el QR digital de un negocio desde su panel de administración
- **THEN** el sistema le entrega un QR que apunta a la URL pública estable del perfil del negocio

#### Scenario: El QR sigue siendo válido tras cambios de contenido
- **WHEN** el negocio modifica su contenido después de haberse generado su QR
- **THEN** el QR continúa apuntando al mismo perfil y no necesita regenerarse ni reimprimirse

#### Scenario: El propietario no gestiona el QR
- **WHEN** el propietario del negocio revisa su panel
- **THEN** no encuentra una sección de QR/NFC ni opciones para descargar o gestionar el QR

#### Scenario: El QR no se muestra en el perfil público
- **WHEN** un visitante abre el perfil público de un negocio
- **THEN** el perfil no muestra el código QR digital

### Requirement: Asociar accesos físicos al negocio

El sistema debe permitir que un negocio tenga asociados accesos físicos (tarjetas, adhesivos o soportes con QR o NFC) que dirijan a su misma URL pública. La asociación de accesos físicos se administra desde el panel de administración de la plataforma, no por el propietario del negocio. En el MVP la asociación es conceptual: sin gestión individual de dispositivos por el dueño.

#### Scenario: Asociar un acceso físico al negocio
- **WHEN** el administrador de la plataforma registra un acceso físico perteneciente a un negocio
- **THEN** el sistema lo asocia al negocio y este dirige a la URL pública estable del perfil

#### Scenario: Múltiples accesos al mismo perfil
- **WHEN** un negocio tiene varios accesos físicos (por ejemplo, uno por mesa en un restaurante)
- **THEN** todos dirigen al mismo perfil digital del negocio

## ADDED Requirements

### Requirement: El propietario conserva su URL pública

El propietario del negocio debe poder ver su URL pública estable (para compartir su perfil), pero sin herramientas de gestión de códigos QR/NFC.

#### Scenario: El dueño ve su URL sin gestión
- **WHEN** el propietario consulta la información de su negocio en su panel
- **THEN** el sistema le muestra su URL pública estable y no le ofrece gestión de códigos QR/NFC
