<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Communication extends Model
{
    use BelongsToBusiness, HasFactory;

    public const TYPES = ['promo', 'oferta', 'anuncio', 'noticia', 'cambio_horario', 'general'];

    public static function typeOptions(): array
    {
        return [
            'promo' => 'Promoción',
            'oferta' => 'Oferta',
            'anuncio' => 'Anuncio',
            'noticia' => 'Noticia',
            'cambio_horario' => 'Cambio de horario',
            'general' => 'General',
        ];
    }

    public const AUDIENCE_ALL = 'all';

    public const AUDIENCE_TAG = 'tag';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    protected $fillable = [
        'business_id',
        'type',
        'is_commercial',
        'subject',
        'body',
        'audience',
        'tag_name',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'is_commercial' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function sends(): HasMany
    {
        return $this->hasMany(CommunicationSend::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'promo' => 'Promoción',
            'oferta' => 'Oferta',
            'anuncio' => 'Anuncio',
            'noticia' => 'Noticia',
            'cambio_horario' => 'Cambio de horario',
            default => 'General',
        };
    }
}
