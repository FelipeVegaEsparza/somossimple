## Context

Proyecto greenfield: no existe código ni stack definido. La motivación está en `proposal.md`. Este diseño establece la arquitectura conceptual y el modelo de datos del baseline del MVP (9 capacidades), para que las specs y las tareas tengan una base coherente. El stack tecnológico se decidirá en una conversación posterior y no condiciona las decisiones aquí: todo lo descrito es agnóstico de tecnología.

Restricciones de entrada:

- Una cuenta → un negocio (§33). Datos aislados por negocio (§32).
- Perfil gratuito siempre disponible; los demás módulos son activables de forma independiente (§5, §27-28).
- Desactivar un módulo nunca elimina sus datos (§28, §30, §45).
- El MVP no inventa funcionalidades fuera del documento maestro (§46).

## Goals / Non-Goals

**Goals:**

- Fijar un modelo de datos que soporte las 9 capacidades sin ambigüedad de entidades entre módulos.
- Hacer del "perfil dinámico" (§8) una proyección derivada de la activación de módulos, no una página distinta por configuración.
- Separar de forma estructural: cliente vs. consentimiento por canal; módulo vs. activación; contenido vs. presentación.
- Definir el motor de disponibilidad de reservas como un problema acotado (una agenda por negocio).

**Non-Goals:**

- Elegir stack, base de datos, framework, infraestructura o proveedores (decisión posterior).
- Diseñar facturación, pasarela de pago, precios ni planes (§29: se definen después).
- Diseñar la venta/gestión de productos físicos QR/NFC/3D (§22-23: fuera del MVP).
- Diseñar infraestructura de envío de email (§E: fuera de la definición funcional).
- Múltiples empleados, sucursales, roles o negocios por cuenta (§33, §43).

## Decisions

### 1. El negocio es la raíz de todos los datos

Todo dato de producto (perfil, catálogo, reservas, clientes, campañas, eventos) pertenece a un `Business`. La cuenta (`Account`) del MVP se asocia a exactamente un negocio. El aislamiento (§32) es una consecuencia del modelo: ninguna consulta cruza negocios, salvo el acceso público de solo lectura al perfil por su slug.

### 2. Activación de módulos como registro de primer nivel

Existe un registro `module_access(Business, Module)` con estado activo/inactivo. Los módulos del MVP son fijos: `profile` (gratuito, siempre activo), `catalog`, `reservations`, `clients`, `communications`.

- "Determinar módulos activos" (§F) = leer este registro; no hay fechas de cobro ni pasarela en MVP.
- La activación/desactivación es una operación del panel/plataforma, no un checkout.
- Gating y datos van separados: desactivar cambia el flag, jamás dispara borrado (§28/§30). La regla se cumple por diseño: ninguna operación de desactivación elimina datos de módulo.

### 3. Perfil público = proyección de datos filtrados por módulos activos

El perfil público renderiza secciones de datos del negocio **solo si el módulo correspondiente está activo** (§8). Un único renderizador condicional, sin duplicar el modelo de contenido:

```
Perfil público (Business por slug)
 ├─ perfil .................. siempre (gratuito)
 ├─ catálogo ................ si module_access(catalog)  activo
 ├─ reservas (Agendar) ...... si module_access(reservations) activo
 └─ registro promociones .... si module_access(clients) activo  (+ consentimiento)
```

### 4. Modelo de precio del catálogo

Un ítem de catálogo tiene precio opcional con modalidad: `exact` ("$12.000"), `from` ("Desde $50.000") o ausente ("Consultar precio"). El valor es numérico solo cuando hay modalidad exacta o desde. Moneda de display CLP (mercado inicial). Un servicio sin precio fijo no queda forzado a un valor (§9).

### 5. Los servicios reservables viven en Reservas, no en Catálogo

Como los módulos se activan de forma independiente (§28), Reservas no puede depender de Catálogo: un negocio puede tener Reservas sin Catálogo. El módulo Reservas define su propia entidad `BookableService` (nombre, duración, precio, activo) + horarios de atención. Vincular un ítem reservable con un ítem de catálogo se deja como evolución, no MVP.

### 6. Identidad del cliente: teléfono normalizado como heurística, no clave absoluta

- Todo cliente pertenece a un negocio; no existe identidad global entre negocios.
- El teléfono, normalizado a formato internacional (`+56...`), es el criterio principal para detectar un cliente ya existente dentro del mismo negocio (por ejemplo, al crear un cliente desde una reserva o registro voluntario).
- No es una identidad absoluta: no bloquea la creación de registros. Puede haber duplicados; el sistema sugiere/relaciona, no impide (§A).
- Email y nombre son complementarios, no identificadores primarios.

