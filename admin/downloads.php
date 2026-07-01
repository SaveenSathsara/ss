<?php
$pageTitle  = 'Manage Downloads';
$activePage = 'downloads';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Manage <span class="grad-text">Downloads</span></h1>
        <p class="text-slate-500 text-sm mt-1">Create categories, nested subcategories, and add resources</p>
      </div>
      <button onclick="openModal('addCategoryModal')" class="btn-primary py-2.5 px-5 rounded-xl">
        + Add Category
      </button>
    </div>

    <!-- Categories List -->
    <div id="downloadsContainer" class="space-y-6">
      <div class="glass rounded-2xl p-10 text-center border border-white/8">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading categories...</p>
      </div>
    </div>

  </main>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1">Add New Category</h3>
    <p class="text-slate-400 text-sm mb-5">Create a top-level category for downloads.</p>
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Category Name</label>
        <input id="catName" type="text" class="input-field" placeholder="e.g. Software Tools">
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('addCategoryModal')" class="btn-ghost flex-1 py-2.5 text-sm">Close</button>
      <button onclick="saveCategory()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save Category</button>
    </div>
  </div>
</div>

<!-- Add Subcategory / Sub-subcategory Modal -->
<div id="addSubModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1" id="addSubModalTitle">Add Subcategory</h3>
    <p class="text-slate-400 text-sm mb-3" id="addSubModalDesc">Max 100 subcategories.</p>
    
    <div id="addSubSuccess" class="hidden mb-4 p-2 rounded-lg bg-green-500/20 border border-green-500/30 text-green-400 text-sm flex items-center gap-2">
      <span>✅</span> <span id="addSubSuccessText">Added! You can add more.</span>
    </div>

    <input type="hidden" id="parentCatId">
    <input type="hidden" id="parentSubId"> <!-- If set, we are adding a sub-sub -->
    
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Name</label>
        <input id="subName" type="text" class="input-field" placeholder="e.g. Windows Versions">
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeAddSubModal()" class="btn-ghost flex-1 py-2.5 text-sm">Close</button>
      <button onclick="saveSubcategory()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Add & Continue</button>
    </div>
  </div>
</div>

<!-- Add Resource Modal -->
<div id="addResourceModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-lg w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0 my-8">
    <h3 class="text-xl font-bold text-white mb-1">Add Resource</h3>
    <p class="text-slate-400 text-sm mb-5">Upload a file or add a link.</p>
    
    <input type="hidden" id="resCatId">
    <input type="hidden" id="resSubId">
    <input type="hidden" id="resSubSubId">
    
    <div class="flex border-b border-white/10 mb-5">
      <button id="tabBtn-upload" onclick="switchResTab('upload')" class="flex-1 pb-3 text-sm font-semibold border-b-2 border-violet-500 text-violet-400 transition">Upload File</button>
      <button id="tabBtn-link" onclick="switchResTab('link')" class="flex-1 pb-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-300 transition">Add Link</button>
    </div>

    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Display Name</label>
        <input id="resName" type="text" class="input-field" placeholder="e.g. Setup Guide V2">
      </div>
      
      <!-- Upload Section -->
      <div id="resTab-upload" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Select File</label>
          <input id="uploadInput" type="file" class="input-field py-2" accept=".pdf,.png,.jpg,.jpeg,.mp3,.mp4,.docx,.pptx,.xlsx,.xls">
          <p class="text-[10px] text-slate-500 mt-1">Allowed: PDF, JPG, PNG, MP3, MP4, DOCX, PPTX, XLSX, XLS</p>
        </div>
      </div>
      
      <!-- Link Section -->
      <div id="resTab-link" class="space-y-4 hidden">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Link Type</label>
          <select id="linkType" class="input-field bg-dark">
            <option value="youtube">🎥 YouTube Video</option>
            <option value="google_doc">📄 Google Docs</option>
            <option value="google_sheet">📊 Google Sheets</option>
            <option value="google_form">📋 Google Forms</option>
            <option value="google_drive">💾 Google Drive</option>
            <option value="ms_form">📝 MS Forms</option>
            <option value="chatgpt">🤖 ChatGPT Shared Page</option>
            <option value="link">🔗 Generic Link</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">URL</label>
          <input id="linkUrl" type="url" class="input-field" placeholder="https://...">
        </div>
      </div>
      
    </div>
    
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('addResourceModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="saveResource()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save Resource</button>
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
    if (!pDoc.exists || !pDoc.data().permissions?.manage_downloads) {
      window.location.href = 'dashboard.php';
    }
  }
});

