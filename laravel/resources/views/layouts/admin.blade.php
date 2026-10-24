<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') — Admin Aksata Cerita</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $nav = [
        ['admin.dashboard', 'Overview', 'grid', 'admin.dashboard'],
        ['admin.packages.index', 'Paket & Harga', 'camera', 'admin.packages.*'],
        ['admin.frames.index', 'Katalog Frame', 'sparkle', 'admin.frames.*'],
        ['admin.gallery.index', 'Galeri Momen', 'grid', 'admin.gallery.*'],
        ['admin.instagram.index', 'Instagram', 'camera', 'admin.instagram.*'],
        ['admin.settings.edit', 'Info Studio', 'map', 'admin.settings.*'],
        ['admin.account.edit', 'Akun', 'user', 'admin.account.*'],
    ];
@endphp
<div class="admin">
    <aside>
        <x-brand :light="true" />
        <div class="admin-studio"><span>A</span><div><b>Aksatacerita</b><small>Studio workspace</small></div></div>
        <nav>
            @foreach ($nav as [$route, $label, $icon, $pattern])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}"><x-icon :name="$icon" />{{ $label }}</a>
            @endforeach
        </nav>
        <a class="back-site" href="{{ route('home') }}" target="_blank">← Lihat website</a>
    </aside>
    <div class="admin-main">
        <header>
            <div><small>CONTENT MANAGER</small><h1>@yield('title')</h1></div>
            <div class="admin-user">
                <span>{{ auth()->user()->email }}</span>
                <form method="post" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
                <b>{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</b>
            </div>
        </header>
        @if (session('ok'))<div class="toast">✓ {{ session('ok') }}</div>@endif
        @if ($errors->any())<div class="flash err">Periksa lagi isian form di bawah.</div>@endif
        <div class="admin-content">@yield('content')</div>
    </div>
</div>
</body>
</html>
