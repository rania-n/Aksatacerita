@extends('layouts.admin')
@section('title', 'Paket & Harga')
@section('content')
@php $e = $editing; @endphp
<div class="admin-grid">
    <section class="panel">
        <div class="panel-head"><div><span>DAFTAR LAYANAN</span><h2>Paket & Harga</h2></div><a class="icon-button" href="{{ route('admin.packages.index') }}" aria-label="Tambah paket"><x-icon name="plus" /></a></div>
        <div class="admin-list">
            @forelse ($items as $item)
                <div class="admin-item {{ $item->is_active ? '' : 'off' }}">
                    <div class="admin-thumb {{ $item->color }}"><x-icon name="camera" /></div>
                    <div><b>{{ $item->name }}@if ($item->is_featured)<span class="badge">UNGGULAN</span>@endif{{ $item->is_active ? '' : ' (disembunyikan)' }}</b><small>{{ collect([$item->duration, implode(' · ', $item->features ?? [])])->filter()->implode(' · ') }}</small></div>
                    <div class="price"><strong>Rp{{ $item->price }}{{ $item->price_unit ? ' '.$item->price_unit : '' }}</strong>@if ($item->old_price)<s>Rp{{ $item->old_price }}</s>@endif</div>
                    <div class="row-actions">
                        <x-reorder type="paket" :item="$item" :first="$loop->first" :last="$loop->last" />
                        <a href="{{ route('admin.packages.index', $item) }}" aria-label="Ubah {{ $item->name }}"><x-icon name="edit" :size="17" /></a>
                        <form method="post" action="{{ route('admin.packages.destroy', $item) }}" onsubmit="return confirm({{ Illuminate\Support\Js::from('Hapus paket '.$item->name.'?') }})">@csrf @method('DELETE')<button aria-label="Hapus {{ $item->name }}"><x-icon name="trash" :size="17" /></button></form>
                    </div>
                </div>
            @empty
                <div class="empty">Belum ada paket.</div>
            @endforelse
        </div>
    </section>
    <form class="panel admin-form" method="post" action="{{ $e ? route('admin.packages.update', $e) : route('admin.packages.store') }}">
        @csrf @if ($e) @method('PUT') @endif
        <div class="panel-head"><div><span>{{ $e ? 'UBAH PAKET' : 'QUICK ACTION' }}</span><h2>{{ $e ? 'Ubah Paket' : 'Tambah Paket' }}</h2></div></div>
        <label>Nama paket<input name="name" value="{{ old('name', $e->name ?? '') }}" required maxlength="80" placeholder="Contoh: Bestie Session">@error('name')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Deskripsi singkat<input name="description" value="{{ old('description', $e->description ?? '') }}" maxlength="120" placeholder="Contoh: Cocok untuk berdua atau bertiga">@error('description')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Durasi sesi<input name="duration" value="{{ old('duration', $e->duration ?? '') }}" maxlength="40" placeholder="Contoh: 15 menit">@error('duration')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Teks di atas harga<input name="price_note" value="{{ old('price_note', $e ? $e->price_note : 'Mulai dari') }}" maxlength="30" placeholder="Contoh: Mulai dari (kosongkan bila tidak perlu)">@error('price_note')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Harga<input name="price" value="{{ old('price', $e->price ?? '') }}" required maxlength="20" placeholder="Contoh: 50K atau 50.000"><span class="hint">Awalan "Rp" otomatis ditambahkan, tidak perlu diketik.</span>@error('price')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Satuan harga<input name="price_unit" value="{{ old('price_unit', $e->price_unit ?? '') }}" maxlength="20" placeholder="Contoh: / sesi atau / orang">@error('price_unit')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Harga coret (promo)<input name="old_price" value="{{ old('old_price', $e->old_price ?? '') }}" maxlength="20" placeholder="Contoh: 75K"><span class="hint">Isi bila sedang ada diskon. Ditampilkan dicoret di samping harga.</span>@error('old_price')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Label<input name="tag" value="{{ old('tag', $e->tag ?? '') }}" maxlength="40" placeholder="Contoh: Most Picked">@error('tag')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Warna kartu<select name="color">@foreach (\App\Models\Package::COLORS as $k => $v)<option value="{{ $k }}" @selected(old('color', $e->color ?? 'blue') === $k)>{{ $v }}</option>@endforeach</select>@error('color')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Apa saja yang didapat?<textarea name="features" rows="5" maxlength="1000" placeholder="Satu poin per baris">{{ old('features', implode("\n", $e->features ?? [])) }}</textarea><span class="hint">Satu poin per baris.</span>@error('features')<span class="error">{{ $message }}</span>@enderror</label>
        <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $e->is_featured ?? false))> Paket unggulan <small>(kartu ini tampil menonjol saat halaman dibuka)</small></label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>
        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Publikasikan' }} <x-icon name="arrow" /></button>
        @if ($e)<a class="btn-ghost" href="{{ route('admin.packages.index') }}">Batal</a>@endif
    </form>
</div>
@endsection
