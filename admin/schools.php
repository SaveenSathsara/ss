<?php
$pageTitle  = 'Manage Schools';
$activePage = 'schools';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Manage <span class="grad-text">Schools</span></h1>
        <p class="text-slate-500 text-sm mt-1">Add and manage the global list of schools for students.</p>
      </div>
      <button onclick="openModal('addSchoolModal')" class="btn-primary py-2.5 px-5 rounded-xl">
        + Add School
      </button>
    </div>

    <!-- Schools List -->
    <div id="schoolsContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div class="col-span-full glass rounded-2xl p-10 text-center border border-white/8">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading schools...</p>
      </div>
    </div>

  </main>
</div>

<!-- Add/Edit School Modal -->
<div id="addSchoolModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1" id="schoolModalTitle">Add New School</h3>
    <p class="text-slate-400 text-sm mb-5">Enter school details to add it to the global list.</p>
    
    <input type="hidden" id="editSchoolId">
    
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">School Name</label>
        <input id="schoolName" type="text" class="input-field" placeholder="e.g. Royal College">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Registration Number</label>
        <input id="schoolNo" type="text" class="input-field" placeholder="e.g. RC-1001">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Address</label>
        <input id="schoolAddress" type="text" class="input-field" placeholder="Colombo 07">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Type</label>
        <select id="schoolType" class="input-field bg-dark">
          <option value="National">National School</option>
          <option value="Provincial">Provincial School</option>
          <option value="Private">Private School</option>
          <option value="International">International School</option>
        </select>
      </div>
    </div>
    
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('addSchoolModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="saveSchool()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save School</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.html'; return; }
  const ud = JSON.parse(localStorage.getItem('userData') || '{}');
  if (ud.userLevel !== 'admin') {
    const pDoc = await db.collection('userLevels').doc(ud.userLevel).get();
    if (!pDoc.exists || !pDoc.data().permissions?.add_schools) {
      window.location.href = 'dashboard.php';
    }
  }
});

let allSchools = [];

db.collection('schools').orderBy('createdAt', 'desc').onSnapshot(snap => {
  allSchools = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  renderSchools();
});

function renderSchools() {
  const container = document.getElementById('schoolsContainer');
  if (allSchools.length === 0) {
    container.innerHTML = `<div class="col-span-full glass rounded-2xl p-12 text-center border border-white/8">
      <p class="text-4xl mb-3">🏫</p>
      <p class="text-slate-500">No schools found. Add one to get started.</p></div>`;
    return;
  }

  container.innerHTML = allSchools.map(school => `
    <div class="glass rounded-2xl p-6 border border-white/8 hover:border-violet-500/30 transition group flex flex-col h-full fade-in">
      <div class="flex justify-between items-start mb-4">
        <div class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-2xl">🏫</div>
        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
          <button onclick="editSchool('${school.id}')" class="text-slate-400 hover:text-white p-1" title="Edit">✏️</button>
          <button onclick="deleteSchool('${school.id}')" class="text-slate-400 hover:text-red-400 p-1" title="Delete">🗑️</button>
        </div>
      </div>
      
      <h3 class="text-xl font-bold text-white mb-1 line-clamp-1" title="${school.name}">${school.name}</h3>
      <p class="text-sm text-slate-400 mb-4 line-clamp-1">No: <span class="text-slate-300 font-medium">${school.no}</span></p>
      
      <div class="mt-auto pt-4 border-t border-white/5 space-y-2">
        <div class="flex justify-between text-xs text-slate-500">
          <span>Type:</span> <span class="text-violet-300 font-medium">${school.type}</span>
        </div>
        <div class="flex justify-between text-xs text-slate-500">
          <span>Location:</span> <span class="text-slate-300 truncate max-w-[120px]" title="${school.address}">${school.address}</span>
        </div>
      </div>
    </div>
  `).join('');
}

function editSchool(id) {
  const school = allSchools.find(s => s.id === id);
  if (!school) return;
  document.getElementById('schoolModalTitle').textContent = 'Edit School';
  document.getElementById('editSchoolId').value = school.id;
  document.getElementById('schoolName').value = school.name;
  document.getElementById('schoolNo').value = school.no;
  document.getElementById('schoolAddress').value = school.address;
  document.getElementById('schoolType').value = school.type;
  openModal('addSchoolModal');
}

function openModal(id) {
  if (id === 'addSchoolModal' && !document.getElementById('editSchoolId').value) {
    document.getElementById('schoolModalTitle').textContent = 'Add New School';
    document.getElementById('schoolName').value = '';
    document.getElementById('schoolNo').value = '';
    document.getElementById('schoolAddress').value = '';
    document.getElementById('schoolType').value = 'National';
  }
  const el = document.getElementById(id);
  el.classList.remove('hidden');
  setTimeout(() => el.querySelector('.modal-box').classList.remove('scale-95', 'opacity-0'), 10);
}

function closeModal(id) {
  const el = document.getElementById(id);
  el.querySelector('.modal-box').classList.add('scale-95', 'opacity-0');
  setTimeout(() => {
    el.classList.add('hidden');
    document.getElementById('editSchoolId').value = '';
  }, 200);
}

async function saveSchool() {
  const id = document.getElementById('editSchoolId').value;
  const name = document.getElementById('schoolName').value.trim();
  const no = document.getElementById('schoolNo').value.trim();
  const address = document.getElementById('schoolAddress').value.trim();
  const type = document.getElementById('schoolType').value;
  
  if (!name || !no) {
    showToast('Name and Registration Number are required', 'error');
    return;
  }
  
  try {
    showLoading();
    if (id) {
      await db.collection('schools').doc(id).update({ name, no, address, type });
      showToast('School updated!', 'success');
    } else {
      await db.collection('schools').add({
        name, no, address, type,
        addedBy: auth.currentUser.uid,
        createdAt: firebase.firestore.FieldValue.serverTimestamp()
      });
      showToast('School added!', 'success');
    }
    closeModal('addSchoolModal');
  } catch(err) {
    showToast(err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function deleteSchool(id) {
  showConfirm('Delete School', 'Are you sure you want to delete this school? Students assigned to it may face issues.', async () => {
    try {
      showLoading();
      await db.collection('schools').doc(id).delete();
      showToast('School deleted', 'info');
    } catch(err) {
      showToast(err.message, 'error');
    } finally {
      hideLoading();
    }
  });
}
</script>
