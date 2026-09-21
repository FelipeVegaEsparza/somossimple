# qr-nfc Specification

## Purpose
Define el mecanismo de acceso físico al perfil digital: una URL pública estable a la que apuntan el QR digital y los accesos físicos conceptuales, sin que el acceso dependa del contenido del negocio.

## Requirements

### Requirement: Acceso por URL pública estable

El perfil digital de un negocio debe ser accesible a través de una URL pública estable, propia de cada negocio. La URL es el punto de acceso permanente: si el negocio modifica su información, sus precios o sus horarios, la URL y su QR no cambian.

#### Scenario: El perfil se mantiene accesible tras cambios de contenido
- **WHEN** el propietario modifica el teléfono, las redes sociales, los precios o las fotografías de su negocio
- **THEN** la URL pública del perfil sigue siendo la misma y dirige al contenido actualizado

### Requirement: Generar el QR digital del perfil

El sistema debe generar un código QR digital por negocio que apunte a la URL pública estable de su perfil. El propietario debe poder obtenerlo desde su panel.

#### Scenario: Descargar el QR digital
- **WHEN** el propietario solicita el QR digital de su negocio desde el panel
- **THEN** el sistema le entrega un QR que apunta a la URL pública estable del perfil

#### Scenario: El QR sigue siendo válido tras cambios de contenido
- **WHEN** el propietario modifica el contenido de su negocio después de haber generado su QR
- **THEN** el QR continúa apuntando al mismo perfil y no necesita regenerarse ni reimprimirse

### Requirement: Asociar accesos físicos al negocio

Un negocio debe poder tener asociados accesos físicos (como tarjetas, adhesivos o soportes con QR o NFC) que dirijan a su misma URL pública. En el MVP la asociación es conceptual: los accesos no requieren gestión individual de dispositivos.

#### Scenario: Asociar un acceso físico al negocio
- **WHEN** el propietario registra un acceso físico perteneciente a su negocio
- **THEN** el sistema lo asocia al negocio y este dirige a la URL pública estable del perfil

#### Scenario: Múltiples accesos al mismo perfil
- **WHEN** un negocio tiene varios accesos físicos (por ejemplo, uno por mesa en un restaurante)
- **THEN** todos dirigen al mismo perfil digital del negocio
