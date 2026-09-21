<?php

namespace App\Http;

use App\Enums\Module;

class Navigation
{
    /**
     * Navegación del panel del negocio agrupada en secciones. Las secciones de
     * módulos de pago solo aparecen si el negocio tiene el módulo activo.
     *
     * @return list<array{label: string, items: list<array{route: string, label: string, active: string, icon: string}>}>
     */
    public static function sections(): array
    {
        $business = auth()->user()?->business;

        $sections = [
            [
                'label' => 'Principal',
                'items' => [
                    self::item('panel.index', 'Dashboard', 'panel.index', 'M1.5 4.5v3m0 0h3m-3 0h-3m3 0v-3M5 7a.5.5 0 0 1 .5.5M5 14v2M5 9.5V12m0 5.5a.5.5 0 0 0 .5.5M5 17.5a.5.5 0 0 1 .5.5M9 4.5h6M9 9h2M9 13.5h4M9 18h6m3 0h-2.5m2.5 0V9l-3.5-3.5M7 5.5h3.5'),
                    self::item('panel.modules', 'Módulos', 'panel.modules', 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5'),
                ],
            ],
            [
                'label' => 'Mi negocio',
                'items' => [
                    self::item('panel.profile.edit', 'Perfil', 'panel.profile.edit', 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z'),
                    self::item('panel.billing', 'Mi plan', 'panel.billing', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'),
                    self::item('panel.kit.index', 'Mi kit', 'panel.kit.*', 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'),
                ],
            ],
        ];

        $operacion = [];

        if ($business) {
            if ($business->isModuleActive(Module::Catalog)) {
                $operacion[] = self::item('panel.catalog.index', 'Catálogo', 'panel.catalog.*', 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z');
            }
            if ($business->isModuleActive(Module::Menu)) {
                $operacion[] = self::item('panel.menu.index', 'Menú', 'panel.menu.*', 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z');
            }
            if ($business->isModuleActive(Module::Services)) {
                $operacion[] = self::item('panel.services.index', 'Servicios', 'panel.services.*', 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z');
            }
            if ($business->isModuleActive(Module::Promotions)) {
                $operacion[] = self::item('panel.promotions.index', 'Promociones', 'panel.promotions.*', 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z');
            }
            if ($business->isModuleActive(Module::Events)) {
                $operacion[] = self::item('panel.events.index', 'Eventos', 'panel.events.*', 'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 4.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-4.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z');
            }
            if ($business->isModuleActive(Module::Gallery)) {
                $operacion[] = self::item('panel.gallery.index', 'Galería', 'panel.gallery.*', 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z');
            }
            if ($business->isModuleActive(Module::Reservations)) {
                $operacion[] = self::item('panel.reservations.index', 'Reservas', 'panel.reservations.*', 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z');
            }
            if ($business->isModuleActive(Module::Clients)) {
                $operacion[] = self::item('panel.clients.index', 'Clientes', 'panel.clients.*', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z');
            }
            if ($business->isModuleActive(Module::Communications)) {
                $operacion[] = self::item('panel.communications.index', 'Comunicaciones', 'panel.communications.*', 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75');
            }
            if ($business->isModuleActive(Module::Loyalty)) {
                $operacion[] = self::item('panel.loyalty.index', 'Fidelización', 'panel.loyalty.*', 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z');
            }
            if ($business->isModuleActive(Module::Ticketera)) {
                $operacion[] = self::item('panel.ticketera.index', 'Ticketera', 'panel.ticketera.*', 'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 4.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-4.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z');
            }
        }

        if ($operacion !== []) {
            $sections[] = ['label' => 'Operación', 'items' => $operacion];
        }

        return $sections;
    }

    private static function item(string $route, string $label, string $active, string $iconPath): array
    {
        return [
            'route' => $route,
            'label' => $label,
            'active' => $active,
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="'.$iconPath.'"/></svg>',
        ];
    }
}
