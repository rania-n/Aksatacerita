@extends('layouts.public')

@section('content')
@php
    $wa = 'https://wa.me/'.$s['whatsapp'];
    $photos = $gallery->map->image_url->values();
    $p = fn ($i) => $photos->isEmpty() ? '' : $photos[$i % $photos->count()];
    $icons = ['camera', 'sparkle', 'grid'];
    $waText = $wa.'?text='.rawurlencode($s['wa_message']);
    $hasPin = is_numeric($s['latitude']) && is_numeric($s['longitude']);
    $mapQuery = $hasPin ? $s['latitude'].','.$s['longitude'] : $s['studio_name'].' '.str_replace("\n", ' ', $s['address']);
    $mapsUrl = $s['maps_url'] ?: 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($mapQuery);
    $sameAs = array_values(array_filter([
        'https://instagram.com/'.$s['instagram'],
        $s['tiktok'] ? 'https://www.tiktok.com/@'.$s['tiktok'] : null,
        $s['facebook'] ?: null,
    ]));
    $categories = collect($categories)->merge($gallery->pluck('category'))->unique()->values();
    $schema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Aksata Cerita',
        'description' => $s['meta_description'],
        'url' => url('/'),
        'image' => asset('assets/aksata-logo.png'),
        'telephone' => '+'.$s['whatsapp'],
        'email' => $s['email'] ?: null,
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => str_replace("\n", ' ', $s['address']), 'addressLocality' => $s['city'], 'addressCountry' => 'ID'],
        'geo' => $hasPin ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $s['latitude'], 'longitude' => (float) $s['longitude']] : null,
        'sameAs' => $sameAs,
    ]);
