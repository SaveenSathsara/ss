<?php
$pageTitle  = 'Admin Settings';
$activePage = 'settings';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden max-w-3xl">

    <div class="mb-8 fade-in">
      <h1 class="text-3xl font-black text-white">Admin <span class="grad-text">Settings</span></h1>
      <p class="text-slate-500 text-sm mt-1">Manage your account and system configuration</p>
    </div>

    <!-- Profile Card -->
    <div class="glass rounded-2xl border border-white/8 overflow-hidden mb-6 fade-in" style="animation-delay:.05s">
      <div class="px-6 py-4 border-b border-white/8 flex items-center gap-2">
        <span class="text-lg">👤</span>
        <h2 class="font-bold text-white">Admin Profile</h2>
      </div>
      <div class="p-6 flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-2xl font-black text-white">S</div>
        <div>
          <p class="text-lg font-bold text-white" data-nav-fullname>Saveen Admin</p>
          <p class="text-sm text-violet-400 font-semibold">@<span data-nav-username>saveen</span></p>
          <p class="badge bg-amber-500/20 text-amber-400 border-amber-500/30 mt-2">👑 Administrator</p>
        </div>
      </div>
    </div>

    <!-- Change Password Card -->
    <div class="glass rounded-2xl border border-white/8 overflow-hidden mb-6 fade-in" style="animation-delay:.1s">
      <div class="px-6 py-4 border-b border-white/8 flex items-center gap-2">
        <span class="text-lg">🔒</span>
        <h2 class="font-bold text-white">Change Password</h2>
      </div>
      <div class="p-6">
        <form onsubmit="handlePasswordChange(event)" class="space-y-4 max-w-md">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Current Password</label>
            <div class="relative">
              <input id="currentPw" type="password" class="input-field pr-11" placeholder="Enter current password" required>
              <button type="button" onclick="togglePw('currentPw')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">👁</button>
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">New Password</label>
            <div class="relative">
              <input id="newPw" type="password" class="input-field pr-11" placeholder="New password (min 6 chars)" required minlength="6">
              <button type="button" onclick="togglePw('newPw')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">👁</button>
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Confirm New Password</label>
            <input id="confirmPw" type="password" class="input-field" placeholder="Repeat new password" required minlength="6">
            <p id="pwMatchMsg2" class="text-xs mt-1 hidden"></p>
          </div>
          <button type="submit" class="btn-primary py-2.5 px-6 rounded-xl">
            🔐 Update Password
          </button>
        </form>
      </div>
    </div>

    <!-- Site Config Card -->
    <div class="glass rounded-2xl border border-white/8 overflow-hidden mb-6 fade-in" style="animation-delay:.15s">
      <div class="px-6 py-4 border-b border-white/8 flex items-center gap-2">
        <span class="text-lg">⚙️</span>
        <h2 class="font-bold text-white">Site Configuration</h2>
      </div>
      <div class="p-6 space-y-5">
        <div class="flex items-center justify-between py-3 border-b border-white/5">
          <div>
            <p class="text-sm font-semibold text-white">Allow New Registrations</p>
            <p class="text-xs text-slate-500 mt-0.5">Users can submit registration requests</p>
          </div>
          <label class="perm-toggle">
            <input type="checkbox" id="configRegistrations" checked onchange="saveSiteConfig()">
            <span class="slider"></span>
          </label>
        </div>
        <div class="flex items-center justify-between py-3 border-b border-white/5">
          <div>
            <p class="text-sm font-semibold text-white">Maintenance Mode</p>
            <p class="text-xs text-slate-500 mt-0.5">Redirect all non-admin visitors to maintenance page</p>
          </div>
          <label class="perm-toggle">
            <input type="checkbox" id="configMaintenance" onchange="saveSiteConfig()">
            <span class="slider"></span>
          </label>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Site Name</label>
          <div class="flex gap-3">
            <input id="configSiteName" type="text" class="input-field" value="Saveen Portal">
            <button onclick="saveSiteConfig()" class="btn-primary py-2 px-4 text-sm rounded-xl whitespace-nowrap">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Danger Zone -->
    <div class="glass rounded-2xl border border-red-500/20 overflow-hidden fade-in" style="animation-delay:.2s">
      <div class="px-6 py-4 border-b border-red-500/20 flex items-center gap-2 bg-red-500/5">
        <span class="text-lg">⚠️</span>
        <h2 class="font-bold text-red-400">Danger Zone</h2>
      </div>
      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-sm font-semibold text-white">Clear All Declined Requests</p>
            <p class="text-xs text-slate-500 mt-0.5">Permanently delete all declined registration requests</p>
          </div>
          <button onclick="clearDeclinedRequests()" class="btn-danger py-2 px-4 text-xs rounded-xl whitespace-nowrap">
            Clear Declined
          </button>
        </div>
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-sm font-semibold text-white">Export User List</p>
            <p class="text-xs text-slate-500 mt-0.5">Download all user data as JSON</p>
          </div>
          <button onclick="exportUsers()" class="btn-ghost py-2 px-4 text-xs rounded-xl whitespace-nowrap">
            Export JSON
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

