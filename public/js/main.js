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

  // If this is the login page (imgui skin), show asset installer until images loaded
  if (document.querySelector('.imgui-skin')) {
    const assets = ['/img/login-bg.png', '/img/kurumi.png'];
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

/* ── Theme and Lang Toggles ─────────────────────────── */
function toggleTheme() {
  const isLight = document.documentElement.classList.toggle('light-theme');
  localStorage.setItem('theme', isLight ? 'light' : 'dark');
  updateThemeBtn();
}
function updateThemeBtn() {
  const btn = document.getElementById('theme-btn');
  if(!btn) return;
  if(document.documentElement.classList.contains('light-theme')) {
    btn.innerHTML = '🌙 Dark';
  } else {
    btn.innerHTML = '☀️ Light';
  }
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
  if(btn) btn.innerHTML = lang === 'id' ? '🇬🇧 EN' : '🇮🇩 ID';

  document.querySelectorAll('.lang-id').forEach(el => el.style.display = lang === 'id' ? '' : 'none');
  document.querySelectorAll('.lang-en').forEach(el => el.style.display = lang === 'en' ? '' : 'none');
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
  updateThemeBtn();
  updateLangUI();
});