<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Enlace social o botón personalizado del perfil de un negocio.
 */
class ProfileLink extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'kind',
        'network',
        'label',
        'url',
        'position',
    ];

    public function displayLabel(): string
    {
        if ($this->kind === 'button') {
            return $this->label;
        }

        return match ($this->network) {
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            default => ucfirst((string) $this->network),
        };
    }
}
