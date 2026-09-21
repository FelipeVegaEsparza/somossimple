<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'business_id',
        'title',
        'description',
        'image_path',
        'starts_on',
        'ends_on',
        'active',
        'position',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'active' => 'boolean',
    ];

    public function isLive(?Carbon $today = null): bool
    {
        $today = $today ?? Carbon::today();

        if ($this->starts_on && $this->starts_on->greaterThan($today)) {
            return false;
        }

        if ($this->ends_on && $this->ends_on->lessThan($today)) {
            return false;
        }

        return true;
    }

    public function validityDisplay(): ?string
    {
        if (! $this->starts_on && ! $this->ends_on) {
            return null;
        }

        if ($this->starts_on && $this->ends_on) {
            return 'Del '.$this->starts_on->format('d/m').' al '.$this->ends_on->format('d/m');
        }

        if ($this->ends_on) {
            return 'Hasta el '.$this->ends_on->format('d/m');
        }

        return 'Desde el '.$this->starts_on->format('d/m');
    }
}
