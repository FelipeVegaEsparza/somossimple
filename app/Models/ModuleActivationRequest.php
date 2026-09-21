<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Solicitud del dueño de un negocio para activar o desactivar un módulo de
 * pago. El administrador la aprueba o rechaza desde su área.
 */
class ModuleActivationRequest extends Model
{
    use BelongsToBusiness, HasFactory;

    public const ACTION_ACTIVATE = 'activate';

    public const ACTION_DEACTIVATE = 'deactivate';

    protected $fillable = [
        'business_id',
        'module',
        'action',
        'status',
    ];
}
