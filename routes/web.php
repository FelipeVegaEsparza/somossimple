<?php

use App\Http\Controllers\Admin\BackupsController;
use App\Http\Controllers\Admin\KitRequestsController;
use App\Http\Controllers\Admin\KitsController;
use App\Http\Controllers\Admin\ModuleRequestsController;
use App\Http\Controllers\Admin\NegociosController;
use App\Http\Controllers\Admin\PagosController;
use App\Http\Controllers\Admin\PreciosController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\KitResolverController;
use App\Http\Controllers\Panel\BillingController;
use App\Http\Controllers\Panel\BookingSettingsController;
use App\Http\Controllers\Panel\Catalog\CatalogController;
use App\Http\Controllers\Panel\Catalog\CategoryController;
use App\Http\Controllers\Panel\ClientsController;
use App\Http\Controllers\Panel\CommunicationsController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\Events\EventController;
use App\Http\Controllers\Panel\Gallery\GalleryController;
use App\Http\Controllers\Panel\KitController;
use App\Http\Controllers\Panel\Loyalty\ActivityController as LoyaltyActivityController;
use App\Http\Controllers\Panel\Loyalty\CardController as LoyaltyCardController;
use App\Http\Controllers\Panel\Loyalty\DashboardController as LoyaltyDashboardController;
use App\Http\Controllers\Panel\Loyalty\MemberController as LoyaltyMemberController;
use App\Http\Controllers\Panel\Loyalty\ProgramController as LoyaltyProgramController;
use App\Http\Controllers\Panel\Loyalty\QuickController as LoyaltyQuickController;
use App\Http\Controllers\Panel\Loyalty\RewardController as LoyaltyRewardController;
use App\Http\Controllers\Panel\Menu\MenuCategoryController;
use App\Http\Controllers\Panel\Menu\MenuItemController;
use App\Http\Controllers\Panel\ModulesController;
use App\Http\Controllers\Panel\ProfileController;
use App\Http\Controllers\Panel\Promotions\PromotionController;
use App\Http\Controllers\Panel\ReservationsController;
use App\Http\Controllers\Panel\Services\ServiceController;
use App\Http\Controllers\Panel\Ticketera\AccessController as TicketeraAccessController;
use App\Http\Controllers\Panel\Ticketera\DashboardController as TicketeraDashboardController;
use App\Http\Controllers\Panel\Ticketera\EventController as TicketeraEventController;
use App\Http\Controllers\Panel\Ticketera\ExportController as TicketeraExportController;
use App\Http\Controllers\Panel\Ticketera\OrderController as TicketeraOrderController;
use App\Http\Controllers\Panel\Ticketera\StaffController as TicketeraStaffController;
use App\Http\Controllers\Panel\Ticketera\TicketController as TicketeraTicketController;
use App\Http\Controllers\Panel\Ticketera\TicketTypeController as TicketeraTicketTypeController;
use App\Http\Controllers\PublicAccessController;
use App\Http\Controllers\PublicClickController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicLandingController;
use App\Http\Controllers\PublicLoyaltyController;
use App\Http\Controllers\PublicModuleController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VoluntaryRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicLandingController::class, 'home'])->name('landing.home');
Route::post('kit/solicitar', [PublicLandingController::class, 'storeKitRequest'])->name('landing.kit.request');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('admin/volver', [NegociosController::class, 'leaveImpersonation'])
    ->middleware('auth')
    ->name('admin.impersonate.leave');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [NegociosController::class, 'index'])->name('index');
    Route::get('negocios', [NegociosController::class, 'listNegocios'])->name('business.index');
    Route::get('negocios/{business}', [NegociosController::class, 'show'])->name('business.show');
    Route::post('negocios/{business}/modulo/{module}', [NegociosController::class, 'toggleModule'])->name('business.module');
    Route::post('negocios/{business}/pausa', [NegociosController::class, 'togglePause'])->name('business.pause');
    Route::post('negocios/{business}/url', [NegociosController::class, 'updateSlug'])->name('business.slug');
    Route::delete('negocios/{business}', [NegociosController::class, 'destroy'])->name('business.destroy');
    Route::post('negocios/{business}/cuenta', [NegociosController::class, 'updateAccount'])->name('business.account');
    Route::post('negocios/{business}/contrasena', [NegociosController::class, 'resetPassword'])->name('business.password');
    Route::post('negocios/{business}/entrar', [NegociosController::class, 'impersonate'])->name('business.impersonate');

    Route::get('precios', [PreciosController::class, 'index'])->name('billing.prices.index');
    Route::post('precios', [PreciosController::class, 'update'])->name('billing.prices.update');
    Route::get('pagos', [PagosController::class, 'index'])->name('billing.payments.index');
    Route::get('pagos/{business}', [PagosController::class, 'show'])->name('billing.payments.show');
    Route::post('pagos/{business}', [PagosController::class, 'store'])->name('billing.payments.store');

    Route::get('kits', [KitsController::class, 'index'])->name('kits.index');
    Route::post('kits', [KitsController::class, 'store'])->name('kits.store');
    Route::post('kits/{code}/vender', [KitsController::class, 'sell'])->name('kits.sell');
    Route::get('kits/{code}/qr', [KitsController::class, 'qrImage'])->name('kits.qr');
    Route::post('kits/{business}/activar', [KitsController::class, 'activateFor'])->name('kits.activate');

    Route::get('solicitudes', [KitRequestsController::class, 'index'])->name('kitrequests.index');
    Route::post('solicitudes/{solicitud}/contactar', [KitRequestsController::class, 'markContacted'])->name('kitrequests.contact');
    Route::delete('solicitudes/{solicitud}', [KitRequestsController::class, 'destroy'])->name('kitrequests.destroy');

    Route::get('activaciones', [ModuleRequestsController::class, 'index'])->name('modulerequests.index');
    Route::post('activaciones/{modulerequest}/aprobar', [ModuleRequestsController::class, 'approve'])->name('modulerequests.approve');
    Route::post('activaciones/{modulerequest}/rechazar', [ModuleRequestsController::class, 'decline'])->name('modulerequests.decline');

    Route::get('respaldos', [BackupsController::class, 'index'])->name('backups.index');
    Route::post('respaldos', [BackupsController::class, 'store'])->name('backups.store');
    Route::post('respaldos/restaurar', [BackupsController::class, 'restore'])->name('backups.restore');
    Route::get('respaldos/{file}/descargar', [BackupsController::class, 'download'])->name('backups.download');
    Route::delete('respaldos/{file}', [BackupsController::class, 'destroy'])->name('backups.destroy');
});

