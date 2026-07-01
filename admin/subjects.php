<?php
$pageTitle  = 'Manage Subjects';
$activePage = 'subjects';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Manage <span class="grad-text">Subjects</span></h1>
        <p class="text-slate-500 text-sm mt-1">Configure subjects, baskets, and sub-subjects for term marks.</p>
      </div>
      <button onclick="openModal('addSubjectModal')" class="btn-primary py-2.5 px-5 rounded-xl">
        + Add Subject
      </button>
    </div>

    <!-- Subjects List -->
    <div id="subjectsContainer" class="space-y-4">
      <div class="glass rounded-2xl p-10 text-center border border-white/8">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading subjects...</p>
      </div>
    </div>

  </main>
</div>

<!-- Add/Edit Subject Modal -->
<div id="addSubjectModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-lg w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0 my-8">
    <h3 class="text-xl font-bold text-white mb-1" id="subjectModalTitle">Add New Subject</h3>
    <p class="text-slate-400 text-sm mb-5">Define how this subject will be graded.</p>
    
    <input type="hidden" id="editSubjectId">
    
    <div class="space-y-5">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Subject Name</label>
        <input id="subName" type="text" class="input-field" placeholder="e.g. Mathematics, Aesthetics">
      </div>
      
      <!-- Basket Options -->
      <div class="p-4 rounded-xl bg-black/20 border border-white/5 space-y-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" id="isBasket" onchange="toggleSections()" class="w-4 h-4 rounded bg-white/5 border-white/10 text-violet-500 focus:ring-violet-500 focus:ring-offset-dark">
          <span class="text-sm font-semibold text-white">Is this a Basket Subject?</span>
        </label>
        <p class="text-[10px] text-slate-400">If checked, students will pick one option from this basket (e.g. Aesthetics -> Music/Art/Dancing).</p>
        
        <div id="basketSection" class="hidden">
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Basket Options (comma separated)</label>
          <input id="basketOptions" type="text" class="input-field" placeholder="Music, Art, Dancing">
        </div>
      </div>
      
      <!-- Sub Subjects -->
      <div class="p-4 rounded-xl bg-black/20 border border-white/5 space-y-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" id="hasSubSubjects" onchange="toggleSections()" class="w-4 h-4 rounded bg-white/5 border-white/10 text-violet-500 focus:ring-violet-500 focus:ring-offset-dark">
          <span class="text-sm font-semibold text-white">Has Sub-Subjects?</span>
        </label>
        <p class="text-[10px] text-slate-400">If checked, students will enter marks for each sub-subject (e.g. English -> Theory/Oral).</p>
        
        <div id="subSubjectsSection" class="hidden">
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Sub-Subjects (comma separated)</label>
          <input id="subSubjects" type="text" class="input-field" placeholder="Theory, Oral, Practical">
        </div>
      </div>
      
    </div>
    
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('addSubjectModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="saveSubject()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save Subject</button>
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
    if (!pDoc.exists || !pDoc.data().permissions?.manage_subjects) {
      window.location.href = 'dashboard.php';
    }
  }
});

let allSubjects = [];

db.collection('subjectTemplates').orderBy('name').onSnapshot(snap => {
  allSubjects = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  renderSubjects();
});

function toggleSections() {
  const isBasket = document.getElementById('isBasket').checked;
  const hasSub = document.getElementById('hasSubSubjects').checked;
  document.getElementById('basketSection').style.display = isBasket ? 'block' : 'none';
  document.getElementById('subSubjectsSection').style.display = hasSub ? 'block' : 'none';
}

