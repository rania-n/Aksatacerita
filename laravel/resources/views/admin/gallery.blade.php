@extends('layouts.admin')
@section('title', 'Galeri Momen')
@section('content')
@php $e = $editing; @endphp
<div class="admin-grid">
    <section class="panel">
        <div class="panel-head"><div><span>TEMAN CERITA</span><h2>Foto Galeri</h2></div><a class="icon-button" href="{{ route('admin.gallery.index') }}" aria-label="Tambah foto"><x-icon name="plus" /></a></div>
        @if ($items->isEmpty())
            <div class="empty">Belum ada foto.</div>
        @else
        <div class="gallery-admin">
            @foreach ($items as $item)
                <article class="{{ $item->is_active ? '' : 'off' }}">
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">
                    <div><b>{{ $item->title }}</b><small>{{ $item->category }}{{ $item->is_active ? '' : ' · disembunyikan' }}</small></div>
                    <div class="row-actions">
                        <a href="{{ route('admin.gallery.index', $item) }}">Ubah</a>
                        <form method="post" action="{{ route('admin.gallery.destroy', $item) }}" onsubmit="return confirm('Hapus foto ini?')">@csrf @method('DELETE')<button>Hapus</button></form>
                    </div>
                </article>
            @endforeach
        </div>
        @endif
    </section>
    <form class="panel admin-form" method="post" enctype="multipart/form-data" action="{{ $e ? route('admin.gallery.update', $e) : route('admin.gallery.store') }}">
        @csrf @if ($e) @method('PUT') @endif
        <div class="panel-head"><div><span>{{ $e ? 'UBAH FOTO' : 'UNGGAH' }}</span><h2>{{ $e ? 'Ubah Foto' : 'Tambah Foto' }}</h2></div></div>
        <label>Judul<input name="title" value="{{ old('title', $e->title ?? '') }}" required maxlength="80" placeholder="Contoh: Sunday with besties">@error('title')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Kategori<select name="category">@foreach (\App\Models\GalleryItem::CATEGORIES as $c)<option @selected(old('category', $e->category ?? '') === $c)>{{ $c }}</option>@endforeach</select>@error('category')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Foto{{ $e ? ' (opsional)' : '' }}<input type="file" name="image" accept="image/png,image/jpeg,image/webp" @required(! $e)><span class="hint">JPG, PNG, atau WebP, maksimal 6 MB.</span>@error('image')<span class="error">{{ $message }}</span>@enderror</label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>
        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Unggah foto' }} <x-icon name="arrow" /></button>
        @if ($e)<a class="btn-ghost" href="{{ route('admin.gallery.index') }}">Batal</a>@endif
    </form>
</div>
@endsection
