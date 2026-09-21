## Purpose

Área de administración de la plataforma: permite al operador listar y gestionar las cuentas y negocios del sistema, activar o desactivar los módulos de pago de cada negocio, pausar cuentas, editar datos de cuenta y personificar a un negocio para soporte, siempre sobre el mismo estado de módulos que gobierna el resto del producto.

## ADDED Requirements

### Requirement: Acceso de administrador de la plataforma

Una cuenta marcada como administrador de la plataforma debe poder acceder a un área de administración protegida. Las cuentas sin ese rol no deben poder acceder a esa área. El acceso se realiza con la misma cuenta y el mismo inicio de sesión que el resto del sistema.

#### Scenario: El administrador accede al área de administración
- **WHEN** un usuario con rol de administrador inicia sesión y navega al área de administración
- **THEN** el sistema le permite acceder y le muestra la gestión de cuentas y negocios

#### Scenario: Una cuenta sin rol no accede al área de administración
- **WHEN** un propietario sin rol de administrador intenta abrir el área de administración
- **THEN** el sistema bloquea el acceso e informa que no tiene permisos

### Requirement: Listar y buscar negocios del sistema

El administrador debe poder ver el listado de negocios registrados con su cuenta asociada, y buscar por nombre de negocio, nombre de la cuenta o correo.

#### Scenario: Listar los negocios del sistema
- **WHEN** el administrador consulta el listado de negocios
- **THEN** el sistema le muestra los negocios registrados con su cuenta y estado

#### Scenario: Buscar un negocio
- **WHEN** el administrador busca por nombre de negocio, nombre de cuenta o correo
- **THEN** el sistema muestra solo los resultados que coinciden

### Requirement: Ver la ficha de un negocio

El administrador debe poder abrir la ficha de cada negocio, que muestre los datos de la cuenta, los datos del negocio, el estado de activación de cada módulo y el estado de pausa de la cuenta.

#### Scenario: Abrir la ficha de un negocio
- **WHEN** el administrador abre la ficha de un negocio
- **THEN** el sistema le muestra los datos de la cuenta, los del negocio, el estado de módulos y si la cuenta está pausada

### Requirement: Activar y desactivar módulos de pago desde la administración

El administrador debe poder activar y desactivar los módulos de pago (catálogo, reservas, clientes y comunicaciones) de cada negocio desde su ficha. El Perfil Digital es gratuito y siempre activo: el administrador no debe poder desactivarlo. Desactivar un módulo nunca elimina sus datos.

#### Scenario: El administrador activa un módulo de pago
- **WHEN** el administrador activa el módulo de reservas de un negocio
- **THEN** el módulo queda activo y sus funcionalidades disponibles, igual que si lo hubiera activado el propietario

#### Scenario: El administrador desactiva un módulo de pago
- **WHEN** el administrador desactiva el módulo de catálogo de un negocio
- **THEN** el módulo deja de estar disponible públicamente y sus datos se conservan

#### Scenario: El Perfil Digital no se puede desactivar
- **WHEN** el administrador intenta desactivar el módulo Perfil Digital de un negocio
- **THEN** el sistema no lo permite, porque el perfil gratuito permanece siempre activo

### Requirement: Pausar y reactivar una cuenta

El administrador debe poder pausar la cuenta de un negocio. Al pausar, el perfil público del negocio deja de estar disponible para los visitantes y el propietario no puede iniciar sesión. Ningún dato se elimina. Al reactivar, el perfil público y el acceso del propietario vuelven a funcionar con su información conservada.

#### Scenario: Pausar la cuenta de un negocio
- **WHEN** el administrador pausa la cuenta de un negocio
- **THEN** el perfil público deja de estar disponible, el propietario no puede iniciar sesión y los datos se conservan

#### Scenario: Reactivar una cuenta pausada
- **WHEN** el administrador reactiva una cuenta pausada
- **THEN** el propietario vuelve a iniciar sesión y su perfil público queda disponible con sus datos conservados

### Requirement: Editar datos de cuenta y restablecer contraseña

El administrador debe poder editar el nombre y el correo de la cuenta de un negocio y restablecer su contraseña. Tras el restablecimiento, el propietario debe poder iniciar sesión con la nueva contraseña.

#### Scenario: Editar los datos de una cuenta
- **WHEN** el administrador modifica el nombre o el correo de la cuenta de un negocio
- **THEN** el sistema guarda los cambios y la cuenta conserva sus accesos

#### Scenario: Restablecer la contraseña de un propietario
- **WHEN** el administrador restablece la contraseña de la cuenta de un negocio
- **THEN** el propietario puede iniciar sesión con la nueva contraseña

### Requirement: Personificar un negocio

El administrador debe poder entrar al panel de un negocio actuando como su propietario, con un aviso visible de que está personificando y un control para volver a su sesión de administrador. Mientras personifica, el administrador actúa sobre el negocio como lo haría su dueño, y al volver se restaura su sesión de administrador.

#### Scenario: Entrar como un negocio
- **WHEN** el administrador elige personificar un negocio
- **THEN** el sistema abre el panel del negocio con un aviso visible de personificación

#### Scenario: Actuar como el propietario
- **WHEN** el administrador personificando un negocio realiza una acción en el panel (por ejemplo, desactiva un módulo o edita el perfil)
- **THEN** la acción se aplica al negocio como si la realizara el propietario

#### Scenario: Volver a la sesión de administrador
- **WHEN** el administrador personificando un negocio usa el control para volver
- **THEN** el sistema restaura su sesión de administrador y regresa al área de administración

### Requirement: La URL pública no se modifica desde la administración

El administrador no debe poder cambiar la URL pública (slug) de un negocio, para mantener la estabilidad del acceso físico (QR/NFC) apuntando al perfil.

#### Scenario: No existe edición de la URL pública
- **WHEN** el administrador revisa la ficha de un negocio
- **THEN** la URL pública se muestra como información estable y no ofrece la opción de modificarla
