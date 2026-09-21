## 1. Base y activación del baseline

- [x] 1.1 Confirmar stack tecnológico con el equipo y registrarlo en `openspec/config.yaml` (context), verificando que la decisión no contradice ninguna spec (se mantiene el modelo por negocio y el gating por módulos)
- [x] 1.2 Inicializar el esqueleto del proyecto según el stack elegido, verificando que compila y ejecuta una ruta de salud
- [x] 1.3 Implementar el modelo base de datos (entidades: negocio como raíz, cuenta → negocio, registro de acceso por módulo), verificando que la migración/creación de esquema aplica limpio
- [x] 1.4 Aplicar el aislamiento por negocio como restricción de acceso a datos en la capa de datos, verificando que ninguna consulta de un negocio expone datos de otro

## 2. Cuenta (accounts)

- [x] 2.1 Implementar registro de cuenta con correo único, nombre y contraseña, verificando los escenarios "Registro exitoso" y "Registro con correo ya utilizado"
- [x] 2.2 Implementar inicio de sesión por correo y contraseña, verificando los escenarios "Credenciales correctas" y "Contraseña incorrecta"
- [x] 2.3 Implementar recuperación de acceso por el correo registrado de la cuenta, verificando los escenarios de recuperación exitosa y de cuenta inexistente (sin revelar existencia)
- [x] 2.4 Implementar la relación de una cuenta con un único negocio, verificando los escenarios de creación del primer negocio y de rechazo de un segundo negocio

## 3. Suscripciones y activación de módulos (subscriptions)

- [x] 3.1 Implementar el registro de activación por módulo y negocio con los 5 módulos fijos (perfil gratuito siempre activo), verificando el escenario "Estado inicial de un negocio nuevo"
- [x] 3.2 Implementar la consulta de módulos activos de un negocio para el panel, verificando el escenario "Consultar los módulos activos de un negocio"
- [x] 3.3 Implementar activación y desactivación de módulos desde el panel sin facturación, verificando los escenarios "Activar el módulo de reservas" y "Desactivar un módulo de pago"
- [x] 3.4 Implementar la regla de que un módulo inactivo no queda disponible públicamente, verificando el escenario "Módulo inactivo no disponible públicamente"
- [x] 3.5 Implementar la conservación de datos al desactivar y la restauración al reactivar un módulo (sin operación de borrado al desactivar), verificando los escenarios "Datos conservados tras desactivar un módulo" y "Reactivar un módulo conserva su información"

## 4. Perfil digital (business-profile)

- [x] 4.1 Implementar la administración de la información del negocio (nombre, descripción, logo, portada, galería, dirección, ubicación, horarios, teléfono, WhatsApp, email, sitio web), verificando los escenarios de actualización y de galería múltiple
- [x] 4.2 Implementar la gestión de redes sociales y sitio web sin límite innecesario de redes, verificando los escenarios "Agregar una red social" y "Eliminar una red social"
- [x] 4.3 Implementar botones personalizados con etiqueta y destino, verificando los escenarios de creación y eliminación
- [x] 4.4 Implementar la URL pública por negocio (slug único, estable ante cambios de contenido), verificando los tres escenarios de "URL pública estable y única"
- [x] 4.5 Implementar el perfil público dinámico que renderiza secciones solo según módulos activos, verificando los cuatro escenarios de "Perfil dinámico según módulos activos"

## 5. QR y NFC (qr-nfc)

- [x] 5.1 Implementar la generación del QR digital por negocio apuntando a la URL estable, verificando los escenarios "Descargar el QR digital" y "El QR sigue siendo válido tras cambios de contenido"
- [x] 5.2 Implementar la asociación conceptual de accesos físicos al negocio (misma URL, sin gestión de dispositivos), verificando los escenarios "Asociar un acceso físico al negocio" y "Múltiples accesos al mismo perfil"

## 6. Catálogo (catalog)

- [x] 6.1 Implementar el catálogo unificado (sin separación por productos/servicios/menú), verificando el escenario "Negocios de distinto rubro usan el mismo catálogo"
- [x] 6.2 Implementar creación y edición de elementos (nombre, descripción, imagen, precio, categoría, estado), verificando los escenarios de creación y edición
- [x] 6.3 Implementar el precio opcional con modalidades exacto, "desde" y sin valor ("Consultar precio"), verificando los tres escenarios de "Precio opcional con modalidades"
- [x] 6.4 Implementar categorías opcionales del catálogo, verificando los escenarios "Agrupar elementos por categoría" y "Elemento sin categoría"
- [x] 6.5 Implementar activar/desactivar elementos conservando datos, verificando los escenarios "Desactivar un elemento" y "Reactivar un elemento"

