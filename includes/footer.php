<?php
/**
 * includes/footer.php
 * Closes body and html. Include at the bottom of every PHP page.
 */
?>
<!-- Accessibility Floating Menu -->
<div class="fixed bottom-4 left-4 z-50 flex items-center gap-2 glass px-3 py-2 rounded-full border border-white/10 shadow-xl fade-in" style="animation-delay: 1s;">
  <div id="google_translate_element" class="mr-2"></div>
  <div class="h-4 w-[1px] bg-white/20 mx-1"></div>
  <button onclick="setZoom(0.1)" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center text-slate-300 hover:text-white transition" title="Zoom In">A+</button>
  <button onclick="setZoom(-0.1)" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center text-slate-300 hover:text-white transition" title="Zoom Out">A-</button>
  <button onclick="resetZoom()" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center text-slate-300 hover:text-white transition text-xs" title="Reset Zoom">↺</button>
</div>

<script>
  let currentZoom = 1;
  function setZoom(change) {
    currentZoom += change;
    if (currentZoom < 0.5) currentZoom = 0.5;
    if (currentZoom > 2) currentZoom = 2;
    document.getElementById('appBody').style.transform = `scale(${currentZoom})`;
  }
  function resetZoom() {
    currentZoom = 1;
    document.getElementById('appBody').style.transform = 'scale(1)';
  }
</script>

<!-- Add space at bottom so footer doesn't hide behind floating menu on mobile -->
<div class="h-20 sm:h-10"></div>

<!-- ── Footer ─────────────────────────────────────────────────── -->
<footer class="mt-20 border-t border-white/5">
  <div class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <span class="text-xl">⚡</span>
        <span class="font-bold grad-text">Saveen Portal</span>
      </div>
      <p class="text-slate-600 text-sm">
        &copy; <?= date('Y') ?> Saveen Web Portal. All rights reserved.
      </p>
      <div class="flex items-center gap-1">
        <div class="pulse-dot"></div>
        <span class="text-slate-600 text-xs ml-1">All systems operational</span>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
