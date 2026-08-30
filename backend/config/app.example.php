<?php
/**
 * Application configuration - TEMPLATE
 * ------------------------------------------------------------
 * Copy this file to `backend/config/app.php` and fill in your own
 * credentials. The real `backend/config/app.php` is git-ignored and
 * must NEVER be committed to a public repository.
 */

// Midtrans Configuration
// Get these from https://dashboard.midtrans.com → Settings → Access Keys
define('MIDTRANS_SERVER_KEY', 'YOUR_MIDTRANS_SERVER_KEY');
define('MIDTRANS_CLIENT_KEY', 'YOUR_MIDTRANS_CLIENT_KEY');
define('MIDTRANS_IS_PRODUCTION', false); // true = production, false = sandbox
define('MIDTRANS_API_BASE', 'https://api.midtrans.com');
define('MIDTRANS_SNAP_BASE', 'https://app.midtrans.com/snap/v1');

// App Configuration
define('APP_URL', 'https://yourdomain.com'); // your deployed domain
define('ADMIN_WA_1', '62XXXXXXXXXXX');       // admin WhatsApp number 1
define('ADMIN_WA_2', '62XXXXXXXXXXX');       // admin WhatsApp number 2

// Firebase (must match frontend/assets/js/firebase-init.js)
define('FIREBASE_PROJECT_ID', 'your-firebase-project-id');
define('FIREBASE_WEB_API_KEY', 'your-firebase-web-api-key'); // same apiKey as firebase-init.js - public by design, used server-side to verify ID tokens

// Service account for server-side Firestore writes (checkout, deposit crediting,
// voucher redemption). Generate via Firebase Console -> Project Settings ->
// Service Accounts -> Generate new private key, save the JSON here. This is far
// more sensitive than the keys above (full read/write on Firestore) - never commit it.
define('FIREBASE_SERVICE_ACCOUNT_PATH', __DIR__ . '/firebase-service-account.json');

// Anthropic (Claude) API - AI live-chat auto-reply
// Get a key from https://console.anthropic.com/settings/keys
define('ANTHROPIC_API_KEY', 'YOUR_ANTHROPIC_API_KEY');
define('ANTHROPIC_MODEL', 'claude-haiku-4-5');

// Upload config
define('UPLOAD_DIR', __DIR__ . '/../../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Temp dir for payment status
define('TEMP_DIR', __DIR__ . '/../../temp/');

// Ensure directories exist
if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
if (!is_dir(TEMP_DIR)) @mkdir(TEMP_DIR, 0755, true);
