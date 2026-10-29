@extends('layouts.admin')
@section('title', 'Paket & Harga')
@section('desc', 'Daftar paket yang tampil di bagian "Pilih ceritamu" pada website. Ubah urutan dengan tombol panah.')
@section('content')
<section class="panel">
    <div class="panel-head"><div><span>DAFTAR LAYANAN</span><h2>{{ $items->count() }} paket</h2></div><a class="btn btn-primary btn-sm" href="{{ route('admin.packages.create') }}"><x-icon name="plus" :size="16" /> Tambah paket</a></div>
    @if ($items->isNotEmpty())<x-bulk-bar type="paket" />@endif
    <div class="admin-list">
        @forelse ($items as $item)
            <div id="item-paket-{{ $item->id }}" class="admin-item {{ $item->is_active ? '' : 'off' }}">
                <div class="admin-thumb" style="background:{{ $item->color_hex }}22;color:{{ $item->color_hex }}"><x-icon name="camera" /></div>
                <div><b>{{ $item->name }}@if ($item->is_featured)<span class="badge">UNGGULAN</span>@endif{{ $item->is_active ? '' : ' (disembunyikan)' }}</b><small>{{ collect([$item->duration_text, implode(' · ', $item->features ?? [])])->filter()->implode(' · ') }}</small></div>
                <div class="price"><strong>Rp{{ $item->price_text }}</strong>@if ($item->old_price_text)<s>Rp{{ $item->old_price_text }}</s>@endif</div>
                <x-row-actions type="paket" :item="$item" :first="$loop->first" :last="$loop->last" :edit="route('admin.packages.edit', $item)" :destroy="route('admin.packages.destroy', $item)" :confirm="'Hapus paket '.$item->name.'?'" />
            </div>
        @empty
            <div class="empty">Belum ada paket. Klik "Tambah paket" untuk mulai.</div>
        @endforelse
    </div>
</section>
@endsection
