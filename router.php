<?php
/**
 * Router for `php -S localhost:8000 router.php` (local dev only).
 * PHP's built-in server never reads .htaccess, so this mirrors the same
 * /name/hexsuffix -> name-hexsuffix.php rewrite that .htaccess does for
 * production/Apache. Not used in production - Apache reads .htaccess
 * directly and never touches this file.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if (preg_match('#^/admin/([a-z][a-z-]*)/([0-9a-f]{12})/?$#', $uri, $m)) {
    $target = __DIR__ . "/admin/{$m[1]}-{$m[2]}.php";
    if (is_file($target)) {
        // Relative require_once('../backend/...') inside admin/*.php
        // resolves against the entry script's directory, not this file's
        // own - since router.php (the entry script here) sits at the
        // project root rather than admin/, those requires would otherwise
        // resolve one level too high. chdir into admin/ first so it
        // matches what happens when the built-in server serves the file
        // directly (no router).
        chdir(dirname($target));
        require $target;
        return true;
    }
}
if (preg_match('#^/([a-z][a-z-]*)/([0-9a-f]{12})/?$#', $uri, $m)) {
    $target = __DIR__ . "/{$m[1]}-{$m[2]}.php";
    if (is_file($target)) { chdir(dirname($target)); require $target; return true; }
}

return false; // fall through to the built-in server's normal static/file handling
