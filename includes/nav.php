<?php
/**
 * includes/nav.php
 * Responsive glassmorphism navigation bar.
 * Requires: $root (from header.php), $activePage (optional)
 */
$activePage = $activePage ?? '';
?>
<!-- ── Navigation ──────────────────────────────────────────── -->
<nav id="mainNav" class="fixed top-0 left-0 right-0 z-40 transition-all duration-300">
  <div class="glass mx-4 mt-3 px-5 py-3 flex items-center justify-between rounded-2xl border border-white/8"
       style="background:rgba(7,7,26,.7); backdrop-filter:blur(24px);">

    <!-- Brand -->
    <a href="<?= $root ?>index.html" class="flex items-center gap-2 text-lg font-bold">
      <span class="text-2xl">⚡</span>
      <span class="grad-text">Saveen</span>
      <span class="text-slate-300">Portal</span>
    </a>

    <!-- Desktop links -->
    <div class="hidden md:flex items-center gap-1">
      <a href="<?= $root ?>index.html"
         class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:text-violet-300 <?= $activePage==='home' ? 'text-violet-400 bg-violet-500/10':'text-slate-400' ?>">
        Home
      </a>
      <a href="<?= $root ?>about.html"
         data-permission="view_about"
         class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:text-violet-300 <?= $activePage==='about' ? 'text-violet-400 bg-violet-500/10':'text-slate-400' ?>">
        About
      </a>
      <a href="<?= $root ?>contact.html"
         data-permission="view_contact"
         class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:text-violet-300 <?= $activePage==='contact' ? 'text-violet-400 bg-violet-500/10':'text-slate-400' ?>">
        Contact
      </a>
      <a href="<?= $root ?>downloads.php"
         data-permission="view_downloads"
         class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:text-violet-300 <?= $activePage==='downloads' ? 'text-violet-400 bg-violet-500/10':'text-slate-400' ?>">
        Downloads
      </a>

      <!-- Logged-in only -->
      <a href="<?= $root ?>user/dashboard.php"
         data-auth="true"
         data-permission="view_user_dashboard"
         class="nav-user hidden px-3 py-1.5 rounded-lg text-sm font-medium text-slate-400 hover:text-violet-300 transition-all <?= $activePage==='dashboard' ? 'text-violet-400 bg-violet-500/10':'text-slate-400' ?>">
        Dashboard
      </a>

      <!-- Admin only -->
      <a href="<?= $root ?>admin/dashboard.php"
         data-admin
         class="nav-admin hidden px-3 py-1.5 rounded-lg text-sm font-medium text-amber-400/80 hover:text-amber-300 transition-all">
        Admin
      </a>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-2">
      <!-- Guest: Login button -->
      <a href="<?= $root ?>login.html"
         data-auth="false"
         class="nav-guest btn-primary py-2 px-4 text-sm">
        Login
      </a>

      <!-- User: Avatar dropdown -->
      <div class="nav-user hidden relative" id="userMenuWrap">
        <button onclick="document.getElementById('userDropdown').classList.toggle('hidden')"
                class="flex items-center gap-2 glass px-3 py-1.5 rounded-xl hover:border-violet-500/40 transition-all text-sm">
          <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-xs font-bold text-white overflow-hidden border border-white/10">
            <span data-nav-username>U</span>
          </div>
          <span class="hidden sm:block text-slate-300 font-medium" data-nav-username>User</span>
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <!-- Dropdown -->
        <div id="userDropdown"
             class="hidden absolute right-0 top-12 w-52 glass-strong rounded-xl shadow-2xl border border-white/10 overflow-hidden z-50 fade-in">
          <div class="px-4 py-3 border-b border-white/8">
            <p class="text-xs text-slate-500 mb-0.5">Logged in as</p>
            <p class="text-sm font-semibold text-white" data-nav-username>User</p>
            <p class="text-xs text-violet-400" data-nav-level>User</p>
          </div>
          <a href="<?= $root ?>user/profile.php"
             class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-all">
            <span>👤</span> My Profile
          </a>
          <a href="<?= $root ?>user/dashboard.php"
             class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-all">
            <span>🏠</span> Dashboard
          </a>
          <a href="<?= $root ?>admin/dashboard.php"
             data-admin
             class="nav-admin hidden flex items-center gap-2 px-4 py-2.5 text-sm text-amber-400/80 hover:bg-amber-500/10 hover:text-amber-300 transition-all">
            <span>⚙️</span> Admin Panel
          </a>
          <div class="border-t border-white/8">
            <button onclick="logout()"
                    class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-400/80 hover:bg-red-500/10 hover:text-red-400 transition-all">
              <span>🚪</span> Logout
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile hamburger -->
      <button onclick="toggleMobileMenu()" class="md:hidden glass p-2 rounded-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile menu -->
  <div id="mobileMenu"
       class="hidden md:hidden mx-4 mt-1 glass rounded-2xl border border-white/8 overflow-hidden">
    <div class="flex flex-col py-2">
      <a href="<?= $root ?>index.html" class="px-5 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">🏠 Home</a>
      <a href="<?= $root ?>about.html" data-permission="view_about" class="px-5 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">ℹ️ About</a>
      <a href="<?= $root ?>contact.html" data-permission="view_contact" class="px-5 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">📬 Contact</a>
      <a href="<?= $root ?>downloads.php" data-permission="view_downloads" class="px-5 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">📥 Downloads</a>
      <a href="<?= $root ?>user/dashboard.php" class="nav-user hidden px-5 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">📊 Dashboard</a>
      <a href="<?= $root ?>admin/dashboard.php" class="nav-admin hidden px-5 py-3 text-sm text-amber-400/80 hover:bg-amber-500/10 transition">⚙️ Admin Panel</a>
      <a href="<?= $root ?>user/profile.php" class="nav-user hidden px-5 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">👤 My Profile</a>
      <button onclick="logout()" class="nav-user hidden text-left px-5 py-3 text-sm text-red-400/80 hover:bg-red-500/10 transition">🚪 Logout</button>
      <a href="<?= $root ?>login.html" class="nav-guest px-5 py-3 text-sm text-violet-400 hover:bg-violet-500/10 transition">🔐 Login / Register</a>
    </div>
  </div>
</nav>

<!-- Spacer -->
<div class="h-20"></div>

<script>
function toggleMobileMenu() {
  document.getElementById('mobileMenu').classList.toggle('hidden');
}
// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  const wrap = document.getElementById('userMenuWrap');
  const dd   = document.getElementById('userDropdown');
  if (wrap && !wrap.contains(e.target) && dd) dd.classList.add('hidden');
});
</script>
