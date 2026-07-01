<?php
$pageTitle  = 'My Student Profiles';
$activePage = 'profiles';
$depth      = 1;
include '../includes/header.php';
?>

<main class="w-full max-w-7xl mx-auto px-6 py-8 min-h-[70vh]">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Student <span class="grad-text">Profiles</span></h1>
        <p class="text-slate-500 text-sm mt-1">Register your Entrance Numbers across different schools.</p>
      </div>
      <button onclick="openModal('addProfileModal')" class="btn-primary py-2.5 px-5 rounded-xl">
        + Add Profile
      </button>
    </div>

    <div id="profilesContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="col-span-full glass rounded-2xl p-10 text-center border border-white/8">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading profiles...</p>
      </div>
    </div>

  </main>

<!-- Add Profile Modal -->
<div id="addProfileModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1">Add Student Profile</h3>
    <p class="text-slate-400 text-sm mb-5">Link your account to a school.</p>
    
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Select School</label>
        <select id="schoolSelect" class="input-field bg-dark">
          <option value="">Loading schools...</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Entrance Number</label>
        <input id="entranceNumber" type="text" class="input-field" placeholder="e.g. 10455">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Full Name</label>
        <input id="fullName" type="text" class="input-field" placeholder="John Doe">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Date of Birth</label>
        <input id="birthdate" type="date" class="input-field">
      </div>
    </div>
    
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('addProfileModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="saveProfile()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save Profile</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
auth.onAuthStateChanged(user => {
  if (!user) { window.location.href = '../login.html'; return; }
  loadData(user.uid);
});

let allSchools = [];
let myProfiles = [];

async function loadData(uid) {
  // Load Schools
  db.collection('schools').orderBy('name').onSnapshot(snap => {
    allSchools = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    updateSchoolSelect();
    renderProfiles();
  });
  
  // Load My Profiles
  db.collection('studentProfiles').where('uid', '==', uid).onSnapshot(snap => {
    myProfiles = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    renderProfiles();
  });
}

function updateSchoolSelect() {
  const sel = document.getElementById('schoolSelect');
  if (allSchools.length === 0) {
    sel.innerHTML = '<option value="">No schools available</option>';
    return;
  }
  sel.innerHTML = '<option value="">Select a school...</option>' + 
    allSchools.map(s => `<option value="${s.id}">${s.name} (${s.no})</option>`).join('');
}

function renderProfiles() {
  const container = document.getElementById('profilesContainer');
  if (myProfiles.length === 0) {
    container.innerHTML = `<div class="col-span-full glass rounded-2xl p-12 text-center border border-white/8">
      <p class="text-4xl mb-3">🎓</p>
      <p class="text-slate-500">You haven't linked any student profiles yet.</p></div>`;
    return;
  }

  container.innerHTML = myProfiles.map(p => {
    const school = allSchools.find(s => s.id === p.schoolId);
    const schoolName = school ? school.name : 'Unknown School';
    
    return `
    <div class="glass rounded-2xl p-6 border border-white/8 hover:border-violet-500/30 transition fade-in relative overflow-hidden group">
      <div class="absolute -right-6 -top-6 w-24 h-24 bg-violet-500/10 rounded-full blur-xl group-hover:bg-violet-500/20 transition"></div>
      
      <div class="flex justify-between items-start mb-6">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-white text-xl font-bold border-2 border-white/10 shadow-lg">
            ${p.fullName.charAt(0).toUpperCase()}
          </div>
          <div>
            <h3 class="text-xl font-bold text-white line-clamp-1" title="${p.fullName}">${p.fullName}</h3>
            <p class="text-sm text-violet-300 font-medium">${schoolName}</p>
          </div>
        </div>
        <button onclick="deleteProfile('${p.id}')" class="text-slate-500 hover:text-red-400 p-2 opacity-0 group-hover:opacity-100 transition" title="Delete">✕</button>
      </div>
      
      <div class="grid grid-cols-2 gap-4 border-t border-white/5 pt-4">
        <div>
          <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Entrance No</p>
          <p class="text-slate-300 font-medium">${p.entranceNumber}</p>
        </div>
        <div>
          <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Birthdate</p>
          <p class="text-slate-300 font-medium">${p.birthdate}</p>
        </div>
      </div>
    </div>
  `;
  }).join('');
}

function openModal(id) {
  document.getElementById('schoolSelect').value = '';
  document.getElementById('entranceNumber').value = '';
  document.getElementById('fullName').value = '';
  document.getElementById('birthdate').value = '';
  
  const el = document.getElementById(id);
  el.classList.remove('hidden');
  setTimeout(() => el.querySelector('.modal-box').classList.remove('scale-95', 'opacity-0'), 10);
}

function closeModal(id) {
  const el = document.getElementById(id);
  el.querySelector('.modal-box').classList.add('scale-95', 'opacity-0');
  setTimeout(() => el.classList.add('hidden'), 200);
}

async function saveProfile() {
  const schoolId = document.getElementById('schoolSelect').value;
  const entranceNumber = document.getElementById('entranceNumber').value.trim();
  const fullName = document.getElementById('fullName').value.trim();
  const birthdate = document.getElementById('birthdate').value;
  
  if (!schoolId || !entranceNumber || !fullName || !birthdate) {
    showToast('All fields are required', 'error'); return;
  }
  
  try {
    showLoading();
    // Check if profile already exists for this entrance number in this school
    const exists = myProfiles.find(p => p.schoolId === schoolId && p.entranceNumber === entranceNumber);
    if (exists) {
      showToast('Profile already exists for this entrance number.', 'warning');
      return;
    }
    
    await db.collection('studentProfiles').add({
      uid: auth.currentUser.uid,
      schoolId,
      entranceNumber,
      fullName,
      birthdate,
      createdAt: firebase.firestore.FieldValue.serverTimestamp()
    });
    
    showToast('Profile added successfully!', 'success');
    closeModal('addProfileModal');
  } catch(err) {
    showToast(err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function deleteProfile(id) {
  showConfirm('Delete Profile', 'Are you sure? This will not delete your term marks, but you will lose the link to this school profile.', async () => {
    try {
      showLoading();
      await db.collection('studentProfiles').doc(id).delete();
      showToast('Profile deleted', 'info');
    } catch(err) {
      showToast(err.message, 'error');
    } finally {
      hideLoading();
    }
  });
}
</script>
