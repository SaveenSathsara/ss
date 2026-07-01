<?php
$pageTitle  = 'Add Term Record';
$activePage = 'terms';
$depth      = 1;
include '../includes/header.php';
?>

<style>
  .subject-chip {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1.5px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.03);
    cursor: pointer;
    transition: all .15s;
    user-select: none;
  }
  .subject-chip:hover { border-color: rgba(124,58,237,.4); background: rgba(124,58,237,.06); }
  .subject-chip.selected { border-color: rgba(124,58,237,.6); background: rgba(124,58,237,.12); }
  .subject-chip input[type=checkbox] { accent-color: #7c3aed; width:16px; height:16px; }
  .mark-row { display:flex; align-items:center; gap:12px; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.04); }
  .mark-row:last-child { border-bottom: none; }
  .mark-badge { display:inline-flex; align-items:center; justify-content:center;
    min-width:48px; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700;
    background:rgba(255,255,255,.06); color:#94a3b8; }
  .mark-badge.red   { background:rgba(239,68,68,.15);   color:#f87171; }
  .mark-badge.amber { background:rgba(251,191,36,.12);  color:#fbbf24; }
  .mark-badge.green { background:rgba(52,211,153,.12);  color:#34d399; }
</style>

<main class="w-full max-w-4xl mx-auto px-6 py-8 min-h-[70vh]">

  <div class="mb-8 fade-in">
    <a href="my-terms.php" class="text-xs text-slate-500 hover:text-white transition uppercase font-bold tracking-wider mb-2 inline-block">← Back to Term Marks</a>
    <h1 class="text-3xl font-black text-white">Add <span class="grad-text">Term Record</span></h1>
    <p class="text-slate-500 text-sm mt-1">Enter your academic details and subject marks.</p>
  </div>

  <div id="loadingBox" class="glass rounded-2xl p-12 text-center border border-white/8">
    <div class="loader mx-auto mb-3"></div>
    <p class="text-slate-500 text-sm">Loading form...</p>
  </div>

  <form id="termForm" class="hidden space-y-6 fade-in" onsubmit="event.preventDefault(); saveTerm();">

    <!-- ① Basic Information -->
    <div class="glass rounded-2xl p-6 border border-white/8">
      <h2 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center text-xs font-black">1</span>
        Basic Information
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-full">
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Student Profile (School)</label>
          <select id="profileId" class="input-field bg-dark" required>
            <option value="">Select a profile...</option>
          </select>
          <p class="text-[10px] text-slate-500 mt-1">No profile? <a href="student-profiles.php" class="text-violet-400 hover:underline">Create one first</a>.</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Year</label>
          <input id="year" type="number" class="input-field" placeholder="e.g. 2026" required min="1900" max="2100" value="<?= date('Y') ?>">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Term</label>
          <select id="term" class="input-field bg-dark" required>
            <option value="1">Term 1</option>
            <option value="2">Term 2</option>
            <option value="3">Term 3</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Grade</label>
          <input id="grade" type="number" class="input-field" placeholder="e.g. 10" required min="1" max="13">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Class</label>
          <input id="className" type="text" class="input-field" placeholder="e.g. A" required>
        </div>
      </div>
    </div>

    <!-- ② Class Statistics -->
    <div class="glass rounded-2xl p-6 border border-white/8">
      <h2 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-xs font-black">2</span>
        Class Statistics <span class="text-slate-500 text-sm font-normal ml-1">(Optional)</span>
      </h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Rank in Class</label>
          <input id="rankInClass" type="number" class="input-field" placeholder="e.g. 1" min="1">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Rank in Grade</label>
          <input id="rankInGrade" type="number" class="input-field" placeholder="e.g. 5" min="1">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">No. of Students</label>
          <input id="studentCount" type="number" class="input-field" placeholder="e.g. 35" min="1">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Class Average</label>
          <input id="classAvg" type="number" class="input-field" placeholder="e.g. 72.5" min="0" max="100" step="0.01">
        </div>
      </div>
    </div>

    <!-- ③ Attendance -->
    <div class="glass rounded-2xl p-6 border border-white/8">
      <h2 class="text-lg font-bold text-white mb-5 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">3</span>
        Attendance <span class="text-slate-500 text-sm font-normal ml-1">(Optional)</span>
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Total School Days</label>
          <input id="totalDays" type="number" class="input-field" placeholder="e.g. 60" min="1" oninput="calcAttendance()">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Days Absent</label>
          <input id="absentDays" type="number" class="input-field" placeholder="e.g. 3" min="0" oninput="calcAttendance()">
        </div>
      </div>
      <div id="attendancePreview" class="hidden mt-4 flex gap-3 flex-wrap"></div>
    </div>

    <!-- ④ Select Subjects -->
    <div class="glass rounded-2xl p-6 border border-white/8">
      <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
          <span class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs font-black">4</span>
          Select Your Subjects
        </h2>
        <div class="flex gap-2">
          <button type="button" onclick="selectAll()" class="text-[11px] text-violet-400 hover:text-violet-300 px-3 py-1 rounded-lg border border-violet-500/30 hover:border-violet-500/60 transition">Select All</button>
          <button type="button" onclick="clearAll()" class="text-[11px] text-slate-500 hover:text-slate-300 px-3 py-1 rounded-lg border border-white/10 hover:border-white/30 transition">Clear</button>
        </div>
      </div>
      <p class="text-[11px] text-slate-500 mb-4">Check the subjects you are doing this term. Leave unchecked to exclude from record.</p>
      <div id="subjectChipsWrapper" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <!-- Populated by JS -->
      </div>
    </div>

    <!-- ⑤ Enter Marks (dynamic, shown after subjects selected) -->
    <div id="marksSection" class="hidden glass rounded-2xl p-6 border border-white/8">
      <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs font-black">5</span>
        Enter Marks
      </h2>
      <p class="text-[11px] text-slate-500 mb-5">Enter marks for each selected subject. Enter <span class="text-white font-bold">0</span> for zero marks.</p>
      <div id="marksWrapper" class="space-y-5"></div>

      <!-- Running Total -->
      <div id="totalPreview" class="mt-5 pt-4 border-t border-white/8 flex flex-wrap gap-5 items-center">
        <div>
          <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Total</p>
          <p class="text-2xl font-black text-white" id="runningTotal">0</p>
        </div>
        <div>
          <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">Subjects</p>
          <p class="text-2xl font-black text-violet-400" id="runningCount">0</p>
        </div>
        <div>
          <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">My Average</p>
          <p class="text-2xl font-black text-emerald-400" id="runningAvg">—</p>
        </div>
      </div>
    </div>

    <!-- ⑥ Save -->
    <div>
      <button type="submit" id="submitBtn"
        class="btn-primary w-full py-4 text-lg justify-center rounded-xl shadow-[0_0_25px_rgba(124,58,237,0.3)] hover:shadow-[0_0_35px_rgba(124,58,237,0.5)] transition-shadow">
        💾 Save Term Record
      </button>
    </div>

  </form>
</main>

<?php include '../includes/footer.php'; ?>

<script>
let myProfiles       = [];
let allSchools       = [];
let subjectTemplates = [];

auth.onAuthStateChanged(user => {
  if (!user) { window.location.href = '../login.html'; return; }
  loadFormData(user.uid);
});

async function loadFormData(uid) {
  try {
    const [profilesSnap, schoolsSnap, subjectsSnap] = await Promise.all([
      db.collection('studentProfiles').where('uid', '==', uid).get(),
      db.collection('schools').get(),
      db.collection('subjectTemplates').get()
    ]);

    allSchools       = schoolsSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    myProfiles       = profilesSnap.docs.map(d => ({ id: d.id, ...d.data() }));
    subjectTemplates = subjectsSnap.docs.map(d => ({ id: d.id, ...d.data() }))
                       .sort((a, b) => a.name.localeCompare(b.name));

    populateProfiles();
    renderSubjectChips();

    document.getElementById('loadingBox').classList.add('hidden');
    document.getElementById('termForm').classList.remove('hidden');
  } catch(err) {
    document.getElementById('loadingBox').innerHTML =
      `<p class="text-red-400 text-sm">Failed to load: ${err.message}</p>`;
  }
}

function populateProfiles() {
  const sel = document.getElementById('profileId');
  if (myProfiles.length === 0) {
    sel.innerHTML = '<option value="">No profiles – please create one first.</option>';
    return;
  }
  sel.innerHTML = '<option value="">Select a profile...</option>' + myProfiles.map(p => {
    const school = allSchools.find(s => s.id === p.schoolId);
    return `<option value="${p.id}">${p.fullName} — ${school ? school.name : 'Unknown'} (${p.entranceNumber})</option>`;
  }).join('');
}

// ── Subject Selection Chips ───────────────────────────────
function renderSubjectChips() {
  const wrap = document.getElementById('subjectChipsWrapper');
  if (subjectTemplates.length === 0) {
    wrap.innerHTML = '<p class="text-slate-500 text-sm col-span-full">No subjects configured. Ask an Admin to add subjects first.</p>';
    return;
  }
  wrap.innerHTML = subjectTemplates.map(sub => `
    <label class="subject-chip" onclick="onChipClick(event, '${sub.id}')">
      <input type="checkbox" id="chip_${sub.id}" value="${sub.id}" onchange="onSubjectToggle()">
      <span class="flex-1 text-sm font-medium text-slate-300">${sub.name}</span>
      ${sub.isBasket ? '<span class="text-[10px] text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full font-semibold">Basket</span>' : ''}
      ${sub.hasSubSubjects ? '<span class="text-[10px] text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full font-semibold">Multi-part</span>' : ''}
    </label>
  `).join('');
}

function onChipClick(e, subId) {
  // prevent double-toggle when clicking the actual checkbox
  if (e.target.type === 'checkbox') return;
  const cb = document.getElementById('chip_' + subId);
  cb.checked = !cb.checked;
  const label = cb.closest('label');
  label.classList.toggle('selected', cb.checked);
  onSubjectToggle();
}

function onSubjectToggle() {
  // sync chip selected class
  subjectTemplates.forEach(sub => {
    const cb = document.getElementById('chip_' + sub.id);
    if (cb) cb.closest('label').classList.toggle('selected', cb.checked);
  });

  const selected = subjectTemplates.filter(sub => {
    const cb = document.getElementById('chip_' + sub.id);
    return cb && cb.checked;
  });
  buildMarksSection(selected);
}

function selectAll() {
  subjectTemplates.forEach(sub => {
    const cb = document.getElementById('chip_' + sub.id);
    if (cb) { cb.checked = true; cb.closest('label').classList.add('selected'); }
  });
  buildMarksSection(subjectTemplates);
}

function clearAll() {
  subjectTemplates.forEach(sub => {
    const cb = document.getElementById('chip_' + sub.id);
    if (cb) { cb.checked = false; cb.closest('label').classList.remove('selected'); }
  });
  document.getElementById('marksSection').classList.add('hidden');
  document.getElementById('marksWrapper').innerHTML = '';
}

// ── Marks Input Section ───────────────────────────────────
function buildMarksSection(selectedSubs) {
  const section = document.getElementById('marksSection');
  const wrapper = document.getElementById('marksWrapper');

  if (selectedSubs.length === 0) {
    section.classList.add('hidden');
    wrapper.innerHTML = '';
    return;
  }

  section.classList.remove('hidden');

  wrapper.innerHTML = selectedSubs.map(sub => {
    let html = `<div class="subject-card p-4 bg-black/20 rounded-xl border border-white/5" data-sub-id="${sub.id}">
      <p class="font-bold text-slate-200 text-sm mb-3">${sub.name}</p>`;

    // Basket dropdown
    if (sub.isBasket) {
      const opts = (sub.basketOptions || []);
      html += `<div class="mb-3">
        <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Basket Option</label>
        <select class="input-field bg-dark py-1.5 text-sm basket-select" data-sub-name="${sub.name}">
          <option value="">— Choose —</option>
          ${opts.map(o => `<option value="${o}">${o}</option>`).join('')}
        </select>
      </div>`;
    }

    // Mark inputs
    html += `<div class="space-y-1">`;
    if (sub.hasSubSubjects && sub.subSubjects && sub.subSubjects.length > 0) {
      sub.subSubjects.forEach(ss => {
        html += markRow(sub.name, sub.isBasket, ss);
      });
    } else {
      html += markRow(sub.name, sub.isBasket, null);
    }
    html += `</div></div>`;
    return html;
  }).join('');

  updateRunningTotal();
}

function markRow(subName, isBasket, subSubject) {
  const label = subSubject || 'Overall';
  return `<div class="mark-row">
    <label class="w-1/3 text-xs text-slate-400 shrink-0">${label}</label>
    <input type="number"
      class="input-field py-1.5 text-sm flex-1 mark-input"
      data-sub-name="${subName}"
      data-is-basket="${isBasket}"
      ${subSubject ? `data-sub-subject="${subSubject}"` : ''}
      placeholder="Enter marks (0–100)"
      min="0" max="100"
      oninput="onMarkInput(this)">
    <span class="mark-badge" id="badge_${subName}_${subSubject||'overall'}">—</span>
  </div>`;
}

function onMarkInput(inp) {
  const val = inp.value.trim();
  const subName    = inp.getAttribute('data-sub-name');
  const subSubject = inp.getAttribute('data-sub-subject') || 'overall';
  const badge      = document.getElementById(`badge_${subName}_${subSubject}`);

  if (val === '' || isNaN(parseFloat(val))) {
    if (badge) badge.textContent = '—', badge.className = 'mark-badge';
  } else {
    const n = parseFloat(val);
    if (badge) {
      badge.textContent = n;
      badge.className = 'mark-badge ' + (n < 40 ? 'red' : n < 60 ? 'amber' : 'green');
    }
  }
  updateRunningTotal();
}

function updateRunningTotal() {
  let total = 0, count = 0;
  document.querySelectorAll('.mark-input').forEach(inp => {
    const v = parseFloat(inp.value);
    // 0 is valid!
    if (inp.value.trim() !== '' && !isNaN(v)) { total += v; count++; }
  });
  document.getElementById('runningTotal').textContent = total;
  document.getElementById('runningCount').textContent = count;
  document.getElementById('runningAvg').textContent   = count > 0 ? (total / count).toFixed(1) : '—';
}

// ── Attendance Preview ────────────────────────────────────
function calcAttendance() {
  const total  = parseInt(document.getElementById('totalDays').value) || 0;
  const absent = parseInt(document.getElementById('absentDays').value) || 0;
  const preview = document.getElementById('attendancePreview');
  if (total <= 0) { preview.classList.add('hidden'); return; }
  const present = Math.max(0, total - absent);
  const pct     = ((present / total) * 100).toFixed(1);
  preview.classList.remove('hidden');
  preview.innerHTML = `
    <span class="px-3 py-1.5 rounded-lg bg-blue-500/10 text-blue-400 text-sm font-semibold">📅 ${total} days</span>
    <span class="px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-sm font-semibold">✓ ${present} present</span>
    <span class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 text-sm font-semibold">✗ ${absent} absent</span>
    <span class="px-3 py-1.5 rounded-lg bg-violet-500/10 text-violet-400 text-sm font-semibold">${pct}% attendance</span>`;
}

// ── Save ──────────────────────────────────────────────────
async function saveTerm() {
  const profileId = document.getElementById('profileId').value;
  if (!profileId) { showToast('Please select a student profile.', 'error'); return; }

  const year      = parseInt(document.getElementById('year').value);
  const term      = document.getElementById('term').value;
  const grade     = parseInt(document.getElementById('grade').value);
  const className = document.getElementById('className').value.trim();
  if (!year || !grade || !className) { showToast('Please fill in all basic details.', 'error'); return; }

  const rankInClass  = parseInt(document.getElementById('rankInClass').value)  || null;
  const rankInGrade  = parseInt(document.getElementById('rankInGrade').value)  || null;
  const studentCount = parseInt(document.getElementById('studentCount').value) || null;
  const classAvg     = parseFloat(document.getElementById('classAvg').value)   || null;
  const totalDays    = parseInt(document.getElementById('totalDays').value)    || null;
  const absentDays   = parseInt(document.getElementById('absentDays').value);
  const absentSave   = isNaN(absentDays) ? null : absentDays;

  // Check subjects selected
  const anySelected = subjectTemplates.some(sub => {
    const cb = document.getElementById('chip_' + sub.id);
    return cb && cb.checked;
  });
  if (!anySelected) { showToast('Please select at least one subject.', 'error'); return; }

  // Gather marks — only from visible inputs with a value (including 0)
  const marks = [];
  let hasError = false;

  document.querySelectorAll('.mark-input').forEach(inp => {
    const val = inp.value.trim();
    if (val === '') return; // not entered → skip

    const markNum = parseFloat(val);
    if (markNum < 0 || markNum > 100) { hasError = true; return; }

    const subName    = inp.getAttribute('data-sub-name');
    const isBasket   = inp.getAttribute('data-is-basket') === 'true';
    const subSubject = inp.getAttribute('data-sub-subject') || null;
    let basketOpt    = null;
    if (isBasket) {
      const card = inp.closest('.subject-card');
      const sel  = card ? card.querySelector('.basket-select') : null;
      basketOpt  = sel ? sel.value : null;
    }
    marks.push({ subject: subName, basket: basketOpt, subSubject, marks: markNum });
  });

  if (hasError) { showToast('Marks must be between 0 and 100.', 'error'); return; }
  if (marks.length === 0) { showToast('Please enter marks for at least one subject field.', 'error'); return; }

  const payload = {
    uid: auth.currentUser.uid,
    studentProfileId: profileId,
    year, term, grade, className,
    rankInClass, rankInGrade, studentCount, classAvg,
    totalDays, absentDays: absentSave,
    marks,
    createdAt: firebase.firestore.FieldValue.serverTimestamp()
  };

  try {
    showLoading();
    document.getElementById('submitBtn').disabled = true;
    await db.collection('termMarks').add(payload);
    showToast('Term marks saved!', 'success');
    setTimeout(() => { window.location.href = 'my-terms.php'; }, 900);
  } catch(err) {
    showToast(err.message, 'error');
    document.getElementById('submitBtn').disabled = false;
  } finally {
    hideLoading();
  }
}
</script>
