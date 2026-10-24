@extends('layouts.admin')
@section('title', 'Instagram')
@section('content')
@php $e = $editing; @endphp
<div class="admin-grid">
    <section class="panel">
        <div class="panel-head"><div><span>FEED</span><h2>Postingan Instagram</h2></div><a class="icon-button" href="{{ route('admin.instagram.index') }}" aria-label="Tambah postingan"><x-icon name="plus" /></a></div>
        @forelse ($items as $item)
            <div class="admin-item {{ $item->is_active ? '' : 'off' }}">
                <div class="admin-thumb blue"><x-icon name="camera" /></div>
                <div><b>{{ str_replace(['https://www.instagram.com/', '/'], ['', ' / '], rtrim($item->url, '/')) }}</b><small><a href="{{ $item->url }}" target="_blank" rel="noreferrer">{{ $item->url }}</a>{{ $item->is_active ? '' : ' · disembunyikan' }}</small></div>
                <span></span>
                <div class="row-actions">
                    <x-reorder type="instagram" :item="$item" :first="$loop->first" :last="$loop->last" />
                    <a href="{{ route('admin.instagram.index', $item) }}" aria-label="Ubah"><x-icon name="edit" :size="16" /></a>
                    <form method="post" action="{{ route('admin.instagram.destroy', $item) }}" onsubmit="return confirm('Hapus postingan ini?')">@csrf @method('DELETE')<button aria-label="Hapus"><x-icon name="trash" :size="16" /></button></form>
                </div>
            </div>
        @empty
            <div class="empty">Belum ada postingan. Tempel link dari Instagram.</div>
        @endforelse
    </section>
    <form class="panel admin-form" method="post" action="{{ $e ? route('admin.instagram.update', $e) : route('admin.instagram.store') }}">
        @csrf @if ($e) @method('PUT') @endif
        <div class="panel-head"><div><span>{{ $e ? 'UBAH' : 'TAMBAH' }}</span><h2>{{ $e ? 'Ubah Postingan' : 'Tambah Postingan' }}</h2></div></div>
        <label>Link postingan<input name="url" value="{{ old('url', $e->url ?? '') }}" required maxlength="300" placeholder="https://www.instagram.com/p/AbC123/">
            <span class="hint">Buka postingan di Instagram, ketuk ⋯ lalu "Salin link". Postingan harus dari akun publik. Mendukung post dan reel.</span>@error('url')<span class="error">{{ $message }}</span>@enderror</label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>
        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Tambah postingan' }} <x-icon name="arrow" /></button>
        @if ($e)<a class="btn-ghost" href="{{ route('admin.instagram.index') }}">Batal</a>@endif
    </form>
</div>
@endsection
