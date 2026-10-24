@extends('layouts.admin')
@section('title', 'Info Studio')
@section('content')
<form class="settings-form" method="post" action="{{ route('admin.settings.update') }}">
    @csrf @method('PUT')
    <p class="req-note"><i class="req">*</i> wajib diisi</p>

    <section class="panel admin-form">
        <div class="panel-head"><div><span>01</span><h2>Lokasi &amp; Peta</h2></div></div>
        <div class="row">
            <label>Nama studio / lokasi <i class="req">*</i><input name="studio_name" value="{{ old('studio_name', $s['studio_name']) }}" required maxlength="80">@error('studio_name')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Kota <i class="req">*</i><input name="city" value="{{ old('city', $s['city']) }}" required maxlength="40" placeholder="Contoh: Tulungagung">@error('city')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <label>Alamat lengkap <i class="req">*</i><textarea name="address" id="f-address" rows="3" required maxlength="300">{{ old('address', $s['address']) }}</textarea>@error('address')<span class="error">{{ $message }}</span>@enderror</label>

        <div class="map-tools">
            <input type="search" id="map-search" placeholder="Cari tempat atau alamat di peta..." aria-label="Cari lokasi">
            <button type="button" id="map-find">Cari</button>
            <button type="button" id="map-locate">Lokasi saya</button>
        </div>
        <div id="map-picker" data-icons="{{ asset('vendor/leaflet/images') }}/" data-lat="{{ old('latitude', $s['latitude']) }}" data-lng="{{ old('longitude', $s['longitude']) }}"></div>
        <p class="map-status" id="map-status">Klik peta atau geser pin untuk menandai lokasi studio. Koordinat terisi otomatis.</p>
        <div class="row">
            <label>Latitude<input name="latitude" id="f-latitude" value="{{ old('latitude', $s['latitude']) }}" inputmode="decimal" placeholder="-8.0654">@error('latitude')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Longitude<input name="longitude" id="f-longitude" value="{{ old('longitude', $s['longitude']) }}" inputmode="decimal" placeholder="111.9025">@error('longitude')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <label>Link Google Maps (opsional)<input name="maps_url" value="{{ old('maps_url', $s['maps_url']) }}" maxlength="500" placeholder="https://maps.app.goo.gl/..."><span class="hint">Dipakai untuk tombol "Buka di Google Maps". Kosongkan bila ingin otomatis dibuat dari pin di atas.</span>@error('maps_url')<span class="error">{{ $message }}</span>@enderror</label>
    </section>

    <section class="panel admin-form">
        <div class="panel-head"><div><span>02</span><h2>Jam Buka</h2></div></div>
        <div class="row">
            <label>Hari buka <i class="req">*</i><input name="open_days" value="{{ old('open_days', $s['open_days']) }}" required maxlength="40" placeholder="Contoh: Setiap Hari">@error('open_days')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Jam buka <i class="req">*</i><input name="open_hours" value="{{ old('open_hours', $s['open_hours']) }}" required maxlength="60" placeholder="Contoh: 10.00 — 22.00 WIB">@error('open_hours')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <label>Sesi terakhir<input name="last_session" value="{{ old('last_session', $s['last_session']) }}" maxlength="60" placeholder="Contoh: Last session 21.30">@error('last_session')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Teks pita di atas website<input name="ticker" value="{{ old('ticker', $s['ticker']) }}" maxlength="100" placeholder="Contoh: STUDIO OPEN DAILY · 10.00—22.00 WIB"><span class="hint">Kosongkan untuk menyembunyikan pita.</span>@error('ticker')<span class="error">{{ $message }}</span>@enderror</label>
    </section>

    <section class="panel admin-form">
        <div class="panel-head"><div><span>03</span><h2>Kontak &amp; Sosial Media</h2></div></div>
        <label>Nomor WhatsApp <i class="req">*</i><input name="whatsapp" value="{{ old('whatsapp', $s['whatsapp']) }}" required inputmode="tel" placeholder="085117375305"><span class="hint">Boleh diawali 0, +62, atau 62. Otomatis diubah ke format 62.</span>@error('whatsapp')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Pesan awal WhatsApp <i class="req">*</i><input name="wa_message" value="{{ old('wa_message', $s['wa_message']) }}" required maxlength="200"><span class="hint">Teks yang sudah terisi saat pengunjung membuka WhatsApp. Nama paket ditambahkan setelahnya.</span>@error('wa_message')<span class="error">{{ $message }}</span>@enderror</label>
        <div class="row">
            <label>Username Instagram <i class="req">*</i><input name="instagram" value="{{ old('instagram', $s['instagram']) }}" required placeholder="aksatacerita"><span class="hint">Tanpa @. Link profil juga boleh ditempel.</span>@error('instagram')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Username TikTok (opsional)<input name="tiktok" value="{{ old('tiktok', $s['tiktok']) }}" placeholder="aksatacerita">@error('tiktok')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <div class="row">
            <label>Link Facebook (opsional)<input name="facebook" value="{{ old('facebook', $s['facebook']) }}" placeholder="https://facebook.com/...">@error('facebook')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Email (opsional)<input type="email" name="email" value="{{ old('email', $s['email']) }}" placeholder="halo@aksatacerita.com">@error('email')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <label class="check"><input type="checkbox" name="show_instagram" value="1" @checked(old('show_instagram', $s['show_instagram'] === '1'))> Tampilkan feed Instagram di website <small>(isi postingannya di menu Instagram)</small></label>
    </section>

    <section class="panel admin-form">
        <div class="panel-head"><div><span>04</span><h2>Teks Halaman Depan</h2></div></div>
        <div class="row">
            <label>Judul baris 1 <i class="req">*</i><input name="hero_line1" value="{{ old('hero_line1', $s['hero_line1']) }}" required maxlength="40">@error('hero_line1')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Judul baris 2 <i class="req">*</i><input name="hero_line2" value="{{ old('hero_line2', $s['hero_line2']) }}" required maxlength="40">@error('hero_line2')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <label>Kata yang disorot <i class="req">*</i><input name="hero_highlight" value="{{ old('hero_highlight', $s['hero_highlight']) }}" required maxlength="30">@error('hero_highlight')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Kalimat pembuka <i class="req">*</i><textarea name="hero_text" rows="3" required maxlength="300">{{ old('hero_text', $s['hero_text']) }}</textarea>@error('hero_text')<span class="error">{{ $message }}</span>@enderror</label>
        <label class="check"><input type="checkbox" name="proof_show" value="1" @checked(old('proof_show', $s['proof_show'] === '1'))> Tampilkan rating &amp; jumlah pelanggan</label>
        <div class="row">
            <label>Rating<input name="proof_rating" value="{{ old('proof_rating', $s['proof_rating']) }}" maxlength="20">@error('proof_rating')<span class="error">{{ $message }}</span>@enderror</label>
            <label>Teks di bawah rating<input name="proof_text" value="{{ old('proof_text', $s['proof_text']) }}" maxlength="80"><span class="hint">Pastikan angka ini sesuai kenyataan.</span>@error('proof_text')<span class="error">{{ $message }}</span>@enderror</label>
        </div>
        <label class="check"><input type="checkbox" name="show_frames" value="1" @checked(old('show_frames', $s['show_frames'] === '1'))> Tampilkan bagian Katalog Frame</label>
        <label class="check"><input type="checkbox" name="show_booth" value="1" @checked(old('show_booth', $s['show_booth'] === '1'))> Tampilkan bagian "Try the booth" (simulasi photobooth)</label>
        <label>Kategori galeri <i class="req">*</i><input name="gallery_categories" value="{{ old('gallery_categories', $s['gallery_categories']) }}" required maxlength="200"><span class="hint">Pisahkan dengan koma, maksimal 8. Contoh: Event, Wisuda, Couple, Bestie</span>@error('gallery_categories')<span class="error">{{ $message }}</span>@enderror</label>
    </section>

    <section class="panel admin-form">
        <div class="panel-head"><div><span>05</span><h2>Tampilan di Google</h2></div></div>
        <label>Judul <i class="req">*</i><input name="meta_title" value="{{ old('meta_title', $s['meta_title']) }}" required maxlength="70"><span class="hint">Maksimal 70 karakter.</span>@error('meta_title')<span class="error">{{ $message }}</span>@enderror</label>
        <label>Deskripsi <i class="req">*</i><textarea name="meta_description" rows="2" required maxlength="200">{{ old('meta_description', $s['meta_description']) }}</textarea><span class="hint">Maksimal 200 karakter. Muncul di hasil pencarian dan saat link dibagikan.</span>@error('meta_description')<span class="error">{{ $message }}</span>@enderror</label>
    </section>

    <div class="sticky-save"><button class="btn btn-primary" type="submit">Simpan semua <x-icon name="arrow" /></button></div>
</form>

<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('js/admin-map.js') }}"></script>
@endsection
