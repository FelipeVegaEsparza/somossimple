# Directrices de diseño — Plataforma Digital para Negocios

> Guía operativa de diseño para todo el desarrollo. Las specs de OpenSpec son la
> fuente operativa del comportamiento; este documento define **cómo se ve y cómo
> se siente**. Referencia visual de inspiración: `ejemplo.png` (raíz del repo).
>
> **Regla transversal**: el resultado no debe parecer hecho por IA ni por plantilla.
> Ante la duda entre "se ve genérico" y "se ve cuidado", gana lo cuidado.

## Principios

1. **Aire y limpieza.** El espacio es parte del diseño: márgenes generosos, pocos
   elementos por pantalla, jerarquía clara. Menos decoración, más orden.
2. **Confiable y moderno.** Tonos agradables, contraste suave, azul como color de
   confianza. Nada estridente, nada "cartel".
3. **El perfil público es del negocio.** El marco de la plataforma (panel, flujos,
   navegación) lleva el sistema de diseño. Dentro del perfil del negocio, su logo,
   portada y fotografías son los protagonistas: el marco debe ser casi invisible.
4. **Mobile first.** El visitante llega escaneando un QR desde su teléfono.
5. **Sin ruido.** Un solo acento fuerte (azul), un acento puntual (ámbar), cero
   gradientes decorativos, cero emojis como iconos de UI.

## Tokens de color

| Token | Hex | Uso |
| --- | --- | --- |
| `bg-app` | `#F5F6FA` | Fondo general del panel y pantallas de la plataforma |
| `surface` | `#FFFFFF` | Tarjetas, cajas, inputs, menús |
| `border-subtle` | `#E4E7EC` | Bordes de tarjetas e inputs |
| `text-primary` | `#1A2233` | Títulos y texto principal |
| `text-secondary` | `#5B6472` | Descripciones, metadatos |
| `text-tertiary` | `#98A1B3` | Placeholders, contenido deshabilitado |
| `primary` | `#437EFF` | Acción principal, enlaces, elementos activos |
| `primary-hover` | `#2E6BE8` | Hover de la acción principal |
| `primary-tint` | `#EDF2FF` | Fondo suave de filas/elementos seleccionados |
| `accent-amber` | `#FFB400` | Badges y destacados puntuales (uso mesurado; siempre con texto oscuro) |
| `success` | `#1F9D55` | Estados positivos (p. ej. módulo activo) |
| `warning` | `#B45309` | Avisos |
| `danger` | `#D92D20` | Destructivo / errores |
| `whatsapp` | `#25D366` | Solo el botón de WhatsApp del perfil público (color de marca, no del sistema) |

Reglas de uso del color:

- El azul es el único acento del sistema. El ámbar aparece en superficies pequeñas
  (badges, destacados); si un área pide más de un acento a la vez, es señal de
  sobrecarga: simplificar.
- Texto sobre `primary` siempre blanco. Texto sobre `accent-amber` siempre oscuro.
- Nada de gradientes (ni en botones ni en fondos) ni sombras difusas exageradas.

## Tipografía

- Familia base: **Inter**. Una sola familia, sin mezclar display ni scripts.
- Escala (desktop; en móvil bajar un punto los display):

| Token | Tamaño / línea | Peso | Uso |
| --- | --- | --- | --- |
| `display` | 36/40 | 700 | Números de dashboard, encabezados de página |
| `heading-lg` | 30/36 | 600 | Título de página |
| `heading` | 24/30 | 600 | Título de tarjeta / sección |
| `title` | 20/26 | 600 | Subsecciones |
| `body` | 16/24 | 400 | Texto general |
| `body-sm` | 14/20 | 400 | Metadatos, descripciones cortas |
| `label` | 13/16 | 600 | Etiquetas de formulario, encabezados de tabla, badges |
| `caption` | 12/16 | 400 | Ayudas, pies |

Reglas: títulos con `letter-spacing: -0.01em`; el texto nunca se justifica; nada de
mayúsculas sostenidas en bloques largos (solo en etiquetas cortas si aporta).

## Forma, superficie y sombras

- Tarjetas: radio 12, borde `1px solid border-subtle`, fondo `surface`.
- Inputs y botones: radio 8. Chips/badges: radio completo.
- Sombras en dos niveles, siempre tenues:
  - `shadow-sm`: `0 1px 2px rgba(16,24,40,.05)`
  - `shadow-card`: `0 1px 3px rgba(16,24,40,.04), 0 4px 12px rgba(16,24,40,.06)`
- Sin glows, sin blur grande, sin sombras de color.

## Componentes base

