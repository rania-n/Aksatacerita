<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Aksata Cerita — Photobooth Studio Tulungagung')</title>
    <meta name="description" content="Aksata Cerita, photobooth studio di Tulungagung. Pilih paket, pilih frame, abadikan ceritamu.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
@yield('content')
<script src="{{ asset('js/site.js') }}" defer></script>
</body>
</html>
