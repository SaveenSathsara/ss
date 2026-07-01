<?php
$pageTitle  = 'View Student Marks';
$activePage = 'terms';
$depth      = 1;
include '../includes/header.php';
?>

<main class="w-full max-w-7xl mx-auto px-6 py-8 min-h-[70vh]">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
    <div>
      <a href="dashboard.php" class="text-xs text-slate-500 hover:text-white transition uppercase font-bold tracking-wider mb-2 inline-block">← Back to Dashboard</a>
      <h1 class="text-3xl font-black text-white">Student <span class="grad-text">Directory</span></h1>
      <p class="text-slate-500 text-sm mt-1">Search and view academic records of students.</p>
    </div>
  </div>

  <div id="loadingBox" class="glass rounded-2xl p-12 text-center border border-white/8">
    <div class="loader mx-auto mb-3"></div>
    <p class="text-slate-500 text-sm">Loading database...</p>
  </div>

  <div id="mainContent" class="hidden fade-in space-y-6">
    
    <!-- Search Bar -->
    <div class="glass rounded-2xl p-6 border border-white/8 flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Search Name / Entrance No.</label>
        <input type="text" id="searchInput" oninput="filterProfiles()" class="input-field" placeholder="Type here...">
      </div>
      <div class="md:w-1/3">
        <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Filter by School</label>
        <select id="schoolFilter" onchange="filterProfiles()" class="input-field bg-dark">
          <option value="">All Schools</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Student List -->
      <div class="lg:col-span-1 glass rounded-2xl border border-white/8 flex flex-col h-[600px] overflow-hidden">
        <div class="px-5 py-4 border-b border-white/8 bg-white/5">
          <h2 class="font-bold text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-violet-500"></span> Profiles
          </h2>
        </div>
        <div id="profilesList" class="flex-1 overflow-y-auto p-4 space-y-2">
          <!-- Populated by JS -->
        </div>
      </div>

      <!-- Term Marks View -->
      <div class="lg:col-span-2 glass rounded-2xl border border-white/8 flex flex-col h-[600px] overflow-hidden relative">
        <div class="px-5 py-4 border-b border-white/8 bg-white/5 flex justify-between items-center">
          <h2 class="font-bold text-white flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Academic Records
          </h2>
          <span id="selectedStudentName" class="text-sm font-semibold text-violet-400 hidden">Select a student</span>
        </div>
        
        <div id="recordsView" class="flex-1 overflow-y-auto p-6">
          <div class="h-full flex flex-col items-center justify-center text-center opacity-50">
            <p class="text-6xl mb-4">👀</p>
            <p class="text-white font-semibold text-lg">Select a profile</p>
            <p class="text-sm text-slate-400">Click on a student from the list to view their term marks.</p>
          </div>
        </div>
        
        <!-- Loading overlay for fetching marks -->
        <div id="marksLoadingOverlay" class="hidden absolute inset-0 bg-black/50 backdrop-blur-sm z-10 flex items-center justify-center">
          <div class="loader"></div>
        </div>
      </div>

    </div>

  </div>

</main>

<?php include '../includes/footer.php'; ?>

<script>
// Check Permission First
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.html'; return; }
  
  let hasPerm = false;
  const userDoc = await db.collection('users').doc(user.uid).get();
  if (userDoc.exists) {
    const ud = userDoc.data();
    if (ud.userLevel === 'admin') hasPerm = true;
    else {
      const pDoc = await db.collection('userLevels').doc(ud.userLevel).get();
      if (pDoc.exists && pDoc.data().permissions?.view_others_marks) hasPerm = true;
    }
  }
  
  if (!hasPerm) {
    window.location.href = 'dashboard.php';
    return;
  }
  
  loadData();
});

let allSchools = [];
let allProfiles = [];

async function loadData() {
  try {
    const [schoolsSnap, profilesSnap] = await Promise.all([
      db.collection('schools').get(),
      db.collection('studentProfiles').get() // Admins/Teachers get all profiles
    ]);
    
    allSchools = schoolsSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    allProfiles = profilesSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    
    // Populate school filter
    const schoolFilter = document.getElementById('schoolFilter');
    allSchools.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.id;
      opt.textContent = `${s.name} (${s.no})`;
      schoolFilter.appendChild(opt);
    });
    
    document.getElementById('loadingBox').classList.add('hidden');
    document.getElementById('mainContent').classList.remove('hidden');
    
    filterProfiles();
  } catch (err) {
    showToast('Error loading data: ' + err.message, 'error');
  }
}

function filterProfiles() {
  const term = document.getElementById('searchInput').value.toLowerCase().trim();
  const sid = document.getElementById('schoolFilter').value;
  
  let filtered = allProfiles;
  
  if (sid) {
    filtered = filtered.filter(p => p.schoolId === sid);
  }
  
  if (term) {
    filtered = filtered.filter(p => 
      p.fullName.toLowerCase().includes(term) || 
      p.entranceNumber.toLowerCase().includes(term)
    );
  }
  
  renderProfilesList(filtered);
}