let allCategories = [];
let allFiles = []; 
let currentResTab = 'upload';

db.collection('dl_categories').orderBy('createdAt').onSnapshot(snap => {
  allCategories = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  renderDownloads();
});

db.collection('dl_files').orderBy('createdAt', 'desc').onSnapshot(snap => {
  allFiles = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  renderDownloads();
});

function switchResTab(tab) {
  currentResTab = tab;
  if (tab === 'upload') {
    document.getElementById('tabBtn-upload').classList.replace('border-transparent','border-violet-500');
    document.getElementById('tabBtn-upload').classList.replace('text-slate-500','text-violet-400');
    document.getElementById('tabBtn-link').classList.replace('border-violet-500','border-transparent');
    document.getElementById('tabBtn-link').classList.replace('text-violet-400','text-slate-500');
    document.getElementById('resTab-upload').classList.remove('hidden');
    document.getElementById('resTab-link').classList.add('hidden');
  } else {
    document.getElementById('tabBtn-link').classList.replace('border-transparent','border-violet-500');
    document.getElementById('tabBtn-link').classList.replace('text-slate-500','text-violet-400');
    document.getElementById('tabBtn-upload').classList.replace('border-violet-500','border-transparent');
    document.getElementById('tabBtn-upload').classList.replace('text-violet-400','text-slate-500');
    document.getElementById('resTab-link').classList.remove('hidden');
    document.getElementById('resTab-upload').classList.add('hidden');
  }
}

