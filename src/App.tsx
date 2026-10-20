import { useEffect, useRef, useState } from "react";

type IconName = "arrow" | "camera" | "clock" | "grid" | "map" | "menu" | "plus" | "sparkle" | "trash" | "whatsapp" | "x";

const Icon = ({ name, size = 20 }: { name: IconName; size?: number }) => {
  const paths: Record<IconName, React.ReactNode> = {
    arrow: <><path d="M5 12h14M13 6l6 6-6 6" /></>,
    camera: <><path d="M4 7h3l2-3h6l2 3h3v12H4z" /><circle cx="12" cy="13" r="4" /></>,
    clock: <><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></>,
    grid: <><rect x="4" y="4" width="6" height="6" /><rect x="14" y="4" width="6" height="6" /><rect x="4" y="14" width="6" height="6" /><rect x="14" y="14" width="6" height="6" /></>,
    map: <><path d="M12 22s7-6.3 7-13a7 7 0 1 0-14 0c0 6.7 7 13 7 13z" /><circle cx="12" cy="9" r="2.5" /></>,
    menu: <><path d="M4 7h16M4 12h16M4 17h16" /></>,
    plus: <><path d="M12 5v14M5 12h14" /></>,
    sparkle: <><path d="M12 2l1.7 6.3L20 10l-6.3 1.7L12 18l-1.7-6.3L4 10l6.3-1.7z" /></>,
    trash: <><path d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13" /></>,
    whatsapp: <><path d="M20 11.5A8.5 8.5 0 0 1 7.4 19l-4.4 1 1.1-4.2A8.5 8.5 0 1 1 20 11.5z" /><path d="M8 8c.5 3 2 4.5 5 5" /></>,
    x: <><path d="M6 6l12 12M18 6L6 18" /></>,
  };
  return <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[name]}</svg>;
};

const photos = [
  "https://images.unsplash.com/photo-1631832255415-8583788a11cb?auto=format&fit=crop&w=900&q=85",
  "https://images.unsplash.com/photo-1631832187986-85ce443a65d9?auto=format&fit=crop&w=900&q=85",
  "https://images.unsplash.com/photo-1582510871461-27ff4aafa053?auto=format&fit=crop&w=900&q=85",
  "https://images.unsplash.com/photo-1609084415979-e38c11583e8b?auto=format&fit=crop&w=900&q=85",
  "https://images.unsplash.com/photo-1738156684532-b79bfb589344?auto=format&fit=crop&w=900&q=85",
  "https://images.unsplash.com/photo-1631821657340-b07983bfc5f9?auto=format&fit=crop&w=900&q=85",
];

const initialPackages = [
  { id: 1, name: "Regular Photobox", price: "35K", tag: "Most Picked", color: "blue", features: ["15 menit sesi", "2 strip cetak", "All soft files"] },
  { id: 2, name: "Self Photo Studio", price: "75K", tag: "Slow & Sweet", color: "orange", features: ["30 menit sesi", "2 foto cetak 4R", "All soft files"] },
  { id: 3, name: "Passport Booth", price: "25K", tag: "Ready in 10 min", color: "pink", features: ["6 pas foto", "Background pilihan", "File high-res"] },
];

type Pkg = (typeof initialPackages)[number];
const STORAGE_KEY = "aksata-packages";

function loadPackages(): Pkg[] {
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return initialPackages;
    const parsed = JSON.parse(saved);
    if (Array.isArray(parsed) && parsed.every((p) => p && typeof p.name === "string" && Array.isArray(p.features))) return parsed;
  } catch {
    /* data rusak: pakai default */
  }
  return initialPackages;
}

const frames = [
  { name: "Retro Blue", code: "01", className: "frame-blue", label: "SIGNATURE" },
  { name: "Tangerine Day", code: "02", className: "frame-orange", label: "NEW DROP" },
  { name: "Garden Club", code: "03", className: "frame-green", label: "LIMITED" },
  { name: "Pink Crush", code: "04", className: "frame-pink", label: "FAVORITE" },
];

const gallery = [
  { src: photos[0], cat: "Bestie", title: "Sunday with besties" },
  { src: photos[3], cat: "Couple", title: "Us being us" },
  { src: photos[4], cat: "Event", title: "Birthday chaos" },
  { src: photos[2], cat: "Wisuda", title: "Finally graduated!" },
  { src: photos[5], cat: "Bestie", title: "Main character energy" },
  { src: photos[1], cat: "Event", title: "A little celebration" },
];

