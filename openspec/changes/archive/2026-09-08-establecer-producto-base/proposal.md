## Why

El producto está definido a nivel conceptual en el documento maestro (`docs/documento-maestro.md`), pero el desarrollo no puede ejecutarse guiado por un documento narrativo: necesita una fuente operativa descompuesta, validable y con prioridad clara. Este cambio establece el baseline de specs de las 9 capacidades del MVP, fijando el alcance para impedir que se inventen funcionalidades fuera de lo definido (regla §46 del documento maestro).

## What Changes

- Se crea el baseline de specs de las 9 capacidades del MVP, cada una como spec delta `ADDED` bajo `openspec/changes/establecer-producto-base/specs/`.
- El documento maestro pasa a ser contexto general; las specs de OpenSpec son la fuente operativa y tienen prioridad ante contradicción.
- Se incorporan a las specs las decisiones A-F cerradas en exploración:
  - Teléfono como criterio principal de detección de cliente existente dentro de un negocio (no identidad absoluta).
  - Consentimiento de comunicaciones independiente de la existencia del cliente y asociado a canal (MVP: solo email).
  - Precio de catálogo opcional con modalidades: exacto, "desde", o "Consultar precio" sin valor.
  - Reservas: una única agenda por negocio; disponibilidad según horario + duración del servicio + reservas existentes. Sin empleados ni calendarios externos.
  - Comunicaciones: solo comportamiento funcional (campaña, destinatarios, historial); infraestructura de envío fuera de la definición.
  - Suscripciones MVP: solo concepto de activación/acceso por módulo; sin facturación ni pasarela de pago. Datos nunca se eliminan al desactivar/expirar.
- Decisiones derivadas que las specs codifican como reglas:
  - Reservas con estado Pendiente/Confirmada bloquean capacidad; Completada/Cancelada la liberan.
  - Los servicios reservables pertenecen al módulo Reservas (independiente del módulo Catálogo), con nombre, duración y precio propios.
  - Consentimiento registrado como estado por canal con origen y fecha.
  - Dedupe por teléfono normalizado (formato +56) dentro del mismo negocio.
  - Activación de módulo MVP = registro de acceso por (negocio, módulo), sin fechas de cobro ni checkout.

## Capabilities

### New Capabilities

- `accounts`: cuenta de usuario, registro, inicio de sesión, recuperación de acceso, relación 1 cuenta → 1 negocio.
- `business-profile`: perfil público del negocio, edición de su información, URL estable y renderizado dinámico según módulos activos.
- `catalog`: catálogo unificado (productos/servicios/menú), categorías opcionales, ítems con precio opcional y estado activo/inactivo.
- `reservations`: configuración de servicios reservables y horarios, solicitud de reserva, disponibilidad, estados y agenda de un negocio.
- `clients`: base de clientes del negocio, registro (reserva, voluntario, manual), etiquetas, notas, historial y consentimiento por canal.
- `communications`: campañas de email, selección de destinatarios por consentimiento/segmento, e historial de envíos.
- `qr-nfc`: URL estable de acceso, QR digital y asociación conceptual del acceso físico con el negocio.
- `analytics`: estadísticas básicas del negocio (visitas, escaneos, clicks, reservas).
- `subscriptions`: activación/desactivación de módulos por negocio, determinación de módulos activos y conservación de datos al expirar.

### Modified Capabilities

No hay capacidades existentes: es el baseline inicial del proyecto.

## Impact

- Estructura: se crean specs nuevas bajo `openspec/specs/` al archivar el cambio (baseline de las 9 capacidades).
- Contrato de comportamiento: primer estado operativo del producto; cambios futuros se modelan como deltas sobre estas specs.
- Sin impacto de código: el cambio es solo de definición. El stack tecnológico se definirá en una conversación posterior y no condiciona estas specs.
- Reglas transversales codificadas: perfil dinámico según módulos activos (§8), sin pérdida de datos al desactivar (§28/§30), perfil gratuito siempre disponible (§31), aislamiento de datos por negocio (§32), una cuenta → un negocio (§33), alcance MVP §45 y no-scope §43.
