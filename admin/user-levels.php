<?php
$pageTitle  = 'User Levels & Permissions';
$activePage = 'levels';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">User Levels & <span class="grad-text">Permissions</span></h1>
        <p class="text-slate-500 text-sm mt-1">Toggle permissions per user level in real-time</p>
      </div>
      <button onclick="openModal('createLevelModal')" class="btn-primary py-2.5 px-5 rounded-xl">
        + New Level
      </button>
    </div>

    <!-- Levels list -->
    <div id="levelsContainer" class="space-y-5">
      <div class="glass rounded-2xl p-10 text-center border border-white/8">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading levels...</p>
      </div>
    </div>

  </main>
</div>

<!-- Create Level Modal -->
<div id="createLevelModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1">Create New Level</h3>
    <p class="text-slate-400 text-sm mb-5">Define a new user level. You can configure permissions after creation.</p>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Level ID (no spaces)</label>
        <input id="newLevelId" type="text" class="input-field" placeholder="e.g. vip_user" pattern="[a-z0-9_]+" required>
        <p class="text-xs text-slate-600 mt-1">Lowercase letters, numbers, underscores only</p>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Display Name</label>
        <input id="newLevelName" type="text" class="input-field" placeholder="e.g. VIP User" required>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Icon + Display Name</label>
        <input id="newLevelDisplay" type="text" class="input-field" placeholder="e.g. 💎 VIP User">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Level Order (lower = higher privilege)</label>
        <input id="newLevelOrder" type="number" class="input-field" value="5" min="1" max="100">
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('createLevelModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="createLevel()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Create Level</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
requireAdmin();

const PERM_LABELS = {
  view_home:           '🏠 View Home',
  view_about:          'ℹ️ View About',
  view_contact:        '📬 View Contact',
  view_all_pages:      '📄 View All Pages',
  view_downloads:      '📥 View Downloads',
  view_user_dashboard: '📊 User Dashboard',
  edit_own_profile:    '✏️ Edit Own Profile',
  add_own_marks:       '📝 Add Own Marks',
  view_others_marks:   '👀 View Others Marks',
  manage_users:        '👥 Manage Users',
  manage_user_levels:  '🏷️ Manage User Levels',
  manage_pages:        '📑 Manage Pages',
  manage_requests:     '📬 Manage Requests',
  manage_settings:     '⚙️ Manage Settings',
  manage_downloads:    '📦 Manage Downloads',
  manage_subjects:     '📚 Manage Subjects',
  add_schools:         '🏫 Add Schools',
  view_admin_dashboard:'👑 Admin Dashboard',
};

const PERM_GROUPS = {
  'Site Access': ['view_home','view_about','view_contact','view_all_pages','view_downloads'],
  'User Features': ['view_user_dashboard','edit_own_profile','add_own_marks','view_others_marks'],
  'Admin Powers': ['manage_users','manage_user_levels','manage_pages','manage_requests','manage_settings','manage_downloads','manage_subjects','add_schools','view_admin_dashboard'],
};

// Real-time listener on userLevels
db.collection('userLevels').orderBy('level').onSnapshot(snap => {
  const levels = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  renderLevels(levels);
});

function renderLevels(levels) {
  const container = document.getElementById('levelsContainer');
  if (levels.length === 0) {
    container.innerHTML = `<div class="glass rounded-2xl p-12 text-center border border-white/8">
      <p class="text-slate-500">No levels found. Run setup.html first.</p></div>`;
    return;
  }

  container.innerHTML = levels.map(level => `
    <div class="glass rounded-2xl border border-white/8 overflow-hidden hover:border-violet-500/20 transition-all fade-in" id="levelCard-${level.id}">
      <!-- Level header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-white/8">
        <div class="flex items-center gap-3">
          <span class="text-2xl">${(level.displayName || '').split(' ')[0] || '🏷️'}</span>
          <div>
            <h3 class="font-bold text-white">${level.displayName || level.name}</h3>
            <p class="text-xs text-slate-500 font-mono">id: ${level.id} · level: ${level.level}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button onclick="toggleAllPerms('${level.id}', true)"
                  class="text-xs px-3 py-1.5 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500/20 transition border border-green-500/20 font-medium">
            Grant All
          </button>
          <button onclick="toggleAllPerms('${level.id}', false)"
                  class="text-xs px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition border border-red-500/20 font-medium">
            Revoke All
          </button>
          ${level.id !== 'admin' ? `<button onclick="deleteLevel('${level.id}','${level.displayName}')"
                  class="text-xs px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition border border-red-500/20 font-medium">
            Delete
          </button>` : ''}
        </div>
      </div>

      <!-- Permissions grid grouped -->
      <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        ${Object.entries(PERM_GROUPS).map(([group, perms]) => `
          <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">${group}</p>
            <div class="space-y-2">
              ${perms.map(perm => {
                const isEnabled = level.permissions?.[perm] === true;
                const isAdmin = level.id === 'admin';
                return `
                <div class="flex items-center justify-between gap-3">
                  <span class="text-sm text-slate-300">${PERM_LABELS[perm] || perm}</span>
                  <label class="perm-toggle ${isAdmin ? 'opacity-50 pointer-events-none' : ''}" title="${isAdmin ? 'Admin always has all permissions' : ''}">
                    <input type="checkbox" ${isEnabled ? 'checked' : ''} ${isAdmin ? 'disabled' : ''}
                           onchange="updatePermission('${level.id}','${perm}',this.checked)">
                    <span class="slider"></span>
                  </label>
                </div>`;
              }).join('')}
            </div>
          </div>
        `).join('')}
      </div>
    </div>
  `).join('');
}

async function updatePermission(levelId, perm, value) {
  if (levelId === 'admin') return; // Admin always has all
  try {
    await db.collection('userLevels').doc(levelId).update({
      [`permissions.${perm}`]: value
    });
    showToast(`Permission "${PERM_LABELS[perm]}" ${value ? 'granted ✅' : 'revoked ❌'}`, value ? 'success' : 'warning', 2500);
  } catch(err) {
    showToast('Failed to update: ' + err.message, 'error');
  }
}

async function toggleAllPerms(levelId, value) {
  if (levelId === 'admin') return;
  try {
    showLoading();
    const updates = {};
    Object.keys(PERM_LABELS).forEach(p => updates[`permissions.${p}`] = value);
    await db.collection('userLevels').doc(levelId).update(updates);
    showToast(value ? 'All permissions granted ✅' : 'All permissions revoked ❌', value ? 'success' : 'warning');
  } catch(err) {
    showToast('Failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function createLevel() {
  const id      = document.getElementById('newLevelId').value.trim().toLowerCase().replace(/\s/g,'_');
  const name    = document.getElementById('newLevelName').value.trim();
  const display = document.getElementById('newLevelDisplay').value.trim() || name;
  const level   = parseInt(document.getElementById('newLevelOrder').value) || 5;

  if (!id || !name) { showToast('Please fill all required fields.', 'warning'); return; }
  if (!/^[a-z0-9_]+$/.test(id)) { showToast('ID must be lowercase letters, numbers, underscores only.', 'warning'); return; }

  try {
    showLoading();
    await db.collection('userLevels').doc(id).set({
      name, displayName: display, level,
      permissions: Object.fromEntries(Object.keys(PERM_LABELS).map(p => [p, false]))
    });
    showToast(`Level "${display}" created! ✅`, 'success');
    closeModal('createLevelModal');
    document.getElementById('newLevelId').value = '';
    document.getElementById('newLevelName').value = '';
    document.getElementById('newLevelDisplay').value = '';
  } catch(err) {
    showToast('Failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function deleteLevel(id, name) {
  showConfirm(`Delete Level "${name}"`,
    `This will permanently delete the user level. Users currently on this level will need to be reassigned.`,
    async () => {
      try {
        showLoading();
        await db.collection('userLevels').doc(id).delete();
        showToast(`Level "${name}" deleted.`, 'info');
      } catch(err) {
        showToast('Failed: ' + err.message, 'error');
      } finally {
        hideLoading();
      }
    }, 'Delete Level', true
  );
}
</script>