Route::middleware('auth')->prefix('panel')->name('panel.')->group(function () {
    Route::get('/', DashboardController::class)->name('index');
    Route::get('mi-plan', [BillingController::class, 'index'])->name('billing');
    Route::get('modulos', [ModulesController::class, 'index'])->name('modules');
    Route::post('negocio', [BusinessController::class, 'store'])->name('business.store');

    Route::get('perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('perfil/galeria/{image}', [ProfileController::class, 'destroyGalleryImage'])
        ->name('profile.gallery.destroy');
    Route::delete('perfil/logo', [ProfileController::class, 'destroyLogo'])
        ->name('profile.logo.destroy');
    Route::delete('perfil/portada', [ProfileController::class, 'destroyCover'])
        ->name('profile.cover.destroy');

    Route::get('kit', [KitController::class, 'index'])->name('kit.index');
    Route::post('kit', [KitController::class, 'activate'])->name('kit.activate');

    Route::get('catalogo', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('catalogo/nuevo', [CatalogController::class, 'create'])->name('catalog.create');
    Route::post('catalogo', [CatalogController::class, 'store'])->name('catalog.store');
    Route::get('catalogo/{item}/editar', [CatalogController::class, 'edit'])->name('catalog.edit');
    Route::put('catalogo/{item}', [CatalogController::class, 'update'])->name('catalog.update');
    Route::post('catalogo/{item}/estado', [CatalogController::class, 'toggle'])->name('catalog.toggle');
    Route::delete('catalogo/{item}', [CatalogController::class, 'destroy'])->name('catalog.destroy');

    Route::post('catalogo/categorias', [CategoryController::class, 'store'])->name('catalog.category.store');
    Route::delete('catalogo/categorias/{category}', [CategoryController::class, 'destroy'])->name('catalog.category.destroy');

    Route::get('menu', [MenuItemController::class, 'index'])->name('menu.index');
    Route::get('menu/nuevo', [MenuItemController::class, 'create'])->name('menu.create');
    Route::post('menu', [MenuItemController::class, 'store'])->name('menu.store');
    Route::get('menu/{item}/editar', [MenuItemController::class, 'edit'])->name('menu.edit');
    Route::put('menu/{item}', [MenuItemController::class, 'update'])->name('menu.update');
    Route::post('menu/{item}/estado', [MenuItemController::class, 'toggle'])->name('menu.toggle');
    Route::delete('menu/{item}', [MenuItemController::class, 'destroy'])->name('menu.destroy');
    Route::post('menu/categorias', [MenuCategoryController::class, 'store'])->name('menu.category.store');
    Route::delete('menu/categorias/{category}', [MenuCategoryController::class, 'destroy'])->name('menu.category.destroy');

    Route::get('servicios', [ServiceController::class, 'index'])->name('services.index');
    Route::get('servicios/nuevo', [ServiceController::class, 'create'])->name('services.create');
    Route::post('servicios', [ServiceController::class, 'store'])->name('services.store');
    Route::get('servicios/{service}/editar', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('servicios/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::post('servicios/{service}/estado', [ServiceController::class, 'toggle'])->name('services.toggle');
    Route::delete('servicios/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('promociones', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('promociones/nueva', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('promociones', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('promociones/{promotion}/editar', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('promociones/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::post('promociones/{promotion}/estado', [PromotionController::class, 'toggle'])->name('promotions.toggle');
    Route::delete('promociones/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    Route::get('eventos', [EventController::class, 'index'])->name('events.index');
    Route::get('eventos/nuevo', [EventController::class, 'create'])->name('events.create');
    Route::post('eventos', [EventController::class, 'store'])->name('events.store');
    Route::get('eventos/{event}/editar', [EventController::class, 'edit'])->name('events.edit');
    Route::put('eventos/{event}', [EventController::class, 'update'])->name('events.update');
    Route::post('eventos/{event}/estado', [EventController::class, 'toggle'])->name('events.toggle');
    Route::delete('eventos/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::get('galeria', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('galeria', [GalleryController::class, 'store'])->name('gallery.store');
    Route::delete('galeria/{image}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    Route::get('reservas', [ReservationsController::class, 'index'])->name('reservations.index');
    Route::post('reservas/{reservation}/estado', [ReservationsController::class, 'updateStatus'])->name('reservations.update-status');
    Route::get('reservas/configuracion', [BookingSettingsController::class, 'index'])->name('reservations.config');
    Route::post('reservas/configuracion/servicios', [BookingSettingsController::class, 'storeService'])->name('reservations.service.store');
    Route::post('reservas/configuracion/servicios/{service}/estado', [BookingSettingsController::class, 'toggleService'])->name('reservations.service.toggle');
    Route::delete('reservas/configuracion/servicios/{service}', [BookingSettingsController::class, 'destroyService'])->name('reservations.service.destroy');
    Route::post('reservas/configuracion/horarios', [BookingSettingsController::class, 'updateHours'])->name('reservations.hours');
    Route::post('reservas/configuracion/dias', [BookingSettingsController::class, 'storeDayOff'])->name('reservations.dayoff.store');
    Route::delete('reservas/configuracion/dias/{dayOff}', [BookingSettingsController::class, 'destroyDayOff'])->name('reservations.dayoff.destroy');

    Route::get('clientes', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('clientes/nuevo', [ClientsController::class, 'create'])->name('clients.create');
    Route::post('clientes', [ClientsController::class, 'store'])->name('clients.store');
    Route::get('clientes/{client}', [ClientsController::class, 'show'])->name('clients.show');
    Route::post('clientes/{client}/notas', [ClientsController::class, 'storeNote'])->name('clients.note.store');
    Route::delete('clientes/notas/{note}', [ClientsController::class, 'destroyNote'])->name('clients.note.destroy');
    Route::post('clientes/{client}/etiquetas', [ClientsController::class, 'attachTag'])->name('clients.tag.attach');
    Route::delete('clientes/{client}/etiquetas/{tag}', [ClientsController::class, 'detachTag'])->name('clients.tag.detach');
    Route::post('clientes/{client}/consentimiento', [ClientsController::class, 'updateConsent'])->name('clients.consent');

    Route::get('comunicaciones', [CommunicationsController::class, 'index'])->name('communications.index');
    Route::get('comunicaciones/nueva', [CommunicationsController::class, 'create'])->name('communications.create');
    Route::post('comunicaciones', [CommunicationsController::class, 'store'])->name('communications.store');
    Route::post('comunicaciones/{communication}/enviar', [CommunicationsController::class, 'send'])->name('communications.send');

    Route::prefix('fidelizacion')->name('loyalty.')->group(function () {
        Route::get('/', [LoyaltyDashboardController::class, 'index'])->name('index');

        Route::get('configuracion', [LoyaltyProgramController::class, 'edit'])->name('program');
        Route::put('configuracion', [LoyaltyProgramController::class, 'update'])->name('program.update');

        Route::get('tarjeta', [LoyaltyCardController::class, 'edit'])->name('card');
        Route::put('tarjeta', [LoyaltyCardController::class, 'update'])->name('card.update');

        Route::get('clientes', [LoyaltyMemberController::class, 'index'])->name('members.index');
        Route::get('clientes/nuevo', [LoyaltyMemberController::class, 'create'])->name('members.create');
        Route::post('clientes', [LoyaltyMemberController::class, 'store'])->name('members.store');
        Route::get('clientes/{member}', [LoyaltyMemberController::class, 'show'])->name('members.show');
        Route::post('clientes/{member}/estado', [LoyaltyMemberController::class, 'toggleStatus'])->name('members.status');
        Route::delete('clientes/{member}', [LoyaltyMemberController::class, 'destroy'])->name('members.destroy');
        Route::get('clientes/{member}/qr', [LoyaltyMemberController::class, 'qr'])->name('members.qr');
        Route::post('clientes/{member}/canjear/{reward}', [LoyaltyMemberController::class, 'reward'])->name('members.reward');
        Route::post('clientes/{member}/wallet/{platform}', [LoyaltyMemberController::class, 'wallet'])->name('members.wallet');

        Route::get('recompensas', [LoyaltyRewardController::class, 'index'])->name('rewards.index');
        Route::get('recompensas/nueva', [LoyaltyRewardController::class, 'create'])->name('rewards.create');
        Route::post('recompensas', [LoyaltyRewardController::class, 'store'])->name('rewards.store');
        Route::get('recompensas/{reward}/editar', [LoyaltyRewardController::class, 'edit'])->name('rewards.edit');
        Route::put('recompensas/{reward}', [LoyaltyRewardController::class, 'update'])->name('rewards.update');
        Route::post('recompensas/{reward}/estado', [LoyaltyRewardController::class, 'toggle'])->name('rewards.toggle');
        Route::delete('recompensas/{reward}', [LoyaltyRewardController::class, 'destroy'])->name('rewards.destroy');

        Route::get('actividad', [LoyaltyActivityController::class, 'index'])->name('activities.index');

        Route::get('registrar', [LoyaltyQuickController::class, 'index'])->name('quick');
        Route::post('registrar', [LoyaltyQuickController::class, 'store'])->name('quick.store');
    });

    Route::prefix('ticketera')->name('ticketera.')->group(function () {
        Route::get('/', [TicketeraDashboardController::class, 'index'])->name('index');

        Route::get('eventos', [TicketeraEventController::class, 'index'])->name('events.index');
        Route::get('eventos/nuevo', [TicketeraEventController::class, 'create'])->name('events.create');
        Route::post('eventos', [TicketeraEventController::class, 'store'])->name('events.store');
        Route::get('eventos/{event}', [TicketeraEventController::class, 'show'])->name('events.show');
        Route::get('eventos/{event}/editar', [TicketeraEventController::class, 'edit'])->name('events.edit');
        Route::put('eventos/{event}', [TicketeraEventController::class, 'update'])->name('events.update');
        Route::post('eventos/{event}/estado', [TicketeraEventController::class, 'status'])->name('events.status');
        Route::delete('eventos/{event}', [TicketeraEventController::class, 'destroy'])->name('events.destroy');

        Route::post('eventos/{event}/tipos', [TicketeraTicketTypeController::class, 'store'])->name('types.store');
        Route::put('eventos/{event}/tipos/{type}', [TicketeraTicketTypeController::class, 'update'])->name('types.update');
        Route::post('eventos/{event}/tipos/{type}/estado', [TicketeraTicketTypeController::class, 'toggle'])->name('types.toggle');
        Route::delete('eventos/{event}/tipos/{type}', [TicketeraTicketTypeController::class, 'destroy'])->name('types.destroy');

        Route::get('ordenes', [TicketeraOrderController::class, 'index'])->name('orders.index');
        Route::get('ordenes/{order}', [TicketeraOrderController::class, 'show'])->name('orders.show');
        Route::post('ordenes/{order}/reembolso', [TicketeraOrderController::class, 'refund'])->name('orders.refund');

        Route::get('entradas', [TicketeraTicketController::class, 'index'])->name('tickets.index');
        Route::get('entradas/{ticket}', [TicketeraTicketController::class, 'show'])->name('tickets.show');

        Route::get('acceso', [TicketeraAccessController::class, 'index'])->name('access');
        Route::post('acceso/validar', [TicketeraAccessController::class, 'validateToken'])->name('access.validate');

        Route::get('personal', [TicketeraStaffController::class, 'index'])->name('staff.index');
        Route::post('personal', [TicketeraStaffController::class, 'store'])->name('staff.store');
        Route::post('personal/{staff}/estado', [TicketeraStaffController::class, 'toggle'])->name('staff.toggle');
        Route::delete('personal/{staff}', [TicketeraStaffController::class, 'destroy'])->name('staff.destroy');

        Route::get('eventos/{event}/exportar/ordenes', [TicketeraExportController::class, 'orders'])->name('export.orders');
        Route::get('eventos/{event}/exportar/entradas', [TicketeraExportController::class, 'tickets'])->name('export.tickets');
        Route::get('eventos/{event}/exportar/accesos', [TicketeraExportController::class, 'accesses'])->name('export.accesses');
    });
});

Route::get('k/{serial}', [KitResolverController::class, 'show'])->name('k.show');

// Compatibilidad: la URL pública vivía en /p/{slug} y ahora vive en la raíz.
Route::get('p/{slug}', [PublicProfileController::class, 'legacyRedirect'])->name('p.legacy');

Route::get('{slug}/click/{type}', [PublicClickController::class, 'track'])->name('p.click');

Route::controller(ReservationController::class)->prefix('{slug}/reservar')->group(function () {
    Route::get('/', 'show')->name('p.reservation');
    Route::get('/horas', 'times')->name('p.reservation.times');
    Route::post('/', 'store')->name('p.reservation.store');
});

// Página pública de fidelización (antes de las rutas de módulos para no ser eclipsadas).
Route::get('fidelizacion/{slug}', [PublicLoyaltyController::class, 'show'])->name('loyalty.public');
Route::get('fidelizacion/{slug}/registro', [PublicLoyaltyController::class, 'registerForm'])->name('loyalty.register');
Route::post('fidelizacion/{slug}/registro', [PublicLoyaltyController::class, 'register']);
Route::post('fidelizacion/{slug}/identificar', [PublicLoyaltyController::class, 'identify'])->name('loyalty.identify');
Route::get('fidelizacion/{slug}/tarjeta/{token}', [PublicLoyaltyController::class, 'card'])->name('loyalty.card');
Route::get('fidelizacion/{slug}/tarjeta/{token}/qr', [PublicLoyaltyController::class, 'qr'])->name('loyalty.qr');

// Ticketera pública: evento, compra, entrada digital y control de acceso.
Route::get('evento/{slug}', [PublicEventController::class, 'show'])->name('ticketera.event');
Route::post('evento/{slug}/comprar', [PublicEventController::class, 'store'])->name('ticketera.checkout');
Route::get('evento/{slug}/orden/{number}', [PublicEventController::class, 'order'])->name('ticketera.order');
Route::get('entrada/{token}', [PublicEventController::class, 'ticket'])->name('ticketera.ticket');
Route::get('entrada/{token}/qr', [PublicEventController::class, 'qr'])->name('ticketera.ticket.qr');
Route::get('entradas/recuperar', [PublicEventController::class, 'recoverForm'])->name('ticketera.recover');
Route::post('entradas/recuperar', [PublicEventController::class, 'recover'])->name('ticketera.recover.submit');
Route::get('ticketera/acceso/{token}', [PublicAccessController::class, 'scanner'])->name('ticketera.access');
Route::post('ticketera/acceso/{token}/validar', [PublicAccessController::class, 'validateToken'])->name('ticketera.access.validate');

// Páginas públicas de cada módulo (se abren desde las cards del perfil).
Route::get('{slug}/catalogo', [PublicModuleController::class, 'catalog'])->name('p.catalog');
Route::get('{slug}/menu', [PublicModuleController::class, 'menu'])->name('p.menu');
Route::get('{slug}/servicios', [PublicModuleController::class, 'services'])->name('p.services');
Route::get('{slug}/promociones', [PublicModuleController::class, 'promotions'])->name('p.promotions');
Route::get('{slug}/novedades', [PublicModuleController::class, 'clients'])->name('p.clients');
Route::get('{slug}/eventos', [PublicModuleController::class, 'events'])->name('p.events');
Route::get('{slug}/galeria', [PublicModuleController::class, 'gallery'])->name('p.gallery');

// PWA por negocio: manifiesto, service worker e íconos.
Route::get('{slug}/manifest.webmanifest', [PwaController::class, 'manifest'])->name('p.manifest');
Route::get('{slug}/sw.js', [PwaController::class, 'serviceWorker'])->name('p.sw');
Route::get('{slug}/icon-{size}.png', [PwaController::class, 'icon'])->whereNumber('size')->name('p.icon');

Route::post('{slug}/registro', [VoluntaryRegistrationController::class, 'store'])->name('p.voluntary');

// Catch-all del perfil público en la raíz: debe ir al final.
Route::get('{slug}', [PublicProfileController::class, 'show'])->name('p.show');
