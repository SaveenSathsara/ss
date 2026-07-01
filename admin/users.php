<?php
$pageTitle  = 'Manage Users';
$activePage = 'users';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Manage <span class="grad-text">Users</span></h1>
        <p class="text-slate-500 text-sm mt-1">View, edit levels, activate or deactivate users</p>
      </div>
      <!-- Search -->
      <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">🔍</span>
        <input id="searchInput" type="text" placeholder="Search users..."
               class="input-field pl-9 w-full sm:w-64" oninput="filterUsers()">
      </div>
    </div>

    <!-- Users Table -->
    <div class="glass rounded-2xl border border-white/8 overflow-hidden fade-in" style="animation-delay:.1s">
      <div class="overflow-x-auto">
        <table class="data-table" id="usersTable">
          <thead>
            <tr>
              <th>User</th>
              <th>Username</th>
              <th>Level</th>
              <th>Status</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersBody">
            <tr>
              <td colspan="6" class="py-12 text-center">
                <div class="loader mx-auto mb-2" style="width:24px;height:24px;border-width:2px;"></div>
                <p class="text-slate-600 text-sm">Loading users...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-6 py-3 border-t border-white/5 flex items-center justify-between">
        <p class="text-xs text-slate-600" id="userCount">Loading...</p>
        <p class="text-xs text-slate-700">Real-time via Firebase</p>
      </div>
    </div>
  </main>
</div>

<!-- Edit Level Modal -->
<div id="editLevelModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1">Change User Level</h3>
    <p class="text-slate-400 text-sm mb-5">Select a new level for <strong id="editUserName" class="text-white"></strong></p>
    <select id="levelSelect" class="input-field mb-5">
      <!-- Populated by JS -->
    </select>
    <div class="flex gap-3">
      <button onclick="closeModal('editLevelModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="saveUserLevel()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save Changes</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
requireAdmin();

let allUsers = [];
let editingUserId = null;
let allLevels = [];

// Load users (real-time)
db.collection('users').orderBy('createdAt','desc').onSnapshot(snap => {
  allUsers = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  document.getElementById('userCount').textContent = `${allUsers.length} user${allUsers.length !== 1 ? 's' : ''}`;
  filterUsers();
});

// Load levels for dropdown
db.collection('userLevels').orderBy('level').get().then(snap => {
  allLevels = snap.docs.map(d => ({ id: d.id, ...d.data() }));
});

function filterUsers() {
  const q = (document.getElementById('searchInput').value || '').toLowerCase();
  const filtered = q
    ? allUsers.filter(u =>
        u.fullName?.toLowerCase().includes(q) ||
        u.username?.toLowerCase().includes(q) ||
        u.email?.toLowerCase().includes(q))
    : allUsers;
  renderUsers(filtered);
}

function renderUsers(users) {
  const tbody = document.getElementById('usersBody');
  if (users.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-slate-600 text-sm">No users found.</td></tr>`;
    return;
  }
  tbody.innerHTML = users.map(u => {
    const levelBadge = `<span class="badge text-xs ${getLevelBadgeClass(u.userLevel)}">${getLevelDisplayName(u.userLevel)}</span>`;
    const statusBadge = u.status === 'active'
      ? `<span class="badge bg-green-500/20 text-green-400 border-green-500/30 text-xs">● Active</span>`
      : `<span class="badge bg-red-500/20 text-red-400 border-red-500/30 text-xs">● Inactive</span>`;

    return `
    <tr>
      <td>
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
            ${u.fullName?.[0]?.toUpperCase() || '?'}
          </div>
          <div>
            <p class="font-semibold text-white text-sm">${u.fullName || '—'}</p>
            <p class="text-xs text-slate-600">${u.email || '—'}</p>
          </div>
        </div>
      </td>
      <td class="font-mono text-xs text-violet-300">@${u.username || '—'}</td>
      <td>${levelBadge}</td>
      <td>${statusBadge}</td>
      <td class="text-xs text-slate-500">${formatDate(u.createdAt)}</td>
      <td>
        <div class="flex items-center gap-2">
          <button onclick="openEditLevel('${u.id}','${u.fullName}','${u.userLevel}')"
                  class="text-xs px-3 py-1.5 rounded-lg bg-violet-500/15 text-violet-400 hover:bg-violet-500/25 transition border border-violet-500/25 font-medium">
            Edit Level
          </button>
          ${u.userLevel !== 'admin' ? `
          <button onclick="toggleStatus('${u.id}','${u.status}')"
                  class="text-xs px-3 py-1.5 rounded-lg transition border font-medium ${u.status === 'active'
                    ? 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border-red-500/25'
                    : 'bg-green-500/10 text-green-400 hover:bg-green-500/20 border-green-500/25'}">
            ${u.status === 'active' ? 'Deactivate' : 'Activate'}
          </button>` : ''}
        </div>
      </td>
    </tr>`;
  }).join('');
}

function openEditLevel(uid, fullName, currentLevel) {
  editingUserId = uid;
  document.getElementById('editUserName').textContent = fullName;

  const sel = document.getElementById('levelSelect');
  sel.innerHTML = allLevels.map(l =>
    `<option value="${l.id}" ${l.id === currentLevel ? 'selected' : ''}>${l.displayName || l.name}</option>`
  ).join('');

  openModal('editLevelModal');
}

async function saveUserLevel() {
  if (!editingUserId) return;
  const newLevel = document.getElementById('levelSelect').value;
  try {
    showLoading();
    await db.collection('users').doc(editingUserId).update({ userLevel: newLevel });
    showToast('User level updated! ✅', 'success');
    closeModal('editLevelModal');
  } catch(err) {
    showToast('Failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function toggleStatus(uid, currentStatus) {
  const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
  const action = newStatus === 'active' ? 'activate' : 'deactivate';

  showConfirm(`${action.charAt(0).toUpperCase() + action.slice(1)} User`,
    `Are you sure you want to <strong>${action}</strong> this user?`,
    async () => {
      try {
        showLoading();
        await db.collection('users').doc(uid).update({ status: newStatus });
        showToast(`User ${action}d successfully.`, 'success');
      } catch(err) {
        showToast('Failed: ' + err.message, 'error');
      } finally {
        hideLoading();
      }
    }, action.charAt(0).toUpperCase() + action.slice(1), newStatus === 'inactive'
  );
}
</script>
