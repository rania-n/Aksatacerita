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
const FRAMES_KEY = "aksata-frames";
const GALLERY_KEY = "aksata-gallery";
const INFO_KEY = "aksata-info";

function loadStored<T>(key: string, fallback: T, valid: (data: unknown) => boolean): T {
  try {
    const saved = localStorage.getItem(key);
    if (!saved) return fallback;
    const parsed = JSON.parse(saved);
    if (valid(parsed)) return parsed as T;
  } catch {
    /* data rusak: pakai default */
  }
  return fallback;
}

function saveStored(key: string, value: unknown): boolean {
  try {
    localStorage.setItem(key, JSON.stringify(value));
    return true;
  } catch {
    return false;
  }
}

const isArrayOf = (check: (x: any) => boolean) => (data: unknown) => Array.isArray(data) && data.every((x) => x && check(x));

function loadPackages(): Pkg[] {
  return loadStored(STORAGE_KEY, initialPackages, isArrayOf((p) => typeof p.name === "string" && Array.isArray(p.features)));
}

type Frame = { id: number; name: string; className: string; label: string };
const frameColors = [
  { className: "frame-blue", label: "Biru" },
  { className: "frame-orange", label: "Oranye" },
  { className: "frame-green", label: "Hijau" },
  { className: "frame-pink", label: "Pink" },
];
const defaultFrames: Frame[] = [
  { id: 1, name: "Retro Blue", className: "frame-blue", label: "SIGNATURE" },
  { id: 2, name: "Tangerine Day", className: "frame-orange", label: "NEW DROP" },
  { id: 3, name: "Garden Club", className: "frame-green", label: "LIMITED" },
  { id: 4, name: "Pink Crush", className: "frame-pink", label: "FAVORITE" },
];
const loadFrames = () => loadStored<Frame[]>(FRAMES_KEY, defaultFrames, isArrayOf((f) => typeof f.name === "string" && typeof f.className === "string"));

type GalleryItem = { id: number; src: string; cat: string; title: string };
const galleryCategories = ["Event", "Wisuda", "Couple", "Bestie"];
const defaultGallery: GalleryItem[] = [
  { id: 1, src: photos[0], cat: "Bestie", title: "Sunday with besties" },
  { id: 2, src: photos[3], cat: "Couple", title: "Us being us" },
  { id: 3, src: photos[4], cat: "Event", title: "Birthday chaos" },
  { id: 4, src: photos[2], cat: "Wisuda", title: "Finally graduated!" },
  { id: 5, src: photos[5], cat: "Bestie", title: "Main character energy" },
  { id: 6, src: photos[1], cat: "Event", title: "A little celebration" },
];
const loadGallery = () => loadStored<GalleryItem[]>(GALLERY_KEY, defaultGallery, isArrayOf((g) => typeof g.src === "string" && typeof g.title === "string"));

const defaultInfo = {
  place: "Ruma Pinara",
  address: "Jl. I Gusti Ngurah Rai Gg. 8 No. 90,\nJepun, Tulungagung, Jawa Timur",
  days: "Setiap Hari",
  hours: "10.00 — 22.00 WIB",
  lastSession: "Last session 21.30",
  whatsapp: "6285117375305",
  instagram: "aksatacerita",
  mapsQuery: "Ruma Pinara Tulungagung",
};
type Info = typeof defaultInfo;
const loadInfo = (): Info => ({ ...defaultInfo, ...loadStored<Partial<Info>>(INFO_KEY, {}, (d) => !!d && typeof d === "object" && !Array.isArray(d)) });

// Kecilkan gambar upload supaya muat di localStorage
function fileToDataUrl(file: File, maxSize = 900): Promise<string> {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(new Error("read"));
    reader.onload = () => {
      const img = new Image();
      img.onerror = () => reject(new Error("image"));
      img.onload = () => {
        const scale = Math.min(1, maxSize / Math.max(img.width, img.height));
        const canvas = document.createElement("canvas");
        canvas.width = Math.round(img.width * scale);
        canvas.height = Math.round(img.height * scale);
        canvas.getContext("2d")!.drawImage(img, 0, 0, canvas.width, canvas.height);
        resolve(canvas.toDataURL("image/jpeg", 0.8));
      };
      img.src = reader.result as string;
    };
    reader.readAsDataURL(file);
  });
}

