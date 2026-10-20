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
                    <div><b>{{ $item->name }}{{ $item->is_active ? '' : ' (disembunyikan)' }}</b><small>{{ implode(' · ', $item->features ?? []) }}</small></div>
                    <strong>Rp{{ $item->price }}</strong>
                    <div class="row-actions">
                        <a href="{{ route('admin.packages.index', $item) }}" aria-label="Ubah {{ $item->name }}"><x-icon name="edit" :size="17" /></a>
                        <form method="post" action="{{ route('admin.packages.destroy', $item) }}" onsubmit="return confirm('Hapus paket {{ e($item->name) }}?')">@csrf @method('DELETE')<button aria-label="Hapus {{ $item->name }}"><x-icon name="trash" :size="17" /></button></form>
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
        <label>Harga mulai<input name="price" value="{{ old('price', $e->price ?? '') }}" required maxlength="20" placeholder="Contoh: 50K">@error('price')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Label<input name="tag" value="{{ old('tag', $e->tag ?? '') }}" maxlength="40" placeholder="Contoh: Most Picked">@error('tag')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Warna kartu<select name="color">@foreach (\App\Models\Package::COLORS as $k => $v)<option value="{{ $k }}" @selected(old('color', $e->color ?? 'blue') === $k)>{{ $v }}</option>@endforeach</select></label>
        <label>Apa saja yang didapat?<textarea name="features" rows="4" placeholder="Satu poin per baris">{{ old('features', implode("\n", $e->features ?? [])) }}</textarea><span class="hint">Satu poin per baris.</span></label>
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $e->is_active ?? true))> Tampilkan di website</label>
        <button class="btn btn-primary" type="submit">{{ $e ? 'Simpan perubahan' : 'Publikasikan' }} <x-icon name="arrow" /></button>
        @if ($e)<a class="btn-ghost" href="{{ route('admin.packages.index') }}">Batal</a>@endif
    </form>
</div>
@endsection
