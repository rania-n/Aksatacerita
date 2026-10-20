(function () {
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  // Scroll progress
  const bar = $('.scroll-progress');
  const onScroll = () => {
    const max = document.documentElement.scrollHeight - window.innerHeight;
    bar.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile menu
  const links = $('.nav-links');
  const menuBtn = $('.menu-btn');
  menuBtn.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    menuBtn.dataset.open = open;
  });
  $$('a', links).forEach((a) => a.addEventListener('click', () => links.classList.remove('open')));

  // Package cards
  const cards = $$('.package-card');
  cards.forEach((card) => {
    const activate = () => { cards.forEach((c) => c.classList.toggle('active', c === card)); };
    card.addEventListener('mouseenter', activate);
    card.addEventListener('click', activate);
  });

  // Booking drawer
  const drawer = $('.booking-drawer');
  const backdrop = $('.booking-backdrop');
  const submit = $('.booking-submit');
  const options = $$('.session-options > button');
  const setBooking = (open) => {
    drawer.classList.toggle('show', open);
    backdrop.classList.toggle('show', open);
    drawer.setAttribute('aria-hidden', String(!open));
  };
  const selectSession = (name) => {
    let found = false;
    options.forEach((o) => {
      const on = o.dataset.name === name;
      found = found || on;
      o.classList.toggle('active', on);
      $('i', o).textContent = on ? '✓' : '○';
    });
    if (found) submit.href = submit.dataset.base + encodeURIComponent(name);
  };
  $$('[data-open-booking]').forEach((b) => b.addEventListener('click', () => {
    if (b.dataset.session) selectSession(b.dataset.session);
    setBooking(true);
  }));
  $$('[data-close-booking]').forEach((b) => b.addEventListener('click', () => setBooking(false)));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setBooking(false); });
  options.forEach((o) => o.addEventListener('click', () => selectSession(o.dataset.name)));
  if (options[0]) selectSession(options[0].dataset.name);

  // Date chips
  const chips = $$('.date-chips button');
  chips.forEach((c) => c.addEventListener('click', () => chips.forEach((x) => x.classList.toggle('active', x === c))));

  // Gallery filter
  const filters = $$('.filters button');
  const figures = $$('.masonry figure');
  const applyFilter = (cat) => {
    filters.forEach((f) => f.classList.toggle('active', f.dataset.cat === cat));
    let n = 0;
    figures.forEach((fig) => {
      const show = cat === 'Semua' || fig.dataset.cat === cat;
      fig.hidden = !show;
      fig.classList.toggle('tall', show && n++ % 3 === 1);
    });
  };
  filters.forEach((f) => f.addEventListener('click', () => applyFilter(f.dataset.cat)));
  if (filters.length) applyFilter('Semua');

  // Photobooth playground
  const machine = $('.booth-machine');
  if (machine) {
    const photos = JSON.parse(machine.dataset.photos || '[]');
    const img = $('.booth-screen > img', machine);
    const counter = $('.frame-counter', machine);
    const shutter = $('.shutter', machine);
    const label = $('.shutter-label', machine);
    const screen = $('.booth-screen', machine);
    let index = 0;
    let busy = false;
    shutter.addEventListener('click', () => {
      if (busy || !photos.length) return;
      busy = true;
      shutter.disabled = true;
      label.textContent = 'Hold that pose...';
      const show = (n) => {
        let el = $('.countdown', screen);
        if (!n) { if (el) el.remove(); return; }
        if (!el) { el = document.createElement('strong'); el.className = 'countdown'; screen.appendChild(el); }
        el.textContent = n;
        el.style.animation = 'none'; el.offsetWidth; el.style.animation = '';
      };
      show(3);
      setTimeout(() => show(2), 650);
      setTimeout(() => show(1), 1300);
      setTimeout(() => {
        show(0);
        index = (index + 1) % photos.length;
        img.src = photos[index];
        counter.textContent = 'FRAME 0' + (index + 1);
        machine.classList.add('is-flashing');
      }, 1950);
      setTimeout(() => {
        machine.classList.remove('is-flashing');
        busy = false;
        shutter.disabled = false;
        label.textContent = 'Take a photo';
      }, 2400);
    });
  }
})();