<?php include '../includes/footer.php'; ?>

<script>
requireAdmin();

function togglePw(id) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
}

document.getElementById('confirmPw').addEventListener('input', function() {
  const msg = document.getElementById('pwMatchMsg2');
  msg.classList.remove('hidden');
  if (this.value === document.getElementById('newPw').value) {
    msg.textContent = '✓ Passwords match';
    msg.className = 'text-xs mt-1 text-green-400';
  } else {
    msg.textContent = '✕ Passwords do not match';
    msg.className = 'text-xs mt-1 text-red-400';
  }
});

function handlePasswordChange(e) {
  e.preventDefault();
  const curr    = document.getElementById('currentPw').value;
  const newPw   = document.getElementById('newPw').value;
  const confirm = document.getElementById('confirmPw').value;
  if (newPw !== confirm) { showToast('New passwords do not match.', 'error'); return; }
  changePassword(curr, newPw);
}

// Load site config
async function loadSiteConfig() {
  try {
    const doc = await db.collection('adminSettings').doc('siteConfig').get();
    if (doc.exists) {
      const cfg = doc.data();
      document.getElementById('configRegistrations').checked = cfg.allowRegistrations !== false;
      document.getElementById('configMaintenance').checked   = !!cfg.maintenanceMode;
      document.getElementById('configSiteName').value        = cfg.siteName || 'Saveen Portal';
    }
  } catch(_) {}
}

async function saveSiteConfig() {
  try {
    await db.collection('adminSettings').doc('siteConfig').set({
      allowRegistrations: document.getElementById('configRegistrations').checked,
      maintenanceMode:    document.getElementById('configMaintenance').checked,
      siteName:           document.getElementById('configSiteName').value.trim(),
      updatedAt:          firebase.firestore.FieldValue.serverTimestamp(),
    }, { merge: true });
    showToast('Config saved! ✅', 'success', 2000);
  } catch(err) {
    showToast('Failed: ' + err.message, 'error');
  }
}

async function clearDeclinedRequests() {
  showConfirm('Clear Declined Requests',
    'This will permanently delete all declined registration requests. This cannot be undone.',
    async () => {
      try {
        showLoading();
        const snap = await db.collection('registrationRequests').where('status','==','declined').get();
        const batch = db.batch();
        snap.forEach(doc => batch.delete(doc.ref));
        await batch.commit();
        showToast(`${snap.size} declined request(s) cleared.`, 'info');
      } catch(err) {
        showToast('Failed: ' + err.message, 'error');
      } finally {
        hideLoading();
      }
    }, 'Clear All', true
  );
}

async function exportUsers() {
  try {
    showLoading();
    const snap = await db.collection('users').get();
    const users = snap.docs.map(d => {
      const u = d.data();
      return { id:d.id, fullName:u.fullName, username:u.username, email:u.email, userLevel:u.userLevel, status:u.status };
    });
    const blob = new Blob([JSON.stringify(users, null, 2)], { type:'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = `saveen-users-${Date.now()}.json`;
    document.body.appendChild(a); a.click(); a.remove();
    URL.revokeObjectURL(url);
    showToast('User list exported! ✅', 'success');
  } catch(err) {
    showToast('Export failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

auth.onAuthStateChanged(user => { if (user) loadSiteConfig(); });
</script>
