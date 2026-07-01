<?php
/**
 * admin/sidebar.php
 * Shared admin sidebar navigation. Included by every admin page.
 */
$activePage = $activePage ?? '';
?>

<!-- Sidebar overlay (mobile) -->
<div id="sidebarOverlay" class="hidden lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-39" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed lg:sticky lg:top-0 top-0 left-0 h-screen w-64 flex flex-col z-40 lg:z-auto
              transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
       style="background: rgba(7,7,26,.95); backdrop-filter:blur(20px); border-right:1px solid rgba(255,255,255,.07);">

  <!-- Brand -->
  <div class="px-5 py-5 border-b border-white/7">
    <a href="../index.html" class="flex items-center gap-2 group">
      <span class="text-2xl">⚡</span>
      <div>
        <p class="font-black grad-text leading-none">Saveen Portal</p>
        <p class="text-[10px] text-amber-400/70 font-semibold tracking-wider mt-0.5">ADMIN PANEL</p>
      </div>
    </a>
  </div>

  <!-- Admin info -->
  <div class="px-4 py-4 border-b border-white/7">
    <div class="flex items-center gap-3 glass px-3 py-2.5 rounded-xl border border-amber-500/15 bg-amber-500/5">
      <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-sm font-bold text-white" data-nav-username>A</div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-white truncate" data-nav-username>Admin</p>
        <p class="text-[10px] text-amber-400 font-medium">👑 Administrator</p>
      </div>
      <div class="pulse-dot shrink-0"></div>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
    <p class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider px-3 mb-2">Overview</p>

    <a href="dashboard.php"
       class="sidebar-link <?= $activePage==='dashboard' ? 'active' : '' ?>">
      <span class="icon">📊</span> Dashboard
    </a>

    <p class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider px-3 mb-2 mt-4">Management</p>

    <a href="requests.php"
       class="sidebar-link <?= $activePage==='requests' ? 'active' : '' ?> relative">
      <span class="icon">📬</span> Requests
      <span id="sidebarPendingBadge" class="hidden ml-auto badge bg-amber-500/20 text-amber-400 border-amber-500/30 text-[10px]">0</span>
    </a>
    <a href="users.php"
       class="sidebar-link <?= $activePage==='users' ? 'active' : '' ?>">
      <span class="icon">👥</span> Users
    </a>

    <a href="user-levels.php"
       class="sidebar-link <?= $activePage==='levels' ? 'active' : '' ?>">
      <span class="icon">🏷️</span> User Levels
    </a>

    <a href="pages.php"
       class="sidebar-link <?= $activePage==='pages' ? 'active' : '' ?>">
      <span class="icon">📄</span> Site Pages
    </a>

    <a href="downloads.php"
       class="sidebar-link <?= $activePage==='downloads' ? 'active' : '' ?>">
      <span class="icon">📥</span> Downloads
    </a>

    <p class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider px-3 mb-2 mt-4">Academic Data</p>
    
    <a href="schools.php"
       class="sidebar-link <?= $activePage==='schools' ? 'active' : '' ?>">
      <span class="icon">🏫</span> Schools
    </a>
    
    <a href="subjects.php"
       class="sidebar-link <?= $activePage==='subjects' ? 'active' : '' ?>">
      <span class="icon">📚</span> Subjects
    </a>

    <p class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider px-3 mb-2 mt-4">Account</p>

    <a href="settings.php"
       class="sidebar-link <?= $activePage==='settings' ? 'active' : '' ?>">
      <span class="icon">⚙️</span> Settings
    </a>

    <a href="../user/profile.php" class="sidebar-link">
      <span class="icon">👤</span> My Profile
    </a>

    <p class="text-[10px] text-slate-600 font-semibold uppercase tracking-wider px-3 mb-2 mt-4">Site</p>

    <a href="../index.html" class="sidebar-link">
      <span class="icon">🏠</span> View Site
    </a>
  </nav>

  <!-- Logout -->
  <div class="px-3 py-4 border-t border-white/7">
    <button onclick="logout()"
            class="sidebar-link w-full text-red-400/80 hover:bg-red-500/10 hover:text-red-400">
      <span class="icon">🚪</span> Logout
    </button>
  </div>
</aside>

<!-- Mobile top bar -->
<div class="lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between px-4 py-3"
     style="background:rgba(7,7,26,.9); backdrop-filter:blur(16px); border-bottom:1px solid rgba(255,255,255,.07);">
  <button onclick="toggleSidebar()" class="glass p-2 rounded-lg">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>
  <span class="font-bold grad-text">Admin Panel</span>
  <a href="../index.html" class="glass p-2 rounded-lg text-slate-400 hover:text-white transition">🏠</a>
</div>
<!-- Mobile spacer -->
<div class="lg:hidden h-14"></div>

<script>
// Live pending badge in sidebar
db.collection('registrationRequests')
  .where('status','==','pending')
  .onSnapshot(snap => {
    const badge = document.getElementById('sidebarPendingBadge');
    if (!badge) return;
    if (snap.size > 0) { badge.textContent = snap.size; badge.classList.remove('hidden'); }
    else badge.classList.add('hidden');
  });
</script>
