(function () {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const fire = (el) => el.dispatchEvent(new Event('input', { bubbles: true }));

  // Pilihan cepat: klik untuk mengisi kolom teks
  $$('[data-chips]').forEach((box) => {
    const input = document.getElementById(box.dataset.chips);
    box.addEventListener('click', (e) => {
      const b = e.target.closest('button[data-value]');
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
      setText(q('description'), f.description.value.trim());
      toggle(q('description'), f.description.value.trim() !== '');
      setText(q('note'), f.price_note.value.trim());
      toggle(q('note'), f.price_note.value.trim() !== '');
      setText(q('price'), money(price, style));
      setText(q('unit'), ' ' + f.price_unit.value.trim());
      toggle(q('unit'), f.price_unit.value.trim() !== '');
      setText(q('old'), 'Rp' + money(old, style));
      toggle(q('old'), old > 0);
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
    const strip = $('.frame-strip', frame);
    const original = q('image').getAttribute('src') || '';
    let blob = null;
    const update = () => {
      setText(q('name'), f.name.value.trim() || 'Nama frame');
      const label = f.label.value.trim().toUpperCase();
      setText(q('label'), label);
      toggle(q('label'), label !== '');
      strip.style.background = f.color.value;
      strip.style.color = textOn(f.color.value);
      const file = f.image.files && f.image.files[0];
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
      const file = f.image.files && f.image.files[0];
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
})();
