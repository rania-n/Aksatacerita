@extends('layouts.admin')
@section('title', $editing ? 'Ubah Frame' : 'Tambah Frame')
@section('desc', 'Unggah foto frame asli dan pilih warna bingkainya. Kartu pratinjau menunjukkan tampilannya di website.')
@section('content')
@php $e = $editing; $f = $frame; @endphp
<a class="back-link" href="{{ route('admin.frames.index') }}">← Kembali ke daftar frame</a>
<form class="form-page" method="post" enctype="multipart/form-data" action="{{ $e ? route('admin.frames.update', $e) : route('admin.frames.store') }}">
    @csrf @if ($e) @method('PUT') @endif
    <div class="panel admin-form">
        <div class="panel-head"><div><span>{{ $e ? 'UBAH FRAME' : 'FRAME BARU' }}</span><h2>{{ $e ? $e->name : 'Isi data frame' }}</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>

        <label>Nama frame <i class="req">*</i><input name="name" value="{{ old('name', $f->name) }}" required maxlength="60" placeholder="Contoh: Retro Blue">@error('name')<span class="error">{{ $message }}</span>@enderror</label>

        <label>Label<input name="label" id="f-label" value="{{ old('label', $f->label) }}" maxlength="20" placeholder="Contoh: NEW DROP"><span class="hint">Klik salah satu di bawah, atau ketik sendiri. Label yang pernah Anda ketik akan muncul di sini otomatis.</span>@error('label')<span class="error">{{ $message }}</span>@enderror</label>
        <x-chips target="f-label" :items="$labelSuggestions" />
        <div class="spacer"></div>

        <div class="field">
            <span class="lbl">Warna frame <i class="req">*</i></span>
            <x-swatches name="color" :value="$f->color" :swatches="$swatches" />
            <span class="hint">Pilih warna yang mirip dengan frame aslinya. Warna ini jadi latar kartu frame di website. Tombol "+" untuk warna lain.</span>
            @error('color')<span class="error">{{ $message }}</span>@enderror
        </div>

        <label>Foto frame
            <input type="file" name="image" accept="image/png,image/jpeg,image/webp">
            <span class="hint">@if ($e?->image_url)Sudah ada gambar. Pilih file baru hanya bila ingin menggantinya. @endif JPG, PNG, atau WebP, maksimal 4 MB (otomatis dikompres). Tanpa foto, website memakai foto galeri sebagai contoh.</span>@error('image')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $f->is_active ?? true))> Tampilkan di website</label>

        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Tambah frame' }} <x-icon name="arrow" /></button>
        <a class="btn-ghost" href="{{ route('admin.frames.index') }}">Batal</a>
    </div>

    <div class="form-preview">
        <span class="preview-label">PRATINJAU DI WEBSITE</span>
        <div class="preview-box" data-preview="frame">
            <div class="frame-strip" style="background:{{ $f->color_hex }};color:{{ $f->text_color }}">
                <div class="frame-head"><b>AKSATA<br>CERITA</b><span data-p="label" {{ $f->label ? '' : 'hidden' }}>{{ $f->label }}</span></div>
                <img data-p="image" alt="" src="{{ $f->image_url }}" style="height:260px" {{ $f->image_url ? '' : 'hidden' }}>
                <div class="frame-ph" data-p="placeholder" {{ $f->image_url ? 'hidden' : '' }}><i></i><i></i></div>
                <div class="frame-bottom"><span data-p="name">{{ $f->name ?: 'Nama frame' }}</span><b>01 / 04</b></div>
            </div>
        </div>
    </div>
</form>
@endsection
