<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Masuk — Admin Aksata Cerita</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<main class="login-page">
    <form class="login-card" method="post" action="{{ url('/login') }}">
        @csrf
        <x-brand />
        <div><small>ADMIN AREA</small><h1>Masuk.</h1></div>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"></label>
        <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
        @error('email')<div class="error">{{ $message }}</div>@enderror
        <label class="remember"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="btn btn-primary" type="submit">Masuk <x-icon name="arrow" /></button>
        <a class="back" href="{{ route('home') }}">← Kembali ke website</a>
    </form>
</main>
</body>
</html>
