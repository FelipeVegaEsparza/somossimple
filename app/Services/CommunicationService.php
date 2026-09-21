<?php

namespace App\Services;

use App\Mail\BusinessMessage;
use App\Models\Business;
use App\Models\Client;
use App\Models\Communication;
use App\Models\CommunicationSend;
use Illuminate\Support\Facades\Mail;

/**
 * Comportamiento funcional de las comunicaciones. El envío concreto queda
 * desacoplado: en el MVP se envía por el mailer configurado (infraestructura
 * fuera de la definición funcional).
 */
class CommunicationService
{
    public function resolveRecipients(Communication $communication)
    {
        $query = Client::ofBusiness($communication->business)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($communication->audience === Communication::AUDIENCE_TAG && $communication->tag_name) {
            $query->whereHas('tags', fn ($q) => $q->where('client_tags.name', $communication->tag_name));
        }

        if ($communication->is_commercial) {
            $query->whereHas('consents', fn ($q) => $q
                ->where('channel', 'email')
                ->where('granted', true));
        }

        return $query->get();
    }

    public function send(Communication $communication): int
    {
        if ($communication->status === Communication::STATUS_SENT) {
            return 0;
        }

        $business = $communication->business;
        $recipients = $this->resolveRecipients($communication);
        $count = 0;

        foreach ($recipients as $client) {
            Mail::to($client->email)->send(new BusinessMessage($business->name, $communication->subject, $communication->body));

            CommunicationSend::create([
                'communication_id' => $communication->id,
                'client_id' => $client->id,
                'email' => $client->email,
                'status' => 'sent',
            ]);

            $count++;
        }

        $communication->update([
            'status' => Communication::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return $count;
    }
}
