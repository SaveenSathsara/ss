<?php
$pageTitle  = 'My Dashboard';
$activePage = 'home';
$depth      = 1;
include '../includes/header.php';
include '../includes/nav.php';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

  <!-- Welcome banner -->
  <div class="glass-strong rounded-2xl border border-violet-500/15 p-6 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-5 fade-in relative overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background:radial-gradient(ellipse at top left,#7c3aed,transparent 60%)"></div>
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-2xl font-black text-white relative z-10" id="avatarLetter">?</div>
    <div class="relative z-10 flex-1">
      <p class="text-slate-400 text-sm mb-0.5">Welcome back,</p>
      <h1 class="text-2xl font-black text-white" data-nav-fullname>Loading...</h1>
      <div class="flex flex-wrap items-center gap-2 mt-2">
        <span class="text-xs text-slate-500 font-mono">@<span data-nav-username>...</span></span>
        <span class="badge" id="levelBadge">Loading...</span>
        <span class="badge bg-green-500/20 text-green-400 border-green-500/30">● Active</span>
      </div>
    </div>
    <a href="profile.php" class="btn-ghost py-2 px-4 text-sm rounded-xl relative z-10">✏️ Edit Profile</a>
  </div>

  <!-- Stats row -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="stat-card text-center fade-in" style="animation-delay:.05s">
      <p class="text-2xl font-black text-white" id="myLevel">—</p>
      <p class="text-xs text-slate-500 mt-1">My Level</p>
    </div>
    <div class="stat-card text-center fade-in" style="animation-delay:.1s">
      <p class="text-2xl font-black text-white" id="permCount">—</p>
      <p class="text-xs text-slate-500 mt-1">Permissions</p>
    </div>
    <div class="stat-card text-center fade-in" style="animation-delay:.15s">
      <p class="text-2xl font-black text-white" id="pageCount">—</p>
      <p class="text-xs text-slate-500 mt-1">Accessible Pages</p>
    </div>
    <div class="stat-card text-center fade-in" style="animation-delay:.2s">
      <p class="text-2xl font-black grad-text" id="joinDate">—</p>
      <p class="text-xs text-slate-500 mt-1">Member Since</p>
    </div>
  </div>

  <!-- Two columns -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- My Permissions -->
    <div class="glass rounded-2xl border border-white/8 overflow-hidden fade-in" style="animation-delay:.25s">
      <div class="px-5 py-4 border-b border-white/8">
        <h2 class="font-bold text-white">🔑 My Permissions</h2>
        <p class="text-xs text-slate-500 mt-0.5">What you can access on this portal</p>
      </div>
      <div id="permList" class="p-5 space-y-2">
        <div class="loader mx-auto" style="width:20px;height:20px;border-width:2px;"></div>
      </div>
    </div>

    <!-- Accessible Pages -->
    <div class="glass rounded-2xl border border-white/8 overflow-hidden fade-in" style="animation-delay:.3s">
      <div class="px-5 py-4 border-b border-white/8">
        <h2 class="font-bold text-white">📄 Accessible Pages</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pages available to you</p>
      </div>
      <div id="pageList" class="p-4 space-y-2">
        <div class="loader mx-auto" style="width:20px;height:20px;border-width:2px;"></div>
      </div>
      <!-- Academic Pages -->
      <div class="px-5 py-4 border-b border-white/8 mt-6">
        <h2 class="font-bold text-white">🎓 Academic Features</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage your academic records</p>
      </div>
      <div class="p-4 space-y-2">
        <a href="student-profiles.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/20 transition-all group">
          <span class="text-xl">🎓</span>
          <div class="flex-1">
            <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">My Student Profiles</p>
            <p class="text-xs text-slate-600">Register Entrance Numbers</p>
          </div>
          <span class="text-slate-600 group-hover:text-violet-400 transition text-sm">→</span>
        </a>
        <a href="my-terms.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/20 transition-all group">
          <span class="text-xl">📝</span>
          <div class="flex-1">
            <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">My Term Marks</p>
            <p class="text-xs text-slate-600">View and Add Marks</p>
          </div>
          <span class="text-slate-600 group-hover:text-violet-400 transition text-sm">→</span>
        </a>
      </div>
    </div>

  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
