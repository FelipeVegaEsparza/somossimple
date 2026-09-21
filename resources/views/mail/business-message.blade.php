<!DOCTYPE html>
<html lang="es">
<body style="margin:0;background:#f5f6fa;font-family:Inter,Arial,sans-serif;">
    <div style="max-width:560px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border:1px solid #e4e7ec;border-radius:12px;padding:32px;">
            <p style="margin:0 0 16px;font-size:13px;color:#5b6472;font-weight:600;">{{ $businessName }}</p>
            <div style="font-size:15px;line-height:1.6;color:#1a2233;">{!! $body !!}</div>
        </div>
        <p style="margin:16px 0 0;font-size:12px;color:#98a1b3;text-align:center;">Recibiste este mensaje porque aceptaste recibir comunicaciones de {{ $businessName }}.</p>
    </div>
</body>
</html>
