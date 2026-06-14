// Firebase Configuration - TEMPLATE
// ------------------------------------------------------------
// Copy this file to `frontend/assets/js/firebase-init.js` and fill in
// your own Firebase web config. The real file is git-ignored.
// Get the config from: Firebase Console → Project Settings →
// "Your apps" → Web app → SDK setup and configuration.
const firebaseConfig = {
  apiKey: "YOUR_FIREBASE_API_KEY",
  authDomain: "YOUR_PROJECT.firebaseapp.com",
  projectId: "YOUR_PROJECT_ID",
  storageBucket: "YOUR_PROJECT.firebasestorage.app",
  messagingSenderId: "YOUR_SENDER_ID",
  appId: "YOUR_APP_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

const auth = firebase.auth();
const db = firebase.firestore();
const storage = typeof firebase.storage === 'function' ? firebase.storage() : null;

// Google Auth Provider
const googleProvider = new firebase.auth.GoogleAuthProvider();
googleProvider.setCustomParameters({ prompt: 'select_account' });

// Midtrans client key (safe to expose on the frontend)
const MIDTRANS_CLIENT_KEY = 'YOUR_MIDTRANS_CLIENT_KEY';
