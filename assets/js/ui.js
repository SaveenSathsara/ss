// ============================================================
//  ui.js  –  UI Utilities (Toasts, Modals, Nav updates, etc.)
// ============================================================

// ── Toast Notifications ───────────────────────────────────────────────────────

function showToast(message, type = 'info', duration = 4500) {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-2 max-w-xs w-full';
    document.body.appendChild(container);
  }

  const cfg = {
    success: { bg: 'from-emerald-500 to-green-600',  icon: '✓', ring: 'ring-emerald-400/30' },
    error:   { bg: 'from-red-500 to-rose-600',        icon: '✕', ring: 'ring-red-400/30'     },
    warning: { bg: 'from-amber-500 to-yellow-600',    icon: '⚠', ring: 'ring-amber-400/30'  },
    info:    { bg: 'from-blue-500 to-indigo-600',     icon: 'ℹ', ring: 'ring-blue-400/30'   },
  };
  const c = cfg[type] || cfg.info;

  const toast = document.createElement('div');
  toast.className = [
    'flex items-start gap-3 px-4 py-3 rounded-xl text-white shadow-2xl ring-1',
    `bg-gradient-to-r ${c.bg} ${c.ring}`,
    'transform translate-x-[120%] transition-transform duration-300 ease-out',
  ].join(' ');
  toast.innerHTML = `
    <span class="text-lg font-bold mt-0.5 shrink-0">${c.icon}</span>
    <span class="flex-1 text-sm leading-snug">${message}</span>
    <button class="shrink-0 opacity-70 hover:opacity-100 transition-opacity mt-0.5" onclick="this.closest('[id]')?.remove()">✕</button>
  `;
  container.appendChild(toast);
  requestAnimationFrame(() => toast.classList.remove('translate-x-[120%]'));

  setTimeout(() => {
    toast.classList.add('translate-x-[120%]');
    setTimeout(() => toast.remove(), 320);
  }, duration);
}

// ── Loading Overlay ───────────────────────────────────────────────────────────

function showLoading() {
  document.body.style.cursor = 'wait';
}

function hideLoading() {
  document.body.style.cursor = 'default';
}

// ── Modal Helpers ─────────────────────────────────────────────────────────────

function openModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.remove('hidden');
  setTimeout(() => {
    m.querySelector('.modal-box')?.classList.remove('opacity-0', 'scale-95');
  }, 10);
}

function closeModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.querySelector('.modal-box')?.classList.add('opacity-0', 'scale-95');
  setTimeout(() => m.classList.add('hidden'), 200);
}

// ── Confirm Dialog ────────────────────────────────────────────────────────────

function showConfirm(title, message, onConfirm, btnLabel = 'Confirm', danger = true) {
  const overlay = document.createElement('div');
  overlay.className = 'fixed inset-0 bg-black/70 backdrop-blur-sm z-[9998] flex items-center justify-center p-4';
  overlay.innerHTML = `
    <div class="bg-[#1a1a2e] border border-white/10 rounded-2xl p-6 max-w-md w-full shadow-2xl transform transition-all">
      <h3 class="text-xl font-bold text-white mb-2">${title}</h3>
      <p class="text-slate-400 mb-6 text-sm">${message}</p>
      <div class="flex gap-3 justify-end">
        <button id="__cfCancel" class="px-4 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition text-sm">Cancel</button>
        <button id="__cfOk" class="px-4 py-2 rounded-lg text-white transition text-sm font-semibold ${danger ? 'bg-red-500 hover:bg-red-600' : 'bg-violet-600 hover:bg-violet-700'}">${btnLabel}</button>
      </div>
    </div>
  `;
  document.body.appendChild(overlay);
  overlay.querySelector('#__cfOk').onclick    = () => { overlay.remove(); onConfirm && onConfirm(); };
  overlay.querySelector('#__cfCancel').onclick = () => overlay.remove();
  overlay.onclick = e => { if (e.target === overlay) overlay.remove(); };
}

// ── Nav helpers ───────────────────────────────────────────────────────────────

