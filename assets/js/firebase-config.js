// ============================================================
//  FIREBASE CONFIGURATION
//  ⚠️  Replace the values below with YOUR Firebase project credentials
//  Steps: Firebase Console → Project Settings → General → Your Apps → SDK Setup
// ============================================================

const firebaseConfig = {
  apiKey:            "AIzaSyC7POm00kMuEbTXFqVsgyYAuSEcboVrMOc",
  authDomain:        "saveen-sathsara.firebaseapp.com",
  projectId:         "saveen-sathsara",
  storageBucket:     "saveen-sathsara.firebasestorage.app",
  messagingSenderId: "331873514643",
  appId:             "1:331873514643:web:18bbeb733ac77bbf7e0966",
  measurementId:     "G-G850JC8B7X"
};

// Initialize primary Firebase app
firebase.initializeApp(firebaseConfig);

// Global references used across the site
const auth = firebase.auth();
const db   = firebase.firestore();

// Expose config globally so auth.js can use the apiKey for REST calls
window.__fbConfig = firebaseConfig;

// ── Enable offline persistence (real-time sync) ─────────────────────────────
db.enablePersistence({ synchronizeTabs: true }).catch(err => {
  if (err.code === 'failed-precondition') {
    console.warn('Multiple tabs open – persistence disabled for this tab.');
  } else if (err.code === 'unimplemented') {
    console.warn('Browser does not support persistence.');
  }
});
