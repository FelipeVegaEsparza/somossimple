<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de primer nivel: activación de un módulo para un negocio.
 * Desactivar/expirar un módulo cambia este registro, nunca elimina datos.
 */
class ModuleAccess extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'module_access';

    protected $fillable = [
        'business_id',
        'module',
        'active',
        'activated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'activated_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function activate(): self
    {
        $this->update([
            'active' => true,
            'activated_at' => $this->activated_at ?? now(),
        ]);

        return $this;
    }

    public function deactivate(): self
    {
        $this->update(['active' => false]);

        return $this;
    }
}
