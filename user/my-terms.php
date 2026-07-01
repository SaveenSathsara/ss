<?php
$pageTitle  = 'My Term Marks';
$activePage = 'terms';
$depth      = 1;
include '../includes/header.php';
?>

<main class="w-full max-w-7xl mx-auto px-6 py-8 min-h-[70vh]">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
    <div>
      <a href="dashboard.php" class="text-xs text-slate-500 hover:text-white transition uppercase font-bold tracking-wider mb-2 inline-block">← Back to Dashboard</a>
      <h1 class="text-3xl font-black text-white">Term <span class="grad-text">Marks</span></h1>
      <p class="text-slate-500 text-sm mt-1">Track your academic progress across different terms.</p>
    </div>
    <div class="flex gap-3">
      <a href="add-term.php" class="btn-primary py-2.5 px-5 rounded-xl text-sm font-semibold text-center">+ Add Term Record</a>
      <a href="others-marks.php" class="btn-ghost py-2.5 px-5 rounded-xl text-sm font-semibold hidden" id="btnViewOthers">View Others' Marks</a>
    </div>
  </div>

  <!-- Stats -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="glass rounded-xl p-5 border border-white/8 text-center fade-in">
      <p class="text-2xl font-black text-violet-400" id="statTerms">0</p>
      <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mt-1">Total Terms</p>
    </div>
    <div class="glass rounded-xl p-5 border border-white/8 text-center fade-in" style="animation-delay:.05s">
      <p class="text-2xl font-black text-blue-400" id="statProfiles">0</p>
      <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mt-1">Student Profiles</p>
    </div>
    <div class="glass rounded-xl p-5 border border-white/8 text-center fade-in" style="animation-delay:.1s">
      <p class="text-2xl font-black text-amber-400" id="statBestRank">—</p>
      <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mt-1">Best Class Rank</p>
    </div>
    <div class="glass rounded-xl p-5 border border-white/8 text-center fade-in" style="animation-delay:.15s">
      <p class="text-2xl font-black text-emerald-400" id="statAvg">—</p>
      <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold mt-1">My Avg Marks</p>
    </div>
  </div>

  <!-- Term Records List -->
  <div id="termsContainer" class="space-y-6">
    <div class="glass rounded-2xl p-12 text-center border border-white/8">
      <div class="loader mx-auto mb-3"></div>
      <p class="text-slate-500 text-sm">Loading records...</p>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>

<script>
auth.onAuthStateChanged(async user => {
  if (!user) { window.location.href = '../login.html'; return; }

  // Check view_others_marks permission
  const userDoc = await db.collection('users').doc(user.uid).get();
  if (userDoc.exists) {
    const ud = userDoc.data();
    if (ud.userLevel === 'admin') {
      document.getElementById('btnViewOthers').classList.remove('hidden');
    } else {
      const pDoc = await db.collection('userLevels').doc(ud.userLevel).get();
      if (pDoc.exists && pDoc.data().permissions?.view_others_marks) {
        document.getElementById('btnViewOthers').classList.remove('hidden');
      }
    }
  }

  loadTerms(user.uid);
});

let myTerms = [];
let myProfiles = [];
let allSchools = [];

async function loadTerms(uid) {
  try {
    // Load without compound orderBy to avoid index requirement
    const [schoolsSnap, profilesSnap, termsSnap] = await Promise.all([
      db.collection('schools').get(),
      db.collection('studentProfiles').where('uid', '==', uid).get(),
      db.collection('termMarks').where('uid', '==', uid).get()
    ]);

    allSchools  = schoolsSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    myProfiles  = profilesSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    myTerms     = termsSnap.docs.map(d => ({ id: d.id, ...d.data() }));

    // Sort client-side: newest year first, then newest term first
    myTerms.sort((a, b) => {
      if (b.year !== a.year) return (b.year || 0) - (a.year || 0);
      return (b.term || 0) - (a.term || 0);
    });

    updateStats();
    renderTerms();
  } catch (err) {
    document.getElementById('termsContainer').innerHTML =
      `<div class="glass rounded-2xl p-10 text-center border border-red-500/20">
        <p class="text-red-400 text-sm">Failed to load records: ${err.message}</p>
       </div>`;
  }
}