## 7. Reservas (reservations)

- [x] 7.1 Implementar configuración de servicios reservables con nombre, duración y precio dentro del módulo de reservas (independiente del módulo de catálogo), verificando los escenarios de "Configurar servicios reservables"
- [x] 7.2 Implementar configuración de días/horarios de atención y días no disponibles, verificando los escenarios de "Configurar horarios de atención"
- [x] 7.3 Implementar la solicitud de reserva del visitante (servicio → fecha → hora → nombre → teléfono, email opcional), verificando los escenarios de "Solicitar una reserva"
- [x] 7.4 Implementar el cálculo de disponibilidad (horario + duración + reservas bloqueantes) donde pendiente/confirmada bloquean y cancelada/completada liberan, verificando los tres escenarios de "Calcular disponibilidad de horas"
- [x] 7.5 Implementar los estados de reserva y su gestión desde el panel, verificando los escenarios de "Gestionar estados de las reservas"
- [x] 7.6 Implementar la agenda única por negocio (día y semana), verificando los escenarios de "Consultar la agenda de reservas"

## 8. Clientes (clients)

- [x] 8.1 Implementar registro/listado/búsqueda de clientes por negocio, verificando los escenarios de "Registrar clientes con datos básicos"
- [x] 8.2 Implementar detección de cliente existente por teléfono normalizado (+56) al crear desde reserva o registro voluntario, verificando los escenarios de "Detectar un cliente existente por teléfono"
- [x] 8.3 Implementar el registro voluntario en el perfil con aceptación explícita de comunicaciones, verificando los escenarios de "Registro voluntario con aceptación explícita"
- [x] 8.4 Implementar etiquetas (fijas + personalizadas) y notas por cliente, verificando los escenarios de "Etiquetar y anotar clientes"
- [x] 8.5 Implementar el historial básico del cliente (reservas, primera/última, total), verificando el escenario "Ver el historial de reservas de un cliente"
- [x] 8.6 Implementar el consentimiento por canal (email) independiente del cliente con estado, origen y fecha, y su registro/retiro desde el panel, verificando los escenarios de "Consentimiento por canal independiente del cliente"
- [x] 8.7 Verificar el aislamiento de clientes por negocio con el escenario "El negocio no ve clientes ajenos"

## 9. Comunicaciones (communications)

- [x] 9.1 Implementar creación de comunicaciones con tipo (promociones, ofertas, anuncios, noticias, cambio de horario, general) y contenido, verificando los escenarios de "Crear una comunicación"
- [x] 9.2 Implementar la selección de destinatarios por segmento (todos con consentimiento, nuevos, frecuentes, inactivos, VIP, etiqueta) excluyendo clientes sin consentimiento o sin email, verificando los tres escenarios de "Seleccionar destinatarios por segmento"
- [x] 9.3 Implementar la regla de consentimiento comercial por canal (cliente ≠ suscriptor) y la distinción operacional/comercial, verificando los escenarios de "Consentimiento para comunicaciones comerciales"
- [x] 9.4 Implementar el historial de envíos con tipo, contenido, destinatarios y estado, verificando los escenarios de "Registrar el historial de envíos"

## 10. Estadísticas (analytics)

- [x] 10.1 Implementar el registro de eventos del perfil público (visitas, escaneos QR, accesos NFC, clicks en contacto/redes), verificando los escenarios de "Registrar eventos de acceso e interacción"
- [x] 10.2 Implementar la presentación de estadísticas básicas en el panel (conteos por negocio), verificando los escenarios de "Mostrar estadísticas del negocio"
- [x] 10.3 Verificar el aislamiento de estadísticas por negocio con el escenario "Las estadísticas no mezclan negocios"

## 11. Integración y cierre del baseline

- [x] 11.1 Verificar de extremo a extremo el perfil dinámico (perfil gratuito, con catálogo, con reservas y con módulo desactivado) sobre un negocio de ejemplo
- [x] 11.2 Verificar el ciclo completo de datos sin pérdida: activar reservas → generar reservas y clientes → desactivar reservas → reactivar y comprobar que la información continúa disponible
- [x] 11.3 Ejecutar la validación de specs y tareas del cambio (`openspec validate` y revisión de tareas completadas) antes de archivar
- [x] 11.4 Archivar el cambio para establecer las specs operativas bajo `openspec/specs/` y verificar que las 9 capacidades quedan en el estado operativo del proyecto