function renderSubjects() {
  const container = document.getElementById('subjectsContainer');
  if (allSubjects.length === 0) {
    container.innerHTML = `<div class="glass rounded-2xl p-12 text-center border border-white/8">
      <p class="text-4xl mb-3">📚</p>
      <p class="text-slate-500">No subjects defined yet.</p></div>`;
    return;
  }

  container.innerHTML = allSubjects.map(sub => `
    <div class="glass rounded-xl p-5 border border-white/8 hover:border-violet-500/30 transition group flex items-center justify-between fade-in gap-4">
      <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-xl">📚</div>
        <div>
          <h3 class="text-lg font-bold text-white leading-tight">${sub.name}</h3>
          <div class="flex gap-2 mt-1">
            ${sub.isBasket ? `<span class="badge bg-amber-500/10 text-amber-400 border-amber-500/20 text-[10px]">BASKET</span>` : ''}
            ${sub.hasSubSubjects ? `<span class="badge bg-emerald-500/10 text-emerald-400 border-emerald-500/20 text-[10px]">SUB-SUBJECTS</span>` : ''}
            ${!sub.isBasket && !sub.hasSubSubjects ? `<span class="badge bg-slate-500/10 text-slate-400 border-slate-500/20 text-[10px]">STANDARD</span>` : ''}
          </div>
        </div>
      </div>
      
      <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
        <button onclick="editSubject('${sub.id}')" class="text-xs px-3 py-1.5 rounded-lg bg-white/5 text-white hover:bg-white/10 transition border border-white/10">Edit</button>
        <button onclick="deleteSubject('${sub.id}')" class="text-xs px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition border border-red-500/20">Delete</button>
      </div>
    </div>
  `).join('');
}

function editSubject(id) {
  const sub = allSubjects.find(s => s.id === id);
  if (!sub) return;
  document.getElementById('subjectModalTitle').textContent = 'Edit Subject';
  document.getElementById('editSubjectId').value = sub.id;
  document.getElementById('subName').value = sub.name;
  document.getElementById('isBasket').checked = sub.isBasket || false;
  document.getElementById('basketOptions').value = (sub.basketOptions || []).join(', ');
  document.getElementById('hasSubSubjects').checked = sub.hasSubSubjects || false;
  document.getElementById('subSubjects').value = (sub.subSubjects || []).join(', ');
  
  toggleSections();
  openModal('addSubjectModal');
}

function openModal(id) {
  if (id === 'addSubjectModal' && !document.getElementById('editSubjectId').value) {
    document.getElementById('subjectModalTitle').textContent = 'Add New Subject';
    document.getElementById('subName').value = '';
    document.getElementById('isBasket').checked = false;
    document.getElementById('basketOptions').value = '';
    document.getElementById('hasSubSubjects').checked = false;
    document.getElementById('subSubjects').value = '';
    toggleSections();
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
    document.getElementById('editSubjectId').value = '';
  }, 200);
}

async function saveSubject() {
  const id = document.getElementById('editSubjectId').value;
  const name = document.getElementById('subName').value.trim();
  const isBasket = document.getElementById('isBasket').checked;
  const basketStr = document.getElementById('basketOptions').value;
  const hasSub = document.getElementById('hasSubSubjects').checked;
  const subStr = document.getElementById('subSubjects').value;
  
  if (!name) {
    showToast('Name is required', 'error');
    return;
  }
  
  const basketOptions = isBasket ? basketStr.split(',').map(s=>s.trim()).filter(s=>s) : [];
  const subSubjects = hasSub ? subStr.split(',').map(s=>s.trim()).filter(s=>s) : [];
  
  if (isBasket && basketOptions.length < 2) {
    showToast('A basket subject needs at least 2 options', 'warning'); return;
  }
  if (hasSub && subSubjects.length < 1) {
    showToast('Please enter at least 1 sub-subject', 'warning'); return;
  }
  
  try {
    showLoading();
    const payload = {
      name, isBasket, basketOptions, hasSubSubjects: hasSub, subSubjects
    };
    
    if (id) {
      await db.collection('subjectTemplates').doc(id).update(payload);
      showToast('Subject updated!', 'success');
    } else {
      payload.createdAt = firebase.firestore.FieldValue.serverTimestamp();
      await db.collection('subjectTemplates').add(payload);
      showToast('Subject added!', 'success');
    }
    closeModal('addSubjectModal');
  } catch(err) {
    showToast(err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function deleteSubject(id) {
  showConfirm('Delete Subject', 'Are you sure? Existing term marks will keep their current records, but this subject will no longer be available for new records.', async () => {
    try {
      showLoading();
      await db.collection('subjectTemplates').doc(id).delete();
      showToast('Subject deleted', 'info');
    } catch(err) {
      showToast(err.message, 'error');
    } finally {
      hideLoading();
    }
  });
}
</script>
