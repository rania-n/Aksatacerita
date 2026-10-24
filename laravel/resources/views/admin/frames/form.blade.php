@extends('layouts.admin')
@section('title', $editing ? 'Ubah Frame' : 'Tambah Frame')
@section('desc', 'Unggah foto frame asli. Foto itulah yang tampil di website, persis seperti hasilnya. Kartu pratinjau menunjukkan tampilannya.')
@section('content')
@php $e = $editing; $f = $frame; @endphp
<a class="back-link" href="{{ route('admin.frames.index') }}">← Kembali ke daftar frame</a>
<form class="form-page" method="post" enctype="multipart/form-data" action="{{ $e ? route('admin.frames.update', $e) : route('admin.frames.store') }}">
    @csrf @if ($e) @method('PUT') @endif
    <div class="panel admin-form">
        <div class="panel-head"><div><span>{{ $e ? 'UBAH FRAME' : 'FRAME BARU' }}</span><h2>{{ $e ? $e->name : 'Isi data frame' }}</h2></div><small class="req-note"><i class="req">*</i> wajib diisi</small></div>

        <label>Nama frame <i class="req">*</i><input name="name" value="{{ old('name', $f->name) }}" required maxlength="60" placeholder="Contoh: Retro Blue">@error('name')<span class="error">{{ $message }}</span>@enderror</label>

        <label>Label<input name="label" id="f-label" value="{{ old('label', $f->label) }}" maxlength="20" placeholder="Contoh: NEW DROP"><span class="hint">Klik pilihan di bawah atau ketik sendiri. Yang Anda ketik akan tersimpan jadi pilihan baru. Tekan × untuk menghapus pilihan.</span>@error('label')<span class="error">{{ $message }}</span>@enderror</label>
        <x-chips target="f-label" list="frame_label" :items="$labelOptions" />

        <label>Foto frame @unless ($e)<i class="req">*</i>@endunless
            <input type="file" name="image" accept="image/png,image/jpeg,image/webp" @required(! $e)>
            <span class="hint">@if ($e?->image_url)Sudah ada gambar. Pilih file baru hanya bila ingin menggantinya. @endif JPG, PNG, atau WebP, maksimal 4 MB (otomatis dikompres).</span>@error('image')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $f->is_active ?? true))> Tampilkan di website</label>

        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Tambah frame' }} <x-icon name="arrow" /></button>
        <a class="btn-ghost" href="{{ route('admin.frames.index') }}">Batal</a>
    </div>

    <div class="form-preview">
        <span class="preview-label">PRATINJAU DI WEBSITE</span>
        <div class="preview-box" data-preview="frame">
            <div class="frame-photo">
                <img data-p="image" alt="" src="{{ $f->image_url }}" {{ $f->image_url ? '' : 'hidden' }}>
                <div class="frame-ph" data-p="placeholder" {{ $f->image_url ? 'hidden' : '' }}>Pilih foto frame untuk melihat pratinjau</div>
                <span class="frame-tag" data-p="label" {{ $f->label ? '' : 'hidden' }}>{{ $f->label }}</span>
            </div>
            <div class="frame-meta"><span>01</span><h3 data-p="name">{{ $f->name ?: 'Nama frame' }}</h3></div>
        </div>
    </div>
</form>
@endsection