### 7. Consentimiento por canal, desacoplado del cliente

`ClientConsent(Client, Channel)` registra estado (`granted` / `not_granted`), origen (reserva, registro voluntario, manual) y fecha. MVP: solo canal `email`. Ser cliente no implica consentimiento (§20, §B). La segmentación para campañas (Clientes nuevos/frecuentes/VIP, etiquetas) filtra clientes; el consentimiento por canal filtra a quién se puede enviar.

### 8. Motor de disponibilidad acotado

Una sola agenda por negocio (§D). Un horario semanal de atención por día y franjas; `BookableService` con duración. Disponibilidad de un servicio en una fecha = huecos del horario que no colisionan con reservas **bloqueantes**. Estados bloqueantes: `pending`, `confirmed`; no bloquean: `cancelled`, `completed`. Sin empleados, recursos, ni calendarios externos (§13, §43).

```
horario_semanal(negocio, dia) -> [franjas]
reservas_bloqueantes(negocio, fecha) -> [inicio, fin]
huecos(servicio, fecha) = franjas - reservas_bloqueantes
                        (inicio libre a múltiplos de 15 min, >= duración(servicio))
```

### 9. Comunicaciones con interfaz de envío desacoplada

La capacidad define comportamiento funcional: crear comunicación (promoción/oferta/anuncio/etc.), resolver destinatarios (consentimiento email + segmento) y registrar envío en historial con estado. El envío real pasa por un puerto/interfaz cuyo implementador (proveedor SMTP/email) queda fuera del alcance funcional (§E). Las comunicaciones operacionales (p. ej. de una reserva) se modelan como categoría distinta de las comerciales (§20).

### 10. QR/NFC y eventos de acceso

El acceso público es una URL estable `.../p/<slug>` (§21). El QR digital se genera desde esa URL; un acceso físico (conceptual, §23) apunta a la misma URL. Los eventos de acceso (visita, escaneo QR, tap NFC, click en red/WhatsApp/llamar) se registran como eventos con tipo y timestamp por negocio y alimentan `analytics`. No hay gestión de dispositivos físicos en MVP.

### 11. Analytics como agregados sobre eventos

`analytics` agrega eventos simples (visitas, escaneos, clicks, reservas) en el dashboard (§24/§26). El MVP no requiere pipeline de analítica: conteos básicos sobre eventos recientes.

## Risks / Trade-offs

- **Deriva entre documento maestro y specs** → Las specs tienen prioridad declarada (config + proposal); el documento maestro queda como contexto, no como fuente operativa.
- **Scope creep ("el agente inventa")** → 9 capacidades fijas + sección no-scope §43 + regla §46 explicitada en el context del proyecto.
- **Dedupe por teléfono genera duplicados/errores de matching** (dos personas comparten teléfono, formato sucio) → Aceptado en MVP: es heurística, normalización +56, sin bloqueo; el negocio puede corregir manualmente.
- **Motor de disponibilidad simplificado puede ser insuficiente en casos límite** (sin rango máximo de anticipación definido, sin recursos) → Aceptado: agenda única por negocio es el límite del MVP (§D). Rango de anticipación se tratará como decisión menor de implementación.
- **Separación servicio reservable vs. catálogo duplica ingreso de datos** para negocios que usan ambos módulos → Trade-off deliberado por independencia de módulos; la vinculación futura lo mitiga sin migración destructiva.

## Migration Plan

Baseline sin código existente: no hay migración de sistema. Al archivar el cambio, las specs delta se aplican a `openspec/specs/`, creando el estado operativo inicial de las 9 capacidades.

## Open Questions

- ~~Stack tecnológico~~ **Decidido (no modifica specs):** PHP + Laravel puro (sin Filament), MySQL; Blade + Tailwind + Alpine.js para el perfil público y Livewire para el panel. Diseño definido en `docs/directrices-diseno.md`.
- Canal de recuperación de acceso (accounts): se asume en el MVP el correo electrónico de la cuenta; el documento maestro no lo especifica. No cambia la estructura del baseline, solo el canal.
- Detalles de UX del panel y del perfil público (disposición, componentes): dependientes del stack; fuera del alcance de este cambio.
- Precios/planes comerciales (§29): post-MVP por definición.