function Brand({ light = false }: { light?: boolean }) {
  return <a className={`brand official-brand ${light ? "brand-light" : ""}`} href="/" aria-label="Aksata Cerita"><span className="brand-crop"><img src="/assets/aksata-logo.png" alt="Aksata Cerita" /></span></a>;
}

function PublicPage() {
  const [menu, setMenu] = useState(false);
  const [packages] = useState<Pkg[]>(loadPackages);
  const [filter, setFilter] = useState("Semua");
  const [activePackage, setActivePackage] = useState(0);
  const [booking, setBooking] = useState(false);
  const [selectedSession, setSelectedSession] = useState(() => loadPackages()[0]?.name ?? "Regular Photobox");
  const [progress, setProgress] = useState(0);
  const [boothIndex, setBoothIndex] = useState(0);
  const [countdown, setCountdown] = useState(0);
  const [snapping, setSnapping] = useState(false);
  const visible = filter === "Semua" ? gallery : gallery.filter((item) => item.cat === filter);
  useEffect(() => {
    const onScroll = () => setProgress((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);
  const snapPhoto = () => {
    if (snapping) return;
    setSnapping(true);
    setCountdown(3);
    window.setTimeout(() => setCountdown(2), 650);
    window.setTimeout(() => setCountdown(1), 1300);
    window.setTimeout(() => {
      setCountdown(0);
      setBoothIndex((current) => (current + 1) % photos.length);
    }, 1950);
    window.setTimeout(() => setSnapping(false), 2350);
  };

  return <main>
    <div className="scroll-progress" style={{ width: `${progress}%` }} />
    <div className="ticker"><span>STUDIO OPEN DAILY · 10.00—22.00 WIB</span><span>WALK IN WELCOME · LAST SESSION 21.30</span><span>STUDIO OPEN DAILY · 10.00—22.00 WIB</span></div>
    <nav className="nav shell">
      <Brand />
      <div className={`nav-links ${menu ? "open" : ""}`}>
        {["Beranda", "Paket", "Frame", "Galeri", "Lokasi"].map((item) => <a key={item} href={`#${item.toLowerCase()}`} onClick={() => setMenu(false)}>{item}</a>)}
        <a className="admin-link" href="/admin">Admin</a>
      </div>
      <button className="btn btn-primary nav-cta" onClick={() => setBooking(true)}>Pesan sesi <Icon name="arrow" /></button>
      <button className="menu-btn" onClick={() => setMenu(!menu)} aria-label="Buka menu"><Icon name={menu ? "x" : "menu"} /></button>
    </nav>

    <section className="hero shell" id="beranda">
      <div className="hero-copy">
        <div className="eyebrow"><span /><span>PHOTOBOOTH STUDIO · TULUNGAGUNG</span></div>
        <h1>Abadikan cerita<br />serumu di <em>Aksata.</em></h1>
        <p>Masuk, pilih pose paling random, lalu bawa pulang potongan cerita yang nggak akan terulang dua kali.</p>
        <div className="hero-actions">
          <a className="btn btn-primary" href="#paket">Lihat paket <Icon name="arrow" /></a>
          <a className="text-link" href="#galeri">Intip cerita mereka <span>↘</span></a>
        </div>
        <div className="hero-proof"><div className="avatar-stack">{photos.slice(0, 3).map((src) => <img src={src} key={src} alt="" />)}</div><div><b>4.9 / 5</b><span>disayang 2,000+ teman cerita</span></div></div>
      </div>
      <div className="hero-collage">
        <div className="sunburst" />
        <div className="flash flash-one">✦</div><div className="flash flash-two">✦</div>
        <div className="photo-card card-one"><img src={photos[0]} alt="Teman-teman berfoto bersama" /><span>say cheese!</span></div>
        <div className="photo-card card-two"><img src={photos[3]} alt="Pasangan berpose di studio" /><span>♥ 14.02.25</span></div>
        <div className="sticker sticker-blue">GOOD<br />TIMES!</div>
        <div className="sticker sticker-orange">#aksata<br />cerita</div>
        <div className="scribble">Our kind of<br /><b>FUN!</b></div>
      </div>
      <div className="scroll-note">SCROLL TO EXPLORE <span>↓</span></div>
    </section>

    <section className="manifesto">
      <div className="shell manifesto-inner"><p>Bukan cuma foto.</p><h2>Ini cara kita <span>menyimpan</span><br />hari-hari paling seru.</h2><div className="mini-note">NO AWKWARD POSES.<br />JUST BE YOU.</div></div>
    </section>
    <div className="moving-type"><div><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i><span>BE YOURSELF</span><i>✦</i><span>SMILE BIG</span><i>✦</i><span>KEEP THE STORY</span><i>✦</i></div></div>

    <section className="section shell" id="paket">
      <div className="section-heading"><div><span className="kicker">01 — PILIH CERITAMU</span><h2>Good moments,<br /><i>fair prices.</i></h2></div><p>Dari foto formal sampai pose paling chaos—ada sesi untuk setiap cerita.</p></div>
      <div className="package-grid">
        {packages.map((item, index) => <article key={item.id} className={`package-card ${activePackage === index ? "active" : ""} ${item.color}`} onMouseEnter={() => setActivePackage(index)} onClick={() => setActivePackage(index)}>
          <div className="package-top"><span>{item.tag}</span><Icon name={index === 0 ? "camera" : index === 1 ? "sparkle" : "grid"} size={28} /></div>
          <div><h3>{item.name}</h3><p>Mulai dari</p><strong><small>Rp</small>{item.price}</strong></div>
          <ul>{item.features.map((feature) => <li key={feature}><span>✓</span>{feature}</li>)}</ul>
          <button className="package-select" onClick={() => { setSelectedSession(item.name); setBooking(true); }}>Pilih paket <Icon name="arrow" /></button>
        </article>)}
      </div>
      <p className="package-footnote">Butuh photobooth untuk wedding, brand activation, atau event? <a href="https://wa.me/6285117375305">Let's collaborate ↗</a></p>
    </section>

    <section className="frames-section" id="frame">
      <div className="shell">
        <div className="section-heading compact"><div><span className="kicker">02 — FRAME OF THE MONTH</span><h2>Pilih mood.<br /><i>Cetak ceritamu.</i></h2></div><div className="limited-pill"><span>●</span> Updated monthly</div></div>
        <div className="frames-scroll">
          {frames.map((frame, i) => <article className="frame-wrap" key={frame.name}>
            <div className={`frame-strip ${frame.className}`}>
              <div className="frame-head"><b>AKSATA<br />CERITA</b><span>{frame.label}</span></div>
              {[photos[(i + 1) % photos.length], photos[(i + 2) % photos.length]].map((src) => <img src={src} alt="" key={src} />)}
              <div className="frame-bottom"><span>{frame.name}</span><b>0{frame.code} / 25</b></div>
            </div>
            <div className="frame-meta"><span>{frame.code}</span><h3>{frame.name}</h3><button aria-label={`Lihat ${frame.name}`}><Icon name="arrow" /></button></div>
          </article>)}
        </div>
      </div>
    </section>

    <section className="booth-lab">
      <div className="shell booth-grid">
        <div className="booth-copy">
          <span className="kicker">AKSATA PLAYGROUND — BETA</span>
          <h2>Try the booth.<br /><i>Strike a pose!</i></h2>
          <p>Pemanasan dulu sebelum datang ke studio. Tekan tombol shutter dan tunjukkan pose paling ikonikmu.</p>
          <div className="booth-tips"><span>01&nbsp; LOOK</span><span>02&nbsp; POSE</span><span>03&nbsp; SNAP!</span></div>
        </div>
        <div className={`booth-machine ${snapping && !countdown ? "is-flashing" : ""}`}>
          <div className="machine-top"><Brand /><span>LIVE PREVIEW <i /></span></div>
          <div className="booth-screen">
            <img src={photos[boothIndex]} alt="Preview photobooth interaktif" />
            <div className="screen-grid" />
            <span className="rec-badge">● REC</span>
            <span className="frame-counter">FRAME 0{boothIndex + 1}</span>
            {countdown > 0 && <strong className="countdown">{countdown}</strong>}
            <div className="screen-caption">YOU LOOK<br /><b>AMAZING!</b></div>
          </div>
          <div className="machine-controls">
            <div><span>FILTER</span><button className="color-dot blue" aria-label="Filter biru" /><button className="color-dot orange" aria-label="Filter oranye" /><button className="color-dot pink" aria-label="Filter merah muda" /></div>
            <button className="shutter" onClick={snapPhoto} disabled={snapping}><span><Icon name="camera" size={25} /></span>{snapping ? "Hold that pose..." : "Take a photo"}</button>
            <div className="machine-status"><i /> READY</div>
          </div>
          <div className="print-slot"><span>YOUR STORY COMES OUT HERE</span><i /></div>
        </div>
      </div>
    </section>

    <section className="section gallery-section shell" id="galeri">
      <div className="gallery-title"><span className="kicker">03 — TEMAN CERITA</span><h2>Real people.<br /><i>Real fun.</i></h2><p>Tag <b>@aksatacerita</b> supaya ceritamu bisa ikut tampil di sini.</p></div>
      <div className="filters">{["Semua", "Event", "Wisuda", "Couple", "Bestie"].map((cat) => <button onClick={() => setFilter(cat)} className={filter === cat ? "active" : ""} key={cat}>{cat}</button>)}</div>
      <div className="masonry">{visible.map((item, index) => <figure className={index % 3 === 1 ? "tall" : ""} key={item.src}><img src={item.src} alt={item.title} /><figcaption><span>{item.cat}</span><b>{item.title}</b></figcaption></figure>)}</div>
    </section>

    <section className="visit" id="lokasi">
      <div className="visit-map"><iframe title="Lokasi Aksatacerita" src="https://www.openstreetmap.org/export/embed.html?bbox=111.89%2C-8.09%2C111.94%2C-8.04&layer=mapnik" loading="lazy" /><div className="map-stamp"><Brand /><span>YOU'RE ALMOST HERE!</span></div></div>
      <div className="visit-copy">
        <span className="kicker">04 — COME SAY HI</span><h2>Sampai ketemu<br />di <i>studio!</i></h2>
        <div className="info-row"><Icon name="map" /><div><b>Ruma Pinara</b><p>Jl. I Gusti Ngurah Rai Gg. 8 No. 90,<br />Jepun, Tulungagung, Jawa Timur</p></div></div>
        <div className="info-row"><Icon name="clock" /><div><b>Setiap Hari</b><p>10.00 — 22.00 WIB<br /><small>Last session 21.30</small></p></div></div>
        <a className="btn btn-dark" href="https://maps.google.com/?q=Ruma+Pinara+Tulungagung" target="_blank" rel="noreferrer">Buka di Google Maps <Icon name="arrow" /></a>
      </div>
    </section>

    <section className="cta-section"><div className="cta-orbit orbit-one">SAY CHEESE · SAY CHEESE ·</div><div className="cta-stars">✦　✧　✦</div><h2>Ready to make<br />your own <i>story?</i></h2><p>Nggak perlu jago pose. Datang aja, sisanya biar kami yang bikin seru.</p><button className="btn btn-light" onClick={() => setBooking(true)}><Icon name="camera" /> Mulai ceritamu</button></section>
    <footer><div className="shell footer-top"><Brand light /><div><b>Explore</b><a href="#paket">Paket & Harga</a><a href="#frame">Katalog Frame</a><a href="#galeri">Galeri Momen</a></div><div><b>Social</b><a href="https://instagram.com/aksatacerita">@aksatacerita ↗</a><a href="https://wa.me/6285117375305">WhatsApp ↗</a></div><div className="footer-note">Made for<br /><i>good stories.</i></div></div><div className="shell footer-bottom"><span>© 2025 AKSATACERITA PHOTOBOOTH</span><span>TULUNGAGUNG, EAST JAVA</span></div></footer>
    <a className="floating-wa" href="https://wa.me/6285117375305" target="_blank" rel="noreferrer" aria-label="Chat WhatsApp"><Icon name="whatsapp" /></a>
    <div className={`booking-backdrop ${booking ? "show" : ""}`} onClick={() => setBooking(false)} />
    <aside className={`booking-drawer ${booking ? "show" : ""}`} aria-hidden={!booking}>
      <div className="booking-head"><div><span>RESERVE YOUR MOMENT</span><h2>Pesan sesi.</h2></div><button onClick={() => setBooking(false)} aria-label="Tutup"><Icon name="x" /></button></div>
      <div className="booking-step"><span>01</span><div><b>Pilih sesi favoritmu</b><p>Kamu bisa konfirmasi ulang lewat WhatsApp.</p></div></div>
      <div className="session-options">{packages.map((item) => <button key={item.id} className={selectedSession === item.name ? "active" : ""} onClick={() => setSelectedSession(item.name)}><span><Icon name="camera" /></span><div><b>{item.name}</b><small>Mulai Rp{item.price}</small></div><i>{selectedSession === item.name ? "✓" : "○"}</i></button>)}</div>
      <div className="booking-step"><span>02</span><div><b>Pilih waktu datang</b><p>Setiap hari, 10.00—22.00 WIB</p></div></div>
      <div className="date-chips"><button>Hari ini<small>Walk in</small></button><button className="active">Besok<small>Available</small></button><button>Pilih tanggal<small>Calendar ↗</small></button></div>
      <a className="btn btn-primary booking-submit" href={`https://wa.me/6285117375305?text=Halo%20Aksatacerita!%20Aku%20mau%20pesan%20${encodeURIComponent(selectedSession)}`} target="_blank" rel="noreferrer"><Icon name="whatsapp" /> Lanjut via WhatsApp <Icon name="arrow" /></a>
      <p className="booking-note">No payment needed now · Fast response during studio hours</p>
    </aside>
  </main>;
}

function AdminPage() {
  const [packages, setPackages] = useState<Pkg[]>(loadPackages);
  const [tab, setTab] = useState("Paket & Harga");
  const [toast, setToast] = useState(false);
  const [form, setForm] = useState({ name: "", price: "", description: "" });
  const nameRef = useRef<HTMLInputElement>(null);
  const showToast = () => { setToast(true); window.setTimeout(() => setToast(false), 1800); };
  const save = (next: Pkg[]) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
    setPackages(next); showToast();
  };
  const addPackage = (e: React.FormEvent) => {
    e.preventDefault();
    const name = form.name.trim();
    const price = form.price.trim().replace(/^rp\s*/i, "");
    if (!name || !price) return;
    const features = form.description.split(/\n|,/).map((f) => f.trim()).filter(Boolean);
    const colors = ["blue", "orange", "pink"];
    save([...packages, { id: Date.now(), name, price, tag: "New", color: colors[packages.length % colors.length], features: features.length ? features : ["15 menit sesi", "Soft files"] }]);
    setForm({ name: "", price: "", description: "" });
  };
  const nav = ["Overview", "Paket & Harga", "Katalog Frame", "Galeri Momen", "Info Studio"];
  return <div className="admin">
    <aside><Brand light /><div className="admin-studio"><span>A</span><div><b>Aksatacerita</b><small>Studio workspace</small></div></div><nav>{nav.map((item, index) => <button className={tab === item ? "active" : ""} onClick={() => setTab(item)} key={item}><Icon name={index === 0 ? "grid" : index === 1 ? "camera" : index === 2 ? "sparkle" : index === 3 ? "grid" : "map"} />{item}</button>)}</nav><a className="back-site" href="/">← Lihat website</a></aside>
    <div className="admin-main">
      <header><div><small>CONTENT MANAGER</small><h1>{tab}</h1></div><div className="admin-user"><span>Tersimpan di browser ini</span><b>AC</b></div></header>
      {tab === "Paket & Harga" || tab === "Overview" ? <div className="admin-content">
        <div className="stats"><article><span>Total paket</span><b>{packages.length}</b><small>Aktif di website</small></article><article><span>Frame aktif</span><b>{String(frames.length).padStart(2, "0")}</b><small>Katalog bulan ini</small></article><article><span>Galeri</span><b>{gallery.length}</b><small>Foto terpublikasi</small></article></div>
        <div className="admin-grid">
          <section className="panel"><div className="panel-head"><div><span>DAFTAR LAYANAN</span><h2>Paket & Harga</h2></div><button className="icon-button" type="button" aria-label="Tambah paket" onClick={() => nameRef.current?.focus()}><Icon name="plus" /></button></div>
            <div className="admin-list">{packages.map((item) => <div className="admin-item" key={item.id}><div className={`admin-thumb ${item.color}`}><Icon name="camera" /></div><div><b>{item.name}</b><small>{item.features.join(" · ")}</small></div><strong>Rp{item.price}</strong><button onClick={() => save(packages.filter((p) => p.id !== item.id))} aria-label="Hapus paket"><Icon name="trash" /></button></div>)}</div>
          </section>
          <form className="panel admin-form" onSubmit={addPackage}><div className="panel-head"><div><span>QUICK ACTION</span><h2>Tambah Paket</h2></div></div><label>Nama paket<input ref={nameRef} value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} placeholder="Contoh: Bestie Session" /></label><label>Harga mulai<input value={form.price} onChange={(e) => setForm({ ...form, price: e.target.value })} placeholder="Contoh: 50K" /></label><label>Deskripsi<textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} placeholder="Satu poin per baris, mis: 15 menit sesi" rows={4} /></label><button className="btn btn-primary" type="submit">Publikasikan <Icon name="arrow" /></button></form>
        </div>
      </div> : <div className="placeholder-panel"><div className="upload-visual"><Icon name={tab === "Info Studio" ? "map" : "plus"} size={36} /></div><span>MODUL KONTEN</span><h2>{tab}</h2><p>Area ini siap untuk mengunggah, menyusun, dan memperbarui konten studio.</p><button className="btn btn-primary" onClick={showToast}><Icon name="plus" /> Tambah konten baru</button></div>}
    </div>
    {toast && <div className="toast">✓ Perubahan berhasil disimpan</div>}
  </div>;
}

export default function App() {
  return /^\/admin(\/|$)/.test(window.location.pathname) ? <AdminPage /> : <PublicPage />;
}
