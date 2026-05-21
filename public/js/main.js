/* ── Modal ─────────────────────────────────────── */
function openModal(id) {
  document.getElementById(id).classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeModal(id) {
  document.getElementById(id).classList.remove('show');
  document.body.style.overflow = '';
}

function closeModalOutside(e, id) {
  if (e.target.id === id) closeModal(id);
}

/* ── Edit Modal ────────────────────────────────── */
function openEdit(key) {
  document.getElementById('edit-key-code').value  = key.key_code;
  document.getElementById('edit-resource').value  = key.resource;
  document.getElementById('edit-active').value    = key.is_active ? '1' : '0';
  document.getElementById('edit-notes').value     = key.notes || '';
  document.getElementById('edit-max-devices').value = key.max_devices || 1;
  document.getElementById('edit-reset-devices').checked = false;

  // Render daftar serials
  let serials = [];
  try { serials = JSON.parse(key.device_serials || '[]'); } catch {}
  const box = document.getElementById('edit-serials-list');
  if (serials.length === 0) {
    box.innerHTML = '<span class="text-muted" style="font-size:.78rem">Belum ada device terdaftar</span>';
  } else {
    box.innerHTML = serials.map((s, i) =>
      `<div class="serial-item">
        <span class="serial-dot"></span>
        <code style="font-size:.75rem;color:var(--text)">${s}</code>
        <span class="serial-idx">#${i+1}</span>
      </div>`
    ).join('');
  }

  if (key.expires_at) {
    const d = new Date(Number(key.expires_at) * 1000);
    const pad = n => String(n).padStart(2, '0');
    const local = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    document.getElementById('edit-expires').value = local;
  }

  document.getElementById('edit-form').action = `/admin/keys/${key.id}/edit`;
  openModal('modal-edit');
}

/* ── Delete Modal ──────────────────────────────── */
function openDelete(id, code) {
  document.getElementById('delete-key-display').textContent = code;
  document.getElementById('delete-form').action = `/admin/keys/${id}/delete`;
  openModal('modal-delete');
}

/* ── Copy key code ─────────────────────────────── */
function copyText(text, el) {
  navigator.clipboard.writeText(text).then(() => {
    el.classList.add('copied');
    showToast('Copied: ' + text);
    setTimeout(() => el.classList.remove('copied'), 1500);
  }).catch(() => {
    // Fallback
    const t = document.createElement('textarea');
    t.value = text;
    document.body.appendChild(t);
    t.select();
    document.execCommand('copy');
    document.body.removeChild(t);
    showToast('Copied!');
  });
}

/* ── Mobile viewport helper (fix 100vh issues on mobile) ───────────────── */
function setVh() {
  try {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  } catch (e) {}
}
setVh();
window.addEventListener('resize', setVh);

/* Adjust background fitting for mobile/portrait devices */
function adjustBackgroundFit() {
  try {
    const bg = document.querySelector('.imgui-skin .login-bg');
    if (!bg) return;
    const w = window.innerWidth;
    const h = window.innerHeight;
    // If portrait on small screens, prefer 'contain' so image isn't cropped
    if (w <= 900 && h > w) {
      bg.classList.add('bg-contain');
    } else {
      bg.classList.remove('bg-contain');
    }
  } catch (e) {}
}
adjustBackgroundFit();
window.addEventListener('resize', adjustBackgroundFit);
window.addEventListener('orientationchange', adjustBackgroundFit);

/* ── Asset installer / loader UI helpers ─────────────────────────────── */
function showAssetLoader() {
  const el = document.getElementById('asset-loader');
  if (el) el.style.display = 'flex';
}
function hideAssetLoader() {
  const el = document.getElementById('asset-loader');
  if (el) el.style.display = 'none';
}
function setLoaderProgress(pct, sub) {
  const fill = document.getElementById('loader-fill');
  const subEl = document.getElementById('loader-sub');
  if (fill) fill.style.width = `${Math.round(pct)}%`;
  if (subEl) subEl.textContent = sub || `Installing assets... ${Math.round(pct)}%`;
}
function loadImages(urls, onProgress, onComplete) {
  if (!urls || !urls.length) { onComplete && onComplete(); return; }
  let loaded = 0, total = urls.length;
  urls.forEach(url => {
    const img = new Image();
    img.onload = img.onerror = () => {
      loaded++;
      onProgress && onProgress((loaded / total) * 100, url, loaded, total);
      if (loaded >= total) onComplete && onComplete();
    };
    img.src = url;
  });
}

/* ── Region time popup (Singapore / Indonesia) ───────────────────────── */
function startRegionTimePopup() {
  if (!document.querySelector('.imgui-skin')) return;
  const popup = document.getElementById('time-popup');
  const regionEl = document.getElementById('time-region');
  const valueEl = document.getElementById('time-value');
  const closeBtn = document.getElementById('time-close');
  if (!popup || !regionEl || !valueEl) return;

  function getZoneInfo() {
    let tz = 'UTC';
    try { tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC'; } catch (e) {}
    const indZones = ['Asia/Jakarta', 'Asia/Pontianak', 'Asia/Makassar', 'Asia/Jayapura'];
    if (/singapore/i.test(tz) || (navigator.language || '').toLowerCase().includes('sg')) {
      return { region: 'Singapore', tz: 'Asia/Singapore', label: 'Singapore Time (SGT)' };
    }
    if (indZones.includes(tz) || (navigator.language || '').toLowerCase().includes('id')) {
      return { region: 'Indonesia', tz: indZones.includes(tz) ? tz : 'Asia/Jakarta', label: 'Indonesia Time' };
    }
    return { region: 'Local', tz: tz, label: tz.replace('_', ' ').split('/').pop() };
  }

  const info = getZoneInfo();
  popup.style.display = 'flex';

  function updateTime() {
    try {
      const now = new Date();
      const opts = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: info.tz };
      const timeStr = new Intl.DateTimeFormat([], opts).format(now);
      let tzAbbrev = '';
      try {
        const parts = new Intl.DateTimeFormat('en-US', { timeZone: info.tz, timeZoneName: 'short' }).formatToParts(now);
        const tzn = parts.find(p => p.type === 'timeZoneName');
        if (tzn) tzAbbrev = ' ' + tzn.value;
      } catch (err) {}
      regionEl.textContent = info.label;
      valueEl.textContent = timeStr + (tzAbbrev || '');
    } catch (e) {}
  }

  updateTime();
  const iv = setInterval(updateTime, 1000);

  if (closeBtn) closeBtn.addEventListener('click', () => { popup.style.display = 'none'; clearInterval(iv); });
}

/* ── Toast ─────────────────────────────────────── */
function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 2200);
}

