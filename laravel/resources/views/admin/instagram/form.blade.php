@extends('layouts.admin')
@section('title', $editing ? 'Ubah Postingan' : 'Tambah Postingan')
@section('desc', 'Buka postingan di Instagram, ketuk titik tiga lalu "Salin link", kemudian tempel di sini.')
@section('content')
@php $e = $editing; @endphp
<a class="back-link" href="{{ route('admin.instagram.index') }}">← Kembali ke daftar</a>
<form class="panel admin-form admin-wide" method="post" action="{{ $e ? route('admin.instagram.update', $e) : route('admin.instagram.store') }}">
    @csrf @if ($e) @method('PUT') @endif
    <div class="panel-head"><div><span>{{ $e ? 'UBAH' : 'BARU' }}</span><h2>{{ $e ? 'Ubah postingan' : 'Tambah postingan' }}</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>
    <label>Link postingan <i class="req">*</i><input name="url" value="{{ old('url', $e->url ?? '') }}" required maxlength="300" placeholder="https://www.instagram.com/p/AbC123/"><span class="hint">Mendukung post dan reel dari akun publik.</span>@error('url')<span class="error">{{ $message }}</span>@enderror</label>
    <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>
    <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Tambah postingan' }} <x-icon name="arrow" /></button>
    <a class="btn-ghost" href="{{ route('admin.instagram.index') }}">Batal</a>
</form>
@endsection
