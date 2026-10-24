@extends('layouts.admin')
@section('title', 'Akun')
@section('desc', 'Data login admin. Untuk mengganti password, kami kirim link aman ke email Anda.')
@section('content')
<div class="stack admin-wide">
    <section class="panel admin-form">
        <div class="panel-head"><div><span>KEAMANAN</span><h2>Ganti password</h2></div></div>
        <p class="login-note">Klik tombol di bawah. Link untuk membuat password baru dikirim ke <b>{{ auth()->user()->email }}</b> dan berlaku 60 menit.</p>
        <form method="post" action="{{ route('admin.account.password-link') }}">@csrf<button class="btn btn-primary" type="submit"><x-icon name="arrow" /> Kirim link ke email saya</button></form>
        <p class="hint">Email tidak masuk? Cek folder spam, atau gunakan cara manual di bagian bawah halaman ini.</p>
    </section>

    <form class="panel admin-form" method="post" action="{{ route('admin.account.update') }}" autocomplete="off">
        @csrf @method('PUT')
        <div class="panel-head"><div><span>PROFIL</span><h2>Nama &amp; email login</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>
        <label>Nama <i class="req">*</i><input name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="60">@error('name')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Email login <i class="req">*</i><input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="100" autocomplete="username"><span class="hint">Link ganti password dikirim ke email ini, jadi pastikan email aktif.</span>@error('email')<span class="error">{{ $message }}</span>@enderror</label>

        <details @if ($errors->has('password')) open @endif>
            <summary>Ganti password manual (tanpa email)</summary>
            <label>Password baru<input type="password" name="password" minlength="8" autocomplete="new-password"><span class="hint">Minimal 8 karakter. Kosongkan bila tidak ingin mengganti.</span>@error('password')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Ulangi password baru<input type="password" name="password_confirmation" autocomplete="new-password"></label>
        </details>

        <label>Password saat ini (untuk konfirmasi) <i class="req">*</i><input type="password" name="current_password" required autocomplete="current-password">@error('current_password')<span class="error">{{ $message }}</span>@enderror</label>
        <button class="btn btn-primary" type="submit">Simpan <x-icon name="arrow" /></button>
    </form>
</div>
@endsection
