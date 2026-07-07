import React, { useEffect, useMemo, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';

const data = window.__APP_DATA__ || { view: 'login' };

function fmtDate(value) {
  if (!value) return '-';
  const num = Number(value);
  const date = Number.isFinite(num) && String(value).length <= 11 ? new Date(num * 1000) : new Date(value);
  if (Number.isNaN(date.getTime())) return '-';
  return date.toLocaleString('id-ID', {
    timeZone: 'Asia/Jakarta',
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function parseSerials(raw) {
  try {
    const parsed = JSON.parse(raw || '[]');
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

function rupiah(value) {
  return `Rp ${Number(value || 0).toLocaleString('id-ID')}`;
}

function escapeAttr(value) {
  return String(value ?? '').replace(/"/g, '&quot;');
}

function useThemeLang() {
  const [theme, setTheme] = useState(() => localStorage.getItem('theme') || 'dark');
  const [lang, setLang] = useState(() => localStorage.getItem('lang') || 'id');

  useEffect(() => {
    document.documentElement.classList.toggle('light-theme', theme === 'light');
    localStorage.setItem('theme', theme);
  }, [theme]);

  useEffect(() => {
    document.documentElement.setAttribute('data-lang', lang);
    localStorage.setItem('lang', lang);
  }, [lang]);

  return {
    theme,
    lang,
    toggleTheme: () => setTheme((v) => (v === 'light' ? 'dark' : 'light')),
    toggleLang: () => setLang((v) => (v === 'id' ? 'en' : 'id'))
  };
}

function Alert({ messages, type }) {
  const [show, setShow] = useState(Boolean(messages && messages.length));
  useEffect(() => {
    if (!messages || messages.length === 0) return undefined;
    const timer = setTimeout(() => setShow(false), 4000);
    return () => clearTimeout(timer);
  }, [messages]);
  if (!show || !messages || messages.length === 0) return null;
  return <div className={`alert alert-${type}`}>{messages[0]}</div>;
}

function Toast({ toast }) {
  return <div className={`toast ${toast ? 'show' : ''}`}>{toast}</div>;
}

function Sidebar({ app, controls }) {
  const [open, setOpen] = useState(false);
  const admin = app.admin || app.user || {};
  const isOwner = admin.role === 'owner';
  const isStore = app.isStoreAdmin;
  const url = app.currentUrl || '';
  const navClass = (match) => `nav-item ${match ? 'active' : ''}`;

  const close = () => {
    setOpen(false);
    document.body.style.overflow = '';
  };
  const toggle = () => {
    setOpen(true);
    document.body.style.overflow = 'hidden';
  };

  return (
    <>
      <div className={`sidebar-backdrop ${open ? 'show' : ''}`} onClick={close} />
      <aside className={`sidebar ${open ? 'open' : ''}`}>
        <div className="sidebar-brand">
          <div className="brand-hex">⬡</div>
          <div><div className="brand-title">{app.panel_name || 'ATTIC PANEL'}</div></div>
          <div className="brand-controls">
            <button className="btn-ghost" type="button" onClick={controls.toggleTheme}>{controls.theme === 'light' ? '☾' : '☀'}</button>
            <button className="btn-ghost" type="button" onClick={controls.toggleLang}>{controls.lang === 'id' ? 'EN' : 'ID'}</button>
          </div>
        </div>
        <nav className="sidebar-nav">
          {isStore ? (
            <>
              <a href="/admin/store" className={navClass(url === '/admin/store' || url === '/admin/store/')}>Dashboard Store</a>
              <a href="/admin/store/products" className={navClass(url.includes('/products'))}>Kelola Produk</a>
              <a href="/admin/store/orders" className={navClass(url.includes('/orders'))}>Kelola Pesanan</a>
              <a href="/admin/store/referrals" className={navClass(url.includes('/referrals'))}>Kelola Referral</a>
              <a href="/admin/dashboard" className="nav-item" style={{ color: 'var(--cyan)' }}>Panel Utama</a>
            </>
          ) : (
            <>
              {isOwner && <a href="/admin/dashboard" className={navClass(url.includes('/admin/dashboard'))}>Dashboard</a>}
              <a href="/admin/keys" className={navClass(url.includes('/admin/keys'))}>Kelola Key</a>
              {isOwner && <a href="/admin/files" className={navClass(url.includes('/admin/files'))}>Kelola File</a>}
              {isOwner && <a href="/admin/settings" className={navClass(url.includes('/admin/settings'))}>Pengaturan</a>}
              {isOwner && <a href="/admin/store" className={navClass(url.startsWith('/admin/store'))}>Kelola Store</a>}
              {!isOwner && <a href="/admin/profile" className={navClass(url.includes('/admin/profile'))}>Profil Saya</a>}
            </>
          )}
        </nav>
        <div className="sidebar-footer">
          <div className="admin-info">
            <div className="admin-dot" />
            <div>
              <span>{admin.username || 'admin'}</span>
              <div className="admin-role">{isOwner ? 'OWNER' : 'RESELLER'}{!isOwner ? ` - ${admin.credit || 0} CREDIT` : ''}</div>
            </div>
          </div>
          <form action="/logout" method="POST"><button type="submit" className="btn-logout">LOGOUT</button></form>
        </div>
      </aside>
      <div className="mobile-navbar">
        <div className="mobile-navbar-title">{app.panel_name || 'ATTIC PANEL'}</div>
        <button className="sidebar-toggle-nav" type="button" onClick={toggle}>☰</button>
      </div>
    </>
  );
}

function Layout({ app, controls, children }) {
  return <div className="layout"><Sidebar app={app} controls={controls} /><main className="main">{children}</main></div>;
}

function Login({ app, controls }) {
  return (
    <div className="login-page">
      <div className="login-card card">
        <div className="card-header"><h1 className="card-title">{app.panel_name || 'ATTIC PANEL'}</h1></div>
        <div className="card-body">
          <Alert messages={app.error_msg} type="error" />
          <Alert messages={app.success_msg} type="success" />
          <form action="/login" method="POST">
            <div className="field"><label className="field-label">USERNAME</label><input className="field-input" name="username" autoComplete="username" required /></div>
            <div className="field"><label className="field-label">PASSWORD</label><input className="field-input" type="password" name="password" autoComplete="current-password" required /></div>
            <button className="btn-primary btn-full" type="submit">Login</button>
          </form>
          <div style={{ display: 'flex', gap: '.5rem', marginTop: '1rem' }}>
            <button className="btn-secondary" type="button" onClick={controls.toggleTheme}>Theme</button>
            <a className="btn-secondary" href="/store">Store</a>
          </div>
        </div>
      </div>
    </div>
  );
}

function Dashboard({ app }) {
  const stats = app.stats || {};
  const recent = app.recent || [];
  return (
    <>
      <div className="topbar"><div><h2 className="page-title">Dashboard</h2><p className="page-sub">Ringkasan panel</p></div></div>
      <Flash app={app} />
      <div className="stats-grid">
        {[
          ['TOTAL KEY', stats.total], ['AKTIF', stats.active], ['EXPIRED', stats.expired], ['LOCKED', stats.locked]
        ].map(([label, value]) => <div className="card stat-card" key={label}><span>{label}</span><strong>{value || 0}</strong></div>)}
      </div>
      <div className="card"><div className="card-header"><h3 className="card-title">Key Terbaru</h3><a className="btn-secondary btn-sm" href="/admin/keys">Lihat semua</a></div>
        <div className="table-wrap"><table className="table"><thead><tr><th>Key</th><th>Resource</th><th>Status</th><th>Expired</th></tr></thead><tbody>{recent.map((k) => <tr key={k.id}><td><code className="key-code">{k.key_code}</code></td><td>{k.resource}</td><td>{keyStatus(k, app.now)}</td><td>{k.expires_at == 0 ? 'Belum aktif' : fmtDate(k.expires_at)}</td></tr>)}{recent.length === 0 && <tr><td colSpan="4" className="text-center text-muted">Belum ada key</td></tr>}</tbody></table></div>
      </div>
    </>
  );
}

function Flash({ app }) {
  return <><Alert messages={app.success_msg} type="success" /><Alert messages={app.error_msg} type="error" /></>;
}

function keyStatus(k, now) {
  if (!k.is_active) return <span className="badge badge-off">NONAKTIF</span>;
  if (Number(k.expires_at) > 0 && Number(k.expires_at) <= Number(now || Date.now() / 1000)) return <span className="badge badge-expired">EXPIRED</span>;
  if (Number(k.expires_at) === 0) return <span className="badge badge-active" style={{ background: '#9b59b6' }}>READY</span>;
  return <span className="badge badge-active">AKTIF</span>;
}

function Modal({ open, title, danger, children, onClose }) {
  useEffect(() => {
    document.body.style.overflow = open ? 'hidden' : '';
    const onKey = (e) => e.key === 'Escape' && onClose();
    if (open) document.addEventListener('keydown', onKey);
    return () => document.removeEventListener('keydown', onKey);
  }, [open, onClose]);
  if (!open) return null;
  return <div className="modal-overlay show" onMouseDown={(e) => e.target === e.currentTarget && onClose()}><div className="modal"><div className="modal-header"><h3 className={`modal-title ${danger ? 'text-danger' : ''}`}>{title}</h3><button className="modal-close" type="button" onClick={onClose}>✕</button></div>{children}</div></div>;
}

function KeysPage({ app }) {
  const admin = app.admin || app.user || {};
  const isOwner = admin.role === 'owner';
  const keys = app.keys || [];
  const [generateOpen, setGenerateOpen] = useState(false);
  const [editKey, setEditKey] = useState(null);
  const [deleteKey, setDeleteKey] = useState(null);
  const [selected, setSelected] = useState([]);
  const [toast, setToast] = useState('');
  const canGenerate = isOwner || (app.gameOptions || []).length > 0;
  const toggleSelect = (id) => setSelected((cur) => cur.includes(id) ? cur.filter((v) => v !== id) : [...cur, id]);
  const copy = async (text) => { try { await navigator.clipboard.writeText(text); } catch {} setToast(`Copied: ${text}`); setTimeout(() => setToast(''), 2200); };
  const bulk = (url, msg) => {
    if (selected.length === 0 || (msg && !confirm(msg))) return;
    const form = document.createElement('form'); form.method = 'POST'; form.action = url;
    selected.forEach((id) => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'ids'; input.value = id; form.appendChild(input); });
    document.body.appendChild(form); form.submit();
  };
  return (
    <>
      <div className="topbar"><div><h2 className="page-title">Manage Keys</h2><p className="page-sub">Total <strong>{app.total || 0}</strong> key {isOwner ? 'terdaftar' : 'milik kamu'} {!isOwner && <>- Credit: <strong>{admin.credit || 0}</strong></>}</p></div><div className="topbar-right"><button className="btn-primary" disabled={!canGenerate} onClick={() => setGenerateOpen(true)}>+ Generate Key</button>{isOwner && <a className="btn-secondary" href="/admin/keys/export">Export</a>}</div></div>
      <Flash app={app} />
      <div className="filter-bar"><form method="GET" action="/admin/keys" className="search-form"><input className="field-input search-input" name="search" placeholder="Cari key, serial, notes..." defaultValue={app.search || ''} /><input type="hidden" name="filter" value={app.filter || 'all'} /><button className="btn-secondary">Cari</button></form><div className="filter-tabs">{['all','active','expired','locked','inactive'].map((f) => <a key={f} className={`filter-tab ${app.filter === f ? 'active' : ''}`} href={`/admin/keys?filter=${f}&search=${encodeURIComponent(app.search || '')}&creator=${app.creator || 'all'}`}>{f}</a>)}</div></div>
      {isOwner && <div style={{ marginBottom: '1.5rem', display: 'flex', gap: '.5rem' }}>{['owner','resellers','all'].map((c) => <a key={c} className={`filter-tab ${app.creator === c ? 'active' : ''}`} href={`/admin/keys?creator=${c}&filter=${app.filter || 'all'}&search=${encodeURIComponent(app.search || '')}`}>{c}</a>)}</div>}
      {selected.length > 0 && <div className="bulk-bar" style={{ display: 'flex' }}><span>{selected.length} dipilih</span><button className="btn-danger btn-sm" onClick={() => bulk('/admin/keys/bulk-delete', 'Hapus semua yang dipilih?')}>Hapus</button>{admin.role !== 'reseller' && <button className="btn-secondary btn-sm" onClick={() => bulk('/admin/keys/bulk-deactivate')}>Nonaktifkan</button>}<button className="btn-ghost btn-sm" onClick={() => setSelected([])}>Batal</button></div>}
      <div className="card"><div className="table-wrap"><table className="table"><thead><tr><th><input type="checkbox" checked={keys.length > 0 && selected.length === keys.length} onChange={(e) => setSelected(e.target.checked ? keys.map((k) => String(k.id)) : [])} /></th><th>KEY CODE</th>{isOwner && <th>RESELLER</th>}<th>RESOURCE</th><th>STATUS</th><th>DEVICE</th><th>DIBUAT</th><th>EXPIRED</th><th>HARGA</th><th>LOGIN</th><th>AKSI</th></tr></thead><tbody>{keys.map((k) => { const serials = parseSerials(k.device_serials); return <tr key={k.id}><td><input type="checkbox" checked={selected.includes(String(k.id))} onChange={() => toggleSelect(String(k.id))} /></td><td><code className="key-code key-copy" onClick={() => copy(k.key_code)}>{k.key_code}</code></td>{isOwner && <td><span className="badge badge-count">{k.created_by_username || 'owner'}</span></td>}<td><span className="badge badge-resource">{k.resource}</span></td><td>{keyStatus(k, app.now)}</td><td><span className="device-slot-badge">{serials.length}/{k.max_devices || 1}</span></td><td className="text-muted">{fmtDate(k.created_at)}</td><td>{k.expires_at == 0 ? 'BELUM AKTIF' : fmtDate(k.expires_at)}</td><td><span className="badge badge-count">{k.price_paid || 0}</span></td><td><span className="badge badge-count">{k.login_count || 0}x</span></td><td><div className="action-btns">{admin.role !== 'reseller' && <button className="btn-icon btn-edit" onClick={() => setEditKey(k)}>✎</button>}<button className="btn-icon btn-del" onClick={() => setDeleteKey(k)}>✕</button></div></td></tr>; })}{keys.length === 0 && <tr><td colSpan={isOwner ? 11 : 10} className="text-center text-muted" style={{ padding: '3rem' }}>Tidak ada key ditemukan</td></tr>}</tbody></table></div></div>
      {(app.totalPages || 0) > 1 && <div className="pagination">{Array.from({ length: app.totalPages }, (_, i) => i + 1).map((p) => <a className={`page-btn ${p === app.currentPage ? 'active' : ''}`} href={`/admin/keys?page=${p}&filter=${app.filter || 'all'}&search=${encodeURIComponent(app.search || '')}&creator=${app.creator || 'all'}`} key={p}>{p}</a>)}</div>}
      <GenerateModal app={app} open={generateOpen} onClose={() => setGenerateOpen(false)} canGenerate={canGenerate} />
      <EditKeyModal k={editKey} onClose={() => setEditKey(null)} />
      <DeleteModal k={deleteKey} onClose={() => setDeleteKey(null)} />
      <Toast toast={toast} />
    </>
  );
}

function GenerateModal({ app, open, onClose, canGenerate }) {
  const admin = app.admin || app.user || {};
  const isOwner = admin.role === 'owner';
  const [game, setGame] = useState((app.gameOptions || [])[0]?.value || 'BS');
  const [duration, setDuration] = useState(30);
  const [bulk, setBulk] = useState(1);
  const days = Math.min(30, Math.max(1, Number(duration) || 1));
  const each = Number(app.priceMatrix?.[game]?.[days]) || days;
  const total = each * Math.max(1, Number(bulk) || 1);
  return <Modal open={open} title="Generate Key" onClose={onClose}><form action="/admin/keys/generate" method="POST" className="modal-form"><div className="field"><label className="field-label">GAME</label><select name="game" className="field-input" disabled={!canGenerate} value={game} onChange={(e) => setGame(e.target.value)}>{(app.gameOptions || []).map((g) => <option key={g.value} value={g.value}>{g.label}</option>)}</select></div><div className="field"><label className="field-label">RESOURCE</label><input name="resource" className="field-input" defaultValue="vip" required /></div><div className="field-row"><div className="field" style={{ flex: 1 }}><label className="field-label">DURASI</label><input name="duration" className="field-input" type="number" value={duration} min="1" max={isOwner ? undefined : 30} onChange={(e) => setDuration(e.target.value)} required /></div><div className="field" style={{ flex: 1 }}><label className="field-label">UNIT</label>{isOwner ? <select name="duration_unit" className="field-input" defaultValue="days"><option value="hours">Jam</option><option value="days">Hari</option><option value="months">Bulan</option></select> : <><input type="hidden" name="duration_unit" value="days" /><input className="field-input" value="Hari" readOnly /></>}</div></div><div className="field-row"><div className="field" style={{ flex: 1 }}><label className="field-label">JUMLAH KEY</label><input name="bulk" className="field-input" type="number" value={bulk} min="1" max="100" onChange={(e) => setBulk(e.target.value)} /></div><div className="field" style={{ flex: 1 }}><label className="field-label">MAX DEVICE</label><input name="max_devices" className="field-input" type="number" defaultValue="1" min="1" max="500" /></div></div>{isOwner && <div className="field"><label className="field-label">CUSTOM KEY</label><input name="custom_key" className="field-input" /></div>}<div className="field"><label className="field-label">NOTES</label><input name="notes" className="field-input" /></div>{!isOwner && <div className={`credit-preview ${(admin.credit || 0) - total < 0 ? 'is-danger' : ''}`}>Harga: <strong>{each}</strong> credit/key · Total: <strong>{total}</strong> · Sisa: <strong>{(admin.credit || 0) - total}</strong></div>}<div className="modal-footer"><button type="button" className="btn-ghost" onClick={onClose}>Batal</button><button className="btn-primary" disabled={!canGenerate}>Generate</button></div></form></Modal>;
}

function toDatetimeLocal(value) {
  if (!value || Number(value) === 0) return '';
  const d = new Date(Number(value) * 1000);
  d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
  return d.toISOString().slice(0, 16);
}

function EditKeyModal({ k, onClose }) {
  const serials = parseSerials(k?.device_serials);
  return <Modal open={Boolean(k)} title="Edit Key" onClose={onClose}><form action={k ? `/admin/keys/${k.id}/edit` : '#'} method="POST" className="modal-form"><div className="field"><label className="field-label">KEY CODE</label><input className="field-input" value={k?.key_code || ''} readOnly /></div><div className="field"><label className="field-label">RESOURCE</label><input name="resource" className="field-input" defaultValue={k?.resource || ''} required /></div><div className="field"><label className="field-label">EXPIRED DATE & TIME</label><input name="expires_at_input" type="datetime-local" className="field-input" defaultValue={toDatetimeLocal(k?.expires_at)} /></div><div className="field"><label className="field-label">STATUS</label><select name="is_active" className="field-input" defaultValue={k?.is_active ? '1' : '0'}><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div><div className="field"><label className="field-label">MAX DEVICE / KEY</label><input name="max_devices" type="number" className="field-input" min="1" max="500" defaultValue={k?.max_devices || 1} /></div><div className="field"><label className="field-label">DEVICE TERDAFTAR</label><div className="serials-box">{serials.length ? serials.map((s, i) => <div className="serial-item" key={s}><span className="serial-dot" /><code>{s}</code><span className="serial-idx">#{i + 1}</span></div>) : 'Belum ada device terdaftar'}</div></div><div className="field-check"><label className="check-label"><input type="checkbox" name="reset_devices" value="1" /> <span>Reset semua device</span></label></div><div className="field"><label className="field-label">NOTES</label><input name="notes" className="field-input" defaultValue={k?.notes || ''} /></div><div className="modal-footer"><button type="button" className="btn-ghost" onClick={onClose}>Batal</button><button className="btn-primary">Simpan</button></div></form></Modal>;
}

function DeleteModal({ k, onClose }) {
  return <Modal open={Boolean(k)} title="Hapus Key" danger onClose={onClose}><div style={{ padding: '1.25rem' }}><p className="text-muted">Yakin ingin menghapus key:</p><code className="key-code" style={{ display: 'block', margin: '.75rem 0' }}>{k?.key_code}</code></div><form action={k ? `/admin/keys/${k.id}/delete` : '#'} method="POST" className="modal-footer"><button type="button" className="btn-ghost" onClick={onClose}>Batal</button><button className="btn-danger">Hapus</button></form></Modal>;
}

function FilesPage({ app }) {
  return <><div className="topbar"><div><h2 className="page-title">Manage Files</h2><p className="page-sub">Storage: {app.storageMode}</p></div><button className="btn-primary" onClick={() => document.getElementById('file-upload-input')?.click()}>Upload</button></div><Flash app={app} /><form action="/admin/files/upload" method="POST" encType="multipart/form-data"><input id="file-upload-input" name="file" type="file" style={{ display: 'none' }} onChange={(e) => e.currentTarget.form.submit()} /></form><div className="card"><div className="table-wrap"><table className="table"><thead><tr><th>Nama</th><th>Size</th><th>URL</th><th>Aksi</th></tr></thead><tbody>{(app.files || []).map((f) => <tr key={f.path || f.name}><td>{f.name}</td><td>{f.size || f.sizeBytes}</td><td><a href={f.url} target="_blank">Open</a></td><td><form action="/admin/files/delete" method="POST" onSubmit={(e) => !confirm('Hapus file?') && e.preventDefault()}><input type="hidden" name="path" value={f.path} /><input type="hidden" name="sha" value={f.sha || ''} /><button className="btn-danger btn-sm">Hapus</button></form></td></tr>)}{(!app.files || app.files.length === 0) && <tr><td colSpan="4" className="text-center text-muted">Belum ada file</td></tr>}</tbody></table></div></div></>;
}

function SettingsPage({ app }) {
  const [tab, setTab] = useState(() => localStorage.getItem('settingsTab') || 'tab-panel');
  const select = (id) => { setTab(id); localStorage.setItem('settingsTab', id); };
  return <><div className="topbar"><div><h2 className="page-title">Pengaturan</h2><p className="page-sub">Konfigurasi panel & API</p></div></div><Flash app={app} /><div className="tabs">{[['tab-panel','Sistem & API'],['tab-reseller','Kelola Reseller'],['tab-price','Daftar Harga']].map(([id,label]) => <button className={`tab-btn ${tab === id ? 'active' : ''}`} type="button" onClick={() => select(id)} key={id}>{label}</button>)}</div>{tab === 'tab-panel' && <PanelSettings app={app} />}{tab === 'tab-reseller' && <ResellerSettings app={app} />}{tab === 'tab-price' && <PriceSettings app={app} />}</>;
}

function PanelSettings({ app }) {
  const cfg = app.cfg || {};
  return <div className="tab-content active"><form action="/admin/settings" method="POST"><div className="settings-grid"><div className="card"><div className="card-header"><h3 className="card-title">Panel Settings</h3></div><div className="card-body"><div className="field"><label className="field-label">NAMA PANEL</label><input name="panel_name" className="field-input" defaultValue={cfg.panel_name || app.panel_name || ''} required /></div><div className="field"><label className="field-label">MAINTENANCE MODE</label><select name="maintenance_mode" className="field-input" defaultValue={cfg.maintenance_mode || '0'}><option value="0">Off</option><option value="1">On</option></select></div></div></div><div className="card"><div className="card-header"><h3 className="card-title">Admin Credentials</h3></div><div className="card-body"><div className="field"><label className="field-label">USERNAME</label><input name="admin_username" className="field-input" defaultValue={cfg.admin_username || ''} required /></div><div className="field"><label className="field-label">PASSWORD BARU</label><input type="password" name="new_password" className="field-input" /></div><div className="field"><label className="field-label">KONFIRMASI PASSWORD</label><input type="password" name="confirm_password" className="field-input" /></div></div></div></div><div style={{ marginTop: '1.5rem', display: 'flex', justifyContent: 'flex-end' }}><button className="btn-primary">Simpan Perubahan</button></div></form></div>;
}

function ResellerSettings({ app }) {
  const games = app.pricingGames || [];
  const [expanded, setExpanded] = useState(null);
  return <div className="tab-content active"><div className="settings-grid"><div className="card"><div className="card-header"><h3 className="card-title">Buat Reseller</h3></div><form action="/admin/resellers" method="POST" className="card-body"><div className="field"><label className="field-label">USERNAME</label><input name="username" className="field-input" required /></div><div className="field"><label className="field-label">PASSWORD</label><input type="password" name="password" className="field-input" required /></div><div className="field-row"><div className="field" style={{ flex: 1 }}><label className="field-label">CREDIT AWAL</label><input type="number" name="credit" className="field-input" defaultValue="0" min="0" /></div><div className="field" style={{ flex: 1 }}><label className="field-label">DURASI AKUN</label><select name="duration" className="field-input" defaultValue="lifetime"><option value="1_month">1 Bulan</option><option value="1_year">1 Tahun</option><option value="lifetime">Lifetime</option></select></div></div><GameChecks games={games} /><button className="btn-primary btn-full">Tambah Reseller</button></form></div><div className="card"><div className="card-header"><h3 className="card-title">Credit Reseller</h3></div><div className="card-body"><div className="reseller-carousel">{(app.resellers || []).map((r) => <div className={`reseller-card ${expanded === r.id ? 'expanded' : ''}`} key={r.id}><div className="reseller-card-header"><div className="reseller-info"><div className="reseller-username">{r.username}</div><div className="reseller-meta">Credit: <strong>{r.credit}</strong></div><div className="reseller-meta">{r.expires_at ? `Exp: ${fmtDate(r.expires_at)}` : 'Lifetime'}</div></div><div className="reseller-actions"><button className="btn-secondary btn-sm" onClick={() => setExpanded(expanded === r.id ? null : r.id)}>Edit</button><form action={`/admin/resellers/${r.id}/delete`} method="POST" onSubmit={(e) => !confirm(`Yakin ingin menghapus reseller ${r.username}?`) && e.preventDefault()}><button className="btn-danger btn-sm">Hapus</button></form></div></div>{expanded === r.id && <form action={`/admin/resellers/${r.id}`} method="POST" className="reseller-card-body" style={{ display: 'block' }}><div className="field"><label className="field-label">USERNAME</label><input name="username" className="field-input" defaultValue={r.username} /></div><div className="field"><label className="field-label">CREDIT</label><input name="credit" type="number" className="field-input" defaultValue={r.credit} /></div><div className="field"><label className="field-label">STATUS</label><select name="is_active" className="field-input" defaultValue={r.is_active ? '1' : '0'}><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div><GameChecks games={games} checked={r.allowedGames || []} /><div className="field"><label className="field-label">PASSWORD BARU</label><input type="password" name="password" className="field-input" /></div><div className="field"><label className="field-label">PERPANJANG DURASI</label><select name="extend_duration" className="field-input"><option value="none">Tidak Perpanjang</option><option value="1_month">Beri 1 Bulan</option><option value="1_year">Beri 1 Tahun</option><option value="lifetime">Jadikan Lifetime</option></select></div><button className="btn-primary btn-full">Simpan Reseller</button></form>}</div>)}{(!app.resellers || app.resellers.length === 0) && <p className="text-muted">Belum ada akun reseller.</p>}</div></div></div></div></div>;
}

function GameChecks({ games, checked = [] }) {
  return <div className="field"><label className="field-label">GAME YANG DIIZINKAN</label><div className="game-permission-grid">{games.map((g) => <label className="permission-chip" key={g.value}><input type="checkbox" name="allowed_games" value={g.value} defaultChecked={checked.includes(g.value)} /><span>{g.label}</span></label>)}</div></div>;
}

function PriceSettings({ app }) {
  const games = app.pricingGames || [];
  const days = app.pricingDays || Array.from({ length: 30 }, (_, i) => i + 1);
  return <div className="tab-content active"><form action="/admin/prices" method="POST"><div className="card"><div className="card-header"><h3 className="card-title">Harga Key Reseller</h3></div><div className="card-body">{games.map((g) => <div className="pricing-block" key={g.value}><div className="pricing-title">{g.label}</div><div className="price-grid">{days.map((day) => <label className="price-cell" key={day}><span>{day}H</span><input type="number" min="1" name={`prices[${g.value}][${day}]`} defaultValue={app.priceMatrix?.[g.value]?.[day] ?? day} /></label>)}</div></div>)}<div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: '1rem' }}><button className="btn-primary">Simpan Harga</button></div></div></div></form></div>;
}

function ProfilePage({ app }) {
  const user = app.user || {};
  return <><div className="topbar"><div><h2 className="page-title">Profil Saya</h2><p className="page-sub">Kelola akun</p></div></div><Flash app={app} /><div className="settings-grid"><div className="card"><div className="card-header"><h3 className="card-title">Info Akun</h3></div><div className="card-body"><p>Username: <strong>{user.username}</strong></p><p>Credit: <strong>{user.credit || 0}</strong></p><p>Expired: <strong>{user.expires_at ? fmtDate(user.expires_at) : 'Lifetime'}</strong></p></div></div><div className="card"><div className="card-header"><h3 className="card-title">Ubah Login</h3></div><form action="/admin/profile" method="POST" className="card-body"><div className="field"><label className="field-label">USERNAME BARU</label><input name="new_username" className="field-input" defaultValue={user.username || ''} /></div><div className="field"><label className="field-label">PASSWORD LAMA</label><input type="password" name="old_password" className="field-input" /></div><div className="field"><label className="field-label">PASSWORD BARU</label><input type="password" name="new_password" className="field-input" /></div><button className="btn-primary">Simpan</button></form></div></div></>;
}

function StoreLayout({ app, children }) {
  return <div className="store-shell"><header className="store-header" style={{ padding: '1rem 5vw', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}><a href="/store" className="brand-title">{app.storeName || 'XSRC'}</a><nav style={{ display: 'flex', gap: '.75rem' }}><a href="/store">Catalog</a>{app.isAdmin ? <a href="/admin/store">Admin</a> : <a href="/login">Login</a>}</nav></header><main style={{ padding: '1rem 5vw' }}>{children}</main></div>;
}

function StoreIndex({ app }) {
  const products = app.products || [];
  return <StoreLayout app={app}><div className="topbar"><div><h2 className="page-title">Store</h2><p className="page-sub">{app.storeTagline}</p></div></div><form method="GET" action="/store" className="search-form" style={{ marginBottom: '1rem' }}><input name="q" className="field-input" defaultValue={app.q || ''} placeholder="Cari produk" /><button className="btn-primary">Cari</button></form><div className="stats-grid">{products.map((p) => <a className="card" href={`/store/produk/${p.slug}`} key={p.id}><div className="card-body"><h3>{p.name}</h3><p className="text-muted">{p.category}</p><p>Stock: {p.stock || 0}</p><strong>{rupiah(p.min_price || p.price)}</strong></div></a>)}{products.length === 0 && <div className="card"><div className="card-body text-muted">Produk belum tersedia.</div></div>}</div></StoreLayout>;
}

function StoreProduct({ app }) {
  const p = app.product || {};
  const variants = app.variants || [];
  const [variant, setVariant] = useState(variants.find((v) => Number(v.stock) > 0));
  return <StoreLayout app={app}><div className="card"><div className="card-body"><h1>{p.name}</h1><p>{p.description}</p><p>Stock total: {p.stock || 0}</p><div className="settings-grid">{variants.map((v) => <button key={v.id} type="button" className={`card ${variant?.id === v.id ? 'active' : ''}`} disabled={Number(v.stock) < 1} onClick={() => setVariant(v)}><h3>{v.name}</h3><strong>{rupiah(v.price)}</strong><p>Stock: {v.stock}</p></button>)}</div><a className={`btn-primary ${!variant ? 'disabled' : ''}`} href={variant ? `/store/checkout/${p.slug}/${variant.id}` : '#'}>Beli Sekarang</a></div></div></StoreLayout>;
}

function StoreCheckout({ app }) {
  const p = app.product || {}, v = app.variant || {};
  return <StoreLayout app={app}><div className="settings-grid"><div className="card"><div className="card-body"><h2>Checkout</h2><p>{p.name} - {v.name}</p><strong>{rupiah(v.price)}</strong>{app.error && <div className="alert alert-error">{app.error}</div>}</div></div><form className="card" action={`/store/checkout/${p.slug}/${v.id}`} method="POST"><div className="card-body"><div className="field"><label className="field-label">Nama</label><input name="customer_name" className="field-input" required /></div><div className="field"><label className="field-label">Email</label><input name="customer_email" type="email" className="field-input" required /></div><div className="field"><label className="field-label">Referral</label><input name="referral_code" className="field-input" /></div><button className="btn-primary btn-full">Buat Pesanan</button></div></form></div></StoreLayout>;
}

function StoreOrder({ app }) {
  const o = app.order || {};
  return <StoreLayout app={app}><div className="card"><div className="card-body"><h2>Order {o.id}</h2><p>{o.product_name} - {o.variant_name}</p><p>Status: <strong>{o.status}</strong></p><p>Total unik: <strong>{rupiah(o.unique_amount)}</strong></p>{o.qris_url && o.status === 'pending' && <img src={o.qris_url} alt="QRIS" style={{ maxWidth: 280 }} />}{o.key_value && <div><p>Key:</p><code className="key-code">{o.key_value}</code></div>}</div></div></StoreLayout>;
}

function StoreAdminDashboard({ app }) {
  const s = app.stats || {};
  return <><div className="topbar"><div><h2 className="page-title">Manage Store</h2></div></div><div className="stats-grid">{Object.entries(s).map(([k, v]) => <div className="card stat-card" key={k}><span>{k}</span><strong>{v}</strong></div>)}</div><div className="card"><div className="card-header"><h3 className="card-title">Recent Orders</h3></div><div className="table-wrap"><table className="table"><tbody>{(app.recentOrders || []).map((o) => <tr key={o.id}><td>{o.id}</td><td>{o.product_name}</td><td>{o.status}</td></tr>)}</tbody></table></div></div></>;
}

function StoreAdminProducts({ app }) {
  return <><div className="topbar"><div><h2 className="page-title">Produk Store</h2></div></div><Flash app={{ success_msg: app.success ? [app.success] : [], error_msg: app.error ? [app.error] : [] }} /><div className="settings-grid"><form className="card" action="/admin/store/products/add" method="POST"><div className="card-body"><h3>Tambah Produk</h3><input name="name" className="field-input" placeholder="Nama" required /><input name="category" className="field-input" placeholder="Kategori" /><input name="logo_url" className="field-input" placeholder="Logo URL" /><textarea name="description" className="field-input" placeholder="Deskripsi" /><button className="btn-primary">Tambah</button></div></form>{(app.products || []).map((p) => <div className="card" key={p.id}><div className="card-body"><h3>{p.name}</h3><p>Stock: {p.stock}</p><a className="btn-secondary btn-sm" href={`/admin/store/products/${p.id}/edit`}>Edit</a> <a className="btn-secondary btn-sm" href={`/admin/store/products/${p.id}/keys`}>Keys</a></div></div>)}</div></>;
}

function StoreAdminProductEdit({ app }) {
  const p = app.product || {};
  return <><div className="topbar"><div><h2 className="page-title">Edit Produk</h2></div></div><form className="card" action={`/admin/store/products/${p.id}/edit`} method="POST"><div className="card-body"><input name="name" className="field-input" defaultValue={p.name} /><input name="category" className="field-input" defaultValue={p.category} /><input name="logo_url" className="field-input" defaultValue={p.logo_url || ''} /><textarea name="description" className="field-input" defaultValue={p.description || ''} /><label><input type="checkbox" name="is_active" defaultChecked={p.is_active} /> Aktif</label><button className="btn-primary">Simpan</button></div></form><div className="card"><div className="card-body"><h3>Varian</h3><form action={`/admin/store/products/${p.id}/variants/add`} method="POST" className="field-row"><input name="name" className="field-input" placeholder="Nama varian" /><input name="price" className="field-input" type="number" placeholder="Harga" /><input name="original_price" className="field-input" type="number" placeholder="Harga coret" /><button className="btn-primary">Tambah</button></form>{(app.variants || []).map((v) => <form key={v.id} action={`/admin/store/products/${p.id}/variants/${v.id}/edit`} method="POST" className="field-row"><input name="name" className="field-input" defaultValue={v.name} /><input name="price" type="number" className="field-input" defaultValue={v.price} /><input name="original_price" type="number" className="field-input" defaultValue={v.original_price || ''} /><button className="btn-secondary">Simpan</button></form>)}</div></div></>;
}

function StoreAdminKeys({ app }) {
  const p = app.product || {};
  return <><div className="topbar"><div><h2 className="page-title">Stock Key {p.name}</h2></div></div><form className="card" action={`/admin/store/products/${p.id}/keys/add`} method="POST"><div className="card-body"><select name="variant_id" className="field-input">{(app.variants || []).map((v) => <option key={v.id} value={v.id}>{v.name}</option>)}</select><textarea name="keys_text" className="field-input" rows="8" placeholder="Satu key per baris" /><button className="btn-primary">Tambah Key</button></div></form><div className="card"><div className="table-wrap"><table className="table"><tbody>{(app.keys || []).map((k) => <tr key={k.id}><td>{k.key_value}</td><td>{k.variant_name}</td><td>{k.is_used ? 'used' : 'ready'}</td></tr>)}</tbody></table></div></div></>;
}

function StoreAdminOrders({ app }) {
  return <><div className="topbar"><div><h2 className="page-title">Orders</h2></div></div><div className="card"><div className="table-wrap"><table className="table"><thead><tr><th>ID</th><th>Produk</th><th>Status</th><th>Total</th></tr></thead><tbody>{(app.orders || []).map((o) => <tr key={o.id}><td><a href={`/store/order/${o.id}`} target="_blank">{o.id}</a></td><td>{o.product_name}</td><td>{o.status}</td><td>{rupiah(o.unique_amount)}</td></tr>)}</tbody></table></div></div></>;
}

function StoreAdminReferrals({ app }) {
  return <><div className="topbar"><div><h2 className="page-title">Referral</h2></div></div><form className="card" action="/admin/store/referrals/add" method="POST"><div className="card-body"><input name="code" className="field-input" placeholder="Kode" /><input name="discount_amount" type="number" className="field-input" placeholder="Diskon" /><input name="expired_at" type="datetime-local" className="field-input" /><button className="btn-primary">Tambah</button></div></form><div className="card"><div className="table-wrap"><table className="table"><tbody>{(app.referrals || []).map((r) => <tr key={r.id}><td>{r.code}</td><td>{rupiah(r.discount_amount)}</td><td><form action={`/admin/store/referrals/${r.id}/delete`} method="POST"><button className="btn-danger btn-sm">Hapus</button></form></td></tr>)}</tbody></table></div></div></>;
}

function App() {
  const controls = useThemeLang();
  const app = data;
  const view = app.view;
  if (view === 'login') return <Login app={app} controls={controls} />;
  if (view.startsWith('store/') && !view.startsWith('store/admin/')) {
    if (view === 'store/index') return <StoreIndex app={app} />;
    if (view === 'store/product') return <StoreProduct app={app} />;
    if (view === 'store/checkout') return <StoreCheckout app={app} />;
    if (view === 'store/order') return <StoreOrder app={app} />;
  }
  const page = (() => {
    if (view === 'dashboard') return <Dashboard app={app} />;
    if (view === 'keys') return <KeysPage app={app} />;
    if (view === 'files') return <FilesPage app={app} />;
    if (view === 'settings') return <SettingsPage app={app} />;
    if (view === 'profile') return <ProfilePage app={app} />;
    if (view === 'store/admin/dashboard') return <StoreAdminDashboard app={app} />;
    if (view === 'store/admin/products') return <StoreAdminProducts app={app} />;
    if (view === 'store/admin/product-edit') return <StoreAdminProductEdit app={app} />;
    if (view === 'store/admin/keys') return <StoreAdminKeys app={app} />;
    if (view === 'store/admin/orders') return <StoreAdminOrders app={app} />;
    if (view === 'store/admin/referrals') return <StoreAdminReferrals app={app} />;
    return <div className="card"><div className="card-body">View React belum tersedia: {view}</div></div>;
  })();
  return <Layout app={app} controls={controls}>{page}</Layout>;
}

createRoot(document.getElementById('root')).render(<App />);
