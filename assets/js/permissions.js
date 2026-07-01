// ============================================================
//  permissions.js  –  Permission System
// ============================================================

let _permissions   = {};
let _userData      = null;
let _levelListener = null;   // Firestore real-time listener

// Default permission keys used across the site
const ALL_PERMISSIONS = [
  'view_home',
  'view_about',
  'view_contact',
  'view_all_pages',
  'view_downloads',
  'view_user_dashboard',
  'edit_own_profile',
  'add_own_marks',
  'view_others_marks',
  'manage_users',
  'manage_user_levels',
  'manage_pages',
  'manage_requests',
  'manage_settings',
  'manage_downloads',
  'manage_subjects',
  'add_schools',
  'view_admin_dashboard',
];

// ── Load permissions for a user level ─────────────────────────────────────────

async function loadUserPermissions(userLevelId) {
  // Unsubscribe previous listener
  if (_levelListener) _levelListener();

  return new Promise((resolve) => {
    _levelListener = db.collection('userLevels').doc(userLevelId)
      .onSnapshot(snap => {
        if (snap.exists) {
          _permissions = snap.data().permissions || {};
        } else {
          _permissions = {};
        }
        localStorage.setItem('userPermissions', JSON.stringify(_permissions));
        applyPermissionsToDOM();
        resolve(_permissions);
      }, err => {
        console.error('Permission load error:', err);
        resolve({});
      });
  });
}

// ── Check a single permission ─────────────────────────────────────────────────

function hasPermission(permission) {
  const ud = getCurrentUserData();

  // Admin has everything
  if (ud && ud.userLevel === 'admin') return true;

  // Try in-memory first
  if (Object.keys(_permissions).length > 0) return _permissions[permission] === true;

  // Fallback to localStorage (page reload case)
  try {
    const stored = JSON.parse(localStorage.getItem('userPermissions') || '{}');
    return stored[permission] === true;
  } catch (_) {
    return false;
  }
}

// ── Get current user data ─────────────────────────────────────────────────────

function getCurrentUserData() {
  if (_userData) return _userData;
  try {
    _userData = JSON.parse(localStorage.getItem('userData'));
    return _userData;
  } catch (_) {
    return null;
  }
}

// ── Apply permissions to DOM ──────────────────────────────────────────────────

function applyPermissionsToDOM() {
  const isLoggedIn = !!auth.currentUser;

  // [data-permission="view_about"] → show only if user has that permission
  document.querySelectorAll('[data-permission]').forEach(el => {
    const perm = el.getAttribute('data-permission');
    el.style.display = hasPermission(perm) ? '' : 'none';
  });

  // [data-auth="true"] → only when logged in
  document.querySelectorAll('[data-auth="true"]').forEach(el => {
    el.style.display = isLoggedIn ? '' : 'none';
  });

  // [data-auth="false"] → only when logged out
  document.querySelectorAll('[data-auth="false"]').forEach(el => {
    el.style.display = !isLoggedIn ? '' : 'none';
  });

  // Admin-only elements
  const ud = getCurrentUserData();
  const isAdmin = ud && ud.userLevel === 'admin';
  document.querySelectorAll('[data-admin]').forEach(el => {
    el.style.display = isAdmin ? '' : 'none';
  });
}

// ── Guard a page – call at top of protected pages ─────────────────────────────

function requireAuth(redirectTo = '../login.html') {
  auth.onAuthStateChanged(user => {
    if (!user) window.location.href = redirectTo;
  });
}

function requirePermission(permission, redirectTo = '../user/dashboard.php') {
  auth.onAuthStateChanged(async user => {
    if (!user) {
      window.location.href = '../login.html';
      return;
    }

    try {
      // Fetch fresh user data directly from Firestore (don't rely on timeout)
      const userDoc = await db.collection('users').doc(user.uid).get();
      if (!userDoc.exists) {
        window.location.href = '../login.html';
        return;
      }

      const userData = { uid: user.uid, ...userDoc.data() };
      localStorage.setItem('userData', JSON.stringify(userData));
      _userData = userData;

      // Admin always has access
      if (userData.userLevel === 'admin') return;

      // Load level permissions from Firestore
      const levelDoc = await db.collection('userLevels').doc(userData.userLevel).get();
      const perms = levelDoc.exists ? (levelDoc.data().permissions || {}) : {};
      _permissions = perms;
      localStorage.setItem('userPermissions', JSON.stringify(perms));

      // Check the specific permission
      if (!perms[permission]) {
        window.location.href = redirectTo;
      }
    } catch (err) {
      console.error('requirePermission error:', err);
      window.location.href = redirectTo;
    }
  });
}

function requireAdmin(redirectTo = '../user/dashboard.php') {
  requirePermission('view_admin_dashboard', redirectTo);
}


// ── Build default permissions for a level ─────────────────────────────────────

function defaultPermissionsForLevel(levelName) {
  const all = {};
  ALL_PERMISSIONS.forEach(p => all[p] = false);

  switch (levelName) {
    case 'admin':
      ALL_PERMISSIONS.forEach(p => all[p] = true);
      break;
    case 'pro_user':
      all['view_home']           = true;
      all['view_about']          = true;
      all['view_contact']        = true;
      all['view_all_pages']      = true;
      all['view_user_dashboard'] = true;
      all['edit_own_profile']    = true;
      all['add_own_marks']       = true;
      all['view_others_marks']   = true;
      break;
    case 'normal_user':
      all['view_home']           = true;
      all['view_about']          = true;
      all['view_contact']        = true;
      all['view_user_dashboard'] = true;
      all['edit_own_profile']    = true;
      all['add_own_marks']       = true;
      break;
    case 'low_user':
      all['view_home']           = true;
      all['view_about']          = true;
      all['view_user_dashboard'] = true;
      all['add_own_marks']       = true;
      break;
    case 'lower_user':
      all['view_home']           = true;
      all['view_user_dashboard'] = true;
      break;
    default:
      all['view_home']           = true;
      all['view_user_dashboard'] = true;
  }
  return all;
}
