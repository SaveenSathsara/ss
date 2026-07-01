// ============================================================
//  auth.js  –  Authentication Logic
// ============================================================

// ── Helpers ──────────────────────────────────────────────────────────────────

/** Derive a Firebase-Auth email from a username (internal use only) */
function usernameToEmail(username) {
  return `${username.toLowerCase().trim()}@portal.saveenweb.local`;
}

// ── Login ─────────────────────────────────────────────────────────────────────

async function login(username, password) {
  try {
    showLoading();

    // 1. Look up user by username in Firestore
    const snap = await db.collection('users')
      .where('username', '==', username.toLowerCase().trim())
      .limit(1)
      .get();

    if (snap.empty) throw new Error('USERNAME_NOT_FOUND');

    const userDoc = snap.docs[0];
    const userData = userDoc.data();

    if (userData.status !== 'active') throw new Error('ACCOUNT_INACTIVE');

    // 2. Sign in with Firebase Auth
    await auth.signInWithEmailAndPassword(userData.authEmail, password);

    showToast('ලොගින් සාර්ථකයි! Welcome back 👋', 'success');

    // 3. Redirect based on level
    setTimeout(() => {
      const isAdmin = userData.userLevel === 'admin';
      window.location.href = isAdmin ? '../admin/dashboard.php' : '../user/dashboard.php';
    }, 1200);

  } catch (err) {
    console.error('Login error:', err);
    const msgs = {
      USERNAME_NOT_FOUND: 'Username හොයාගත නොහැකි. නැවත ඇතුලත් කරන්න.',
      ACCOUNT_INACTIVE: 'ඔබේ account deactivate කර ඇත. Admin ව contact කරන්න.',
      'auth/wrong-password': 'Password වැරදියි.',
      'auth/too-many-requests': 'Login attempts ගොඩ. ටිකක් ඉදලා try කරන්න.',
      'auth/network-request-failed': 'Network error. Internet connection check කරන්න.',
    };
    showToast(msgs[err.message] || msgs[err.code] || 'Login failed. Please try again.', 'error');
  } finally {
    hideLoading();
  }
}

// ── Register Request ──────────────────────────────────────────────────────────

async function submitRegistrationRequest(formData) {
  try {
    showLoading();

    const uname = formData.username.toLowerCase().trim();

    // 1. Check username not already taken in users
    const userCheck = await db.collection('users')
      .where('username', '==', uname).limit(1).get();
    if (!userCheck.empty) throw new Error('USERNAME_TAKEN');

    // 2. Check no pending request with same username
    const pendingCheck = await db.collection('registrationRequests')
      .where('username', '==', uname)
      .where('status', '==', 'pending')
      .limit(1).get();
    if (!pendingCheck.empty) throw new Error('PENDING_EXISTS');

    // 3. Submit request document
    await db.collection('registrationRequests').add({
      fullName: formData.fullName.trim(),
      email: formData.email.toLowerCase().trim(),
      username: uname,
      authEmail: usernameToEmail(uname),
      password: formData.password,          // stored temporarily; used on accept
      birthday: formData.birthday,
      status: 'pending',
      requestedAt: firebase.firestore.FieldValue.serverTimestamp(),
    });

    showToast('ඔබේ request Admin ට යවා ඇත. ඉක්මනින් review කෙරේ! 🎉', 'success', 6000);
    document.getElementById('registerForm').reset();
    setTimeout(() => switchTab('login'), 3000);

  } catch (err) {
    console.error('Register error:', err);
    const msgs = {
      USERNAME_TAKEN: 'Username දැනටමත් use කෙරේ. වෙනත් username එකක් try කරන්න.',
      PENDING_EXISTS: 'ඔබේ pending request එකක් දැනටමත් ඇත.',
    };
    showToast(msgs[err.message] || 'Registration failed. Try again.', 'error');
  } finally {
    hideLoading();
  }
}

// ── Logout ────────────────────────────────────────────────────────────────────

