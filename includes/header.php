<?php
/**
 * includes/header.php
 * Call at the top of every PHP page.
 * Expects: $pageTitle (string), $depth (int – how many folders deep, default 1)
 */
$pageTitle = $pageTitle ?? 'Saveen Web Portal';
$depth     = $depth ?? 1;
$root      = str_repeat('../', $depth);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Saveen Web Portal – Secure multi-level access management system">
  <title><?= htmlspecialchars($pageTitle) ?> | Saveen Web Portal</title>
  <link rel="icon" href="<?= $root ?>assets/images/favicon.svg" type="image/svg+xml">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Tailwind CSS Play CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary:  '#7c3aed',
            accent:   '#3b82f6',
            surface:  '#0d0d24',
            dark:     '#07071a',
          },
          fontFamily: {
            sans: ['Outfit', 'system-ui', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace'],
          },
        }
      }
    }
  </script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= $root ?>assets/css/custom.css">

  <!-- Firebase v9 Compat SDK -->
  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-storage-compat.js"></script>

  <!-- App JS files (must be in this order) -->
  <script src="<?= $root ?>assets/js/firebase-config.js"></script>
  <script src="<?= $root ?>assets/js/ui.js"></script>
  <script src="<?= $root ?>assets/js/permissions.js"></script>
  <script src="<?= $root ?>assets/js/auth.js"></script>

  <!-- Google Translate Script -->
  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,si',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
      }, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

  <style>
    /* Prevent google translate from breaking layout */
    .goog-te-banner-frame { display: none !important; }
    body { top: 0px !important; }
    .goog-logo-link { display:none !important; }
    .goog-te-gadget { color: transparent !important; }
    #google_translate_element select {
      background: rgba(255,255,255,0.05);
      color: #cbd5e1;
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 8px;
      padding: 4px 8px;
      font-size: 12px;
      outline: none;
    }
    #google_translate_element select option { background: #07071a; color: white; }
    /* Zoom transitions */
    body { transition: transform 0.2s ease-out; transform-origin: top center; }
  </style>
</head>
<body class="bg-dark min-h-screen text-slate-100 overflow-x-hidden" id="appBody">

<!-- Loading Overlay -->
<div id="loadingOverlay" class="hidden">
  <div class="text-center">
    <div class="loader mx-auto mb-3"></div>
    <p class="text-slate-400 text-sm">Loading...</p>
  </div>
</div>
