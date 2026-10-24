@extends('layouts.admin')
@section('title', $editing ? 'Ubah Foto' : 'Tambah Foto')
@section('desc', 'Unggah foto pelanggan. Foto otomatis dikecilkan dan dikompres supaya website tetap cepat.')
@section('content')
@php $e = $editing; @endphp
<a class="back-link" href="{{ route('admin.gallery.index') }}">← Kembali ke galeri</a>
<form class="form-page" method="post" enctype="multipart/form-data" action="{{ $e ? route('admin.gallery.update', $e) : route('admin.gallery.store') }}">
    @csrf @if ($e) @method('PUT') @endif
    <div class="panel admin-form">
        <div class="panel-head"><div><span>{{ $e ? 'UBAH FOTO' : 'FOTO BARU' }}</span><h2>{{ $e ? $e->title : 'Unggah foto' }}</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>

        <label>Foto @unless ($e)<i class="req">*</i>@endunless
            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" @required(! $e)>
            <span class="hint">@if ($e)Pilih file baru hanya bila ingin mengganti foto. @endif JPG, PNG, atau WebP, maksimal 6 MB.</span>@error('image')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label>Judul <i class="req">*</i><input name="title" value="{{ old('title', $e->title ?? '') }}" required maxlength="80" placeholder="Contoh: Sunday with besties">@error('title')<span class="error">{{ $message }}</span>@enderror</label>

        @php $selectedCategory = old('category', $e->category ?? $categories[0] ?? ''); @endphp
        <div class="field">
            <span class="lbl">Kategori <i class="req">*</i></span>
            <div class="seg cat-seg" data-cats data-list="gallery_category">
                @foreach ($categories as $c)
                    <span class="chip-opt" data-value="{{ $c }}"><label class="seg-opt"><input type="radio" name="category" value="{{ $c }}" @checked($selectedCategory === $c)><span>{{ $c }}</span></label><button type="button" class="chip-x" aria-label="Hapus kategori {{ $c }}" title="Hapus kategori">×</button></span>
                @endforeach
            </div>
            <div class="add-row">
                <input type="text" id="new-cat" maxlength="30" placeholder="Kategori baru, contoh: Prewedding" aria-label="Nama kategori baru">
                <button type="button" class="act" id="add-cat"><x-icon name="plus" :size="14" /> Tambah</button>
            </div>
            <span class="hint">Klik untuk memilih kategori. Ketik nama lalu "Tambah" untuk membuat kategori baru. Tekan × untuk menghapus kategori dari daftar (foto yang sudah ada tidak hilang).</span>
            @error('category')<span class="error">{{ $message }}</span>@enderror
        </div>

        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>

        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Unggah foto' }} <x-icon name="arrow" /></button>
        <a class="btn-ghost" href="{{ route('admin.gallery.index') }}">Batal</a>
    </div>

    <div class="form-preview">
        <span class="preview-label">PRATINJAU DI WEBSITE</span>
        <div class="preview-box" data-preview="gallery">
            <div class="masonry"><figure>
                <img data-p="image" alt="" src="{{ $e?->image_url }}" {{ $e ? '' : 'hidden' }}>
                <div class="frame-ph gallery-ph" data-p="placeholder" {{ $e ? 'hidden' : '' }}>Pilih foto untuk melihat pratinjau</div>
                <figcaption><span data-p="category">{{ $e->category ?? ($categories[0] ?? '') }}</span><b data-p="title">{{ $e->title ?? 'Judul foto' }}</b></figcaption>
            </figure></div>
            <p class="hint">Di website, judul &amp; kategori muncul saat kursor diarahkan ke foto.</p>
        </div>
    </div>
</form>
@endsection
