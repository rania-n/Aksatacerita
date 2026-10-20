@extends('layouts.public')

@section('content')
@php
    $wa = 'https://wa.me/'.$s['whatsapp'];
    $photos = $gallery->map->image_url->values();
    $p = fn ($i) => $photos->isEmpty() ? '' : $photos[$i % $photos->count()];
    $icons = ['camera', 'sparkle', 'grid'];
@endphp
<main>
    <div class="scroll-progress"></div>
    <div class="ticker"><span>{{ $s['ticker'] }}</span><span>WALK IN WELCOME · {{ strtoupper($s['last_session']) }}</span><span>{{ $s['ticker'] }}</span></div>
    <nav class="nav shell">
        <x-brand />
        <div class="nav-links">
            @foreach (['Beranda', 'Paket', 'Frame', 'Galeri', 'Lokasi'] as $item)
                <a href="#{{ strtolower($item) }}">{{ $item }}</a>
            @endforeach
            <a class="admin-link" href="{{ route('admin.dashboard') }}">Admin</a>
        </div>
        <button class="btn btn-primary nav-cta" data-open-booking>Pesan sesi <x-icon name="arrow" /></button>
        <button class="menu-btn" aria-label="Buka menu"><x-icon name="menu" /></button>
    </nav>

    <section class="hero shell" id="beranda">
        <div class="hero-copy">
            <div class="eyebrow"><span></span><span>PHOTOBOOTH STUDIO · TULUNGAGUNG</span></div>
            <h1>Abadikan cerita<br>serumu di <em>Aksata.</em></h1>
            <p>Masuk, pilih pose paling random, lalu bawa pulang potongan cerita yang nggak akan terulang dua kali.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#paket">Lihat paket <x-icon name="arrow" /></a>
                <a class="text-link" href="#galeri">Intip cerita mereka <span>↘</span></a>
            </div>
            <div class="hero-proof">
                <div class="avatar-stack">@foreach (range(0, min(2, $photos->count() - 1)) as $i)@if ($photos->isNotEmpty())<img src="{{ $p($i) }}" alt="">@endif @endforeach</div>
                <div><b>4.9 / 5</b><span>disayang 2,000+ teman cerita</span></div>
            </div>
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
        <div class="scroll-note">SCROLL TO EXPLORE <span>↓</span></div>
    </section>

    <section class="manifesto">
        <div class="shell manifesto-inner"><p>Bukan cuma foto.</p><h2>Ini cara kita <span>menyimpan</span><br>hari-hari paling seru.</h2><div class="mini-note">NO AWKWARD POSES.<br>JUST BE YOU.</div></div>
    </section>
    <div class="moving-type"><div><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i></div></div>

    <section class="section shell" id="paket">
        <div class="section-heading"><div><span class="kicker">01 — PILIH CERITAMU</span><h2>Good moments,<br><i>fair prices.</i></h2></div><p>Dari foto formal sampai pose paling chaos—ada sesi untuk setiap cerita.</p></div>
        <div class="package-grid">
            @forelse ($packages as $i => $pkg)
                <article class="package-card {{ $i === 0 ? 'active' : '' }} {{ $pkg->color }}">
                    <div class="package-top"><span>{{ $pkg->tag }}</span><x-icon :name="$icons[$i % 3]" :size="28" /></div>
                    <div><h3>{{ $pkg->name }}</h3><p>Mulai dari</p><strong><small>Rp</small>{{ $pkg->price }}</strong></div>
                    <ul>@foreach ($pkg->features ?? [] as $f)<li><span>✓</span>{{ $f }}</li>@endforeach</ul>
                    <button class="package-select" data-open-booking data-session="{{ $pkg->name }}">Pilih paket <x-icon name="arrow" /></button>
                </article>
            @empty
                <p class="package-footnote" style="grid-column:1/-1">Paket segera hadir. Hubungi kami lewat WhatsApp untuk info terbaru.</p>
            @endforelse
        </div>
        <p class="package-footnote">Butuh photobooth untuk wedding, brand activation, atau event? <a href="{{ $wa }}">Let's collaborate ↗</a></p>
    </section>

    <section class="frames-section" id="frame">
        <div class="shell">
            <div class="section-heading compact"><div><span class="kicker">02 — FRAME OF THE MONTH</span><h2>Pilih mood.<br><i>Cetak ceritamu.</i></h2></div><div class="limited-pill"><span>●</span> Updated monthly</div></div>
            <div class="frames-scroll">
                @foreach ($frames as $i => $frame)
                    @php $code = str_pad($i + 1, 2, '0', STR_PAD_LEFT); @endphp
                    <article class="frame-wrap">
                        <div class="frame-strip frame-{{ $frame->color }}">
                            <div class="frame-head"><b>AKSATA<br>CERITA</b><span>{{ $frame->label }}</span></div>
                            @if ($frame->image_url)
                                <img src="{{ $frame->image_url }}" alt="Frame {{ $frame->name }}" style="height:340px">
                            @elseif ($photos->isNotEmpty())
                                <img src="{{ $p($i + 1) }}" alt=""><img src="{{ $p($i + 2) }}" alt="">
                            @endif
                            <div class="frame-bottom"><span>{{ $frame->name }}</span><b>{{ $code }} / {{ str_pad($frames->count(), 2, '0', STR_PAD_LEFT) }}</b></div>
                        </div>
                        <div class="frame-meta"><span>{{ $code }}</span><h3>{{ $frame->name }}</h3><a href="{{ $wa }}?text={{ rawurlencode('Halo Aksatacerita! Aku tertarik dengan frame '.$frame->name) }}" target="_blank" rel="noreferrer" aria-label="Tanya frame {{ $frame->name }}" style="width:34px;height:34px;border:1px solid;display:grid;place-items:center"><x-icon name="arrow" /></a></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="booth-lab">
        <div class="shell booth-grid">
            <div class="booth-copy">
                <span class="kicker">AKSATA PLAYGROUND — BETA</span>
                <h2>Try the booth.<br><i>Strike a pose!</i></h2>
                <p>Pemanasan dulu sebelum datang ke studio. Tekan tombol shutter dan tunjukkan pose paling ikonikmu.</p>
                <div class="booth-tips"><span>01&nbsp; LOOK</span><span>02&nbsp; POSE</span><span>03&nbsp; SNAP!</span></div>
            </div>
            <div class="booth-machine" data-photos='@json($photos)'>
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

    <section class="section gallery-section shell" id="galeri">
        <div class="gallery-title"><span class="kicker">03 — TEMAN CERITA</span><h2>Real people.<br><i>Real fun.</i></h2><p>Tag <b>{{ '@'.$s['instagram'] }}</b> supaya ceritamu bisa ikut tampil di sini.</p></div>
        <div class="filters">@foreach (array_merge(['Semua'], \App\Models\GalleryItem::CATEGORIES) as $cat)<button data-cat="{{ $cat }}" class="{{ $loop->first ? 'active' : '' }}">{{ $cat }}</button>@endforeach</div>
        <div class="masonry">@foreach ($gallery as $item)<figure data-cat="{{ $item->category }}"><img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy"><figcaption><span>{{ $item->category }}</span><b>{{ $item->title }}</b></figcaption></figure>@endforeach</div>
    </section>

    <section class="visit" id="lokasi">
        <div class="visit-map"><iframe title="Lokasi Aksatacerita" src="https://www.openstreetmap.org/export/embed.html?bbox=111.89%2C-8.09%2C111.94%2C-8.04&amp;layer=mapnik" loading="lazy"></iframe><div class="map-stamp"><x-brand /><span>YOU'RE ALMOST HERE!</span></div></div>
        <div class="visit-copy">
            <span class="kicker">04 — COME SAY HI</span><h2>Sampai ketemu<br>di <i>studio!</i></h2>
            <div class="info-row"><x-icon name="map" /><div><b>{{ $s['studio_name'] }}</b><p>{!! nl2br(e($s['address'])) !!}</p></div></div>
            <div class="info-row"><x-icon name="clock" /><div><b>Setiap Hari</b><p>{{ $s['open_hours'] }}<br><small>{{ $s['last_session'] }}</small></p></div></div>
            <a class="btn btn-dark" href="{{ $s['maps_url'] }}" target="_blank" rel="noreferrer">Buka di Google Maps <x-icon name="arrow" /></a>
        </div>
    </section>

    <section class="cta-section"><div class="cta-orbit orbit-one">SAY CHEESE · SAY CHEESE ·</div><div class="cta-stars">✦　✧　✦</div><h2>Ready to make<br>your own <i>story?</i></h2><p>Nggak perlu jago pose. Datang aja, sisanya biar kami yang bikin seru.</p><button class="btn btn-light" data-open-booking><x-icon name="camera" /> Mulai ceritamu</button></section>
    <footer><div class="shell footer-top"><x-brand :light="true" /><div><b>Explore</b><a href="#paket">Paket &amp; Harga</a><a href="#frame">Katalog Frame</a><a href="#galeri">Galeri Momen</a></div><div><b>Social</b><a href="https://instagram.com/{{ $s['instagram'] }}">{{ '@'.$s['instagram'] }} ↗</a><a href="{{ $wa }}">WhatsApp ↗</a></div><div class="footer-note">Made for<br><i>good stories.</i></div></div><div class="shell footer-bottom"><span>© {{ date('Y') }} AKSATACERITA PHOTOBOOTH</span><span>TULUNGAGUNG, EAST JAVA</span></div></footer>
    <a class="floating-wa" href="{{ $wa }}" target="_blank" rel="noreferrer" aria-label="Chat WhatsApp"><x-icon name="whatsapp" /></a>
    <div class="booking-backdrop" data-close-booking></div>
    <aside class="booking-drawer" aria-hidden="true">
        <div class="booking-head"><div><span>RESERVE YOUR MOMENT</span><h2>Pesan sesi.</h2></div><button data-close-booking aria-label="Tutup"><x-icon name="x" /></button></div>
        <div class="booking-step"><span>01</span><div><b>Pilih sesi favoritmu</b><p>Kamu bisa konfirmasi ulang lewat WhatsApp.</p></div></div>
        <div class="session-options">@foreach ($packages as $pkg)<button data-name="{{ $pkg->name }}"><span><x-icon name="camera" /></span><div><b>{{ $pkg->name }}</b><small>Mulai Rp{{ $pkg->price }}</small></div><i>○</i></button>@endforeach</div>
        <div class="booking-step"><span>02</span><div><b>Pilih waktu datang</b><p>Setiap hari, {{ $s['open_hours'] }}</p></div></div>
        <div class="date-chips"><button>Hari ini<small>Walk in</small></button><button class="active">Besok<small>Available</small></button><button>Pilih tanggal<small>Calendar ↗</small></button></div>
        <a class="btn btn-primary booking-submit" href="{{ $wa }}" data-base="{{ $wa }}?text={{ rawurlencode('Halo Aksatacerita! Aku mau pesan ') }}" target="_blank" rel="noreferrer"><x-icon name="whatsapp" /> Lanjut via WhatsApp <x-icon name="arrow" /></a>
        <p class="booking-note">No payment needed now · Fast response during studio hours</p>
    </aside>
</main>
@endsection