/* ── Bulk selection ────────────────────────────── */
function toggleAll(master) {
  document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
  updateBulk();
}

function updateBulk() {
  const checked = document.querySelectorAll('.row-check:checked');
  const bar = document.getElementById('bulk-bar');
  const countEl = document.getElementById('bulk-count');
  if (checked.length > 0) {
    bar.style.display = 'flex';
    countEl.textContent = `${checked.length} dipilih`;
  } else {
    bar.style.display = 'none';
    document.getElementById('select-all').checked = false;
  }
}

function clearSelection() {
  document.querySelectorAll('.row-check, #select-all').forEach(cb => cb.checked = false);
  document.getElementById('bulk-bar').style.display = 'none';
}

function submitBulkAction(url, confirmMsg) {
  if (confirmMsg && !confirm(confirmMsg)) return;

  const checked = Array.from(document.querySelectorAll('.row-check:checked'));
  if (!checked.length) return;

  const form = document.getElementById('bulk-form');
  form.action = url;

  form.querySelectorAll('input[name="ids"]').forEach(el => el.remove());

  checked.forEach(cb => {
    const inp = document.createElement('input');
    inp.type  = 'hidden';
    inp.name  = 'ids';
    inp.value = cb.value;
    form.appendChild(inp);
  });

  form.submit();
}

