@extends('layouts.admin')
@section('title', 'Overview')
@section('content')
@if ($defaultLogin)
    <div class="notice"><b>Ganti email &amp; password bawaan.</b> Akun admin masih memakai data awal. <a href="{{ route('admin.account.edit') }}">Buka pengaturan akun →</a></div>
@endif
<div class="stats">
    @foreach ($stats as [$label, $count, $sub])
        <article><span>{{ $label }}</span><b>{{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</b><small>{{ $sub }}</small></article>
    @endforeach
</div>
<section class="panel">
    <div class="panel-head"><div><span>MENU CEPAT</span><h2>Kelola konten</h2></div></div>
    <div class="admin-list">
        @foreach ([
            ['blue', 'camera', 'Paket & Harga', 'Tambah, ubah harga, promo, atau sembunyikan paket', 'admin.packages.index'],
            ['orange', 'sparkle', 'Katalog Frame', 'Frame bulan ini beserta gambarnya', 'admin.frames.index'],
            ['pink', 'grid', 'Galeri Momen', 'Unggah foto teman cerita', 'admin.gallery.index'],
            ['blue', 'camera', 'Instagram', 'Tampilkan postingan Instagram dengan menempel link', 'admin.instagram.index'],
            ['green', 'map', 'Info Studio', 'Lokasi &amp; peta, jam buka, WhatsApp, sosial media, teks halaman depan', 'admin.settings.edit'],
            ['orange', 'user', 'Akun', 'Ganti email dan password admin', 'admin.account.edit'],
        ] as [$color, $icon, $title, $desc, $route])
            <div class="admin-item"><div class="admin-thumb {{ $color }}"><x-icon :name="$icon" /></div><div><b>{{ $title }}</b><small>{!! $desc !!}</small></div><span></span><div class="row-actions"><a href="{{ route($route) }}" aria-label="Buka {{ $title }}"><x-icon name="arrow" /></a></div></div>
        @endforeach
    </div>
</section>
@endsection