function renderDownloads() {
  const container = document.getElementById('downloadsContainer');
  if (allCategories.length === 0) {
    container.innerHTML = `<div class="glass rounded-2xl p-12 text-center border border-white/8">
      <p class="text-4xl mb-3">📁</p>
      <p class="text-slate-500">No categories found. Add one to get started.</p></div>`;
    return;
  }

  container.innerHTML = allCategories.map(cat => {
    const subcats = cat.subcategories || [];
    
    return `
    <div class="glass rounded-2xl border border-white/8 overflow-hidden fade-in mb-6">
      <div class="px-6 py-4 border-b border-white/8 flex flex-wrap items-center justify-between bg-white/5 gap-3">
        <h2 class="font-bold text-white text-lg flex items-center gap-2">
          <span>📁</span> ${cat.name}
        </h2>
        <div class="flex gap-2">
          ${subcats.length < 100 ? `
            <button onclick="openAddSub('${cat.id}')" class="text-xs px-3 py-1.5 rounded-lg bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 transition border border-violet-500/20">
              + Subcategory
            </button>` : `<span class="text-xs text-slate-500 py-1.5">Max 100 subcats</span>`}
          <button onclick="deleteCategory('${cat.id}')" class="text-xs px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition border border-red-500/20">
            Delete
          </button>
        </div>
      </div>
      
      <div class="p-6 space-y-6">
        ${subcats.length === 0 ? `<p class="text-slate-500 text-sm">No subcategories yet.</p>` : ''}
        
        ${subcats.map(sub => {
          const subSubs = sub.subs || [];
          
          return `
          <div class="border border-white/10 rounded-xl p-4 bg-black/20">
            <div class="flex flex-wrap items-center justify-between mb-3 pb-2 border-b border-white/5 gap-3">
              <h3 class="font-semibold text-violet-300 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                ${sub.name}
              </h3>
              <div class="flex gap-2">
                <button onclick="openAddSub('${cat.id}', '${sub.id}')" class="text-xs px-2 py-1 rounded bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition">+ Sub-Subcat</button>
                <button onclick="openAddResource('${cat.id}', '${sub.id}', null)" class="text-xs px-2 py-1 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition">+ Resource</button>
                <button onclick="deleteSub('${cat.id}', '${sub.id}')" class="text-xs px-2 py-1 rounded bg-red-500/10 text-red-400 hover:bg-red-500/20 transition">Drop</button>
              </div>
            </div>
            
            ${renderFilesList(cat.id, sub.id, null)}
            
            ${subSubs.length > 0 ? `
              <div class="pl-4 mt-4 space-y-4 border-l-2 border-white/5">
                ${subSubs.map(ss => `
                  <div class="border border-white/5 rounded-lg p-3 bg-white/5">
                    <div class="flex flex-wrap items-center justify-between mb-3 border-b border-white/5 pb-2 gap-3">
                      <h4 class="text-sm font-medium text-amber-300 flex items-center gap-2">
                        <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                        ${ss.name}
                      </h4>
                      <div class="flex gap-2">
                        <button onclick="openAddResource('${cat.id}', '${sub.id}', '${ss.id}')" class="text-xs px-2 py-1 rounded bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition">+ Resource</button>
                        <button onclick="deleteSubSub('${cat.id}', '${sub.id}', '${ss.id}')" class="text-xs px-2 py-1 rounded bg-red-500/10 text-red-400 hover:bg-red-500/20 transition">Drop</button>
                      </div>
                    </div>
                    ${renderFilesList(cat.id, sub.id, ss.id)}
                  </div>
                `).join('')}
              </div>
            ` : ''}
            
          </div>`;
        }).join('')}
      </div>
    </div>`;
  }).join('');
}

function renderFilesList(catId, subId, subSubId) {
  const sFiles = allFiles.filter(f => f.catId === catId && f.subId === subId && (f.subSubId || null) === (subSubId || null));
  if (sFiles.length === 0) return `<p class="text-[10px] text-slate-500 uppercase tracking-wider">No resources.</p>`;
  
  return `
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
      ${sFiles.map(f => `
        <div class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/5 hover:border-violet-500/20 transition group">
          <div class="flex items-center gap-3 overflow-hidden">
            <span class="text-xl shrink-0">${getFileIcon(f.type)}</span>
            <div class="min-w-0">
              <a href="${f.url}" target="_blank" class="text-sm text-white font-medium truncate hover:underline" title="${f.name}">${f.name}</a>
              <div class="flex gap-2 items-center">
                <p class="text-[9px] text-slate-400 uppercase">${f.type}</p>
                <p class="text-[9px] text-slate-600">${formatDate(f.createdAt)}</p>
              </div>
            </div>
          </div>
          <button onclick="deleteFile('${f.id}', '${f.path || ''}')" class="text-slate-500 hover:text-red-400 transition text-sm p-1 ml-2">✕</button>
        </div>
      `).join('')}
    </div>
  `;
}

// ── Modals & Actions ────────────────────────────────────────────────────────

function openAddSub(catId, subId = null) {
  document.getElementById('parentCatId').value = catId;
  document.getElementById('parentSubId').value = subId || '';
  document.getElementById('subName').value = '';
  document.getElementById('addSubSuccess').classList.add('hidden');
  
  if (subId) {
    document.getElementById('addSubModalTitle').textContent = 'Add Sub-Subcategory';
    document.getElementById('addSubModalDesc').textContent = 'Max 20 sub-subcategories allowed.';
  } else {
    document.getElementById('addSubModalTitle').textContent = 'Add Subcategory';
    document.getElementById('addSubModalDesc').textContent = 'Max 100 subcategories allowed.';
  }
  
  openModal('addSubModal');
  setTimeout(() => document.getElementById('subName').focus(), 100);
}

