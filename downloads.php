<?php
$pageTitle  = 'Downloads';
$activePage = 'downloads';
$depth      = 0;
include 'includes/header.php';
include 'includes/nav.php';
?>

<div class="h-32"></div>

<main class="max-w-7xl mx-auto px-6 py-8 min-h-[60vh]">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10 fade-in">
    <div>
      <h1 class="text-4xl font-black text-white">Resource <span class="grad-text">Downloads</span></h1>
      <p class="text-slate-400 text-lg mt-1">Access files, software, links, and documents.</p>
    </div>
  </div>

  <div id="loadingBox" class="glass rounded-3xl p-16 text-center border border-white/8">
    <div class="loader mx-auto mb-4"></div>
    <p class="text-slate-400">Loading resources...</p>
  </div>

  <div id="downloadsWrapper" class="space-y-8 hidden fade-in" style="animation-delay:.1s">
    <!-- Categories will render here -->
  </div>

</main>

<?php include 'includes/footer.php'; ?>

<script>
// Enforce permission
requirePermission('view_downloads', 'index.html');

let allCategories = [];
let allFiles = [];

// Load data
db.collection('dl_categories').orderBy('createdAt').get().then(snap => {
  allCategories = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  checkRender();
});

db.collection('dl_files').orderBy('createdAt', 'desc').get().then(snap => {
  allFiles = snap.docs.map(d => ({ id: d.id, ...d.data() }));
  checkRender();
});

function checkRender() {
  if (allCategories && allFiles) {
    document.getElementById('loadingBox').classList.add('hidden');
    document.getElementById('downloadsWrapper').classList.remove('hidden');
    renderDownloads();
  }
}

function renderDownloads() {
  const container = document.getElementById('downloadsWrapper');
  
  if (allCategories.length === 0) {
    container.innerHTML = `<div class="glass rounded-3xl p-16 text-center border border-white/8">
      <p class="text-5xl mb-4">📭</p>
      <p class="text-white text-xl font-bold">No Downloads Available</p>
      <p class="text-slate-400 mt-2">Check back later for new resources.</p>
    </div>`;
    return;
  }

  container.innerHTML = allCategories.map(cat => {
    const subcats = cat.subcategories || [];
    if (subcats.length === 0) return '';
    
    // Check if category has any files
    let hasAnyFiles = false;
    subcats.forEach(s => {
      if (allFiles.some(f => f.catId === cat.id && f.subId === s.id)) hasAnyFiles = true;
    });
    if (!hasAnyFiles) return ''; 
    
    return `
    <div class="glass rounded-3xl border border-white/8 overflow-hidden mb-8">
      <!-- Category Header -->
      <div class="px-8 py-5 border-b border-white/8 bg-gradient-to-r from-white/5 to-transparent flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-violet-500/20 border border-violet-500/30 flex items-center justify-center text-2xl shadow-[0_0_20px_rgba(124,58,237,0.2)]">📁</div>
        <h2 class="text-2xl font-bold text-white">${cat.name}</h2>
      </div>
      
      <div class="p-8 space-y-8">
        ${subcats.map(sub => {
          const subSubs = sub.subs || [];
          const directFiles = allFiles.filter(f => f.catId === cat.id && f.subId === sub.id && !f.subSubId);
          
          // Check if subcat has anything
          if (directFiles.length === 0 && subSubs.length === 0) return '';
          let subHasContent = directFiles.length > 0;
          subSubs.forEach(ss => {
            if (allFiles.some(f => f.catId === cat.id && f.subId === sub.id && f.subSubId === ss.id)) subHasContent = true;
          });
          if (!subHasContent) return '';

          return `
          <div class="bg-black/20 rounded-2xl p-6 border border-white/5">
            <h3 class="text-xl font-bold text-violet-300 mb-5 flex items-center gap-3">
              <span class="w-2.5 h-2.5 rounded-full bg-violet-500 shadow-[0_0_10px_rgba(124,58,237,0.5)]"></span>
              ${sub.name}
            </h3>
            
            ${directFiles.length > 0 ? `
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
                ${directFiles.map(f => renderResourceCard(f)).join('')}
              </div>
            ` : ''}

            ${subSubs.map(ss => {
              const ssFiles = allFiles.filter(f => f.catId === cat.id && f.subId === sub.id && f.subSubId === ss.id);
              if (ssFiles.length === 0) return '';
              
              return `
              <div class="mt-6 border-l-2 border-white/10 pl-5">
                <h4 class="text-lg font-semibold text-amber-300 mb-4 flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  ${ss.name}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                  ${ssFiles.map(f => renderResourceCard(f)).join('')}
                </div>
              </div>`;
            }).join('')}
          </div>`;
        }).join('')}
      </div>
    </div>`;
  }).join('');
}

function renderResourceCard(f) {
  const icon = getFileIcon(f.type);
  const color = getFileColor(f.type);
  const isLink = f.fileKind === 'link';
  const actionText = isLink ? 'Open Link ↗' : 'Download ⬇️';
  
  return `
  <a href="${f.url}" target="_blank" ${!isLink ? 'download' : ''}
     class="group flex flex-col p-5 rounded-2xl glass hover:bg-white/5 border border-white/8 hover:border-${color}-500/40 transition-all hover:-translate-y-1 shadow-lg hover:shadow-${color}-500/10">
    <div class="flex items-start justify-between mb-4">
      <div class="w-12 h-12 rounded-xl bg-${color}-500/10 border border-${color}-500/20 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
        ${icon}
      </div>
      <span class="text-slate-500 group-hover:text-${color}-400 transition text-xs font-semibold">${actionText}</span>
    </div>
    <h4 class="text-white font-bold mb-1 line-clamp-2" title="${f.name}">${f.name}</h4>
    <div class="flex items-center justify-between mt-auto pt-3 border-t border-white/5">
      <span class="badge bg-white/5 text-slate-400 border-white/10 uppercase text-[10px] tracking-wider">${f.type.replace('_',' ')}</span>
      <span class="text-[10px] text-slate-500">${formatDate(f.createdAt)}</span>
    </div>
  </a>`;
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

function getFileColor(type) {
  if (['youtube','pdf','mp4'].includes(type)) return 'red';
  if (['docx','mp3','google_doc','link'].includes(type)) return 'blue';
  if (['xlsx','xls','google_sheet','google_form'].includes(type)) return 'green';
  if (['chatgpt','google_drive','ms_form'].includes(type)) return 'emerald';
  return 'violet';
}
</script>
