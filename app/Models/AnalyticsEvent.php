<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use BelongsToBusiness, HasFactory;

    public const TYPE_PROFILE_VISIT = 'profile_visit';

    public const TYPE_QR_SCAN = 'qr_scan';

    public const TYPE_NFC_TAP = 'nfc_tap';

    public const TYPE_CLICK_WHATSAPP = 'click_whatsapp';

    public const TYPE_CLICK_INSTAGRAM = 'click_instagram';

    public const TYPE_CLICK_PHONE = 'click_phone';

    public const TYPE_RESERVATION = 'reservation';

    protected $fillable = [
        'business_id',
        'type',
    ];
}
