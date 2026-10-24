<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — Admin Aksata Cerita</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $nav = [
        ['admin.dashboard', 'Overview', 'Beranda', 'grid', 'admin.dashboard'],
        ['admin.packages.index', 'Paket & Harga', 'Paket', 'camera', 'admin.packages.*'],
        ['admin.frames.index', 'Katalog Frame', 'Frame', 'sparkle', 'admin.frames.*'],
        ['admin.gallery.index', 'Galeri Momen', 'Galeri', 'grid', 'admin.gallery.*'],
        ['admin.instagram.index', 'Instagram', 'IG', 'camera', 'admin.instagram.*'],
        ['admin.settings.edit', 'Pengaturan', 'Atur', 'sliders', 'admin.settings.*'],
        ['admin.account.edit', 'Akun', 'Akun', 'user', 'admin.account.*'],
    ];
@endphp
<div class="admin" id="admin">
    <aside>
        <x-brand :light="true" />
        <button class="rail-toggle" type="button" aria-label="Tampilkan nama menu" aria-expanded="false"><x-icon name="menu" /></button>
        <div class="admin-studio"><span>A</span><div><b>Aksatacerita</b><small>Studio workspace</small></div></div>
        <nav aria-label="Menu admin">
            @foreach ($nav as [$route, $label, $short, $icon, $pattern])
                <a href="{{ route($route) }}" title="{{ $label }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}" @if (request()->routeIs($pattern)) aria-current="page" @endif><x-icon :name="$icon" /><span class="nav-label">{{ $label }}</span><span class="nav-short">{{ $short }}</span></a>
            @endforeach
        </nav>
        <a class="back-site" href="{{ route('home') }}" target="_blank" title="Lihat website"><x-icon name="arrow" :size="16" /><span class="nav-label">Lihat website</span></a>
        <x-credit variant="admin" />
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
        <div class="admin-content">
            @hasSection('desc')<p class="page-desc">@yield('desc')</p>@endif
            @yield('content')
            <x-credit variant="admin-foot" />
        </div>
    </div>
</div>
<script src="{{ asset('js/admin-form.js') }}" defer></script>
</body>
</html>