**Botones** (altura 40 desktop / 48 móvil):
- Primario: fondo `primary`, texto blanco. Hover `primary-hover`. Acción única por vista.
- Secundario: fondo blanco, borde `border-subtle`, texto `text-primary`. Hover: borde más oscuro.
- Ghost: transparente, texto `primary` o `text-secondary`.
- Destructivo: borde/texto `danger` (fondo blanco).

**Tarjetas:** título `title`, contenido en `body`, divididas por aire, no por líneas
de más. Una tarjeta = una idea.

**Tablas:** encabezado `label` en `text-tertiary` sobre `bg-app`, filas con separador
`sombra de 1px` de `border-subtle`, hover de fila `#FAFBFC`. Badges de estado con
fondo suave del color + texto del mismo color oscurecido.

**Formularios:** etiqueta siempre visible (`label`, `text-secondary`), input con borde
`border-subtle` y foco en `primary`, ayuda en `caption`, errores en `danger` bajo el
campo. Estados vacíos: mensaje claro, texto `text-secondary`, y una acción única.

**Iconos:** una sola familia de iconos de trazo (line icons, grosor 1.75) a 20–24px.
Nunca emojis como iconos funcionales. WhatsApp/Instagram del perfil usan su glifo de
marca, solo como acceso a red, no como decoración.

## Espaciado y layout

- Escala de 4px: 4 · 8 · 12 · 16 · 24 · 32 · 48 · 64.
- Panel admin: contenido en columna centrada de hasta 1120px; encabezado de página
  con `heading-lg` a la izquierda y la acción primaria a la derecha.
- Perfil público: pensado en ~390–430px de ancho (móvil), se ensancha con límite
  cómodo en pantallas grandes.
- Objetivos táctiles en móvil: mínimo 48px de alto.

## Patrones del panel (Livewire)

- Navegación lateral angosta (icono + etiqueta), fondo `bg-app` o `surface`, ítem
  activo con `primary-tint` y texto/icono `primary`.
- Dashboard: tarjetas de métrica (número `display` + etiqueta `label`) en fila, y
  secciones de "Próximas reservas", "Actividad reciente" y "Acciones rápidas".
- Módulos: la sección "Mis módulos" muestra cada módulo con su estado; el gratuito
  siempre "Activo — Gratis", los de pago con acción clara (Activar/Desactivar) y un
  estado visual diferenciado (success = activo, neutro = inactivo).
- Confirmaciones destructivas en diálogo claro (nunca `alert` del navegador).

## Patrones del perfil público

- El marco es neutral (fondo claro, tipografía Inter); el protagonismo lo tiene el
  contenido del negocio: portada, logo, nombre en `heading-lg`, descripción.
- Acciones de contacto como filas de botón grandes y táctiles: Llamar, WhatsApp
  (verde de marca), Ubicación (abre el mapa), redes sociales. Orden y presencia
  según lo que el negocio tenga configurado.
- Secciones condicionadas a módulos activos (spec `business-profile`): catálogo como
  tarjetas de ítem (nombre, descripción, precio según modalidad exacto/"Desde…"/
  "Consultar precio"); reserva como flujo paso a paso simple (servicio → fecha →
  hora → datos → confirmar) sin barroquismo.
- URL/QR: solo lo que el negocio necesite; sin publicidad visual de la plataforma
  que compita con el negocio.

## Checklist "no parece hecho por IA ni plantilla"

Evitar:

- Gradientes, especialmente violetas/morados; "blobs" de fondo; efectos glass.
- Tarjetas idénticas repetidas sin jerarquía (muro de tarjetas).
- Emojis como iconos o decoración; iconos de familias mezcladas.
- Sombras exageradas, radios perfectos idénticos en todo, esquinas absurdas.
- Texto placeholder genérico ("Lorem ipsum", "¡Bienvenido a nuestra plataforma!").
- Fuentes por defecto de frameworks sin sistema tipográfico propio.
- Relleno visual innecesario: si una vista no aporta, se recorta antes de decorar.

Hacer:

- Aplicar siempre los tokens (colores/espaciado/tipografía) desde el sistema, no
  valores sueltos por pantalla.
- Jerarquía tipográfica deliberada y aire generoso.
- Microcopy en español de Chile, cuidado y humano; no traducción literal.
- Formato CLP (`$12.000`, `Desde $50.000`) y fechas en español (`lunes 8 de
  septiembre`), según lo definido en specs.
- Un estado visual claro para cada acción (hover/activo/deshabilitado).
- Revisar cada pantalla nueva contra `ejemplo.png` y estos principios antes de darla
  por terminada.
