<?php
$pageTitle  = 'Registration Requests';
$activePage = 'requests';
$depth      = 1;
include '../includes/header.php';
?>

<div class="flex min-h-screen">
  <?php include 'sidebar.php'; ?>

  <main class="flex-1 p-6 lg:p-8 overflow-x-hidden">

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 fade-in">
      <div>
        <h1 class="text-3xl font-black text-white">Registration <span class="grad-text">Requests</span></h1>
        <p class="text-slate-500 text-sm mt-1">Review and manage new user registration requests</p>
      </div>
      <!-- Filter tabs -->
      <div class="flex glass rounded-xl p-1 border border-white/8 text-sm">
        <button onclick="filterRequests('all')"     id="f-all"      class="px-4 py-2 rounded-lg font-medium transition filter-tab active-tab">All</button>
        <button onclick="filterRequests('pending')" id="f-pending"  class="px-4 py-2 rounded-lg font-medium transition filter-tab text-slate-500">Pending</button>
        <button onclick="filterRequests('accepted')"id="f-accepted" class="px-4 py-2 rounded-lg font-medium transition filter-tab text-slate-500">Accepted</button>
        <button onclick="filterRequests('declined')"id="f-declined" class="px-4 py-2 rounded-lg font-medium transition filter-tab text-slate-500">Declined</button>
      </div>
    </div>

    <!-- Requests list -->
    <div id="requestsContainer" class="space-y-4">
      <div class="glass rounded-2xl p-10 text-center border border-white/8">
        <div class="loader mx-auto mb-3"></div>
        <p class="text-slate-500 text-sm">Loading requests...</p>
      </div>
    </div>

  </main>
</div>

<!-- Accept Modal -->
<div id="acceptModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-2">Accept Request</h3>
    <p class="text-slate-400 text-sm mb-5">Select a user level for <strong id="acceptUserName" class="text-white"></strong></p>
    <select id="acceptUserLevel" class="input-field mb-5">
      <!-- Populated by JS -->
    </select>
    <div class="flex gap-3">
      <button onclick="closeModal('acceptModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="confirmAccept()" class="btn-success flex-1 py-2.5 text-sm">Accept User</button>
    </div>
  </div>
</div>

<!-- Decline Modal -->
<div id="declineModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="modal-box glass-strong rounded-2xl p-7 max-w-md w-full border border-white/10 shadow-2xl transition-all duration-200 scale-95 opacity-0">
    <h3 class="text-xl font-bold text-white mb-2">Decline Request</h3>
    <p class="text-slate-400 text-sm mb-5">Optionally provide a reason for declining. The request will be permanently declined.</p>
    <input id="declineReason" type="text" class="input-field mb-5" placeholder="Reason (optional)">
    <div class="flex gap-3">
      <button onclick="closeModal('declineModal')" class="btn-ghost flex-1 py-2.5 text-sm">Cancel</button>
      <button onclick="confirmDecline()" class="btn-danger flex-1 py-2.5 text-sm">Decline Request</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>

