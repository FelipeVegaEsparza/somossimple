# business-profile Specification

## Purpose
Define el perfil digital público del negocio: la información que el propietario administra, su URL pública estable y el renderizado dinámico que muestra únicamente las funcionalidades de los módulos activos.

## Requirements

### Requirement: Administrar la información del perfil

El propietario debe poder administrar desde su panel la información de su negocio: nombre comercial, descripción, logo, imagen de portada, galería de imágenes, dirección, ubicación, horarios de atención, teléfono, WhatsApp, correo electrónico, sitio web y redes sociales.

#### Scenario: El propietario actualiza los datos de su negocio
- **WHEN** el propietario edita un campo de información de su perfil y guarda los cambios
- **THEN** el sistema persiste el cambio y este se refleja en el perfil público

#### Scenario: Galería con varias imágenes
- **WHEN** el propietario agrega más de una imagen a la galería del perfil
- **THEN** el sistema las almacena y el perfil público las muestra dentro de su galería

### Requirement: Gestionar redes sociales y sitio web

El perfil debe permitir vincular las redes sociales del negocio (como Instagram, Facebook, TikTok, YouTube o LinkedIn) y su sitio web, sin limitar innecesariamente la cantidad de redes soportadas.

#### Scenario: Agregar una red social
- **WHEN** el propietario agrega un enlace a una red social soportada
- **THEN** el sistema lo guarda y el perfil público muestra un acceso a esa red

#### Scenario: Eliminar una red social
- **WHEN** el propietario elimina una red social de su perfil
- **THEN** el sistema deja de mostrarla en el perfil público

### Requirement: Crear botones personalizados

El propietario debe poder crear botones adicionales en su perfil, cada uno con una etiqueta y un destino (por ejemplo, "Visitar tienda online" o "Solicitar presupuesto").

#### Scenario: Crear un botón personalizado
- **WHEN** el propietario crea un botón personalizado con una etiqueta y un destino
- **THEN** el sistema lo muestra en el perfil público con la etiqueta indicada y enlazado a su destino

#### Scenario: Eliminar un botón personalizado
- **WHEN** el propietario elimina un botón personalizado
- **THEN** el sistema deja de mostrarlo en el perfil público

### Requirement: URL pública estable y única

Cada negocio debe contar con una URL pública propia, estable y única dentro de la plataforma (por ejemplo, `plataforma.cl/p/<slug>` del negocio). La URL no debe cambiar cuando el propietario modifica el contenido del perfil.

#### Scenario: Acceder al perfil por su URL
- **WHEN** un visitante abre la URL pública de un negocio
- **THEN** el sistema muestra el perfil digital público de ese negocio

#### Scenario: La URL se mantiene al modificar contenido
- **WHEN** el propietario modifica teléfono, redes, imágenes, precios u horarios de su negocio
- **THEN** la URL pública del negocio permanece igual y sigue mostrando el contenido actualizado

#### Scenario: URLs únicas entre negocios
- **WHEN** un negocio intenta usar una URL pública que ya pertenece a otro negocio
- **THEN** el sistema rechaza esa URL y solicita una distinta

### Requirement: Perfil dinámico según módulos activos

El perfil público debe mostrar únicamente las secciones correspondientes a módulos activos del negocio. La información del perfil (nombre, descripción, contacto, redes, ubicación, horarios y QR) debe mostrarse siempre. El catálogo solo debe mostrarse si el módulo de catálogo está activo, y la opción de agendar solo si el módulo de reservas está activo.

#### Scenario: Negocio solo con perfil gratuito
- **WHEN** un visitante abre el perfil de un negocio que solo tiene el módulo gratuito activo
- **THEN** el perfil muestra la información del negocio, contacto, redes, ubicación y QR, sin secciones de catálogo ni de reservas

#### Scenario: Negocio con catálogo activo
- **WHEN** un visitante abre el perfil de un negocio con el módulo de catálogo activo
- **THEN** el perfil muestra además los elementos del catálogo del negocio

#### Scenario: Negocio con reservas activas
- **WHEN** un visitante abre el perfil de un negocio con el módulo de reservas activo
- **THEN** el perfil muestra además la opción de agendar una hora

#### Scenario: Módulo desactivado no se muestra
- **WHEN** un visitante abre el perfil de un negocio cuyo módulo de reservas no está activo
- **THEN** el perfil no ofrece la opción de agendar hora
