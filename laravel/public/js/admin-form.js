(function () {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const fire = (el) => el.dispatchEvent(new Event('input', { bubbles: true }));

  // Menu samping versi ikon (tablet): tekan ≡ untuk menampilkan nama menu
  const shell = $('#admin');
  const toggle0 = $('.rail-toggle');
  if (shell && toggle0) {
    const setRail = (open) => { shell.classList.toggle('rail-open', open); toggle0.setAttribute('aria-expanded', String(open)); };
    toggle0.addEventListener('click', () => setRail(!shell.classList.contains('rail-open')));
    document.addEventListener('click', (e) => { if (shell.classList.contains('rail-open') && !e.target.closest('.admin > aside')) setRail(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setRail(false); });
  }

  // Pilihan cepat: klik untuk mengisi kolom teks; × untuk menghapus pilihan
  const csrf = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  const removeOption = async (list, value) => {
    const res = await fetch(`/admin/pilihan/${list}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify({ value }),
    });
    return res.ok;
  };
  $$('[data-chips] .chip-x').forEach((x) => x.addEventListener('click', async (e) => {
    e.stopPropagation();
    const chip = x.closest('.chip');
    const box = x.closest('[data-chips]');
    x.disabled = true;
    if (await removeOption(box.dataset.list, chip.dataset.value)) chip.remove();
    else x.disabled = false;
  }));
  $$('[data-chips]').forEach((box) => {
    const input = document.getElementById(box.dataset.chips);
    box.addEventListener('click', (e) => {
      const b = e.target.closest('.chip > button[data-value]');
      if (!b || !input) return;
      input.value = b.dataset.value;
      fire(input);
      input.focus();
    });
  });

  // Warna: klik swatch, atau "+" untuk warna bebas
  $$('[data-swatches]').forEach((box) => {
    const input = $('input[type=hidden]', box);
    const custom = $('input[type=color]', box);
    const customWrap = custom.closest('.swatch-custom');
    const set = (hex) => {
      hex = hex.toLowerCase();
      input.value = hex;
      let known = false;
      $$('.swatch', box).forEach((s) => { const on = s.dataset.value === hex; known = known || on; s.classList.toggle('on', on); });
      customWrap.classList.toggle('on', !known);
      custom.value = hex;
      fire(input);
    };
    box.addEventListener('click', (e) => { const b = e.target.closest('.swatch[data-value]'); if (b) set(b.dataset.value); });
    custom.addEventListener('input', () => set(custom.value));
  });

  // Kategori galeri: tambah baru langsung di form, hapus dengan ×
  const cats = $('[data-cats]');
  if (cats) {
    const addInput = $('#new-cat');
    const make = (value) => {
      const wrap = document.createElement('span');
      wrap.className = 'chip-opt';
      wrap.dataset.value = value;
      wrap.dataset.isNew = '1';
      const label = document.createElement('label');
      label.className = 'seg-opt';
      const radio = document.createElement('input');
      radio.type = 'radio'; radio.name = 'category'; radio.value = value;
      const span = document.createElement('span');
      span.textContent = value;
      label.append(radio, span);
      const x = document.createElement('button');
      x.type = 'button'; x.className = 'chip-x'; x.textContent = '×'; x.title = 'Hapus kategori';
      x.setAttribute('aria-label', 'Hapus kategori ' + value);
      wrap.append(label, x);
      return wrap;
    };
    const add = () => {
      const value = addInput.value.trim().slice(0, 30);
      if (!value) return;
      let radio = $$('input[name="category"]', cats).find((r) => r.value.toLowerCase() === value.toLowerCase());
      if (!radio) { const chip = make(value); cats.append(chip); radio = $('input', chip); }
      radio.checked = true;
      addInput.value = '';
      radio.dispatchEvent(new Event('change', { bubbles: true }));
    };
    $('#add-cat').addEventListener('click', add);
    addInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); add(); } });
    cats.addEventListener('click', async (e) => {
      const x = e.target.closest('.chip-x');
      if (!x) return;
      const chip = x.closest('.chip-opt');
      const wasChecked = $('input', chip).checked;
      if (!chip.dataset.isNew && !(await removeOption(cats.dataset.list, chip.dataset.value))) return;
      chip.remove();
      const first = $('input[name="category"]', cats);
      if (wasChecked && first) { first.checked = true; first.dispatchEvent(new Event('change', { bubbles: true })); }
    });
  }

  const money = (n, style) => (style === 'k' && n >= 1000 && n % 1000 === 0)
    ? (n / 1000) + 'K'
    : n.toLocaleString('id-ID');
  const int = (v) => parseInt(String(v).replace(/\D+/g, ''), 10) || 0;
  const textOn = (hex) => {
    const n = parseInt(hex.slice(1), 16);
    const y = (((n >> 16) & 255) * 299 + ((n >> 8) & 255) * 587 + (n & 255) * 114) / 1000;
    return y >= 150 ? '#111111' : '#ffffff';
  };
  const setText = (el, text) => { if (el) el.textContent = text; };
  const toggle = (el, show) => { if (el) el.hidden = !show; };

  // ---------- Pratinjau paket ----------
  const pkg = $('[data-preview="package"]');
  if (pkg) {
    const form = pkg.closest('form');
    const f = form.elements;
    const q = (n) => $(`[data-p="${n}"]`, pkg);
    const update = () => {
      const price = int(f.price.value);
      const old = int(f.old_price.value);
      const style = f.price_style.value || 'k';
      const dur = int(f.duration_value.value);

      setText(q('name'), f.name.value.trim() || 'Nama paket');
      setText(q('tag'), f.tag.value.trim());
      toggle(q('tag'), f.tag.value.trim() !== '');
      setText(q('description'), f.description.value.trim());
      toggle(q('description'), f.description.value.trim() !== '');
      setText(q('note'), f.price_note.value.trim());
      toggle(q('note'), f.price_note.value.trim() !== '');
      setText(q('price'), money(price, style));
      setText(q('old'), 'Rp' + money(old, style));
      toggle(q('old'), old > 0);
      const addon = int(f.addon_price.value);
      setText(q('addon-price'), 'Rp' + money(addon, style));
      setText(q('addon-text'), f.addon_text.value.trim());
      toggle(q('addon'), addon > 0);
      setText(q('duration-text'), dur + ' ' + (f.duration_unit.value || 'menit'));
      toggle(q('duration'), dur > 0);

      const list = q('features');
      list.replaceChildren();
      f.features.value.split(/\r?\n|,/).map((x) => x.trim()).filter(Boolean).forEach((text) => {
        const li = document.createElement('li');
        const mark = document.createElement('span');
        mark.textContent = '✓';
        li.append(mark, document.createTextNode(text));
        list.append(li);
      });

      $('.package-card', pkg).style.setProperty('--card', f.color.value);

      // contoh tulisan harga di pilihan "cara menulis harga"
      const shown = price || 35000;
      $$('[data-opt="k"]', form).forEach((s) => { s.textContent = 'Rp' + money(shown, 'k'); });
      $$('[data-opt="rp"]', form).forEach((s) => { s.textContent = 'Rp' + money(shown, 'rp'); });
    };
    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
  }

  // ---------- Pratinjau frame ----------
  const frame = $('[data-preview="frame"]');
  if (frame) {
    const form = frame.closest('form');
    const f = form.elements;
    const q = (n) => $(`[data-p="${n}"]`, frame);
    const original = q('image').getAttribute('src') || '';
    let blob = null;
    const update = () => {
      setText(q('name'), f.name.value.trim() || 'Nama frame');
      const size = f.size.value.trim().toUpperCase();
      setText(q('size'), size);
      toggle(q('size'), size !== '');
      const label = f.label.value.trim().toUpperCase();
      setText(q('label'), label);
      toggle(q('label'), label !== '');
      const input = f['image[]'] || f.image;
      const file = input.files && input.files[0];
      if (blob) { URL.revokeObjectURL(blob); blob = null; }
      if (file) blob = URL.createObjectURL(file);
      const src = blob || original;
      if (src) q('image').src = src;
      toggle(q('image'), !!src);
      toggle(q('placeholder'), !src);
    };
    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
  }

  // ---------- Pratinjau galeri ----------
  const gal = $('[data-preview="gallery"]');
  if (gal) {
    const form = gal.closest('form');
    const f = form.elements;
    const q = (n) => $(`[data-p="${n}"]`, gal);
    const original = q('image').getAttribute('src') || '';
    let blob = null;
    const update = () => {
      setText(q('title'), f.title.value.trim() || 'Judul foto');
      setText(q('category'), f.category.value || '');
      const input = f['image[]'] || f.image;
      const file = input.files && input.files[0];
      if (blob) { URL.revokeObjectURL(blob); blob = null; }
      if (file) blob = URL.createObjectURL(file);
      const src = blob || original;
      if (src) q('image').src = src;
      toggle(q('image'), !!src);
      toggle(q('placeholder'), !src);
    };
    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
  }

  // ---------- Pratinjau pengaturan ----------
  const set = $('[data-preview="settings"]');
  if (set) {
    const form = set.closest('form');
    const f = form.elements;
    const fmt = {
      wa: (v) => { const d = v.replace(/\D+/g, ''); return d ? '+' + (d.startsWith('0') ? '62' + d.slice(1) : d) : ''; },
      ig: (v) => { const h = v.trim().replace(/^(https?:\/\/)?(www\.)?instagram\.com\//i, '').split(/[/?#]/)[0].replace(/^@/, ''); return h ? '@' + h : ''; },
    };
    const update = () => {
      $$('[data-s]', set).forEach((el) => {
        const field = f[el.dataset.s];
        if (!field) return;
        const raw = field.value.trim();
        el.textContent = el.dataset.sFmt ? fmt[el.dataset.sFmt](raw) : raw;
      });
      $$('[data-s-nonempty]', set).forEach((el) => { const field = f[el.dataset.sNonempty]; el.hidden = !(field && field.value.trim()); });
      $$('[data-s-if]', set).forEach((el) => { const field = f[el.dataset.sIf]; el.hidden = !(field && field.checked); });
    };
    form.addEventListener('input', update);
    form.addEventListener('change', update);
    update();
  }
})();