async function logout() {
  try {
    await auth.signOut();
    localStorage.removeItem('userData');
    localStorage.removeItem('userPermissions');
    window.location.href = '../index.html';
  } catch (err) {
    showToast('Logout failed.', 'error');
  }
}

// ── Accept Registration Request (Admin only) ──────────────────────────────────

async function acceptRequest(requestId, userLevel = 'normal_user') {
  try {
    showLoading();

    const reqDoc = await db.collection('registrationRequests').doc(requestId).get();
    if (!reqDoc.exists) throw new Error('Request not found');
    const req = reqDoc.data();

    // Create Firebase Auth user using a secondary app instance (keeps admin signed in)
    let secondaryApp;
    try {
      secondaryApp = firebase.app('secondary');
    } catch (_) {
      secondaryApp = firebase.initializeApp(window.__fbConfig, 'secondary');
    }
    const secondaryAuth = secondaryApp.auth();

    const cred = await secondaryAuth
      .createUserWithEmailAndPassword(req.authEmail, req.password);
    const uid = cred.user.uid;
    await secondaryAuth.signOut();

    // Add user to Firestore
    await db.collection('users').doc(uid).set({
      fullName: req.fullName,
      email: req.email,
      authEmail: req.authEmail,
      username: req.username,
      birthday: req.birthday,
      userLevel: userLevel,
      status: 'active',
      createdAt: firebase.firestore.FieldValue.serverTimestamp(),
    });

    // Mark request accepted
    await db.collection('registrationRequests').doc(requestId).update({
      status: 'accepted',
      acceptedAt: firebase.firestore.FieldValue.serverTimestamp(),
    });

    showToast(`${req.fullName} accept කෙරිණ! ✅`, 'success');

  } catch (err) {
    console.error('Accept error:', err);
    showToast('Accept failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

// ── Decline Registration Request (Admin only) ─────────────────────────────────

async function declineRequest(requestId, reason = '') {
  try {
    showLoading();
    await db.collection('registrationRequests').doc(requestId).update({
      status: 'declined',
      reason: reason,
      declinedAt: firebase.firestore.FieldValue.serverTimestamp(),
    });
    showToast('Request decline කෙරිණ.', 'info');
  } catch (err) {
    console.error('Decline error:', err);
    showToast('Decline failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

// ── Change Password ───────────────────────────────────────────────────────────

async function changePassword(currentPassword, newPassword) {
  try {
    showLoading();
    const user = auth.currentUser;
    if (!user) throw new Error('Not logged in');

    // Re-authenticate first
    const cred = firebase.auth.EmailAuthProvider.credential(user.email, currentPassword);
    await user.reauthenticateWithCredential(cred);

    // Update password
    await user.updatePassword(newPassword);
    showToast('Password සාර්ථකව වෙනස් කෙරිණ! 🔒', 'success');

  } catch (err) {
    console.error('Password change error:', err);
    const msgs = {
      'auth/wrong-password': 'Current password වැරදියි.',
      'auth/weak-password': 'New password ගොඩ දුර්වලයි (6+ characters).',
    };
    showToast(msgs[err.code] || 'Password change failed.', 'error');
  } finally {
    hideLoading();
  }
}

// ── Auth State Observer ───────────────────────────────────────────────────────

auth.onAuthStateChanged(async (firebaseUser) => {
  if (firebaseUser) {
    try {
      const userDoc = await db.collection('users').doc(firebaseUser.uid).get();
      if (userDoc.exists) {
        const userData = { uid: firebaseUser.uid, ...userDoc.data() };
        localStorage.setItem('userData', JSON.stringify(userData));
        await loadUserPermissions(userData.userLevel);
        updateNavForLoggedInUser(userData);
      }
    } catch (err) {
      console.error('Auth state error:', err);
    }
  } else {
    localStorage.removeItem('userData');
    localStorage.removeItem('userPermissions');
    updateNavForLoggedOutUser();

    // Guard protected paths
    const path = window.location.pathname;
    if (path.includes('/admin/') || path.includes('/user/')) {
      window.location.href = '../login.html';
    }
  }
});
