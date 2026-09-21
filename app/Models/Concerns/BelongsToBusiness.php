<?php

namespace App\Models\Concerns;

use App\Models\Business;

/**
 * Para los modelos que pertenecen a un negocio (raíz de datos del producto).
 * Asegura que toda consulta se pueda acotar a un negocio y nunca mezcle datos
 * entre negocios.
 */
trait BelongsToBusiness
{
    public function scopeOfBusiness($query, Business $business)
    {
        return $query->where($this->getTable().'.business_id', $business->id);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
