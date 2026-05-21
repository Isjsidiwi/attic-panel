document.addEventListener('DOMContentLoaded', () => {
  // Tab switching logic (no inline handlers)
  const tabButtons = document.querySelectorAll('.tabs .tab-btn');

  function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
    if (btn) btn.classList.add('active');
    try { localStorage.setItem('settingsTab', tabId); } catch (e) {}
  }

  tabButtons.forEach(btn => {
    const tabId = btn.dataset.tab;
    if (!tabId) return;
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      switchTab(tabId, btn);
    });
  });

  // Toggle salt visibility
  const saltInput = document.getElementById('salt-input');
  const saltToggle = document.getElementById('salt-toggle');
  if (saltInput) saltInput.type = 'password';
  function toggleSalt() {
    if (!saltInput || !saltToggle) return;
    if (saltInput.type === 'password') {
      saltInput.type = 'text';
      saltToggle.textContent = 'HIDE';
    } else {
      saltInput.type = 'password';
      saltToggle.textContent = 'SHOW';
    }
  }
  if (saltToggle) {
    saltToggle.addEventListener('click', (e) => { e.preventDefault(); toggleSalt(); });
    saltToggle.textContent = 'SHOW';
  }

  // Restore saved tab state
  try {
    const savedTab = localStorage.getItem('settingsTab');
    if (savedTab && document.getElementById(savedTab)) {
      const btn = document.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
      if (btn) switchTab(savedTab, btn);
    }
  } catch (e) {}
});
