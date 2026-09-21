<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Configuración simple de la plataforma (clave -> valor). Guarda por ahora el
 * precio unitario del kit físico.
 */
class Setting extends Model
{
    use HasFactory;

    public const KIT_PRICE = 'kit_unit_price';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key): ?string
    {
        return self::where('key', $key)->value('value');
    }

    public static function set(string $key, ?string $value): void
    {
        if ($value === null || $value === '') {
            self::where('key', $key)->delete();

            return;
        }

        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getInt(string $key): ?int
    {
        $value = self::get($key);

        return $value === null ? null : (int) $value;
    }
}
