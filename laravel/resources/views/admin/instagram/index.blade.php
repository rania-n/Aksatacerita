@extends('layouts.admin')
@section('title', 'Instagram')
@section('desc', 'Postingan Instagram yang tampil di bagian "From Instagram" pada website. Cukup tempel link postingan, tanpa perlu login atau token.')
@section('content')
<section class="panel">
    <div class="panel-head"><div><span>FEED</span><h2>{{ $items->count() }} postingan</h2></div><a class="btn btn-primary btn-sm" href="{{ route('admin.instagram.create') }}"><x-icon name="plus" :size="16" /> Tambah postingan</a></div>
    @if ($items->isNotEmpty())<x-bulk-bar type="instagram" />@endif
    @forelse ($items as $item)
        <div id="item-instagram-{{ $item->id }}" class="admin-item {{ $item->is_active ? '' : 'off' }}">
            <div class="admin-thumb" style="background:#e5e8ff;color:var(--blue)"><x-icon name="camera" /></div>
            <div><b>{{ str_replace('/', ' / ', trim(str_replace('https://www.instagram.com/', '', $item->url), '/')) }}</b><small><a href="{{ $item->url }}" target="_blank" rel="noreferrer">{{ $item->url }}</a>{{ $item->is_active ? '' : ' · disembunyikan' }}</small></div>
            <span></span>
            <x-row-actions type="instagram" :item="$item" :first="$loop->first" :last="$loop->last" :edit="route('admin.instagram.edit', $item)" :destroy="route('admin.instagram.destroy', $item)" confirm="Hapus postingan ini dari website?" />
        </div>
    @empty
        <div class="empty">Belum ada postingan. Klik "Tambah postingan" untuk mulai.</div>
    @endforelse
</section>
@endsection