function setupCreditPreview() {
  const form = document.querySelector('[data-generate-form]');
  const preview = document.getElementById('credit-preview');
  if (!form || !preview) return;

  const gameInput = form.querySelector('[name="game"]');
  const durationInput = form.querySelector('[name="duration"]');
  const bulkInput = form.querySelector('[name="bulk"]');
  const balance = Number(preview.dataset.balance || window.USER_CREDIT || 0);
  const prices = window.KEY_PRICES || {};

  const render = () => {
    const game = gameInput.value;
    const days = Math.max(1, Math.min(30, parseInt(durationInput.value, 10) || 1));
    const count = Math.max(1, parseInt(bulkInput.value, 10) || 1);
    const each = Number(prices[game] && prices[game][days]) || days;
    const total = each * count;
    const remaining = balance - total;

    preview.classList.toggle('is-danger', remaining < 0);
    preview.innerHTML = `
      <span>Harga: <strong>${each}</strong> credit/key</span>
      <span>Total: <strong>${total}</strong></span>
      <span>Sisa: <strong>${remaining}</strong></span>
    `;
  };

  [gameInput, durationInput, bulkInput].forEach(input => {
    if (input) input.addEventListener('input', render);
    if (input) input.addEventListener('change', render);
  });
  render();
}

/* ── Animated counters ─────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  setupCreditPreview();

  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count, 10);
    if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
    const duration = 800;
    const step = Math.ceil(duration / target);
    let current = 0;
    const timer = setInterval(() => {
      current = Math.min(current + Math.ceil(target / 30), target);
      el.textContent = current;
      if (current >= target) clearInterval(timer);
    }, step);
  });

  // Auto-dismiss alerts
  document.querySelectorAll('.alert').forEach(a => {
    setTimeout(() => {
      a.style.transition = 'opacity .5s';
      a.style.opacity = '0';
      setTimeout(() => a.remove(), 500);
    }, 4000);
  });

  // ESC closes modals
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.show').forEach(m => {
        m.classList.remove('show');
        document.body.style.overflow = '';
      });
    }
  });

  // If this is the login page (imgui skin), detect device and show asset installer
  if (document.querySelector('.imgui-skin')) {
    const isMobile = (() => {
      try {
        const ua = (navigator.userAgent || '').toLowerCase();
        if (/android|iphone|ipod|ipad|windows phone|mobile/.test(ua)) return true;
        return (window.innerWidth <= 900 && window.innerHeight > window.innerWidth);
      } catch (e) { return false; }
    })();

    const loginBg = isMobile ? '/img/mobile-bg.png' : '/img/login-bg.png';
    const bgEl = document.querySelector('.imgui-skin .login-bg');
    if (bgEl) {
      bgEl.style.backgroundImage = `url('${loginBg}')`;
      if (isMobile) bgEl.classList.add('bg-contain'); else bgEl.classList.remove('bg-contain');
    }

    // start region time popup (Singapore / Indonesia) while assets install
    try { startRegionTimePopup(); } catch (e) {}

    const assets = [loginBg, '/img/kurumi.png'];
    showAssetLoader();
    setLoaderProgress(6, 'Preparing download...');
    loadImages(assets, (pct, url) => {
      setLoaderProgress(pct, `Loading ${url.split('/').pop()}`);
    }, () => {
      setLoaderProgress(100, 'Assets installed');
      setTimeout(() => {
        hideAssetLoader();
      }, 350);
    });
  }
});

/* ── Theme and Lang Toggles (SVG icons) ─────────────────────────── */
const _svgSun = `
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
    <circle cx="12" cy="12" r="4"></circle>
    <path d="M12 2v2"></path>
    <path d="M12 20v2"></path>
    <path d="M4.93 4.93l1.41 1.41"></path>
    <path d="M17.66 17.66l1.41 1.41"></path>
    <path d="M2 12h2"></path>
    <path d="M20 12h2"></path>
    <path d="M4.93 19.07l1.41-1.41"></path>
    <path d="M17.66 6.34l1.41-1.41"></path>
  </svg>`;

const _svgMoon = `
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
  </svg>`;

const _svgGlobe = `
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
    <circle cx="12" cy="12" r="10"></circle>
    <path d="M2 12h20"></path>
    <path d="M12 2a15 15 0 0 0 0 20"></path>
    <path d="M12 2a15 15 0 0 1 0 20"></path>
  </svg>`;

