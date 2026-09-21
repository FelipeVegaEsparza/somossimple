<?php

namespace App\Models;

use App\Enums\TicketeraEventStatus;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TicketeraEvent extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'ticketera_events';

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'description',
        'image_path',
        'category',
        'organizer',
        'contact_phone',
        'contact_email',
        'starts_at',
        'ends_at',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_info',
        'status',
        'commission_rate',
        'position',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => TicketeraEventStatus::class,
        'commission_rate' => 'decimal:2',
        'position' => 'integer',
    ];

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketeraTicketType::class, 'event_id')->orderBy('position');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(TicketeraOrder::class, 'event_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(TicketeraTicket::class, 'event_id');
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(TicketeraAccess::class, 'event_id');
    }

    public function status(): TicketeraEventStatus
    {
        return $this->status instanceof TicketeraEventStatus ? $this->status : TicketeraEventStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status() === TicketeraEventStatus::Published;
    }

    public function isCancelled(): bool
    {
        return $this->status() === TicketeraEventStatus::Cancelled;
    }

    public function publicPath(): string
    {
        return '/evento/'.$this->slug;
    }

    public function soldTickets(): int
    {
        return (int) $this->tickets()->whereIn('status', ['issued', 'used'])->count();
    }

    public function availableTickets(): int
    {
        $stock = (int) $this->ticketTypes()->sum('stock');
        $sold = (int) $this->ticketTypes()->sum('sold');

        return max(0, $stock - $sold);
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name, '-', 'es') ?: 'evento';

        if (in_array($base, ['evento', 'entrada', 'ticketera', 'acceso'], true)) {
            $base .= '-evento';
        }

        $slug = $base;

        while (self::where('slug', $slug)->exists()) {
            $slug = $base.'-'.Str::lower(Str::random(4));
        }

        return $slug;
    }
}
