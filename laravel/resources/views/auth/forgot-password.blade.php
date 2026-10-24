<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Lupa password — Admin Aksata Cerita</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<main class="login-page">
    <form class="login-card" method="post" action="{{ route('password.email') }}">
        @csrf
        <x-brand />
        <div><small>LUPA PASSWORD</small><h1>Kirim link.</h1></div>
        <p class="login-note">Masukkan email admin. Kami kirim link untuk membuat password baru.</p>
        @if (session('status'))<div class="login-status">{{ session('status') }}</div>@endif
        <label>Email <i class="req">*</i><input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></label>
        @error('email')<div class="error">{{ $message }}</div>@enderror
        <button class="btn btn-primary" type="submit">Kirim link <x-icon name="arrow" /></button>
        <a class="back" href="{{ route('login') }}">← Kembali ke halaman masuk</a>
        <x-credit variant="login" />
    </form>
</main>
</body>
</html>