let activeProfileId = null;

function renderProfilesList(profiles) {
  const list = document.getElementById('profilesList');
  if (profiles.length === 0) {
    list.innerHTML = '<p class="text-sm text-slate-500 text-center py-4">No matching profiles found.</p>';
    return;
  }
  
  list.innerHTML = profiles.map(p => {
    const school = allSchools.find(s => s.id === p.schoolId);
    const schoolName = school ? school.name : 'Unknown';
    const isActive = p.id === activeProfileId;
    
    return `
    <button onclick="viewProfileMarks('${p.id}', '${p.fullName.replace(/'/g, "\\'")}')" 
            class="w-full text-left p-3 rounded-xl border ${isActive ? 'bg-violet-500/10 border-violet-500/40' : 'bg-white/5 border-white/5 hover:border-violet-500/20 hover:bg-white/10'} transition group">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-white font-bold shrink-0">
          ${p.fullName.charAt(0).toUpperCase()}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-white truncate group-hover:text-violet-300 transition">${p.fullName}</p>
          <p class="text-[10px] text-slate-400 truncate">${schoolName}</p>
          <p class="text-[10px] text-slate-500 mt-0.5">ID: ${p.entranceNumber}</p>
        </div>
      </div>
    </button>
    `;
  }).join('');
}

async function viewProfileMarks(profileId, fullName) {
  activeProfileId = profileId;
  document.getElementById('selectedStudentName').textContent = fullName;
  document.getElementById('selectedStudentName').classList.remove('hidden');
  
  // Update list styling
  filterProfiles(); // Re-render to show active state
  
  document.getElementById('marksLoadingOverlay').classList.remove('hidden');
  
  try {
    const termsSnap = await db.collection('termMarks')
      .where('studentProfileId', '==', profileId)
      .orderBy('year', 'desc')
      .orderBy('term', 'desc')
      .get();
      
    const terms = termsSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    renderTermMarks(terms);
  } catch (err) {
    showToast('Failed to load marks.', 'error');
  } finally {
    document.getElementById('marksLoadingOverlay').classList.add('hidden');
  }
}

function renderTermMarks(terms) {
  const view = document.getElementById('recordsView');
  
  if (terms.length === 0) {
    view.innerHTML = `
      <div class="h-full flex flex-col items-center justify-center text-center opacity-70">
        <p class="text-5xl mb-4">📭</p>
        <p class="text-white font-semibold text-lg">No records found</p>
        <p class="text-sm text-slate-400">This student hasn't added any term marks yet.</p>
      </div>`;
    return;
  }
  
  view.innerHTML = `<div class="space-y-6">` + terms.map(t => {
    // Group marks by main subject
    const marksGrouped = {};
    (t.marks || []).forEach(m => {
      if (!marksGrouped[m.subject]) marksGrouped[m.subject] = [];
      marksGrouped[m.subject].push(m);
    });
    
    return `
    <div class="border border-white/10 rounded-2xl bg-black/20 overflow-hidden fade-in">
      <div class="px-5 py-3 border-b border-white/5 bg-white/5 flex flex-wrap items-center justify-between gap-2">
        <div>
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
            ${t.year} - Term ${t.term}
          </h3>
          <p class="text-[10px] text-slate-400 mt-0.5">Grade ${t.grade} • Class ${t.className}</p>
        </div>
        <div class="flex gap-4">
          <div class="text-center">
            <p class="text-[9px] text-slate-500 uppercase tracking-wider">Class Rank</p>
            <p class="text-base font-bold text-amber-400">${t.rankInClass || '-'}</p>
          </div>
          <div class="text-center">
            <p class="text-[9px] text-slate-500 uppercase tracking-wider">Grade Rank</p>
            <p class="text-base font-bold text-blue-400">${t.rankInGrade || '-'}</p>
          </div>
        </div>
      </div>
      
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          ${Object.keys(marksGrouped).map(subName => {
            const items = marksGrouped[subName];
            return `
            <div class="border border-white/5 rounded-lg p-3 bg-white/5">
              <p class="text-xs font-bold text-slate-300 mb-2">${subName}</p>
              ${items.map(item => `
                <div class="flex justify-between items-center text-xs py-1 border-b border-white/5 last:border-0">
                  <span class="text-slate-400">
                    ${item.basket ? `[${item.basket}] ` : ''}${item.subSubject || 'Overall'}
                  </span>
                  <span class="font-semibold text-white ${Number(item.marks) < 40 ? 'text-red-400' : ''}">${item.marks}</span>
                </div>
              `).join('')}
            </div>`;
          }).join('')}
        </div>
      </div>
    </div>`;
  }).join('') + `</div>`;
}
</script>
