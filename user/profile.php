<?php
$pageTitle  = 'My Profile';
$activePage = 'profile';
$depth      = 1;
include '../includes/header.php';
include '../includes/nav.php';
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">

  <div class="mb-8 fade-in">
    <a href="dashboard.php" class="text-slate-500 hover:text-white transition text-sm flex items-center gap-2 mb-4">
      ← Back to Dashboard
    </a>
    <h1 class="text-3xl font-black text-white">My <span class="grad-text">Profile</span></h1>
    <p class="text-slate-500 text-sm mt-1">Manage your account details and password</p>
  </div>

  <!-- Profile Edit Form -->
  <div class="glass rounded-2xl border border-white/8 overflow-hidden mb-6 fade-in" style="animation-delay:.05s">
    <div class="px-6 py-4 border-b border-white/8 flex justify-between items-center">
      <h2 class="font-bold text-white flex items-center gap-2">
        <span>👤</span> Personal Information
      </h2>
    </div>
    <div class="p-6">
      <!-- Profile Picture -->
      <div class="flex items-center gap-5 mb-6">
        <div class="relative group cursor-pointer" onclick="document.getElementById('profPicInput').click()">
          <div class="w-20 h-20 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-3xl font-black text-white overflow-hidden border-2 border-white/10" id="profPicContainer">
            <span id="profPicLetter">U</span>
            <img id="profPicImg" class="hidden w-full h-full object-cover">
          </div>
          <div class="absolute inset-0 bg-black/50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
            <span class="text-xl">📷</span>
          </div>
          <input type="file" id="profPicInput" class="hidden" accept="image/*" onchange="uploadProfilePic(event)">
        </div>
        <div>
          <p class="text-sm font-semibold text-white">Profile Picture</p>
          <p class="text-xs text-slate-500 mt-1">Click image to upload (JPG/PNG max 2MB)</p>
        </div>
      </div>

      <form id="profileForm" onsubmit="updateProfile(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Full Name</label>
          <input id="profFullName" type="text" class="input-field" required>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Email</label>
          <input id="profEmail" type="email" class="input-field" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Username</label>
            <input id="profUsername" type="text" class="input-field" required>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Birthday</label>
            <input id="profBirthday" type="date" class="input-field" required>
          </div>
        </div>
        <div class="pt-2">
          <button type="submit" class="btn-primary py-2.5 px-6 rounded-xl">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Change Password Form -->
  <div class="glass rounded-2xl border border-white/8 overflow-hidden fade-in" style="animation-delay:.1s">
    <div class="px-6 py-4 border-b border-white/8">
      <h2 class="font-bold text-white flex items-center gap-2">
        <span>🔒</span> Change Password
      </h2>
    </div>
    <div class="p-6">
      <form onsubmit="handlePasswordChange(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Current Password</label>
          <div class="relative">
            <input id="currentPw" type="password" class="input-field pr-11" required>
            <button type="button" onclick="togglePw('currentPw')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">👁</button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">New Password</label>
          <div class="relative">
            <input id="newPw" type="password" class="input-field pr-11" required minlength="6">
            <button type="button" onclick="togglePw('newPw')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition">👁</button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Confirm New Password</label>
          <input id="confirmPw" type="password" class="input-field" required minlength="6">
          <p id="pwMatchMsg" class="text-xs mt-1 hidden"></p>
        </div>
        <div class="pt-2">
          <button type="submit" class="btn-primary py-2.5 px-6 rounded-xl">
            Update Password
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<?php include '../includes/footer.php'; ?>

<script>
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.html'; return; }
  
  try {
    const doc = await db.collection('users').doc(user.uid).get();
    if (doc.exists) {
      const u = doc.data();
      document.getElementById('profFullName').value = u.fullName || '';
      document.getElementById('profEmail').value = u.email || '';
      document.getElementById('profUsername').value = u.username || '';
      document.getElementById('profBirthday').value = u.birthday || '';
      
      const letter = u.fullName?.[0]?.toUpperCase() || 'U';
      if (u.profilePic) {
        document.getElementById('profPicImg').src = u.profilePic;
        document.getElementById('profPicImg').classList.remove('hidden');
        document.getElementById('profPicLetter').classList.add('hidden');
      } else {
        document.getElementById('profPicLetter').textContent = letter;
      }
    }
  } catch(err) {
    showToast('Failed to load profile', 'error');
  }
});

async function uploadProfilePic(e) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    showToast('File size must be less than 2MB', 'error');
    return;
  }
  
  try {
    showLoading();
    const uid = auth.currentUser.uid;
    const storageRef = firebase.storage().ref(`profile_pictures/${uid}_${Date.now()}`);
    await storageRef.put(file);
    const downloadURL = await storageRef.getDownloadURL();
    
    await db.collection('users').doc(uid).update({ profilePic: downloadURL });
    
    document.getElementById('profPicImg').src = downloadURL;
    document.getElementById('profPicImg').classList.remove('hidden');
    document.getElementById('profPicLetter').classList.add('hidden');
    
    // Update local storage
    const ud = JSON.parse(localStorage.getItem('userData') || '{}');
    ud.profilePic = downloadURL;
    localStorage.setItem('userData', JSON.stringify(ud));
    updateNavForLoggedInUser(ud);
    
    showToast('Profile picture updated! 📸', 'success');
  } catch(err) {
    showToast('Failed to upload: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function updateProfile(e) {
  e.preventDefault();
  
  if (!hasPermission('edit_own_profile')) {
    showToast('You do not have permission to edit your profile.', 'error');
    return;
  }

  const fullName = document.getElementById('profFullName').value.trim();
  const email    = document.getElementById('profEmail').value.trim();
  const username = document.getElementById('profUsername').value.trim().toLowerCase();
  const birthday = document.getElementById('profBirthday').value;

  try {
    showLoading();
    const uid = auth.currentUser.uid;
    
    // Check if username is taken by someone else
    const userCheck = await db.collection('users')
      .where('username', '==', username).get();
    
    let isTaken = false;
    userCheck.forEach(d => { if (d.id !== uid) isTaken = true; });
    if (isTaken) throw new Error('Username is already taken');

    await db.collection('users').doc(uid).update({ fullName, email, username, birthday });
    
    // Update local storage
    const ud = JSON.parse(localStorage.getItem('userData') || '{}');
    ud.fullName = fullName;
    ud.email = email;
    ud.username = username;
    ud.birthday = birthday;
    localStorage.setItem('userData', JSON.stringify(ud));
    
    updateNavForLoggedInUser(ud);
    
    showToast('Profile updated! ✅', 'success');
  } catch(err) {
    showToast('Failed to update: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

function togglePw(id) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
}

document.getElementById('confirmPw').addEventListener('input', function() {
  const msg = document.getElementById('pwMatchMsg');
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
</script>
