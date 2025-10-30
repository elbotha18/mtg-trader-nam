<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
	$defaultDescription = 'Browse the latest Magic: The Gathering cards and sellers in Namibia. Search, filter, and connect with local players.';
	$metaDescription = $metaDescription ?? $defaultDescription;
	$ogImage = $ogImage ?? url('/imagine-games.webp');
	$ogTitle = $ogTitle ?? ($title ?? config('app.name'));
@endphp

<meta name="description" content="{{ $metaDescription }}">
<meta property="og:site_name" content="{{ $ogTitle }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<link rel="canonical" href="{{ url()->current() }}" />

{{-- JSON-LD Organization schema (fallback values - override with $og* variables) --}}
<script type="application/ld+json">
{!! json_encode([
	'@context' => 'https://schema.org',
	'@type' => 'Organization',
	'name' => $ogTitle,
	'url' => url('/'),
	'logo' => $ogImage,
	'description' => $metaDescription,
	],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
