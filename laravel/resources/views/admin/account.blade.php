@extends('layouts.admin')
@section('title', 'Akun')
@section('content')
<form class="panel admin-form admin-wide" method="post" action="{{ route('admin.account.update') }}" autocomplete="off">
    @csrf @method('PUT')
    <div class="panel-head"><div><span>KEAMANAN</span><h2>Akun admin</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>
    <label>Nama<input name="name" value="{{ old('name', auth()->user()->name) }}" required maxlength="60">@error('name')<span class="error">{{ $message }}</span>@enderror</label>
    <label>Email login<input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required maxlength="100" autocomplete="username">@error('email')<span class="error">{{ $message }}</span>@enderror</label>
    <label>Password baru<input type="password" name="password" minlength="8" autocomplete="new-password"><span class="hint">Kosongkan bila tidak ingin mengganti password. Minimal 8 karakter.</span>@error('password')<span class="error">{{ $message }}</span>@enderror</label>
    <label>Ulangi password baru<input type="password" name="password_confirmation" autocomplete="new-password"></label>
    <label>Password saat ini (untuk konfirmasi) <i class="req">*</i><input type="password" name="current_password" required autocomplete="current-password">@error('current_password')<span class="error">{{ $message }}</span>@enderror</label>
    <button class="btn btn-primary" type="submit">Simpan akun <x-icon name="arrow" /></button>
</form>
@endsection
