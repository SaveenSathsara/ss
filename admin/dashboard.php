<?php
$pageTitle  = 'Admin Dashboard';
$activePage = 'dashboard';
$depth      = 1;
include '../includes/header.php';
?>

<!-- Admin Layout -->
<div class="flex min-h-screen">

  <!-- ── Sidebar ───────────────────────────────────────────── -->
  <?php include 'sidebar.php'; ?>

  <!-- ── Main content ──────────────────────────────────────── -->
  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden" id="mainContent">

    <!-- Page header -->
    <div class="flex items-center justify-between mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Admin <span class="grad-text">Dashboard</span></h1>
        <p class="text-slate-500 text-sm mt-1">Welcome back, <span class="text-violet-400 font-semibold" data-nav-username>Admin</span></p>
      </div>
      <div class="flex items-center gap-2">
        <div class="glass px-3 py-1.5 rounded-lg text-xs text-slate-400 border border-white/8">
          <span id="liveTime"></span>
        </div>
        <div class="pulse-dot"></div>
        <span class="text-xs text-slate-500">Live</span>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="stat-card fade-in" style="animation-delay:.05s">
        <div class="flex items-center justify-between mb-3">
          <p class="text-slate-500 text-sm font-medium">Total Users</p>
          <div class="w-10 h-10 rounded-xl bg-violet-500/15 border border-violet-500/20 flex items-center justify-center text-lg">👥</div>
        </div>
        <p class="text-3xl font-black text-white" id="statTotalUsers">—</p>
        <p class="text-xs text-slate-600 mt-1">Active accounts</p>
      </div>

      <div class="stat-card fade-in" style="animation-delay:.1s">
        <div class="flex items-center justify-between mb-3">
          <p class="text-slate-500 text-sm font-medium">Pending Requests</p>
          <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/20 flex items-center justify-center text-lg">📬</div>
        </div>
        <p class="text-3xl font-black text-white" id="statPending">—</p>
        <p class="text-xs text-slate-600 mt-1">Awaiting review</p>
      </div>

      <div class="stat-card fade-in" style="animation-delay:.15s">
        <div class="flex items-center justify-between mb-3">
          <p class="text-slate-500 text-sm font-medium">Site Pages</p>
          <div class="w-10 h-10 rounded-xl bg-blue-500/15 border border-blue-500/20 flex items-center justify-center text-lg">📄</div>
        </div>
        <p class="text-3xl font-black text-white" id="statPages">—</p>
        <p class="text-xs text-slate-600 mt-1">Registered pages</p>
      </div>

      <div class="stat-card fade-in" style="animation-delay:.2s">
        <div class="flex items-center justify-between mb-3">
          <p class="text-slate-500 text-sm font-medium">User Levels</p>
          <div class="w-10 h-10 rounded-xl bg-green-500/15 border border-green-500/20 flex items-center justify-center text-lg">🏷️</div>
        </div>
        <p class="text-3xl font-black text-white" id="statLevels">—</p>
        <p class="text-xs text-slate-600 mt-1">Defined levels</p>
      </div>
    </div>

    <!-- Recent Requests & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- Recent Requests -->
      <div class="lg:col-span-2 glass rounded-2xl border border-white/8 overflow-hidden fade-in" style="animation-delay:.25s">
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/8">
          <h2 class="font-bold text-white flex items-center gap-2">
            📬 <span>Recent Registration Requests</span>
            <span id="pendingBadge" class="hidden badge bg-amber-500/20 text-amber-400 border-amber-500/30 text-[10px]">0</span>
          </h2>
          <a href="requests.php" class="text-violet-400 hover:text-violet-300 text-sm transition">View all →</a>
        </div>
        <div id="recentRequests" class="divide-y divide-white/5">
          <div class="px-6 py-8 text-center">
            <div class="loader mx-auto mb-2" style="width:24px;height:24px;border-width:2px;"></div>
            <p class="text-slate-600 text-sm">Loading...</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="space-y-4 fade-in" style="animation-delay:.3s">
        <div class="glass rounded-2xl border border-white/8 p-5">
          <h2 class="font-bold text-white mb-4">⚡ Quick Actions</h2>
          <div class="space-y-2">
            <a href="requests.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/30 transition-all group">
              <span class="text-xl">📬</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">Review Requests</p>
                <p class="text-xs text-slate-600 truncate">Accept or decline new users</p>
              </div>
              <span class="text-slate-600 group-hover:text-violet-400 transition">→</span>
            </a>
            <a href="users.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/30 transition-all group">
              <span class="text-xl">👥</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">Manage Users</p>
                <p class="text-xs text-slate-600 truncate">View, edit, activate users</p>
              </div>
              <span class="text-slate-600 group-hover:text-violet-400 transition">→</span>
            </a>
            <a href="user-levels.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/30 transition-all group">
              <span class="text-xl">🏷️</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">User Levels</p>
                <p class="text-xs text-slate-600 truncate">Manage permissions matrix</p>
              </div>
              <span class="text-slate-600 group-hover:text-violet-400 transition">→</span>
            </a>
            <a href="pages.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/30 transition-all group">
              <span class="text-xl">📄</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">Site Pages</p>
                <p class="text-xs text-slate-600 truncate">Add and manage pages</p>
              </div>
              <span class="text-slate-600 group-hover:text-violet-400 transition">→</span>
            </a>
            <a href="settings.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/30 transition-all group">
              <span class="text-xl">⚙️</span>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">Settings</p>
                <p class="text-xs text-slate-600 truncate">Change password &amp; config</p>
              </div>
              <span class="text-slate-600 group-hover:text-violet-400 transition">→</span>
            </a>
          </div>
        </div>

        <!-- Recent users -->
        <div class="glass rounded-2xl border border-white/8 p-5">
          <h2 class="font-bold text-white mb-3 text-sm">👥 Recently Joined</h2>
          <div id="recentUsers" class="space-y-2">
            <div class="text-center py-3">
              <div class="loader mx-auto" style="width:18px;height:18px;border-width:2px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include '../includes/footer.php'; ?>