function updateStats() {
  document.getElementById('statTerms').textContent    = myTerms.length;
  document.getElementById('statProfiles').textContent = myProfiles.length;

  let bestRank = Infinity, totalMarks = 0, totalSubjects = 0;
  myTerms.forEach(t => {
    if (t.rankInClass && t.rankInClass < bestRank) bestRank = t.rankInClass;
    (t.marks || []).forEach(m => {
      if (m.marks !== null && m.marks !== '' && !isNaN(Number(m.marks))) {
        totalMarks += Number(m.marks); totalSubjects++;
      }
    });
  });

  document.getElementById('statBestRank').textContent = bestRank === Infinity ? '—' : '#' + bestRank;
  document.getElementById('statAvg').textContent      = totalSubjects > 0 ? (totalMarks / totalSubjects).toFixed(1) : '—';
}

function renderTerms() {
  const container = document.getElementById('termsContainer');
  if (myTerms.length === 0) {
    container.innerHTML = `
      <div class="glass rounded-2xl p-14 text-center border border-white/8">
        <p class="text-5xl mb-4">📝</p>
        <p class="text-slate-400 font-medium mb-1">No term records yet</p>
        <p class="text-slate-500 text-sm mb-5">Start tracking your academic progress.</p>
        <a href="add-term.php" class="btn-primary py-2.5 px-6 rounded-xl text-sm inline-block">Add First Record</a>
      </div>`;
    return;
  }

  container.innerHTML = myTerms.map(t => {
    const profile    = myProfiles.find(p => p.id === t.studentProfileId);
    const school     = profile ? allSchools.find(s => s.id === profile.schoolId) : null;
    const schoolName = school ? school.name : 'Unknown School';

    // Group marks by main subject
    const marksGrouped = {};
    (t.marks || []).forEach(m => {
      if (!marksGrouped[m.subject]) marksGrouped[m.subject] = [];
      marksGrouped[m.subject].push(m);
    });

    // Compute my total
    const allMarkVals = (t.marks || []).map(m => Number(m.marks)).filter(v => !isNaN(v) && v !== null);
    const myTotal     = allMarkVals.reduce((a, b) => a + b, 0);
    const myAvg       = allMarkVals.length > 0 ? (myTotal / allMarkVals.length).toFixed(1) : '—';

    // Attendance
    const totalDays   = t.totalDays   || null;
    const absentDays  = t.absentDays  || null;
    const presentDays = (totalDays !== null && absentDays !== null) ? totalDays - absentDays : null;

    return `
    <div class="glass rounded-2xl overflow-hidden border border-white/8 fade-in relative group">

      <!-- Card Header -->
      <div class="px-6 py-4 border-b border-white/8 bg-white/3 flex flex-wrap items-start justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 inline-block"></span>
            ${t.year} · Term ${t.term}
          </h2>
          <p class="text-xs text-slate-400 mt-1">Grade ${t.grade} &bull; Class ${t.className} &bull; ${schoolName}</p>
        </div>

        <!-- Header Stats -->
        <div class="flex flex-wrap items-center gap-4 text-center">
          ${t.studentCount ? `<div>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Students</p>
            <p class="text-lg font-bold text-slate-300">${t.studentCount}</p>
          </div><div class="w-px h-8 bg-white/10"></div>` : ''}
          <div>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Class Avg</p>
            <p class="text-lg font-bold text-blue-400">${t.classAvg || '—'}</p>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">My Avg</p>
            <p class="text-lg font-bold text-violet-400">${myAvg}</p>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Class Rank</p>
            <p class="text-lg font-bold text-amber-400">${t.rankInClass ? '#' + t.rankInClass : '—'}</p>
          </div>
          <div class="w-px h-8 bg-white/10"></div>
          <div>
            <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Grade Rank</p>
            <p class="text-lg font-bold text-emerald-400">${t.rankInGrade ? '#' + t.rankInGrade : '—'}</p>
          </div>
        </div>
      </div>

      <!-- Subject Marks -->
      <div class="p-6">
        <h3 class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-4">Subject Marks</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
          ${Object.keys(marksGrouped).map(subName => {
            const items = marksGrouped[subName];
            const subTotal = items.reduce((a, b) => a + (isNaN(Number(b.marks)) ? 0 : Number(b.marks)), 0);
            return `
            <div class="border border-white/5 rounded-xl p-4 bg-black/20">
              <div class="flex justify-between items-start mb-2">
                <p class="text-sm font-bold text-slate-300">${subName}</p>
                ${items.length > 1 ? `<span class="text-[10px] text-violet-400 font-semibold bg-violet-500/10 px-1.5 py-0.5 rounded">${subTotal} total</span>` : ''}
              </div>
              ${items.map(item => {
                const mn = Number(item.marks);
                const color = mn < 40 ? 'text-red-400' : mn < 60 ? 'text-amber-400' : mn >= 75 ? 'text-emerald-400' : 'text-white';
                return `
                <div class="flex justify-between items-center text-sm py-1 border-b border-white/5 last:border-0">
                  <span class="text-slate-400 text-xs">${item.basket ? '['+item.basket+'] ' : ''}${item.subSubject || 'Overall'}</span>
                  <span class="font-bold ${color}">${item.marks}</span>
                </div>`;
              }).join('')}
            </div>`;
          }).join('')}
        </div>

        <!-- Attendance -->
        ${(totalDays !== null || absentDays !== null) ? `
        <div class="border-t border-white/8 pt-4 flex flex-wrap gap-6">
          <h3 class="w-full text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Attendance</h3>
          ${totalDays !== null ? `<div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 text-sm">📅</span>
            <div>
              <p class="text-[10px] text-slate-500 uppercase font-semibold">School Days</p>
              <p class="text-base font-bold text-blue-400">${totalDays}</p>
            </div>
          </div>` : ''}
          ${presentDays !== null ? `<div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 text-sm">✓</span>
            <div>
              <p class="text-[10px] text-slate-500 uppercase font-semibold">Present</p>
              <p class="text-base font-bold text-emerald-400">${presentDays}</p>
            </div>
          </div>` : ''}
          ${absentDays !== null ? `<div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-400 text-sm">✗</span>
            <div>
              <p class="text-[10px] text-slate-500 uppercase font-semibold">Absent</p>
              <p class="text-base font-bold text-red-400">${absentDays}</p>
            </div>
          </div>` : ''}
          ${(totalDays && absentDays !== null) ? `<div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400 text-sm">%</span>
            <div>
              <p class="text-[10px] text-slate-500 uppercase font-semibold">Attendance</p>
              <p class="text-base font-bold text-violet-400">${((presentDays/totalDays)*100).toFixed(1)}%</p>
            </div>
          </div>` : ''}
        </div>` : ''}
      </div>

      <!-- Edit / Delete footer -->
      <div class="flex justify-end gap-2 px-6 py-3 border-t border-white/5 bg-black/10">
        <a href="edit-term.php?id=${t.id}"
          class="flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-violet-300 px-4 py-2 rounded-lg border border-white/8 hover:border-violet-500/40 hover:bg-violet-500/5 transition">
          ✏️ Edit
        </a>
        <button onclick="deleteTerm('${t.id}')"
          class="flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-red-400 px-4 py-2 rounded-lg border border-white/8 hover:border-red-500/40 hover:bg-red-500/5 transition">
          🗑️ Delete
        </button>
      </div>
    </div>`;
  }).join('');
}

async function deleteTerm(id) {
  showConfirm('Delete Record', 'Are you sure? This action cannot be undone.', async () => {
    try {
      showLoading();
      await db.collection('termMarks').doc(id).delete();
      myTerms = myTerms.filter(t => t.id !== id);
      updateStats();
      renderTerms();
      showToast('Record deleted', 'info');
    } catch(err) {
      showToast(err.message, 'error');
    } finally {
      hideLoading();
    }
  });
}
</script>
