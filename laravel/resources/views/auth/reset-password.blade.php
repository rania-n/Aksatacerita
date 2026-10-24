<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Password baru — Admin Aksata Cerita</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<main class="login-page">
    <form class="login-card" method="post" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-brand />
        <div><small>PASSWORD BARU</small><h1>Buat baru.</h1></div>
        <label>Email <i class="req">*</i><input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username"></label>
        <label>Password baru <i class="req">*</i><input type="password" name="password" required minlength="8" autocomplete="new-password" autofocus></label>
        <label>Ulangi password baru <i class="req">*</i><input type="password" name="password_confirmation" required autocomplete="new-password"></label>
        @foreach (['email', 'password'] as $field)@error($field)<div class="error">{{ $message }}</div>@enderror @endforeach
        <button class="btn btn-primary" type="submit">Simpan password <x-icon name="arrow" /></button>
        <a class="back" href="{{ route('login') }}">← Kembali ke halaman masuk</a>
        <x-credit variant="login" />
    </form>
</main>
</body>
</html>