<script>
requireAdmin();

// Live clock
(function tick() {
  const el = document.getElementById('liveTime');
  if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
  setTimeout(tick, 1000);
})();

// Load stats + recent data (real-time)
function loadDashboard() {
  // Total users
  db.collection('users').onSnapshot(snap => {
    document.getElementById('statTotalUsers').textContent = snap.size;
  });

  // Pending requests
  db.collection('registrationRequests')
    .where('status','==','pending')
    .onSnapshot(snap => {
      const n = snap.size;
      document.getElementById('statPending').textContent = n;
      const badge = document.getElementById('pendingBadge');
      if (n > 0) { badge.textContent = n; badge.classList.remove('hidden'); }
      else badge.classList.add('hidden');
    });

  // Site pages
  db.collection('sitePages').onSnapshot(snap => {
    document.getElementById('statPages').textContent = snap.size;
  });

  // User levels
  db.collection('userLevels').onSnapshot(snap => {
    document.getElementById('statLevels').textContent = snap.size;
  });

  // Recent requests (last 5)
  db.collection('registrationRequests')
    .orderBy('requestedAt','desc')
    .limit(5)
    .onSnapshot(snap => {
      const el = document.getElementById('recentRequests');
      if (snap.empty) {
        el.innerHTML = '<div class="px-6 py-10 text-center"><p class="text-slate-600 text-sm">No requests yet.</p></div>';
        return;
      }
      el.innerHTML = '';
      snap.forEach(doc => {
        const r = doc.data();
        const statusCfg = {
          pending:  { cls:'bg-amber-500/20 text-amber-400 border-amber-500/30',  label:'Pending' },
          accepted: { cls:'bg-green-500/20 text-green-400 border-green-500/30',   label:'Accepted' },
          declined: { cls:'bg-red-500/20 text-red-400 border-red-500/30',         label:'Declined' },
        };
        const s = statusCfg[r.status] || statusCfg.pending;
        el.innerHTML += `
          <div class="flex items-center gap-4 px-6 py-4 hover:bg-white/3 transition">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-sm font-bold text-white shrink-0">
              ${r.fullName?.[0]?.toUpperCase() || '?'}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-white truncate">${r.fullName}</p>
              <p class="text-xs text-slate-600 truncate">@${r.username} · ${timeAgo(r.requestedAt)}</p>
            </div>
            <span class="badge ${s.cls} shrink-0">${s.label}</span>
            ${r.status === 'pending' ? `<a href="requests.php" class="btn-primary py-1 px-3 text-xs shrink-0">Review</a>` : ''}
          </div>`;
      });
    });

  // Recently joined users
  db.collection('users')
    .orderBy('createdAt','desc')
    .limit(4)
    .onSnapshot(snap => {
      const el = document.getElementById('recentUsers');
      if (snap.empty) {
        el.innerHTML = '<p class="text-slate-600 text-xs text-center py-2">No users yet</p>';
        return;
      }
      el.innerHTML = '';
      snap.forEach(doc => {
        const u = doc.data();
        el.innerHTML += `
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-xs font-bold text-white">
              ${u.fullName?.[0]?.toUpperCase() || '?'}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-slate-300 truncate">${u.fullName}</p>
              <p class="text-[10px] text-slate-600">@${u.username}</p>
            </div>
            <span class="badge text-[10px] ${getLevelBadgeClass(u.userLevel)}">${getLevelDisplayName(u.userLevel).replace(/^[^\s]+\s/,'')}</span>
          </div>`;
      });
    });
}

auth.onAuthStateChanged(user => { if (user) loadDashboard(); });
</script>
