@extends('layouts.admin')
@section('title', 'Overview')
@section('content')
<div class="stats">
    @foreach ($stats as [$label, $count, $sub])
        <article><span>{{ $label }}</span><b>{{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</b><small>{{ $sub }}</small></article>
    @endforeach
</div>
<section class="panel">
    <div class="panel-head"><div><span>MENU CEPAT</span><h2>Kelola konten</h2></div></div>
    <div class="admin-list">
        <div class="admin-item"><div class="admin-thumb blue"><x-icon name="camera" /></div><div><b>Paket & Harga</b><small>Tambah, ubah harga, atau sembunyikan paket</small></div><span></span><div class="row-actions"><a href="{{ route('admin.packages.index') }}" aria-label="Buka"><x-icon name="arrow" /></a></div></div>
        <div class="admin-item"><div class="admin-thumb orange"><x-icon name="sparkle" /></div><div><b>Katalog Frame</b><small>Frame bulan ini beserta gambarnya</small></div><span></span><div class="row-actions"><a href="{{ route('admin.frames.index') }}" aria-label="Buka"><x-icon name="arrow" /></a></div></div>
        <div class="admin-item"><div class="admin-thumb pink"><x-icon name="grid" /></div><div><b>Galeri Momen</b><small>Unggah foto teman cerita</small></div><span></span><div class="row-actions"><a href="{{ route('admin.gallery.index') }}" aria-label="Buka"><x-icon name="arrow" /></a></div></div>
        <div class="admin-item"><div class="admin-thumb green"><x-icon name="map" /></div><div><b>Info Studio</b><small>Alamat, jam buka, WhatsApp, Instagram</small></div><span></span><div class="row-actions"><a href="{{ route('admin.settings.edit') }}" aria-label="Buka"><x-icon name="arrow" /></a></div></div>
    </div>
</section>
@endsection