<style>
.active-tab { background:rgba(124,58,237,.2); color:#c4b5fd; }
</style>

<script>
requireAdmin();

let allRequests = [];
let allLevels = [];
let currentFilter = 'all';
let pendingDeclineId = null;
let pendingAcceptId = null;

// Load user levels for accept modal
db.collection('userLevels').orderBy('level').get().then(snap => {
  allLevels = snap.docs.map(d => ({ id: d.id, ...d.data() }));
});

// Real-time listener
db.collection('registrationRequests')
  .orderBy('requestedAt','desc')
  .onSnapshot(snap => {
    allRequests = snap.docs.map(d => ({ id: d.id, ...d.data() }));
    renderRequests(currentFilter);
  });

function filterRequests(filter) {
  currentFilter = filter;
  document.querySelectorAll('.filter-tab').forEach(b => {
    b.classList.remove('active-tab');
    b.classList.add('text-slate-500');
  });
  const active = document.getElementById('f-' + filter);
  if (active) { active.classList.add('active-tab'); active.classList.remove('text-slate-500'); }
  renderRequests(filter);
}

function renderRequests(filter) {
  const container = document.getElementById('requestsContainer');
  const filtered = filter === 'all' ? allRequests : allRequests.filter(r => r.status === filter);

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="glass rounded-2xl p-12 text-center border border-white/8">
        <p class="text-5xl mb-3">📭</p>
        <p class="text-white font-semibold mb-1">No ${filter === 'all' ? '' : filter} requests</p>
        <p class="text-slate-500 text-sm">Nothing to show here.</p>
      </div>`;
    return;
  }

  container.innerHTML = filtered.map(r => {
    const s = {
      pending:  { cls:'bg-amber-500/20 text-amber-400 border-amber-500/30',  label:'Pending', icon:'⏳' },
      accepted: { cls:'bg-green-500/20 text-green-400 border-green-500/30',   label:'Accepted',icon:'✅' },
      declined: { cls:'bg-red-500/20 text-red-400 border-red-500/30',         label:'Declined',icon:'❌' },
    }[r.status] || { cls:'bg-slate-500/20 text-slate-400 border-slate-500/30', label:r.status, icon:'?' };

    const age = timeAgo(r.requestedAt);

    return `
    <div class="glass rounded-2xl border border-white/8 overflow-hidden hover:border-violet-500/20 transition-all fade-in">
      <div class="p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">

          <!-- Avatar -->
          <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-lg font-bold text-white shrink-0">
            ${r.fullName?.[0]?.toUpperCase() || '?'}
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
              <h3 class="text-lg font-bold text-white">${r.fullName}</h3>
              <span class="badge ${s.cls}">${s.icon} ${s.label}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-400">
              <span>👤 @${r.username}</span>
              <span>📧 ${r.email}</span>
              <span>🎂 ${r.birthday || '—'}</span>
              <span>🕐 ${age}</span>
            </div>
          </div>

          <!-- Actions -->
          ${r.status === 'pending' ? `
          <div class="flex gap-2 shrink-0">
            <button onclick="handleAccept('${r.id}','${r.fullName}')"
                    class="btn-success py-2 px-4 text-sm rounded-xl">
              ✅ Accept
            </button>
            <button onclick="handleDecline('${r.id}')"
                    class="btn-danger py-2 px-4 text-sm rounded-xl">
              ❌ Decline
            </button>
          </div>` : ''}

          ${r.status === 'accepted' ? `<div class="text-green-400 text-sm font-medium shrink-0">✅ Accepted<br><span class="text-[11px] text-slate-600">${timeAgo(r.acceptedAt)}</span></div>` : ''}
          ${r.status === 'declined' ? `<div class="text-red-400 text-sm font-medium shrink-0">❌ Declined<br><span class="text-[11px] text-slate-600">${timeAgo(r.declinedAt)}</span></div>` : ''}
        </div>
        ${r.reason ? `<div class="mt-3 glass rounded-lg px-4 py-2 text-xs text-slate-400 border border-white/5"><strong class="text-slate-300">Decline reason:</strong> ${r.reason}</div>` : ''}
      </div>
    </div>`;
  }).join('');
}

function handleAccept(id, fullName) {
  pendingAcceptId = id;
  document.getElementById('acceptUserName').textContent = fullName;
  
  const sel = document.getElementById('acceptUserLevel');
  sel.innerHTML = allLevels.map(l => 
    `<option value="${l.id}" ${l.id === 'normal_user' ? 'selected' : ''}>${l.displayName || l.name}</option>`
  ).join('');
  
  openModal('acceptModal');
}

async function confirmAccept() {
  if (!pendingAcceptId) return;
  const level = document.getElementById('acceptUserLevel').value;
  await acceptRequest(pendingAcceptId, level);
  closeModal('acceptModal');
  pendingAcceptId = null;
}

function handleDecline(id) {
  pendingDeclineId = id;
  document.getElementById('declineReason').value = '';
  openModal('declineModal');
}

async function confirmDecline() {
  if (!pendingDeclineId) return;
  const reason = document.getElementById('declineReason').value.trim();
  await declineRequest(pendingDeclineId, reason);
  closeModal('declineModal');
  pendingDeclineId = null;
}
</script>