function closeAddSubModal() {
  closeModal('addSubModal');
}

function openAddResource(catId, subId, subSubId) {
  document.getElementById('resCatId').value = catId;
  document.getElementById('resSubId').value = subId;
  document.getElementById('resSubSubId').value = subSubId || '';
  document.getElementById('resName').value = '';
  document.getElementById('uploadInput').value = '';
  document.getElementById('linkUrl').value = '';
  switchResTab('upload');
  openModal('addResourceModal');
}

async function saveCategory() {
  const name = document.getElementById('catName').value.trim();
  if (!name) return;
  try {
    showLoading();
    await db.collection('dl_categories').add({
      name,
      subcategories: [],
      createdAt: firebase.firestore.FieldValue.serverTimestamp()
    });
    showToast('Category created! ✅', 'success');
    closeModal('addCategoryModal');
    document.getElementById('catName').value = '';
  } catch(err) {
    showToast(err.message, 'error');
  } finally { hideLoading(); }
}

async function saveSubcategory() {
  const catId = document.getElementById('parentCatId').value;
  const subId = document.getElementById('parentSubId').value;
  const name = document.getElementById('subName').value.trim();
  if (!name || !catId) return;
  
  try {
    // Show spinner in btn but don't block screen
    const btn = event.target;
    const oldText = btn.innerHTML;
    btn.innerHTML = '<div class="loader" style="width:16px;height:16px;border-width:2px"></div>';
    btn.disabled = true;
    
    const catRef = db.collection('dl_categories').doc(catId);
    const doc = await catRef.get();
    let subs = doc.data().subcategories || [];
    
    if (!subId) {
      // Adding Subcategory
      if (subs.length >= 100) throw new Error('Maximum 100 subcategories allowed.');
      subs.push({ id: Date.now().toString(), name, subs: [] });
    } else {
      // Adding Sub-Subcategory
      const subIdx = subs.findIndex(s => s.id === subId);
      if (subIdx === -1) throw new Error('Parent subcategory not found');
      if (!subs[subIdx].subs) subs[subIdx].subs = [];
      if (subs[subIdx].subs.length >= 20) throw new Error('Maximum 20 sub-subcategories allowed.');
      subs[subIdx].subs.push({ id: Date.now().toString(), name });
    }
    
    await catRef.update({ subcategories: subs });
    
    document.getElementById('subName').value = '';
    document.getElementById('addSubSuccessText').textContent = `Added "${name}"! You can add another.`;
    document.getElementById('addSubSuccess').classList.remove('hidden');
    document.getElementById('subName').focus();
    
    btn.innerHTML = oldText;
    btn.disabled = false;
  } catch(err) {
    showToast(err.message, 'error');
    event.target.innerHTML = 'Add & Continue';
    event.target.disabled = false;
  }
}

async function deleteCategory(catId) {
  showConfirm('Delete Category', 'This will delete the category, but NOT the files inside. Delete files manually if needed.', async () => {
    try {
      showLoading();
      await db.collection('dl_categories').doc(catId).delete();
      showToast('Deleted', 'info');
    } catch(err) { showToast(err.message, 'error'); } finally { hideLoading(); }
  });
}

async function deleteSub(catId, subId) {
  showConfirm('Delete Subcategory', 'This will remove the subcategory and sub-subcategories from the list.', async () => {
    try {
      showLoading();
      const catRef = db.collection('dl_categories').doc(catId);
      const doc = await catRef.get();
      let subs = doc.data().subcategories || [];
      subs = subs.filter(s => s.id !== subId);
      await catRef.update({ subcategories: subs });
      showToast('Deleted', 'info');
    } catch(err) { showToast(err.message, 'error'); } finally { hideLoading(); }
  });
}