function updateNavForLoggedInUser(userData) {
  document.querySelectorAll('[data-nav-username]').forEach(el => {
    el.textContent = userData.username || userData.fullName || 'User';
  });
  document.querySelectorAll('[data-nav-fullname]').forEach(el => {
    el.textContent = userData.fullName || 'User';
  });
  
  // Profile Picture logic for navbar/sidebar
  document.querySelectorAll('.nav-user .w-7, .w-8, .w-16, #userMenuWrap .w-7').forEach(el => {
    if (el.tagName === 'DIV' && el.classList.contains('rounded-full') && userData.profilePic) {
      el.innerHTML = `<img src="${userData.profilePic}" class="w-full h-full rounded-full object-cover">`;
    } else if (el.tagName === 'DIV' && el.classList.contains('rounded-full') && !userData.profilePic) {
      const char = (userData.fullName?.[0] || 'U').toUpperCase();
      el.innerHTML = `<span data-nav-username>${char}</span>`;
    }
  });

  document.querySelectorAll('[data-nav-level]').forEach(el => {
    el.textContent = getLevelDisplayName(userData.userLevel);
  });
  document.querySelectorAll('.nav-guest').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.nav-user').forEach(el => el.classList.remove('hidden'));
  if (userData.userLevel === 'admin') {
    document.querySelectorAll('.nav-admin').forEach(el => el.classList.remove('hidden'));
  }
  applyPermissionsToDOM();
}

function updateNavForLoggedOutUser() {
  document.querySelectorAll('.nav-guest').forEach(el => el.classList.remove('hidden'));
  document.querySelectorAll('.nav-user').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.nav-admin').forEach(el => el.classList.add('hidden'));
  applyPermissionsToDOM();
}

// ── Utility Formatters ────────────────────────────────────────────────────────

function getLevelDisplayName(levelId) {
  const names = {
    admin:       '👑 Admin',
    pro_user:    '⭐ Pro User',
    normal_user: '👤 Normal User',
    low_user:    '🔵 Low User',
    lower_user:  '⚪ Lower User',
  };
  return names[levelId] || levelId;
}

function getLevelBadgeClass(levelId) {
  const classes = {
    admin:       'bg-amber-500/20 text-amber-400 border-amber-500/30',
    pro_user:    'bg-violet-500/20 text-violet-400 border-violet-500/30',
    normal_user: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    low_user:    'bg-slate-500/20 text-slate-400 border-slate-500/30',
    lower_user:  'bg-slate-700/30 text-slate-500 border-slate-600/30',
  };
  return classes[levelId] || 'bg-slate-500/20 text-slate-400';
}

function formatDate(ts) {
  if (!ts) return '—';
  const d = ts.toDate ? ts.toDate() : new Date(ts);
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function timeAgo(ts) {
  if (!ts) return '—';
  const d   = ts.toDate ? ts.toDate() : new Date(ts);
  const sec = Math.floor((Date.now() - d) / 1000);
  if (sec < 60)   return 'just now';
  if (sec < 3600) return `${Math.floor(sec / 60)}m ago`;
  if (sec < 86400)return `${Math.floor(sec / 3600)}h ago`;
  if (sec < 604800)return `${Math.floor(sec / 86400)}d ago`;
  return formatDate(ts);
}

// ── Tab Switcher (Login / Register) ──────────────────────────────────────────

function switchTab(tab) {
  const tabs = ['login', 'register'];
  tabs.forEach(t => {
    const btn = document.getElementById(`tab-${t}`);
    const panel = document.getElementById(`panel-${t}`);
    if (!btn || !panel) return;
    if (t === tab) {
      btn.classList.add('border-violet-500', 'text-violet-400');
      btn.classList.remove('border-transparent', 'text-slate-500');
      panel.classList.remove('hidden');
    } else {
      btn.classList.remove('border-violet-500', 'text-violet-400');
      btn.classList.add('border-transparent', 'text-slate-500');
      panel.classList.add('hidden');
    }
  });
}

// ── Sidebar toggle (mobile) ───────────────────────────────────────────────────

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!sidebar) return;
  sidebar.classList.toggle('-translate-x-full');
  if (overlay) overlay.classList.toggle('hidden');
}
