## Purpose

Ciclo de vida comercial y de activación de los kits físicos (tótem QR + etiqueta NFC) que los negocios compran: inventario disponible, venta registrada por el administrador, activación del serial por el dueño del negocio, y resolución pública de la URL genérica del kit hacia el perfil asociado. El kit es independiente del contenido y de los módulos de pago: funciona mientras el negocio tenga su perfil gratuito.

## ADDED Requirements

### Requirement: Identificar un kit por un serial genérico

Cada kit físico debe identificarse con un serial único que representa el QR y la etiqueta NFC del kit a la vez. El contenido impreso del kit debe ser la URL genérica de la plataforma `plataforma/k/{serial}`, no la URL del negocio: así los kits pueden fabricarse e imprimirse por adelantado y reasignarse.

#### Scenario: Un kit se identifica por su serial
- **WHEN** el administrador registra un kit físico con su serial y tipo QR+NFC
- **THEN** el kit queda disponible en el inventario identificado por ese serial

#### Scenario: El contenido impreso es la URL genérica
- **WHEN** se imprime el kit de un serial
- **THEN** el QR y la NFC del kit apuntan a la URL genérica `plataforma/k/{serial}`

### Requirement: Vender un kit

El administrador debe poder registrar la venta y entrega de un kit disponible, pasándolo a estado vendido. La venta no asigna el kit a ningún negocio; esa asignación ocurre en la activación.

#### Scenario: Vender un kit disponible
- **WHEN** el administrador vende un kit disponible
- **THEN** el kit pasa a estado vendido y queda listo para que el comprador lo active

#### Scenario: No se vende un kit ya vendido
- **WHEN** el administrador intenta vender un kit que ya está vendido o activado
- **THEN** el sistema lo impide

### Requirement: Activar un kit desde el panel del dueño

El dueño de un negocio debe poder activar el kit que compró ingresando su serial en una pantalla simple de su panel. El sistema debe validar el serial y asociarlo a su negocio. La pantalla de activación no debe ofrecer gestión de códigos QR/NFC (exclusiva del administrador).

#### Scenario: El dueño activa su kit correctamente
- **WHEN** el dueño ingresa el serial de un kit vendido (no activado) que él compró
- **THEN** el sistema asocia el kit a su negocio y lo marca como activado

#### Scenario: Serial desconocido
- **WHEN** el dueño ingresa un serial que no existe
- **THEN** el sistema lo rechaza e informa que el código no es válido

#### Scenario: Kit no vendido aún
- **WHEN** el dueño ingresa el serial de un kit que sigue disponible (no se ha vendido)
- **THEN** el sistema lo rechaza e informa que el código no está asociado a una venta

#### Scenario: Kit ya activado por otro negocio
- **WHEN** el dueño ingresa el serial de un kit que ya fue activado por otro negocio
- **THEN** el sistema lo rechaza e informa que el código ya está activado

#### Scenario: El dueño no administra códigos en la pantalla de activación
- **WHEN** el dueño abre la sección "Mi kit" de su panel
- **THEN** solo puede ingresar el serial para activar su kit; no ve ni gestiona códigos QR/NFC

### Requirement: Varios kits por negocio

Un negocio debe poder tener varios kits activos asociados a su mismo perfil.

#### Scenario: Activar un segundo kit del mismo negocio
- **WHEN** el dueño de un negocio activa un segundo kit comprado
- **THEN** ambos kits quedan asociados a su negocio y ambos resuelven a su mismo perfil

### Requirement: Resolver la URL genérica del kit

Al acceder a la URL `plataforma/k/{serial}`, el sistema debe resolver el kit: si está activado, redirigir al perfil público del negocio asociado; si no está activado, mostrar una página que indica que el código aún no está activado; si el serial no existe, responder no encontrado.

#### Scenario: Escaneo de un kit activado
- **WHEN** un visitante escanea un kit cuyo serial está activado y asociado a un negocio
- **THEN** el sistema redirige al perfil público del negocio asociado

#### Scenario: Escaneo de un kit no activado
- **WHEN** un visitante escanea un kit cuyo serial no está activado
- **THEN** el sistema muestra una página que indica que el código aún no está activado

#### Scenario: Escaneo de un serial inexistente
- **WHEN** un visitante accede a la URL de un serial que no existe
- **THEN** el sistema responde que el recurso no existe

### Requirement: El kit funciona con el perfil gratuito

La redirección del kit debe depender solo de que el kit esté activado y el negocio conserve su perfil digital gratuito. Desactivar o no pagar los módulos de pago no debe afectar al kit.

#### Scenario: El kit sigue funcionando sin módulos de pago
- **WHEN** el negocio asociado a un kit activado no tiene activos módulos de pago
- **THEN** el escaneo del kit continúa redirigiendo al perfil gratuito del negocio
