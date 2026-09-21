<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketeraStaff extends Model
{
    use BelongsToBusiness, HasFactory;

    public const ROLE_ACCESS = 'access';

    protected $table = 'ticketera_staff';

    protected $fillable = [
        'business_id',
        'name',
        'email',
        'token',
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
