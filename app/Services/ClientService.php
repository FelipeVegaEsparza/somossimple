<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Client;
use App\Models\ClientConsent;

/**
 * Registro y dedupe de clientes por teléfono normalizado (+56), dentro de un
 * mismo negocio. La coincidencia es una señal, no una identidad absoluta.
 */
class ClientService
{
    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '56') && strlen($digits) > 9) {
            return $digits;
        }

        $digits = ltrim($digits, '0');

        if (strlen($digits) === 9) {
            return '56'.$digits;
        }

        return $digits;
    }

    public function resolve(Business $business, string $name, ?string $phone, ?string $email = null): Client
    {
        $normalized = self::normalizePhone($phone);

        $client = $normalized
            ? Client::ofBusiness($business)->where('phone_normalized', $normalized)->first()
            : null;

        if ($client === null) {
            $client = Client::create([
                'business_id' => $business->id,
                'name' => $name,
                'phone' => $phone,
                'phone_normalized' => $normalized,
                'email' => $email,
            ]);

            $this->ensureConsent($client, ClientConsent::CHANNEL_EMAIL, false, null);
        } else {
            $updates = [];

            if ($email !== null && $email !== '' && $client->email === null) {
                $updates['email'] = $email;
            }

            if (! empty($updates)) {
                $client->update($updates);
            }

            $this->ensureConsent($client, ClientConsent::CHANNEL_EMAIL, false, null);
        }

        return $client;
    }

    public function ensureConsent(Client $client, string $channel, bool $granted, ?string $source): ClientConsent
    {
        $consent = ClientConsent::firstOrNew([
            'client_id' => $client->id,
            'channel' => $channel,
        ]);

        $consent->granted = $granted;
        $consent->source = $source;
        $consent->save();

        return $consent;
    }
}
