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
  document.getElementById('edit-key-code').value     = key.key_code;
  document.getElementById('edit-resource').value     = key.resource;
  document.getElementById('edit-active').value       = key.is_active ? '1' : '0';
  document.getElementById('edit-notes').value        = key.notes || '';
  document.getElementById('edit-serial-display').value = key.device_serial || 'Belum terkunci';
  document.getElementById('edit-reset-device').checked = false;

  // Format expires_at for datetime-local input
  if (key.expires_at) {
    const d = new Date(key.expires_at * 1000);
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

  // Kumpulkan semua ID ke hidden input (comma-separated)
  // Backend sudah support array via ?ids=1&ids=2, kita pakai multiple inputs
  const form = document.getElementById('bulk-form');
  form.action = url;

  // Hapus input lama
  form.querySelectorAll('input[name="ids"]').forEach(el => el.remove());

  // Buat input per ID
  checked.forEach(cb => {
    const inp = document.createElement('input');
    inp.type  = 'hidden';
    inp.name  = 'ids';
    inp.value = cb.value;
    form.appendChild(inp);
  });

  form.submit();
}

/* ── Animated counters ─────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
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
});
