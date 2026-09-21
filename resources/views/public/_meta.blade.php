@php
    $metaTitle = $title ?? $business->name;
    $metaDescription = $description ?? ($business->description ?: 'Perfil digital de '.$business->name);
    $metaUrl = $url ?? url()->current();

    if (! empty($image)) {
        $metaImage = $image;
    } elseif ($business->cover_path) {
        $metaImage = asset('storage/'.$business->cover_path);
    } elseif ($business->logo_path) {
        $metaImage = asset('storage/'.$business->logo_path);
    } else {
        $metaImage = route('p.icon', [$business->slug, 512], true);
    }

    $metaDescription = \Illuminate\Support\Str::limit(strip_tags($metaDescription), 160);
@endphp

<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $metaUrl }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $business->name }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:alt" content="{{ $metaTitle }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