@endphp
@section('title', $s['meta_title'])
@push('head')
    <meta name="description" content="{{ $s['meta_description'] }}">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $s['meta_title'] }}">
    <meta property="og:description" content="{{ $s['meta_description'] }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('assets/aksata-logo.png') }}">
    <meta name="twitter:card" content="summary">
    <script type="application/ld+json">@json($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
@endpush
<main>
    <div class="scroll-progress"></div>
    @if ($s['ticker'] !== '' || $s['last_session'] !== '')
    <div class="ticker"><span>{{ $s['ticker'] }}</span><span>WALK IN WELCOME{{ $s['last_session'] !== '' ? ' · '.strtoupper($s['last_session']) : '' }}</span><span>{{ $s['ticker'] }}</span></div>
    @endif
    <nav class="nav shell">
        <x-brand />
        <div class="nav-links">
            @foreach (['Beranda' => true, 'Paket' => true, 'Frame' => $s['show_frames'] === '1' && $frames->isNotEmpty(), 'Galeri' => $gallery->isNotEmpty(), 'Lokasi' => true] as $item => $visible)
                @if ($visible)<a href="#{{ strtolower($item) }}">{{ $item }}</a>@endif
            @endforeach
        </div>
        <button class="btn btn-primary nav-cta" data-open-booking>Pesan sesi <x-icon name="arrow" /></button>
        <button class="menu-btn" aria-label="Buka menu"><x-icon name="menu" /></button>
    </nav>

    <section class="hero shell" id="beranda">
        <div class="hero-copy">
            <div class="eyebrow"><span></span><span>PHOTOBOOTH STUDIO · {{ strtoupper($s['city']) }}</span></div>
            <h1>{{ $s['hero_line1'] }}<br>{{ $s['hero_line2'] }} <em>{{ $s['hero_highlight'] }}</em></h1>
            <p>{{ $s['hero_text'] }}</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#paket">Lihat paket <x-icon name="arrow" /></a>
                <a class="text-link" href="#galeri">Intip cerita mereka <span>↘</span></a>
            </div>
            @if ($s['proof_show'] === '1' && ($s['proof_rating'] !== '' || $s['proof_text'] !== ''))
            <div class="hero-proof">
                @if ($photos->isNotEmpty())<div class="avatar-stack">@foreach (range(0, min(2, $photos->count() - 1)) as $i)<img src="{{ $p($i) }}" alt="">@endforeach</div>@endif
                <div><b>{{ $s['proof_rating'] }}</b><span>{{ $s['proof_text'] }}</span></div>
            </div>
            @endif
        </div>
        <div class="hero-collage">
            <div class="sunburst"></div>
            <div class="flash flash-one">✦</div><div class="flash flash-two">✦</div>
            @if ($photos->isNotEmpty())
            <div class="photo-card card-one"><img src="{{ $p(0) }}" alt="Teman-teman berfoto bersama"><span>say cheese!</span></div>
            <div class="photo-card card-two"><img src="{{ $p(1) }}" alt="Pasangan berpose di studio"><span>♥ 14.02.25</span></div>
            @endif
            <div class="sticker sticker-blue">GOOD<br>TIMES!</div>
            <div class="sticker sticker-orange">#aksata<br>cerita</div>
            <div class="scribble">Our kind of<br><b>FUN!</b></div>
        </div>
    </section>

    <section class="manifesto">
        <div class="shell manifesto-inner"><p>Bukan cuma foto.</p><h2>Ini cara kita <span>menyimpan</span><br>hari-hari paling seru.</h2><div class="mini-note">NO AWKWARD POSES.<br>JUST BE YOU.</div></div>
    </section>
    <div class="moving-type"><div><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i></div></div>

    <section class="section shell" id="paket">
        <div class="section-heading"><div><span class="kicker">01 — PILIH CERITAMU</span><h2>Good moments,<br><i>fair prices.</i></h2></div><p>Dari foto formal sampai pose paling chaos—ada sesi untuk setiap cerita.</p></div>
        <div class="package-grid">
            @php $featuredId = ($packages->firstWhere('is_featured', true) ?? $packages->first())?->id; @endphp
            @forelse ($packages as $i => $pkg)
                <x-package-card :pkg="$pkg" :active="$pkg->id === $featuredId" :icon="$icons[$i % 3]" />
            @empty
                <p class="package-footnote" style="grid-column:1/-1">Paket segera hadir. Hubungi kami lewat WhatsApp untuk info terbaru.</p>
            @endforelse
        </div>
        <p class="package-footnote">Butuh photobooth untuk wedding, brand activation, atau event? <a href="{{ $waText }}Halo, aku mau tanya photobooth untuk event." target="_blank" rel="noreferrer">Let's collaborate ↗</a></p>
    </section>

    @if ($s['show_frames'] === '1' && $frames->isNotEmpty())
    <section class="frames-section" id="frame">
        <div class="shell">
            <div class="section-heading compact"><div><span class="kicker">02 — FRAME OF THE MONTH</span><h2>Pilih mood.<br><i>Cetak ceritamu.</i></h2></div><div class="limited-pill"><span>●</span> Updated monthly</div></div>
            @php $frameTypes = $frames->pluck('type')->filter()->unique()->values(); @endphp
            @if ($frameTypes->count() > 1)
            <div class="filters frame-filters">@foreach ($frameTypes->prepend('Semua') as $type)<button type="button" data-type="{{ $type }}" class="{{ $loop->first ? 'active' : '' }}">{{ $type }}</button>@endforeach</div>
            @endif
            <div class="frames-scroll">
                @foreach ($frames as $i => $frame)
                    @php $code = str_pad($i + 1, 2, '0', STR_PAD_LEFT); @endphp
                    <article class="frame-wrap" data-type="{{ $frame->type }}">
                        @if ($frame->image_url)
                            <div class="frame-photo">
                                <img src="{{ $frame->image_url }}" alt="Frame {{ $frame->name }}" loading="lazy">
                                @if ($frame->label)<span class="frame-tag">{{ $frame->label }}</span>@endif
                            </div>
                        @else
                        <div class="frame-strip" style="background:{{ $frame->color_hex }};color:{{ $frame->text_color }}">
                            <div class="frame-head"><b>AKSATA<br>CERITA</b><span>{{ $frame->label }}</span></div>
                            @if ($photos->isNotEmpty())<img src="{{ $p($i + 1) }}" alt=""><img src="{{ $p($i + 2) }}" alt="">@endif
                            <div class="frame-bottom"><span>{{ $frame->name }}</span><b>{{ $code }} / {{ str_pad($frames->count(), 2, '0', STR_PAD_LEFT) }}</b></div>
                        </div>
                        @endif
                        <div class="frame-meta"><span>{{ $code }}</span><h3>{{ $frame->name }}</h3>@if ($frame->size)<em class="frame-size">{{ $frame->size }}</em>@endif<a href="{{ $wa }}?text={{ rawurlencode('Halo Aksatacerita! Aku tertarik dengan frame '.$frame->name) }}" target="_blank" rel="noreferrer" aria-label="Tanya frame {{ $frame->name }}" style="width:34px;height:34px;border:1px solid;display:grid;place-items:center"><x-icon name="arrow" /></a></div>
                    </article>
                @endforeach
            </div>
            <div class="frames-more" hidden><button type="button" class="btn btn-dark">Lihat semua frame <x-icon name="arrow" /></button></div>
        </div>
    </section>
    @endif

    @if ($s['show_booth'] === '1')
    <section class="booth-lab">
        <div class="shell booth-grid">
            <div class="booth-copy">
                <span class="kicker">AKSATA PLAYGROUND — BETA</span>
                <h2>Try the booth.<br><i>Strike a pose!</i></h2>
                <p>Pemanasan dulu sebelum datang ke studio. Tekan tombol shutter dan tunjukkan pose paling ikonikmu.</p>
                <div class="booth-tips"><span>01&nbsp; LOOK</span><span>02&nbsp; POSE</span><span>03&nbsp; SNAP!</span></div>
            </div>
            <div class="booth-machine" data-photos="{{ json_encode($photos) }}">
                <div class="machine-top"><x-brand /><span>LIVE PREVIEW <i></i></span></div>
                <div class="booth-screen">
                    @if ($photos->isNotEmpty())<img src="{{ $p(0) }}" alt="Preview photobooth interaktif">@endif
                    <div class="screen-grid"></div>
                    <span class="rec-badge">● REC</span>
                    <span class="frame-counter">FRAME 01</span>
                    <div class="screen-caption">YOU LOOK<br><b>AMAZING!</b></div>
                </div>
                <div class="machine-controls">
                    <div><span>FILTER</span><button class="color-dot blue" aria-label="Filter biru"></button><button class="color-dot orange" aria-label="Filter oranye"></button><button class="color-dot pink" aria-label="Filter merah muda"></button></div>
                    <button class="shutter"><span><x-icon name="camera" :size="25" /></span><b class="shutter-label" style="font:inherit">Take a photo</b></button>
                    <div class="machine-status"><i></i> READY</div>
                </div>
                <div class="print-slot"><span>YOUR STORY COMES OUT HERE</span><i></i></div>
            </div>
        </div>
    </section>
    @endif

    @if ($gallery->isNotEmpty())
    <section class="section gallery-section shell" id="galeri">
        <div class="gallery-title"><span class="kicker">03 — TEMAN CERITA</span><h2>Real people.<br><i>Real fun.</i></h2><p>Tag <b>{{ '@'.$s['instagram'] }}</b> supaya ceritamu bisa ikut tampil di sini.</p></div>
        <div class="filters">@foreach ($categories->prepend('Semua') as $cat)<button data-cat="{{ $cat }}" class="{{ $loop->first ? 'active' : '' }}">{{ $cat }}</button>@endforeach</div>
        <div class="masonry">@foreach ($gallery as $item)<figure data-cat="{{ $item->category }}"><img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy"><figcaption><span>{{ $item->category }}</span><b>{{ $item->title }}</b></figcaption></figure>@endforeach</div>
    </section>

    @endif

    @if ($s['show_instagram'] === '1' && $instagram->isNotEmpty())
    <section class="instagram-section shell" id="instagram">
        <div class="gallery-title"><span class="kicker">FROM INSTAGRAM</span><h2>Lagi seru<br><i>di feed.</i></h2><p>Ikuti <a href="https://instagram.com/{{ $s['instagram'] }}" target="_blank" rel="noreferrer"><b>{{ '@'.$s['instagram'] }}</b></a> untuk cerita terbaru.</p></div>
        <div class="ig-grid">@foreach ($instagram as $post)<div class="ig-item"><blockquote class="instagram-media" data-instgrm-permalink="{{ $post->url }}" data-instgrm-version="14"><a href="{{ $post->url }}" target="_blank" rel="noreferrer">Lihat di Instagram</a></blockquote></div>@endforeach</div>
        <script async src="https://www.instagram.com/embed.js"></script>
    </section>
    @endif

    <section class="visit" id="lokasi">
        <div class="visit-map"><iframe title="Lokasi {{ $s['studio_name'] }}" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d991.1886205!2d111.9158272!3d-8.0412672!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78e3ab72caa8a1%3A0xaf08fa6d1fa41c4b!2sAksata+Cerita+Photobooth!5e0!3m2!1sid!2sid!4v1698580800000!5m2!1sid!2sid" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe></div>
        <div class="visit-copy">
            <span class="kicker">04 — COME SAY HI</span><h2>Sampai ketemu<br>di <i>studio!</i></h2>
            <div class="info-row"><x-icon name="map" /><div><b>{{ $s['studio_name'] }}</b><p>{!! nl2br(e($s['address'])) !!}</p></div></div>
            <div class="info-row"><x-icon name="clock" /><div><b>{{ $s['open_days'] }}</b><p>{{ $s['open_hours'] }}@if ($s['last_session'] !== '')<br><small>{{ $s['last_session'] }}</small>@endif</p></div></div>
            @if ($s['email'] !== '')<div class="info-row"><x-icon name="whatsapp" /><div><b>Email</b><p><a href="mailto:{{ $s['email'] }}">{{ $s['email'] }}</a></p></div></div>@endif
            <a class="btn btn-dark" href="{{ $mapsUrl }}" target="_blank" rel="noreferrer">Buka di Google Maps <x-icon name="arrow" /></a>
        </div>
    </section>

    <section class="cta-section"><div class="cta-orbit orbit-one">SAY CHEESE · SAY CHEESE ·</div><div class="cta-stars">✦　✧　✦</div><h2>Ready to make<br>your own <i>story?</i></h2><p>Nggak perlu jago pose. Datang aja, sisanya biar kami yang bikin seru.</p><button class="btn btn-light" data-open-booking><x-icon name="camera" /> Mulai ceritamu</button></section>
    <footer><div class="shell footer-top"><x-brand :light="true" /><div><b>Explore</b><a href="#paket">Paket &amp; Harga</a>@if ($s['show_frames'] === '1' && $frames->isNotEmpty())<a href="#frame">Katalog Frame</a>@endif @if ($gallery->isNotEmpty())<a href="#galeri">Galeri Momen</a>@endif</div><div><b>Social</b><a href="https://instagram.com/{{ $s['instagram'] }}" target="_blank" rel="noreferrer">{{ '@'.$s['instagram'] }} ↗</a>@if ($s['tiktok'] !== '')<a href="https://www.tiktok.com/@{{ $s['tiktok'] }}" target="_blank" rel="noreferrer">TikTok ↗</a>@endif @if ($s['facebook'] !== '')<a href="{{ $s['facebook'] }}" target="_blank" rel="noreferrer">Facebook ↗</a>@endif<a href="{{ $waText }}" target="_blank" rel="noreferrer">WhatsApp ↗</a></div><div class="footer-note">Made for<br><i>good stories.</i></div></div><div class="shell footer-bottom"><span>© {{ date('Y') }} AKSATACERITA PHOTOBOOTH · {{ strtoupper($s['city']) }}</span></div><div class="shell"><x-credit /></div></footer>
    <a class="floating-wa" href="{{ $waText }}" target="_blank" rel="noreferrer" aria-label="Chat WhatsApp"><x-icon name="whatsapp" /></a>
    <div class="booking-backdrop" data-close-booking></div>
    <aside class="booking-drawer" aria-hidden="true">
        <div class="booking-head"><div><span>RESERVE YOUR MOMENT</span><h2>Pesan sesi.</h2></div><button data-close-booking aria-label="Tutup"><x-icon name="x" /></button></div>
        <div class="booking-step"><span>01</span><div><b>Pilih sesi favoritmu</b><p>Kamu bisa konfirmasi ulang lewat WhatsApp.</p></div></div>
        <div class="session-options">@foreach ($packages as $pkg)<button data-name="{{ $pkg->name }}"><span><x-icon name="camera" /></span><div><b>{{ $pkg->name }}</b><small>{{ $pkg->price_note ?: 'Harga' }} Rp{{ $pkg->price_text }}</small></div><i>○</i></button>@endforeach</div>
        <div class="booking-step"><span>02</span><div><b>Pilih waktu datang</b><p>{{ $s['open_days'] }}, {{ $s['open_hours'] }}</p></div></div>
        <div class="date-chips"><button type="button" data-when="hari ini" class="active">Hari ini<small>Walk in</small></button><button type="button" data-when="besok">Besok<small>Datang besok</small></button><button type="button" data-when="date">Pilih tanggal<small>Kalender</small></button></div>
        <input type="date" class="booking-date" aria-label="Tanggal kedatangan" hidden>
        <a class="btn btn-primary booking-submit" href="{{ $waText }}" data-wa="{{ $wa }}" data-message="{{ $s['wa_message'] }}" target="_blank" rel="noreferrer"><x-icon name="whatsapp" /> Lanjut via WhatsApp <x-icon name="arrow" /></a>
        <p class="booking-note">Tidak perlu bayar sekarang · Dibalas cepat di jam operasional</p>
    </aside>
</main>
@endsection
