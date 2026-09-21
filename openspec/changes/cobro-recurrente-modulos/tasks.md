## 1. Datos y base comercial

- [x] 1.1 Crear migraciones `module_prices`, `module_payments` y `module_access.activated_at`, verificando que `php artisan migrate:fresh` aplica limpio y no rompe las specs de `subscriptions`
- [x] 1.2 Crear `BillingService` con: precio vigente de un módulo, cobertura de un pago (30 días), estado al día/atrasado/inactivo de un negocio por módulo (incluida la gracia inicial de 7 días tras `activated_at`), verificando con tests unitarios cada estado
- [x] 1.3 Al activar un módulo pago (desde panel del dueño o administración), registrar `activated_at` la primera vez, verificando que la gracia se calcula desde ahí

## 2. Sección de Precios (área de administración)

- [x] 2.1 Crear vista y rutas de la sección "Precios" del panel admin (editar precio mensual por módulo de pago; Perfil mostrado como Gratis sin precio), verificando el escenario "Definir el precio de un módulo" y que Perfil no permite precio
- [x] 2.2 Agregar los ítems de menú lateral "Precios" y "Pagos" en el layout de administración, verificando que navegan a sus secciones y respetan el rol admin

## 3. Sección de Pagos (área de administración)

- [x] 3.1 Vista "Pagos": listado de negocios con estado por módulo (al día / atrasado / inactivo) y acceso a la ficha de pagos, verificando el escenario "Consultar el estado de pago de un negocio"
- [x] 3.2 En la ficha de pagos de un negocio: registrar un pago (módulo, monto, fecha) e historial, verificando el escenario "Registrar un pago" y que un pago reciente deja el módulo al día
- [x] 3.3 Registrar un pago vigente sobre un módulo apagado por impago lo reactiva, verificando el escenario "Reactivar al registrar un pago"

## 4. Regla de impago

- [x] 4.1 Crear comando `app:apply-billing` que evalúa diariamente los módulos pago con precio definido y activos que no estén al día y los desactiva conservando datos, verificando el escenario "Módulo atrasado tras el período de gracia"
- [x] 4.2 Registrar el comando en el scheduler diario, verificando que se ejecuta sin errores
- [x] 4.3 Verificar con tests que un módulo sin precio definido no se apaga por impago y que el Perfil Digital nunca se apaga, cubriendo los escenarios "Módulo sin precio no se apaga por impago" y "Perfil gratuito activo ante un impago"

## 5. Verificación y cierre

- [x] 5.1 Ejecutar la suite completa de tests y `openspec validate`, verificando que no hay regresiones en `subscriptions`, `panel-admin-plataforma` ni en el flujo público
- [ ] 5.2 Recorrer en pantalla: definir precios, registrar un pago, simular impago (comando) y confirmar reactivación, contrastando el diseño con `docs/directrices-diseno.md`