const PERM_LABELS = {
  view_home:'🏠 View Home', view_about:'ℹ️ View About', view_contact:'📬 View Contact',
  view_all_pages:'📄 View All Pages', view_user_dashboard:'📊 User Dashboard',
  edit_own_profile:'✏️ Edit Own Profile', add_own_marks:'📝 Add Own Marks',
  view_others_marks:'👀 View Others Marks', manage_users:'👥 Manage Users',
  manage_user_levels:'🏷️ Manage Levels', manage_pages:'📑 Manage Pages',
  manage_requests:'📬 Manage Requests', manage_settings:'⚙️ Settings',
  manage_downloads:'📦 Manage Downloads', manage_subjects:'📚 Manage Subjects',
  add_schools:'🏫 Add Schools', view_admin_dashboard:'👑 Admin Dashboard',
};

auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.html'; return; }

  const userDoc = await db.collection('users').doc(user.uid).get();
  if (!userDoc.exists) { auth.signOut(); return; }

  const ud = userDoc.data();

  // Avatar letter
  const letter = ud.fullName?.[0]?.toUpperCase() || 'U';
  document.getElementById('avatarLetter').textContent = letter;

  // Level badge
  const badge = document.getElementById('levelBadge');
  badge.textContent = getLevelDisplayName(ud.userLevel);
  badge.className = 'badge ' + getLevelBadgeClass(ud.userLevel);

  // Stats
  document.getElementById('myLevel').textContent = getLevelDisplayName(ud.userLevel).replace(/^[^\s]+\s/,'');
  document.getElementById('joinDate').textContent = ud.createdAt
    ? new Date(ud.createdAt.toDate()).getFullYear() : '—';

  // Load level permissions
  const levelDoc = await db.collection('userLevels').doc(ud.userLevel).get();
  const perms = levelDoc.exists ? (levelDoc.data().permissions || {}) : {};
  const isAdmin = ud.userLevel === 'admin';

  // Permission count
  const grantedCount = isAdmin
    ? Object.keys(PERM_LABELS).length
    : Object.values(perms).filter(Boolean).length;
  document.getElementById('permCount').textContent = grantedCount;

  // Render permission list
  const permList = document.getElementById('permList');
  permList.innerHTML = Object.entries(PERM_LABELS).map(([key, label]) => {
    const has = isAdmin || perms[key] === true;
    return `
    <div class="flex items-center gap-3 py-1.5">
      <span class="${has ? 'text-green-400' : 'text-slate-700'} text-lg">${has ? '✓' : '✕'}</span>
      <span class="text-sm ${has ? 'text-slate-300' : 'text-slate-600 line-through'}">${label}</span>
    </div>`;
  }).join('');

  // Load accessible pages
  const pagesSnap = await db.collection('sitePages').orderBy('order').get();
  const pages = pagesSnap.docs.map(d => ({ id: d.id, ...d.data() }));
  const accessible = pages.filter(p => p.isPublic || isAdmin || p.requiredLevel === ud.userLevel ||
    (levelDoc.exists && levelDoc.data().level <= (levels_order(p.requiredLevel))));

  document.getElementById('pageCount').textContent = accessible.length;

  const pageList = document.getElementById('pageList');
  if (accessible.length === 0) {
    pageList.innerHTML = '<p class="text-slate-600 text-sm text-center py-4">No accessible pages yet.</p>';
  } else {
    pageList.innerHTML = accessible.map(p => `
      <a href="../${p.filename}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 border border-white/5 hover:border-violet-500/20 transition-all group">
        <span class="text-xl">${p.icon || '📄'}</span>
        <div class="flex-1">
          <p class="text-sm font-medium text-white group-hover:text-violet-300 transition">${p.title}</p>
          <p class="text-xs text-slate-600">${p.slug || p.filename}</p>
        </div>
        <span class="text-slate-600 group-hover:text-violet-400 transition text-sm">→</span>
      </a>
    `).join('');
  }
});

function levels_order(levelId) {
  const orders = { admin:0, pro_user:1, normal_user:2, low_user:3, lower_user:4 };
  return orders[levelId] ?? 99;
}
</script>