function Brand({ light = false }: { light?: boolean }) {
  return <a className={`brand official-brand ${light ? "brand-light" : ""}`} href="/" aria-label="Aksata Cerita"><span className="brand-crop"><img src="/assets/aksata-logo.png" alt="Aksata Cerita" /></span></a>;
}

function PublicPage() {
  const [menu, setMenu] = useState(false);
  const [packages] = useState<Pkg[]>(loadPackages);
  const [frames] = useState<Frame[]>(loadFrames);
  const [gallery] = useState<GalleryItem[]>(loadGallery);
  const [info] = useState<Info>(loadInfo);
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
      <p className="package-footnote">Butuh photobooth untuk wedding, brand activation, atau event? <a href={`https://wa.me/${info.whatsapp}`}>Let's collaborate ↗</a></p>
    </section>

    <section className="frames-section" id="frame">
      <div className="shell">
        <div className="section-heading compact"><div><span className="kicker">02 — FRAME OF THE MONTH</span><h2>Pilih mood.<br /><i>Cetak ceritamu.</i></h2></div><div className="limited-pill"><span>●</span> Updated monthly</div></div>
        <div className="frames-scroll">
          {frames.map((frame, i) => <article className="frame-wrap" key={frame.id}>
            <div className={`frame-strip ${frame.className}`}>
              <div className="frame-head"><b>AKSATA<br />CERITA</b><span>{frame.label}</span></div>
              {[photos[(i + 1) % photos.length], photos[(i + 2) % photos.length]].map((src) => <img src={src} alt="" key={src} />)}
              <div className="frame-bottom"><span>{frame.name}</span><b>{String(i + 1).padStart(2, "0")} / 25</b></div>
            </div>
            <div className="frame-meta"><span>{String(i + 1).padStart(2, "0")}</span><h3>{frame.name}</h3><button aria-label={`Lihat ${frame.name}`}><Icon name="arrow" /></button></div>
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
      <div className="masonry">{visible.map((item, index) => <figure className={index % 3 === 1 ? "tall" : ""} key={item.id}><img src={item.src} alt={item.title} /><figcaption><span>{item.cat}</span><b>{item.title}</b></figcaption></figure>)}</div>
    </section>

    <section className="visit" id="lokasi">
      <div className="visit-map"><iframe title="Lokasi Aksatacerita" src="https://www.openstreetmap.org/export/embed.html?bbox=111.89%2C-8.09%2C111.94%2C-8.04&layer=mapnik" loading="lazy" /><div className="map-stamp"><Brand /><span>YOU'RE ALMOST HERE!</span></div></div>
      <div className="visit-copy">
        <span className="kicker">04 — COME SAY HI</span><h2>Sampai ketemu<br />di <i>studio!</i></h2>
        <div className="info-row"><Icon name="map" /><div><b>{info.place}</b><p>{info.address.split("\n").map((line, i) => <span key={i}>{i > 0 && <br />}{line}</span>)}</p></div></div>
        <div className="info-row"><Icon name="clock" /><div><b>{info.days}</b><p>{info.hours}{info.lastSession && <><br /><small>{info.lastSession}</small></>}</p></div></div>
        <a className="btn btn-dark" href={`https://maps.google.com/?q=${encodeURIComponent(info.mapsQuery)}`} target="_blank" rel="noreferrer">Buka di Google Maps <Icon name="arrow" /></a>
      </div>
    </section>

    <section className="cta-section"><div className="cta-orbit orbit-one">SAY CHEESE · SAY CHEESE ·</div><div className="cta-stars">✦　✧　✦</div><h2>Ready to make<br />your own <i>story?</i></h2><p>Nggak perlu jago pose. Datang aja, sisanya biar kami yang bikin seru.</p><button className="btn btn-light" onClick={() => setBooking(true)}><Icon name="camera" /> Mulai ceritamu</button></section>
    <footer><div className="shell footer-top"><Brand light /><div><b>Explore</b><a href="#paket">Paket & Harga</a><a href="#frame">Katalog Frame</a><a href="#galeri">Galeri Momen</a></div><div><b>Social</b><a href={`https://instagram.com/${info.instagram}`}>@{info.instagram} ↗</a><a href={`https://wa.me/${info.whatsapp}`}>WhatsApp ↗</a></div><div className="footer-note">Made for<br /><i>good stories.</i></div></div><div className="shell footer-bottom"><span>© 2025 AKSATACERITA PHOTOBOOTH</span><span>TULUNGAGUNG, EAST JAVA</span></div></footer>
    <a className="floating-wa" href={`https://wa.me/${info.whatsapp}`} target="_blank" rel="noreferrer" aria-label="Chat WhatsApp"><Icon name="whatsapp" /></a>
    <div className={`booking-backdrop ${booking ? "show" : ""}`} onClick={() => setBooking(false)} />
    <aside className={`booking-drawer ${booking ? "show" : ""}`} aria-hidden={!booking}>
      <div className="booking-head"><div><span>RESERVE YOUR MOMENT</span><h2>Pesan sesi.</h2></div><button onClick={() => setBooking(false)} aria-label="Tutup"><Icon name="x" /></button></div>
      <div className="booking-step"><span>01</span><div><b>Pilih sesi favoritmu</b><p>Kamu bisa konfirmasi ulang lewat WhatsApp.</p></div></div>
      <div className="session-options">{packages.map((item) => <button key={item.id} className={selectedSession === item.name ? "active" : ""} onClick={() => setSelectedSession(item.name)}><span><Icon name="camera" /></span><div><b>{item.name}</b><small>Mulai Rp{item.price}</small></div><i>{selectedSession === item.name ? "✓" : "○"}</i></button>)}</div>
      <div className="booking-step"><span>02</span><div><b>Pilih waktu datang</b><p>Setiap hari, 10.00—22.00 WIB</p></div></div>
      <div className="date-chips"><button>Hari ini<small>Walk in</small></button><button className="active">Besok<small>Available</small></button><button>Pilih tanggal<small>Calendar ↗</small></button></div>
      <a className="btn btn-primary booking-submit" href={`https://wa.me/${info.whatsapp}?text=Halo%20Aksatacerita!%20Aku%20mau%20pesan%20${encodeURIComponent(selectedSession)}`} target="_blank" rel="noreferrer"><Icon name="whatsapp" /> Lanjut via WhatsApp <Icon name="arrow" /></a>
      <p className="booking-note">No payment needed now · Fast response during studio hours</p>
    </aside>
  </main>;
}

function AdminPage() {
  const [packages, setPackages] = useState<Pkg[]>(loadPackages);
  const [frames, setFrames] = useState<Frame[]>(loadFrames);
  const [gallery, setGallery] = useState<GalleryItem[]>(loadGallery);
  const [info, setInfo] = useState<Info>(loadInfo);
  const [tab, setTab] = useState("Overview");
  const [toast, setToast] = useState<{ text: string; error?: boolean } | null>(null);
  const [form, setForm] = useState({ name: "", price: "", description: "" });
  const [frameForm, setFrameForm] = useState({ name: "", label: "NEW DROP", className: "frame-blue" });
  const [galleryForm, setGalleryForm] = useState({ title: "", cat: "Event", src: "" });
  const nameRef = useRef<HTMLInputElement>(null);
  const frameRef = useRef<HTMLInputElement>(null);
  const galleryRef = useRef<HTMLInputElement>(null);
  const showToast = (text = "Perubahan berhasil disimpan", error = false) => { setToast({ text, error }); window.setTimeout(() => setToast(null), 2200); };
  const persist = (key: string, value: unknown, apply: () => void) => {
    if (!saveStored(key, value)) return showToast("Gagal menyimpan: penyimpanan browser penuh", true);
    apply(); showToast();
  };
  const savePackages = (next: Pkg[]) => persist(STORAGE_KEY, next, () => setPackages(next));
  const saveFrames = (next: Frame[]) => persist(FRAMES_KEY, next, () => setFrames(next));
  const saveGallery = (next: GalleryItem[]) => persist(GALLERY_KEY, next, () => setGallery(next));
  const addPackage = (e: React.FormEvent) => {
    e.preventDefault();
    const name = form.name.trim();
    const price = form.price.trim().replace(/^rp\s*/i, "");
    if (!name || !price) return;
    const features = form.description.split(/\n|,/).map((f) => f.trim()).filter(Boolean);
    const colors = ["blue", "orange", "pink"];
    savePackages([...packages, { id: Date.now(), name, price, tag: "New", color: colors[packages.length % colors.length], features: features.length ? features : ["15 menit sesi", "Soft files"] }]);
    setForm({ name: "", price: "", description: "" });
  };
  const addFrame = (e: React.FormEvent) => {
    e.preventDefault();
    const name = frameForm.name.trim();
    if (!name) return;
    saveFrames([...frames, { id: Date.now(), name, className: frameForm.className, label: frameForm.label.trim().toUpperCase() || "NEW DROP" }]);
    setFrameForm({ name: "", label: "NEW DROP", className: "frame-blue" });
  };
  const pickPhoto = async (file?: File) => {
    if (!file) return;
    try { setGalleryForm((g) => ({ ...g, src: "" })); const src = await fileToDataUrl(file); setGalleryForm((g) => ({ ...g, src })); }
    catch { showToast("File bukan gambar yang valid", true); }
  };
  const addGallery = (e: React.FormEvent) => {
    e.preventDefault();
    const title = galleryForm.title.trim();
    if (!galleryForm.src) return showToast("Pilih foto dulu", true);
    if (!title) return;
    saveGallery([{ id: Date.now(), src: galleryForm.src, cat: galleryForm.cat, title }, ...gallery]);
    setGalleryForm({ title: "", cat: galleryForm.cat, src: "" });
  };
  const saveInfo = (e: React.FormEvent) => {
    e.preventDefault();
    const clean: Info = { ...info, whatsapp: info.whatsapp.replace(/\D/g, ""), instagram: info.instagram.replace(/^@/, "").trim() };
    persist(INFO_KEY, clean, () => setInfo(clean));
  };
  const resetAll = () => {
    if (!window.confirm("Kembalikan semua konten ke bawaan?")) return;
    [STORAGE_KEY, FRAMES_KEY, GALLERY_KEY, INFO_KEY].forEach((k) => localStorage.removeItem(k));
    setPackages(initialPackages); setFrames(defaultFrames); setGallery(defaultGallery); setInfo(defaultInfo); showToast("Konten dikembalikan ke bawaan");
  };
  const goAdd = (target: string, ref: React.RefObject<HTMLInputElement | null>) => { setTab(target); window.setTimeout(() => ref.current?.focus(), 0); };
  const setI = (key: keyof Info) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => setInfo({ ...info, [key]: e.target.value });
  const nav = ["Overview", "Paket & Harga", "Katalog Frame", "Galeri Momen", "Info Studio"];
  const stats = <div className="stats"><article><span>Total paket</span><b>{packages.length}</b><small>Aktif di website</small></article><article><span>Frame aktif</span><b>{String(frames.length).padStart(2, "0")}</b><small>Katalog bulan ini</small></article><article><span>Galeri</span><b>{gallery.length}</b><small>Foto terpublikasi</small></article></div>;
  return <div className="admin">
    <aside><Brand light /><div className="admin-studio"><span>A</span><div><b>Aksatacerita</b><small>Studio workspace</small></div></div><nav>{nav.map((item, index) => <button className={tab === item ? "active" : ""} onClick={() => setTab(item)} key={item}><Icon name={index === 0 ? "grid" : index === 1 ? "camera" : index === 2 ? "sparkle" : index === 3 ? "grid" : "map"} />{item}</button>)}</nav><a className="back-site" href="/">← Lihat website</a></aside>
    <div className="admin-main">
      <header><div><small>CONTENT MANAGER</small><h1>{tab}</h1></div><div className="admin-user"><span>Tersimpan di browser ini</span><b>AC</b></div></header>

      {tab === "Overview" && <div className="admin-content">
        {stats}
        <div className="admin-grid">
          <section className="panel"><div className="panel-head"><div><span>RINGKASAN</span><h2>Kelola konten</h2></div></div>
            <div className="admin-list">{[["Paket & Harga", `${packages.length} paket`], ["Katalog Frame", `${frames.length} frame`], ["Galeri Momen", `${gallery.length} foto`], ["Info Studio", info.place]].map(([t, d]) => <div className="admin-item overview-item" key={t}><div className="admin-thumb blue"><Icon name="grid" /></div><div><b>{t}</b><small>{d}</small></div><span /><button onClick={() => setTab(t)} aria-label={`Buka ${t}`}><Icon name="arrow" /></button></div>)}</div>
          </section>
          <section className="panel admin-form"><div className="panel-head"><div><span>PENGATURAN</span><h2>Reset konten</h2></div></div><p className="admin-hint">Data disimpan di browser ini. Reset akan mengembalikan paket, frame, galeri, dan info studio ke bawaan.</p><button className="btn btn-dark" type="button" onClick={resetAll}>Kembalikan ke bawaan</button></section>
        </div>
      </div>}

      {tab === "Paket & Harga" && <div className="admin-content">
        {stats}
        <div className="admin-grid">
          <section className="panel"><div className="panel-head"><div><span>DAFTAR LAYANAN</span><h2>Paket & Harga</h2></div><button className="icon-button" type="button" aria-label="Tambah paket" onClick={() => nameRef.current?.focus()}><Icon name="plus" /></button></div>
            <div className="admin-list">{packages.map((item) => <div className="admin-item" key={item.id}><div className={`admin-thumb ${item.color}`}><Icon name="camera" /></div><div><b>{item.name}</b><small>{item.features.join(" · ")}</small></div><strong>Rp{item.price}</strong><button onClick={() => savePackages(packages.filter((p) => p.id !== item.id))} aria-label="Hapus paket"><Icon name="trash" /></button></div>)}</div>
          </section>
          <form className="panel admin-form" onSubmit={addPackage}><div className="panel-head"><div><span>QUICK ACTION</span><h2>Tambah Paket</h2></div></div><label>Nama paket<input ref={nameRef} value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} placeholder="Contoh: Bestie Session" /></label><label>Harga mulai<input value={form.price} onChange={(e) => setForm({ ...form, price: e.target.value })} placeholder="Contoh: 50K" /></label><label>Deskripsi<textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} placeholder="Satu poin per baris, mis: 15 menit sesi" rows={4} /></label><button className="btn btn-primary" type="submit">Publikasikan <Icon name="arrow" /></button></form>
        </div>
      </div>}

      {tab === "Katalog Frame" && <div className="admin-content">
        <div className="admin-grid">
          <section className="panel"><div className="panel-head"><div><span>KATALOG</span><h2>Frame aktif</h2></div><button className="icon-button" type="button" aria-label="Tambah frame" onClick={() => frameRef.current?.focus()}><Icon name="plus" /></button></div>
            <div className="admin-list">{frames.length === 0 && <p className="admin-hint">Belum ada frame. Tambahkan lewat form.</p>}{frames.map((f) => <div className="admin-item" key={f.id}><div className={`admin-frame-swatch ${f.className}`} /><div><b>{f.name}</b><small>{f.label}</small></div><span /><button onClick={() => saveFrames(frames.filter((x) => x.id !== f.id))} aria-label="Hapus frame"><Icon name="trash" /></button></div>)}</div>
          </section>
          <form className="panel admin-form" onSubmit={addFrame}><div className="panel-head"><div><span>QUICK ACTION</span><h2>Tambah Frame</h2></div></div><label>Nama frame<input ref={frameRef} value={frameForm.name} onChange={(e) => setFrameForm({ ...frameForm, name: e.target.value })} placeholder="Contoh: Sunset Vibes" /></label><label>Label<input value={frameForm.label} onChange={(e) => setFrameForm({ ...frameForm, label: e.target.value })} placeholder="Contoh: NEW DROP" /></label><label>Warna<select value={frameForm.className} onChange={(e) => setFrameForm({ ...frameForm, className: e.target.value })}>{frameColors.map((c) => <option key={c.className} value={c.className}>{c.label}</option>)}</select></label><button className="btn btn-primary" type="submit">Publikasikan <Icon name="arrow" /></button></form>
        </div>
      </div>}

      {tab === "Galeri Momen" && <div className="admin-content">
        <div className="admin-grid">
          <section className="panel"><div className="panel-head"><div><span>GALERI</span><h2>Foto terpublikasi</h2></div><button className="icon-button" type="button" aria-label="Tambah foto" onClick={() => galleryRef.current?.focus()}><Icon name="plus" /></button></div>
            <div className="admin-list">{gallery.length === 0 && <p className="admin-hint">Belum ada foto. Unggah lewat form.</p>}{gallery.map((g) => <div className="admin-item" key={g.id}><img className="admin-photo" src={g.src} alt="" /><div><b>{g.title}</b><small>{g.cat}</small></div><span /><button onClick={() => saveGallery(gallery.filter((x) => x.id !== g.id))} aria-label="Hapus foto"><Icon name="trash" /></button></div>)}</div>
          </section>
          <form className="panel admin-form" onSubmit={addGallery}><div className="panel-head"><div><span>QUICK ACTION</span><h2>Tambah Foto</h2></div></div><label>Foto<input ref={galleryRef} type="file" accept="image/*" onChange={(e) => { pickPhoto(e.target.files?.[0]); e.target.value = ""; }} /></label>{galleryForm.src && <img className="admin-preview" src={galleryForm.src} alt="Pratinjau" />}<label>Judul<input value={galleryForm.title} onChange={(e) => setGalleryForm({ ...galleryForm, title: e.target.value })} placeholder="Contoh: Sunday with besties" /></label><label>Kategori<select value={galleryForm.cat} onChange={(e) => setGalleryForm({ ...galleryForm, cat: e.target.value })}>{galleryCategories.map((c) => <option key={c}>{c}</option>)}</select></label><button className="btn btn-primary" type="submit">Publikasikan <Icon name="arrow" /></button></form>
        </div>
      </div>}

      {tab === "Info Studio" && <div className="admin-content">
        <form className="panel admin-form admin-info" onSubmit={saveInfo}><div className="panel-head"><div><span>PROFIL STUDIO</span><h2>Info Studio</h2></div></div>
          <label>Nama tempat<input value={info.place} onChange={setI("place")} /></label>
          <label>Alamat<textarea rows={3} value={info.address} onChange={setI("address")} /></label>
          <label>Hari buka<input value={info.days} onChange={setI("days")} /></label>
          <label>Jam buka<input value={info.hours} onChange={setI("hours")} /></label>
          <label>Keterangan sesi terakhir<input value={info.lastSession} onChange={setI("lastSession")} /></label>
          <label>WhatsApp (format 62...)<input value={info.whatsapp} onChange={setI("whatsapp")} inputMode="numeric" /></label>
          <label>Instagram<input value={info.instagram} onChange={setI("instagram")} /></label>
          <label>Kata kunci Google Maps<input value={info.mapsQuery} onChange={setI("mapsQuery")} /></label>
          <button className="btn btn-primary" type="submit">Simpan perubahan <Icon name="arrow" /></button>
        </form>
      </div>}
    </div>
    {toast && <div className={`toast ${toast.error ? "error" : ""}`}>{toast.error ? "✕" : "✓"} {toast.text}</div>}
  </div>;
}

export default function App() {
  return /^\/admin(\/|$)/.test(window.location.pathname) ? <AdminPage /> : <PublicPage />;
}
