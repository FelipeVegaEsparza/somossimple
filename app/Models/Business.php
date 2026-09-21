<?php

namespace App\Models;

use App\Enums\Module;
use App\Enums\ProfileTheme;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * El negocio es la raíz de todos los datos del producto.
 */
class Business extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'is_paused',
        'theme',
        'description',
        'logo_path',
        'cover_path',
        'phone',
        'whatsapp',
        'contact_email',
        'website',
        'address',
        'map_url',
        'opening_hours',
    ];

    protected $casts = [
        'is_paused' => 'boolean',
    ];

    /**
     * Crea el negocio de una cuenta (una cuenta = un negocio en el MVP) y
     * deja su registro de activación de módulos inicial: perfil gratuito
     * activo, módulos de pago inactivos.
     */
    public static function createForAccount($account, string $name): self
    {
        if (self::where('account_id', $account->id)->exists()) {
            throw new \RuntimeException('La cuenta ya tiene un negocio asociado.');
        }

        $business = self::create([
            'account_id' => $account->id,
            'name' => $name,
            'slug' => self::uniqueSlug($name),
        ]);

        foreach (Module::cases() as $module) {
            ModuleAccess::create([
                'business_id' => $business->id,
                'module' => $module->value,
                'active' => $module->isFree(),
            ]);
        }

        return $business;
    }

    private static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name, '-', 'es');

        if ($base === '') {
            $base = 'negocio';
        }

        if (self::isSlugReserved($base)) {
            $base = 'negocio';
        }

        $slug = $base;

        while (! self::slugAvailable($slug)) {
            $slug = $base.'-'.Str::lower(Str::random(5));
        }

        return $slug;
    }

    /**
     * Slugs reservados para rutas propias de la plataforma. Al vivir el perfil
     * en la raíz (/{slug}), un negocio no puede ocupar estos nombres.
     *
     * @return list<string>
     */
    public static function reservedSlugs(): array
    {
        return [
            'p', 'k', 'kit', 'kits', 'login', 'register', 'registro', 'logout',
            'forgot-password', 'reset-password', 'password', 'email', 'verify',
            'admin', 'panel', 'dashboard', 'settings', 'cuenta', 'perfil',
            'negocios', 'clientes', 'reservas', 'modulos', 'precios', 'pagos',
            'qr', 'api', 'storage', 'assets', 'build', 'up', 'home', 'reservar',
            'click', 'buscar', 'explorar', 'ayuda', 'soporte', 'terminos', 'privacidad',
            'fidelizacion', 'ticketera', 'evento', 'eventos', 'entrada', 'entradas', 'acceso',
        ];
    }

    public static function isSlugReserved(string $slug): bool
    {
        return in_array(Str::lower($slug), self::reservedSlugs(), true);
    }

    public static function slugAvailable(string $slug, ?int $ignoreBusinessId = null): bool
    {
        if (self::isSlugReserved($slug)) {
            return false;
        }

        $query = self::where('slug', $slug);

        if ($ignoreBusinessId !== null) {
            $query->where('id', '!=', $ignoreBusinessId);
        }

        if ($query->exists()) {
            return false;
        }

        return ! SlugAlias::where('old_slug', $slug)->exists();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_id');
    }

    public function moduleAccess(): HasMany
    {
        return $this->hasMany(ModuleAccess::class);
    }

    public function profileLinks(): HasMany
    {
        return $this->hasMany(ProfileLink::class)->orderBy('position');
    }

    public function catalogCategories(): HasMany
    {
        return $this->hasMany(CatalogCategory::class)->orderBy('position');
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class)->orderBy('position');
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->orderBy('position');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('position');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderBy('position');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class)->orderBy('position');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->orderBy('position');
    }

    public function loyaltyProgram(): HasOne
    {
        return $this->hasOne(LoyaltyProgram::class);
    }

    public function loyaltyMembers(): HasMany
    {
        return $this->hasMany(LoyaltyMember::class);
    }

    public function loyaltyRewards(): HasMany
    {
        return $this->hasMany(LoyaltyReward::class)->orderBy('position');
    }

    public function loyaltyActivities(): HasMany
    {
        return $this->hasMany(LoyaltyActivity::class)->latest();
    }

    public function ticketeraEvents(): HasMany
    {
        return $this->hasMany(TicketeraEvent::class)->orderBy('position');
    }

    public function ticketeraOrders(): HasMany
    {
        return $this->hasMany(TicketeraOrder::class);
    }

    public function ticketeraTickets(): HasMany
    {
        return $this->hasMany(TicketeraTicket::class);
    }

    public function ticketeraStaff(): HasMany
    {
        return $this->hasMany(TicketeraStaff::class);
    }

    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class)->orderBy('position');
    }

    public function bookingWeekHours(): HasMany
    {
        return $this->hasMany(BookingWeekHour::class);
    }

    public function bookingDayOffs(): HasMany
    {
        return $this->hasMany(BookingDayOff::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function clientTags(): HasMany
    {
        return $this->hasMany(ClientTag::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(BusinessGallery::class)->orderBy('position');
    }

    public function socialLinks()
    {
        return $this->profileLinks()->where('kind', 'social');
    }

    public function customButtons()
    {
        return $this->profileLinks()->where('kind', 'button');
    }

    public function isModuleActive(Module $module): bool
    {
        $access = $this->moduleAccess->firstWhere('module', $module->value);

        return $access?->active ?? false;
    }

    public function profileTheme(): ProfileTheme
    {
        return ProfileTheme::tryFrom($this->theme ?? '') ?? ProfileTheme::Clasico;
    }

    public function isPubliclyAvailable(): bool
    {
        return ! $this->is_paused;
    }
}