async function deleteSubSub(catId, subId, subSubId) {
  showConfirm('Delete Sub-Subcategory', 'Remove this sub-subcategory?', async () => {
    try {
      showLoading();
      const catRef = db.collection('dl_categories').doc(catId);
      const doc = await catRef.get();
      let subs = doc.data().subcategories || [];
      const subIdx = subs.findIndex(s => s.id === subId);
      if (subIdx > -1 && subs[subIdx].subs) {
        subs[subIdx].subs = subs[subIdx].subs.filter(ss => ss.id !== subSubId);
        await catRef.update({ subcategories: subs });
      }
      showToast('Deleted', 'info');
    } catch(err) { showToast(err.message, 'error'); } finally { hideLoading(); }
  });
}

// ── Resource Upload / Links ───────────────────────────────────────────────────

async function saveResource() {
  const catId = document.getElementById('resCatId').value;
  const subId = document.getElementById('resSubId').value;
  const subSubId = document.getElementById('resSubSubId').value || null;
  const name = document.getElementById('resName').value.trim();
  
  if (!name) { showToast('Name is required', 'error'); return; }
  
  if (currentResTab === 'upload') {
    const file = document.getElementById('uploadInput').files[0];
    if (!file) { showToast('Please select a file', 'error'); return; }
    
    const ext = file.name.split('.').pop().toLowerCase();
    const allowed = ['pdf','jpg','jpeg','png','mp3','mp4','docx','pptx','xlsx','xls'];
    if (!allowed.includes(ext)) {
      showToast('Invalid file type', 'error'); return;
    }
    
    try {
      showLoading();
      const path = `downloads/${catId}/${subId}/${subSubId ? subSubId+'/' : ''}${Date.now()}_${file.name}`;
      const storageRef = firebase.storage().ref(path);
      await storageRef.put(file);
      const url = await storageRef.getDownloadURL();
      
      await db.collection('dl_files').add({
        catId, subId, subSubId,
        name, url, path,
        fileKind: 'upload',
        type: ext,
        createdAt: firebase.firestore.FieldValue.serverTimestamp()
      });
      showToast('File uploaded successfully! 🚀', 'success');
      closeModal('addResourceModal');
    } catch(err) { showToast(err.message, 'error'); } finally { hideLoading(); }
    
  } else {
    // Link
    const linkType = document.getElementById('linkType').value;
    const url = document.getElementById('linkUrl').value.trim();
    if (!url) { showToast('URL is required', 'error'); return; }
    
    try {
      showLoading();
      await db.collection('dl_files').add({
        catId, subId, subSubId,
        name, url, path: null,
        fileKind: 'link',
        type: linkType,
        createdAt: firebase.firestore.FieldValue.serverTimestamp()
      });
      showToast('Link added successfully! 🚀', 'success');
      closeModal('addResourceModal');
    } catch(err) { showToast(err.message, 'error'); } finally { hideLoading(); }
  }
}

async function deleteFile(fileId, path) {
  showConfirm('Delete Resource', 'Permanently delete this item?', async () => {
    try {
      showLoading();
      if (path) {
        try { await firebase.storage().ref(path).delete(); } catch(e) { console.log('Storage delete err', e); }
      }
      await db.collection('dl_files').doc(fileId).delete();
      showToast('Resource deleted', 'info');
    } catch(err) { showToast(err.message, 'error'); } finally { hideLoading(); }
  });
}

function getFileIcon(type) {
  const icons = {
    pdf: '📄', jpg: '🖼️', jpeg: '🖼️', png: '🖼️',
    mp3: '🎵', mp4: '🎥', docx: '📝', pptx: '📊', xlsx: '📈', xls: '📈',
    youtube: '📺', google_doc: '📝', google_sheet: '📊', google_form: '📋',
    google_drive: '💾', ms_form: '📝', chatgpt: '🤖', link: '🔗'
  };
  return icons[type] || '📦';
}
</script>
