## Purpose

Gestiona la cuenta del propietario que administra su negocio: registro, inicio de sesión y recuperación de acceso, con una única relación de una cuenta hacia un negocio durante el MVP.

## ADDED Requirements

### Requirement: Crear una cuenta de propietario

Una persona debe poder crear una cuenta de propietario indicando su correo electrónico, un nombre y una contraseña. El correo debe identificar de forma única la cuenta dentro de la plataforma.

#### Scenario: Registro exitoso con datos válidos
- **WHEN** una persona ingresa un correo no registrado, un nombre y una contraseña válidos
- **THEN** el sistema crea la cuenta y la persona puede iniciar sesión con ese correo y contraseña

#### Scenario: Registro con correo ya utilizado
- **WHEN** una persona intenta registrarse con un correo que ya pertenece a una cuenta existente
- **THEN** el sistema rechaza el registro e informa que el correo ya está en uso

### Requirement: Iniciar sesión en la cuenta

El sistema debe permitir al propietario iniciar sesión con el correo y la contraseña de su cuenta.

#### Scenario: Credenciales correctas
- **WHEN** el propietario ingresa su correo y contraseña correctos
- **THEN** el sistema le otorga acceso a su panel de administración

#### Scenario: Contraseña incorrecta
- **WHEN** el propietario ingresa una contraseña incorrecta
- **THEN** el sistema rechaza el acceso e informa que las credenciales no son válidas

### Requirement: Recuperar el acceso a la cuenta

El sistema debe permitir al propietario recuperar el acceso a su cuenta cuando no recuerda su contraseña, restableciéndola a través del correo electrónico registrado en la cuenta.

#### Scenario: Restablecimiento exitoso de contraseña
- **WHEN** el propietario solicita recuperar el acceso de una cuenta existente y sigue el mecanismo enviado a su correo registrado
- **THEN** el sistema le permite definir una nueva contraseña e iniciar sesión con ella

#### Scenario: Recuperación de una cuenta inexistente
- **WHEN** el propietario solicita recuperar el acceso de un correo no registrado
- **THEN** el sistema no revela si la cuenta existe e informa que, si el correo estuviera registrado, habría recibido instrucciones

### Requirement: Una cuenta se asocia a un único negocio

Durante el MVP, una cuenta debe poder crear y administrar un único negocio. Una cuenta no debe poder crear un segundo negocio mientras ya tenga uno asociado.

#### Scenario: Cuenta nueva crea su primer negocio
- **WHEN** el propietario con una cuenta recién creada y sin negocio asociado crea un negocio
- **THEN** el sistema asocia ese negocio a la cuenta y lo deja disponible para su administración

#### Scenario: Cuenta con negocio intenta crear otro
- **WHEN** el propietario cuya cuenta ya tiene un negocio asociado intenta crear otro negocio
- **THEN** el sistema no lo permite, porque la cuenta ya administra un negocio
