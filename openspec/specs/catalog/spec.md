# catalog Specification

## Purpose
Representa la oferta del negocio con un catálogo unificado para productos, servicios y menús: elementos con precio opcional, organizables en categorías opcionales y con estado activo o inactivo.

## Requirements

### Requirement: Catálogo unificado para todo tipo de oferta

El sistema no debe mantener modelos separados para productos, servicios o menús: cualquier negocio debe representar su oferta como elementos de un mismo catálogo, sin distinción forzada por rubro.

#### Scenario: Negocios de distinto rubro usan el mismo catálogo
- **WHEN** un restaurante agrega una hamburguesa, una barbería agrega un corte de pelo y un gasfíter agrega una instalación de calefont
- **THEN** los tres elementos se representan con el mismo mecanismo de catálogo del sistema

### Requirement: Crear y administrar elementos del catálogo

Cada elemento del catálogo debe poder tener nombre, descripción, imagen, precio, categoría y estado activo o inactivo. El propietario debe poder crearlos y editarlos desde su panel.

#### Scenario: Crear un elemento completo
- **WHEN** el propietario crea un elemento con nombre, descripción, imagen, precio y categoría
- **THEN** el sistema lo guarda y lo deja disponible según su estado de visibilidad

#### Scenario: Editar un elemento existente
- **WHEN** el propietario modifica un elemento del catálogo y guarda
- **THEN** el sistema persiste el cambio y lo refleja donde el elemento sea visible

### Requirement: Precio opcional con modalidades

El precio de un elemento debe ser opcional. Cuando existe, el sistema debe soportar al menos dos modalidades: precio exacto y precio "desde". Cuando un elemento no tiene precio definido, el perfil público debe mostrarlo con la indicación "Consultar precio".

#### Scenario: Elemento con precio exacto
- **WHEN** el propietario define un precio exacto para un elemento
- **THEN** el sistema lo muestra en el perfil público como valor fijo (por ejemplo, "$12.000")

#### Scenario: Elemento con precio "desde"
- **WHEN** el propietario define un precio con modalidad "desde"
- **THEN** el sistema lo muestra en el perfil público como "Desde <valor>"

#### Scenario: Elemento sin precio
- **WHEN** el propietario no define precio para un elemento cuyo servicio no tiene valor fijo
- **THEN** el sistema no obliga a ingresar un precio y el perfil público muestra "Consultar precio"

### Requirement: Categorías opcionales

El catálogo debe poder organizarse en categorías, pero estas deben ser opcionales: un elemento puede existir sin pertenecer a ninguna categoría.

#### Scenario: Agrupar elementos por categoría
- **WHEN** el propietario crea categorías y asigna elementos a ellas
- **THEN** el perfil público presenta los elementos agrupados por categoría

#### Scenario: Elemento sin categoría
- **WHEN** el propietario crea un elemento sin asignarle categoría
- **THEN** el sistema lo guarda y lo muestra sin agrupación de categoría

### Requirement: Activar y desactivar elementos

El propietario debe poder activar o desactivar cada elemento del catálogo. Un elemento inactivo no debe mostrarse en el perfil público, pero sus datos deben conservarse en el panel.

#### Scenario: Desactivar un elemento
- **WHEN** el propietario desactiva un elemento del catálogo
- **THEN** el elemento deja de mostrarse en el perfil público y permanece disponible para ser reactivado

#### Scenario: Reactivar un elemento
- **WHEN** el propietario reactiva un elemento previamente desactivado
- **THEN** el sistema vuelve a mostrarlo en el perfil público con su información conservada
