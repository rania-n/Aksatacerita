@extends('layouts.admin')
@section('title', 'Katalog Frame')
@section('content')
@php $e = $editing; @endphp
<div class="admin-grid">
    <section class="panel">
        <div class="panel-head"><div><span>FRAME OF THE MONTH</span><h2>Daftar Frame</h2></div><a class="icon-button" href="{{ route('admin.frames.index') }}" aria-label="Tambah frame"><x-icon name="plus" /></a></div>
        <div class="admin-list">
            @forelse ($items as $item)
                <div class="admin-item {{ $item->is_active ? '' : 'off' }}">
                    <div class="admin-thumb {{ $item->color }}">@if ($item->image_url)<img src="{{ $item->image_url }}" alt="">@else<x-icon name="sparkle" />@endif</div>
                    <div><b>{{ $item->name }}{{ $item->is_active ? '' : ' (disembunyikan)' }}</b><small>{{ $item->label }} · {{ \App\Models\Frame::COLORS[$item->color] ?? $item->color }}</small></div>
                    <span></span>
                    <div class="row-actions">
                        <a href="{{ route('admin.frames.index', $item) }}" aria-label="Ubah {{ $item->name }}"><x-icon name="edit" :size="17" /></a>
                        <form method="post" action="{{ route('admin.frames.destroy', $item) }}" onsubmit="return confirm('Hapus frame {{ e($item->name) }}?')">@csrf @method('DELETE')<button aria-label="Hapus {{ $item->name }}"><x-icon name="trash" :size="17" /></button></form>
                    </div>
                </div>
            @empty
                <div class="empty">Belum ada frame.</div>
            @endforelse
        </div>
    </section>
    <form class="panel admin-form" method="post" enctype="multipart/form-data" action="{{ $e ? route('admin.frames.update', $e) : route('admin.frames.store') }}">
        @csrf @if ($e) @method('PUT') @endif
        <div class="panel-head"><div><span>{{ $e ? 'UBAH FRAME' : 'QUICK ACTION' }}</span><h2>{{ $e ? 'Ubah Frame' : 'Tambah Frame' }}</h2></div></div>
        <label>Nama frame<input name="name" value="{{ old('name', $e->name ?? '') }}" required maxlength="60" placeholder="Contoh: Retro Blue">@error('name')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Label<input name="label" value="{{ old('label', $e->label ?? '') }}" maxlength="20" placeholder="Contoh: NEW DROP">@error('label')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Warna frame<select name="color">@foreach (\App\Models\Frame::COLORS as $k => $v)<option value="{{ $k }}" @selected(old('color', $e->color ?? 'blue') === $k)>{{ $v }}</option>@endforeach</select></label>
        <label>Gambar frame (opsional)<input type="file" name="image" accept="image/png,image/jpeg,image/webp">@if ($e?->image_url)<span class="hint">Sudah ada gambar. Pilih file baru untuk mengganti.</span>@else<span class="hint">JPG, PNG, atau WebP, maksimal 4 MB. Tanpa gambar, website memakai foto galeri sebagai contoh.</span>@endif @error('image')<span class="error">{{ $message }}</span>@enderror</label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>
        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Tambah frame' }} <x-icon name="arrow" /></button>
        @if ($e)<a class="btn-ghost" href="{{ route('admin.frames.index') }}">Batal</a>@endif
    </form>
</div>
@endsection
