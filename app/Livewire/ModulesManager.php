<?php

namespace App\Livewire;

use App\Enums\Module;
use App\Models\ModuleAccess;
use App\Models\ModuleActivationRequest;
use Illuminate\View\View;
use Livewire\Component;

class ModulesManager extends Component
{
    public bool $hasBusiness = false;

    public string $notice = '';

    /** @var list<array{key: string, label: string, description: string, active: bool, free: bool, pending: bool}> */
    public array $modules = [];

    public function mount(): void
    {
        $business = auth()->user()->business;

        if (! $business) {
            return;
        }

        $this->hasBusiness = true;
        $this->loadModules($business->id);
    }

    public function solicitar(string $module, string $action): void
    {
        $business = auth()->user()->business;

        if (! $business || ! in_array($action, [ModuleActivationRequest::ACTION_ACTIVATE, ModuleActivationRequest::ACTION_DEACTIVATE], true)) {
            return;
        }

        $moduleEnum = Module::from($module);

        if ($moduleEnum->isFree()) {
            $this->notice = 'El Perfil Digital es gratuito y siempre está activo.';
            $this->loadModules($business->id);

            return;
        }

        $pending = ModuleActivationRequest::ofBusiness($business)->where('module', $module)->exists();

        if ($pending) {
            $this->notice = 'Ya hay una solicitud pendiente para el módulo '.$moduleEnum->label().'.';
            $this->loadModules($business->id);

            return;
        }

        ModuleActivationRequest::create([
            'business_id' => $business->id,
            'module' => $module,
            'action' => $action,
            'status' => 'pending',
        ]);

        $verbo = $action === ModuleActivationRequest::ACTION_ACTIVATE ? 'activar' : 'desactivar';
        $this->notice = 'Solicitud enviada para '.$verbo.' el módulo '.$moduleEnum->label().'. Te contactaremos.';

        $this->loadModules($business->id);
    }

    private function loadModules(int $businessId): void
    {
        $labels = [
            Module::Profile->value => 'Tu página pública y el punto de entrada al producto.',
            Module::Catalog->value => 'Productos, servicios o menú, con precio opcional.',
            Module::Menu->value => 'Tu carta digital con categorías, precios y platos destacados.',
            Module::Services->value => 'Tus servicios con descripción, precio y duración.',
            Module::Promotions->value => 'Ofertas y descuentos visibles en tu perfil y puntos QR/NFC.',
            Module::Events->value => 'Eventos, cursos y actividades con fecha y lugar.',
            Module::Gallery->value => 'Tu negocio en imágenes para mostrar antes de decidir.',
            Module::Reservations->value => 'Agenda simple para que tus clientes pidan hora.',
            Module::Clients->value => 'Tu base de clientes, etiquetas e historial.',
            Module::Communications->value => 'Envía comunicaciones por correo a tus clientes.',
            Module::Loyalty->value => 'Puntos, visitas o sellos con tarjeta digital y recompensas.',
            Module::Ticketera->value => 'Crea eventos, vende entradas online y valida el acceso con QR.',
        ];

        $pendingModules = ModuleActivationRequest::where('business_id', $businessId)->pluck('module');

        $this->modules = collect(Module::cases())->map(function (Module $module) use ($businessId, $labels, $pendingModules) {
            $row = ModuleAccess::where('business_id', $businessId)
                ->where('module', $module->value)
                ->first();

            return [
                'key' => $module->value,
                'label' => $module->label(),
                'description' => $labels[$module->value],
                'active' => $row?->active ?? false,
                'free' => $module->isFree(),
                'pending' => $pendingModules->contains($module->value),
            ];
        })->values()->all();
    }

    public function render(): View
    {
        return view('livewire.modules-manager');
    }
}