function toggleTheme() {
  const isLight = document.documentElement.classList.toggle('light-theme');
  localStorage.setItem('theme', isLight ? 'light' : 'dark');
  updateThemeBtn();
}
function updateThemeBtn() {
  const btn = document.getElementById('theme-btn');
  if(!btn) return;
  const isLight = document.documentElement.classList.contains('light-theme');
  // Icon-only toggle for theme
  btn.innerHTML = isLight ? _svgMoon : _svgSun;
}
function toggleLang() {
  const current = document.documentElement.getAttribute('data-lang') || 'id';
  const next = current === 'id' ? 'en' : 'id';
  document.documentElement.setAttribute('data-lang', next);
  localStorage.setItem('lang', next);
  updateLangUI();
}
function updateLangUI() {
  const lang = document.documentElement.getAttribute('data-lang') || 'id';
  const btn = document.getElementById('lang-btn');
  if(btn) btn.innerHTML = _svgGlobe + '<span class="btn-text" style="margin-left:.45rem">' + (lang === 'id' ? 'EN' : 'ID') + '</span>';

  document.querySelectorAll('.lang-id').forEach(el => el.style.display = lang === 'id' ? '' : 'none');
  document.querySelectorAll('.lang-en').forEach(el => el.style.display = lang === 'en' ? '' : 'none');
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
  updateThemeBtn();
  updateLangUI();
});

// Attach event listeners for pages that rely on data-attributes (CSP-safe)
document.addEventListener('DOMContentLoaded', () => {
  // open modal buttons
  document.querySelectorAll('[data-open-modal]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = btn.dataset.openModal;
      if (id) openModal(id);
    });
  });

  // modal overlay: click outside to close
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        if (overlay.id) closeModal(overlay.id);
      }
    });
  });

  // modal close buttons and cancel buttons
  document.querySelectorAll('.modal-close, .modal-cancel').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const overlay = btn.closest('.modal-overlay');
      if (overlay && overlay.id) closeModal(overlay.id);
    });
  });

  // select-all checkbox
  const selectAll = document.getElementById('select-all');
  if (selectAll) selectAll.addEventListener('change', () => toggleAll(selectAll));

  // row checkboxes
  document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateBulk));

  // bulk action buttons
  document.querySelectorAll('[data-bulk-action-url]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const url = btn.dataset.bulkActionUrl;
      const confirmMsg = btn.dataset.bulkActionConfirm || '';
      submitBulkAction(url, confirmMsg || null);
    });
  });

  // clear selection
  document.querySelectorAll('[data-clear-selection]').forEach(btn => btn.addEventListener('click', clearSelection));

  // copy key behavior (from table only)
  document.querySelectorAll('.key-copy').forEach(el => {
    el.addEventListener('click', () => copyText(el.textContent.trim(), el));
  });

  // edit / delete buttons for keys
  document.querySelectorAll('.btn-edit[data-key-id]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.keyId;
      if (!id) return;
      try {
        const key = window.KEYS_MAP && window.KEYS_MAP[id];
        if (key) openEdit(key);
      } catch (e) {}
    });
  });

  document.querySelectorAll('.btn-del[data-delete-id]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.deleteId;
      if (!id) return;
      const key = window.KEYS_MAP && window.KEYS_MAP[id];
      const code = key ? key.key_code : '';
      openDelete(id, code);
    });
  });

  // Theme / Lang buttons (sidebar)
  const themeBtn = document.getElementById('theme-btn');
  if (themeBtn) themeBtn.addEventListener('click', () => { toggleTheme(); });
  const langBtn = document.getElementById('lang-btn');
  if (langBtn) langBtn.addEventListener('click', () => { toggleLang(); });

  // Sidebar toggle (mobile)
  const sidebarToggle = document.getElementById('sidebarToggle');
  if (sidebarToggle) sidebarToggle.addEventListener('click', () => {
    const sb = document.getElementById('sidebar');
    if (sb) sb.classList.toggle('open');
  });
});