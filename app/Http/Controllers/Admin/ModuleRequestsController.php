<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleAccess;
use App\Models\ModuleActivationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleRequestsController extends Controller
{
    public function index(): View
    {
        $pending = ModuleActivationRequest::where('status', 'pending')
            ->with('business.account')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.modulerequests.index', [
            'pending' => $pending,
        ]);
    }

    public function approve(ModuleActivationRequest $modulerequest): RedirectResponse
    {
        $moduleEnum = \App\Enums\Module::tryFrom($modulerequest->module);

        if ($moduleEnum === null || $moduleEnum->isFree()) {
            $modulerequest->delete();

            return back()->with('error', 'Solicitud inválida eliminada.');
        }

        $access = ModuleAccess::ofBusiness($modulerequest->business)->firstOrCreate(
            ['business_id' => $modulerequest->business_id, 'module' => $modulerequest->module],
            ['active' => false],
        );

        if ($modulerequest->action === ModuleActivationRequest::ACTION_ACTIVATE) {
            $access->activate();
        } else {
            $access->deactivate();
        }

        $modulerequest->delete();

        $verbo = $modulerequest->action === ModuleActivationRequest::ACTION_ACTIVATE ? 'activado' : 'desactivado';

        return back()->with('status', 'Módulo '.$moduleEnum->label().' '.$verbo.' para «'.$modulerequest->business->name.'».');
    }

    public function decline(ModuleActivationRequest $modulerequest): RedirectResponse
    {
        $modulerequest->delete();

        return back()->with('status', 'Solicitud rechazada.');
    }
}
