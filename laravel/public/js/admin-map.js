(function () {
  const box = document.getElementById('map-picker');
  if (!box || !window.L) return;

  const lat = document.getElementById('f-latitude');
  const lng = document.getElementById('f-longitude');
  const status = document.getElementById('map-status');
  const search = document.getElementById('map-search');
  const DEFAULT = [-8.0412672, 111.9158272];

  L.Icon.Default.imagePath = box.dataset.icons || '/vendor/leaflet/images/';

  const parse = (v) => { const n = parseFloat(String(v).replace(',', '.')); return Number.isFinite(n) ? n : null; };
  const start = [parse(box.dataset.lat), parse(box.dataset.lng)];
  const hasStart = start[0] !== null && start[1] !== null;

  const map = L.map(box).setView(hasStart ? start : DEFAULT, hasStart ? 17 : 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(map);

  const marker = L.marker(hasStart ? start : DEFAULT, { draggable: true }).addTo(map);

  const setPoint = (p, zoom) => {
    marker.setLatLng(p);
    lat.value = p.lat.toFixed(6);
    lng.value = p.lng.toFixed(6);
    if (zoom) map.setView(p, zoom);
  };

  map.on('click', (e) => setPoint(e.latlng));
  marker.on('dragend', () => setPoint(marker.getLatLng()));

  const fromInputs = () => {
    const a = parse(lat.value), b = parse(lng.value);
    if (a === null || b === null || Math.abs(a) > 90 || Math.abs(b) > 180) return;
    const p = L.latLng(a, b);
    marker.setLatLng(p);
    map.setView(p, Math.max(map.getZoom(), 16));
  };
  lat.addEventListener('change', fromInputs);
  lng.addEventListener('change', fromInputs);

  const say = (t) => { status.textContent = t; };

  const find = async () => {
    const q = search.value.trim() || (document.getElementById('f-address').value || '').replace(/\s+/g, ' ');
    if (!q) return say('Ketik nama tempat atau alamat dulu.');
    say('Mencari...');
    try {
      const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (!data.length) return say('Tidak ketemu. Coba kata kunci lain, atau klik langsung di peta.');
      setPoint(L.latLng(parseFloat(data[0].lat), parseFloat(data[0].lon)), 17);
      say('Ditemukan: ' + data[0].display_name + '. Geser pin bila kurang pas.');
    } catch (e) {
      say('Pencarian gagal. Klik langsung di peta untuk menandai lokasi.');
    }
  };
  document.getElementById('map-find').addEventListener('click', find);
  search.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); find(); } });

  document.getElementById('map-locate').addEventListener('click', () => {
    if (!navigator.geolocation) return say('Browser ini tidak mendukung lokasi.');
    say('Mengambil lokasi...');
    navigator.geolocation.getCurrentPosition(
      (pos) => { setPoint(L.latLng(pos.coords.latitude, pos.coords.longitude), 18); say('Lokasi Anda ditandai. Geser pin bila kurang pas.'); },
      () => say('Izin lokasi ditolak. Klik langsung di peta untuk menandai lokasi.'),
      { enableHighAccuracy: true, timeout: 10000 },
    );
  });

  // Tab tersembunyi / ukuran berubah
  setTimeout(() => map.invalidateSize(), 200);
})();
