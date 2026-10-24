@extends('layouts.admin')
@section('title', 'Katalog Frame')
@section('desc', 'Frame yang tersedia di studio. Tampil di bagian "Frame of the Month" pada website; pengunjung bisa langsung bertanya lewat WhatsApp.')
@section('content')
<section class="panel">
    <div class="panel-head"><div><span>FRAME OF THE MONTH</span><h2>{{ $items->count() }} frame</h2></div><a class="btn btn-primary btn-sm" href="{{ route('admin.frames.create') }}"><x-icon name="plus" :size="16" /> Tambah frame</a></div>
    <div class="admin-list">
        @forelse ($items as $item)
            <div class="admin-item {{ $item->is_active ? '' : 'off' }}">
                <div class="admin-thumb" style="background:{{ $item->color_hex }}22;color:{{ $item->color_hex }}">@if ($item->image_url)<img src="{{ $item->image_url }}" alt="">@else<x-icon name="sparkle" />@endif</div>
                <div><b>{{ $item->name }}{{ $item->is_active ? '' : ' (disembunyikan)' }}</b><small>{{ $item->label ?: 'Tanpa label' }} · {{ \App\Support\Palette::name($item->color) }}</small></div>
                <span></span>
                <x-row-actions type="frame" :item="$item" :first="$loop->first" :last="$loop->last" :edit="route('admin.frames.edit', $item)" :destroy="route('admin.frames.destroy', $item)" :confirm="'Hapus frame '.$item->name.'?'" />
            </div>
        @empty
            <div class="empty">Belum ada frame. Klik "Tambah frame" untuk mulai.</div>
        @endforelse
    </div>
</section>
@endsection
