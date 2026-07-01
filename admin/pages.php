<?php
$pageTitle  = 'Site Pages';
$activePage = 'pages';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Site <span class="grad-text">Pages</span></h1>
        <p class="text-slate-500 text-sm mt-1">Manage pages and their access level requirements</p>
      </div>
      <button onclick="openModal('addPageModal')" class="btn-primary py-2.5 px-5 rounded-xl">
        + Add Page
      </button>
    </div>

    <!-- Pages grid -->
    <div id="pagesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div class="glass rounded-2xl p-10 text-center border border-white/8 col-span-full">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading pages...</p>
      </div>
    </div>

  </main>
</div>

<!-- Add Page Modal -->
<div id="addPageModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-lg w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-1" id="pageModalTitle">Add New Page</h3>
    <p class="text-slate-400 text-sm mb-5">Register a page in the portal navigation system</p>
    <input type="hidden" id="editPageId">
    <div class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Page Title</label>
        <input id="pageTitle_" type="text" class="input-field" placeholder="e.g. Gallery">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">URL / Slug</label>
        <input id="pageSlug" type="text" class="input-field" placeholder="e.g. /gallery.html">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Filename</label>
        <input id="pageFile" type="text" class="input-field" placeholder="e.g. gallery.html">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Minimum Level Required</label>
        <select id="pageLevel" class="input-field">
          <!-- Populated dynamically -->
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Page Icon (emoji)</label>
        <input id="pageIcon" type="text" class="input-field" placeholder="e.g. 🖼️" maxlength="4">
      </div>
      <div class="flex items-center gap-3">
        <label class="perm-toggle">
          <input type="checkbox" id="pagePublic">
          <span class="slider"></span>
        </label>
        <span class="text-sm text-slate-300">Public page (accessible without login)</span>
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button onclick="closeModal('addPageModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="savePage()" class="btn-primary flex-1 py-2.5 text-sm justify-center">Save Page</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
requireAdmin();

let allLevels = [];

// Load levels for dropdown
async function loadLevels() {
  const snap = await db.collection('userLevels').orderBy('level').get();
  allLevels = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  const sel = document.getElementById('pageLevel');
  sel.innerHTML = allLevels.map(l =>
    `<option value="${l.id}">${l.displayName || l.name}</option>`
  ).join('');
}

// Real-time pages listener
db.collection('sitePages').orderBy('order').onSnapshot(snap => {
  const pages = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  renderPages(pages);
});

function renderPages(pages) {
  const container = document.getElementById('pagesContainer');
  if (pages.length === 0) {
    container.innerHTML = `<div class="glass rounded-2xl p-12 text-center border border-white/8 col-span-full">
      <p class="text-4xl mb-3">📄</p>
      <p class="text-slate-500">No pages yet. Add your first page!</p></div>`;
    return;
  }
  container.innerHTML = pages.map(p => `
    <div class="glass rounded-2xl border border-white/8 p-5 hover:border-violet-500/30 transition-all group fade-in">
      <div class="flex items-start justify-between mb-3">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500/20 to-blue-500/20 border border-violet-500/20 flex items-center justify-center text-xl">
          ${p.icon || '📄'}
        </div>
        <div class="flex gap-1">
          <button onclick="openEditPage('${p.id}')"
                  class="text-xs px-2.5 py-1.5 rounded-lg bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 transition border border-violet-500/20">
            Edit
          </button>
          <button onclick="deletePage('${p.id}','${p.title}')"
                  class="text-xs px-2.5 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition border border-red-500/20">
            Delete
          </button>
        </div>
      </div>
      <h3 class="font-bold text-white mb-1">${p.title}</h3>
      <p class="text-xs text-slate-500 font-mono mb-3">${p.slug || p.filename}</p>
      <div class="flex items-center gap-2 flex-wrap">
        ${p.isPublic
          ? `<span class="badge bg-green-500/20 text-green-400 border-green-500/30 text-[10px]">🌐 Public</span>`
          : `<span class="badge bg-amber-500/20 text-amber-400 border-amber-500/30 text-[10px]">🔒 ${p.requiredLevel || 'Any login'}</span>`
        }
        <a href="../${p.filename}" target="_blank"
           class="badge bg-blue-500/20 text-blue-400 border-blue-500/30 text-[10px] hover:bg-blue-500/30 transition">
          🔗 View
        </a>
      </div>
    </div>
  `).join('');
}

async function openEditPage(pageId) {
  document.getElementById('pageModalTitle').textContent = 'Edit Page';
  document.getElementById('editPageId').value = pageId;
  const doc = await db.collection('sitePages').doc(pageId).get();
  const p = doc.data();
  document.getElementById('pageTitle_').value = p.title || '';
  document.getElementById('pageSlug').value   = p.slug || '';
  document.getElementById('pageFile').value   = p.filename || '';
  document.getElementById('pageIcon').value   = p.icon || '';
  document.getElementById('pagePublic').checked = !!p.isPublic;

  await loadLevels();
  document.getElementById('pageLevel').value = p.requiredLevel || '';

  openModal('addPageModal');
}

async function savePage() {
  const editId = document.getElementById('editPageId').value;
  const data = {
    title:         document.getElementById('pageTitle_').value.trim(),
    slug:          document.getElementById('pageSlug').value.trim(),
    filename:      document.getElementById('pageFile').value.trim(),
    requiredLevel: document.getElementById('pageLevel').value,
    icon:          document.getElementById('pageIcon').value.trim() || '📄',
    isPublic:      document.getElementById('pagePublic').checked,
    order:         Date.now(),
  };

  if (!data.title || !data.slug) {
    showToast('Title and URL are required.', 'warning'); return;
  }

  try {
    showLoading();
    if (editId) {
      await db.collection('sitePages').doc(editId).update(data);
      showToast('Page updated! ✅', 'success');
    } else {
      data.createdAt = firebase.firestore.FieldValue.serverTimestamp();
      await db.collection('sitePages').add(data);
      showToast('Page added! ✅', 'success');
    }
    closeModal('addPageModal');
    document.getElementById('editPageId').value = '';
    document.getElementById('pageModalTitle').textContent = 'Add New Page';
    ['pageTitle_','pageSlug','pageFile','pageIcon'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('pagePublic').checked = false;
  } catch(err) {
    showToast('Failed: ' + err.message, 'error');
  } finally {
    hideLoading();
  }
}

async function deletePage(id, title) {
  showConfirm(`Delete "${title}"`, 'This will remove the page from the portal. The actual file is not deleted.',
    async () => {
      try {
        showLoading();
        await db.collection('sitePages').doc(id).delete();
        showToast(`"${title}" removed.`, 'info');
      } catch(err) {
        showToast('Failed: ' + err.message, 'error');
      } finally {
        hideLoading();
      }
    }, 'Delete', true
  );
}

auth.onAuthStateChanged(user => { if (user) loadLevels(); });
</script>
