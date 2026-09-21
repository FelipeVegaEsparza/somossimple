<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Convierte el perfil público de cada negocio en una PWA instalable:
 * manifiesto, service worker con scope propio e íconos generados.
 */
class PwaController extends Controller
{
    private const ICON_SIZES = [180, 192, 512];

    public function manifest(string $slug): Response
    {
        $business = $this->business($slug);
        $theme = $business->profileTheme()->preview();
        $startUrl = '/'.$business->slug;

        $manifest = [
            'id' => $startUrl,
            'name' => $business->name,
            'short_name' => Str::limit($business->name, 12, ''),
            'description' => $business->description ?: 'Perfil digital de '.$business->name,
            'lang' => 'es',
            'start_url' => route('p.show', $business->slug, false).'?source=pwa',
            'scope' => $startUrl,
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => $theme['app'],
            'theme_color' => $theme['primary'],
            'icons' => [
                ['src' => route('p.icon', [$business->slug, 192], false), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => route('p.icon', [$business->slug, 512], false), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => route('p.icon', [$business->slug, 512], false), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ];

        return response()->json($manifest, 200, ['Content-Type' => 'application/manifest+json']);
    }

    public function serviceWorker(string $slug): Response
    {
        $business = $this->business($slug);
        $scope = '/'.$business->slug;

        $assets = [];
        foreach (['resources/css/app.css', 'resources/js/app.js'] as $entry) {
            try {
                $assets[] = Vite::asset($entry);
            } catch (\Throwable) {
                // Sin build disponible: el service worker cacheará al vuelo.
            }
        }

        $js = view('public.sw', [
            'cache' => 'somossimple-'.$business->id.'-v1',
            'scope' => $scope,
            'start' => route('p.show', $business->slug, false),
            'assets' => $assets,
        ])->render();

        return response($js, 200, [
            'Content-Type' => 'application/javascript',
            'Service-Worker-Allowed' => $scope,
        ]);
    }

    public function icon(string $slug, int $size): Response
    {
        abort_unless(in_array($size, self::ICON_SIZES, true), 404);

        $business = $this->business($slug);
        $theme = $business->profileTheme()->preview();

        $image = imagecreatetruecolor($size, $size);

        $logo = $this->loadLogo($business);

        if ($logo !== null) {
            $this->fill($image, '#ffffff');
            $this->drawContained($image, $logo, $size);
            imagedestroy($logo);
        } else {
            $this->fill($image, $theme['strong'] ?? '#1a2233');
            $this->drawInitial($image, $business->name, $size);
        }

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function business(string $slug): Business
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        if (! $business->isPubliclyAvailable()) {
            abort(404);
        }

        return $business;
    }

    private function loadLogo(Business $business): ?\GdImage
    {
        if (! $business->logo_path || ! Storage::disk('public')->exists($business->logo_path)) {
            return null;
        }

        $image = @imagecreatefromstring(Storage::disk('public')->get($business->logo_path));

        return $image ?: null;
    }

    private function fill(\GdImage $image, string $hex): void
    {
        $hex = ltrim($hex, '#');
        $color = imagecolorallocate(
            $image,
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        );

        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $color);
    }

    private function drawContained(\GdImage $image, \GdImage $logo, int $size): void
    {
        $logoWidth = imagesx($logo);
        $logoHeight = imagesy($logo);
        $padding = (int) ($size * 0.18);
        $max = $size - ($padding * 2);
        $scale = min($max / $logoWidth, $max / $logoHeight);
        $width = max(1, (int) ($logoWidth * $scale));
        $height = max(1, (int) ($logoHeight * $scale));
        $x = (int) (($size - $width) / 2);
        $y = (int) (($size - $height) / 2);

        imagecopyresampled($image, $logo, $x, $y, 0, 0, $width, $height, $logoWidth, $logoHeight);
    }

    private function drawInitial(\GdImage $image, string $name, int $size): void
    {
        $font = resource_path('fonts/DejaVuSans-Bold.ttf');

        if (! file_exists($font)) {
            return;
        }

        $letter = mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
        $fontSize = (int) ($size * 0.46);
        $white = imagecolorallocate($image, 255, 255, 255);
        $box = imagettfbbox($fontSize, 0, $font, $letter);
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = (int) (($size - $textWidth) / 2 - $box[0]);
        $y = (int) (($size - $textHeight) / 2 - $box[7]);

        imagettftext($image, $fontSize, 0, $x, $y, $white, $font, $letter);
    }
}
