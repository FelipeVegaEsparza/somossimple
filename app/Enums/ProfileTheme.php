<?php

namespace App\Enums;

enum ProfileTheme: string
{
    case Clasico = 'clasico';
    case Nocturno = 'nocturno';
    case Calido = 'calido';
    case Minimal = 'minimal';
    case Esmeralda = 'esmeralda';
    case Violeta = 'violeta';
    case Oceano = 'oceano';
    case Elegante = 'elegante';

    public function label(): string
    {
        return match ($this) {
            self::Clasico => 'Clásico',
            self::Nocturno => 'Nocturno',
            self::Calido => 'Cálido',
            self::Minimal => 'Minimal',
            self::Esmeralda => 'Esmeralda',
            self::Violeta => 'Violeta',
            self::Oceano => 'Océano',
            self::Elegante => 'Elegante',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Clasico => 'Azul, claro y ordenado.',
            self::Nocturno => 'Portada oscura con datos sobre la foto.',
            self::Calido => 'Tonos crema y terracota.',
            self::Minimal => 'Blanco y negro, centrado y limpio.',
            self::Esmeralda => 'Verde natural con portada en bloque.',
            self::Violeta => 'Morado moderno con portada oscura.',
            self::Oceano => 'Azul marino, fresco y amplio.',
            self::Elegante => 'Negro y dorado, centrado y clásico.',
        };
    }

    /**
     * Variante de estructura del encabezado del perfil.
     *
     * cover   → portada con logo superpuesto (clásico)
     * overlay → portada oscura con el nombre sobre la imagen
     * central → sin portada, logo y nombre centrados
     * hero    → bloque de color con logo y nombre
     */
    public function layout(): string
    {
        return match ($this) {
            self::Clasico, self::Oceano => 'cover',
            self::Nocturno, self::Violeta => 'overlay',
            self::Calido, self::Esmeralda => 'hero',
            self::Minimal, self::Elegante => 'central',
        };
    }

    /**
     * Colores para la mini-previsualización del selector en el panel y para
     * generar el manifiesto/íconos PWA.
     *
     * @return array{app: string, surface: string, primary: string, ink: string, strong: string}
     */
    public function preview(): array
    {
        return match ($this) {
            self::Clasico => ['app' => '#f5f6fa', 'surface' => '#ffffff', 'primary' => '#437eff', 'ink' => '#1a2233', 'strong' => '#1a2233'],
            self::Nocturno => ['app' => '#0b0d12', 'surface' => '#161922', 'primary' => '#7aa2ff', 'ink' => '#f3f5f9', 'strong' => '#05070b'],
            self::Calido => ['app' => '#faf4ee', 'surface' => '#fffdfb', 'primary' => '#d9694a', 'ink' => '#2b211b', 'strong' => '#3a2c25'],
            self::Minimal => ['app' => '#ffffff', 'surface' => '#ffffff', 'primary' => '#111110', 'ink' => '#111110', 'strong' => '#111110'],
            self::Esmeralda => ['app' => '#f3f8f5', 'surface' => '#ffffff', 'primary' => '#18855c', 'ink' => '#14231c', 'strong' => '#0f3d2c'],
            self::Violeta => ['app' => '#f7f5fc', 'surface' => '#ffffff', 'primary' => '#6d4ae0', 'ink' => '#241b3a', 'strong' => '#2a1f4d'],
            self::Oceano => ['app' => '#f2f8fa', 'surface' => '#ffffff', 'primary' => '#0e8aa8', 'ink' => '#10232b', 'strong' => '#0b2b34'],
            self::Elegante => ['app' => '#f6f4f0', 'surface' => '#fffefc', 'primary' => '#b0894a', 'ink' => '#1c1a17', 'strong' => '#1c1a17'],
        };
    }
}
