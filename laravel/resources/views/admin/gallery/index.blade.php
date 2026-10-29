@extends('layouts.admin')
@section('title', 'Galeri Momen')
@section('desc', 'Foto-foto pelanggan yang tampil di bagian "Teman Cerita" pada website. Pengunjung bisa menyaringnya berdasarkan kategori.')
@section('content')
<section class="panel">
    <div class="panel-head"><div><span>TEMAN CERITA</span><h2>{{ $items->count() }} foto</h2></div><a class="btn btn-primary btn-sm" href="{{ route('admin.gallery.create') }}"><x-icon name="plus" :size="16" /> Tambah foto</a></div>
    @if ($items->isNotEmpty())<x-bulk-bar type="galeri" />@endif
    @if ($items->isEmpty())
        <div class="empty">Belum ada foto. Klik "Tambah foto" untuk mulai.</div>
    @else
    <div class="gallery-admin">
        @foreach ($items as $item)
            <article id="item-galeri-{{ $item->id }}" class="{{ $item->is_active ? '' : 'off' }}">
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy">
                <div><b>{{ $item->title }}</b><small>{{ $item->category }}{{ $item->is_active ? '' : ' · disembunyikan' }}</small></div>
                <x-row-actions type="galeri" :item="$item" :first="$loop->first" :last="$loop->last" :edit="route('admin.gallery.edit', $item)" :destroy="route('admin.gallery.destroy', $item)" :confirm="'Hapus foto '.$item->title.'?'" />
            </article>
        @endforeach
    </div>
    @endif
</section>
@endsection
