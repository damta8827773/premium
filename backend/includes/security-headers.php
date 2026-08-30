<?php
// Mirrors the header set in .htaccess (Header always set ...) - .htaccess
// only applies under Apache, so this copy is what actually takes effect
// when running under `php -S` for local development/testing. Apache's
// "Header always set" replaces rather than duplicates a header PHP already
// sent, so having both is safe in production. Included at the very top of
// every page (before any output) - both the ones using head.php and the
// standalone ones (index/login/register/reset-password) that don't.
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(self)');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://www.gstatic.com https://app.midtrans.com https://app.sandbox.midtrans.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https://*.googleapis.com https://*.firebaseio.com https://*.firebaseapp.com https://firebasestorage.googleapis.com https://api.midtrans.com https://app.midtrans.com https://app.sandbox.midtrans.com https://api.anthropic.com; frame-src https://app.midtrans.com https://app.sandbox.midtrans.com https://accounts.google.com; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
