@php($pwaTheme = $business->profileTheme()->preview())
@php($pwaIconVersion = substr(md5(($business->logo_path ?? '').'|'.($business->updated_at?->timestamp ?? 0)), 0, 8))

<link rel="manifest" href="{{ route('p.manifest', $business->slug) }}">
<meta name="theme-color" content="{{ $pwaTheme['primary'] }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $business->name }}">
<link rel="apple-touch-icon" href="{{ route('p.icon', [$business->slug, 180]) }}?v={{ $pwaIconVersion }}">

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker
                .register(@js(route('p.sw', $business->slug)), { scope: @js('/'.$business->slug) })
                .catch(function () {});
        });
    }
</script>
