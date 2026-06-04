<?php
// ══════════════════════════════════════════════════════════════
// Site Configuration for cPanel Hosting
// IMPORTANT: Edit DB_HOST / DB_NAME / DB_USER / DB_PASS / SITE_URL below.
// ══════════════════════════════════════════════════════════════

// ── Auto-load local dev config (optional) ────────────────
// Place a local-only `dev-config.php` next to this file to override
// DB credentials (and other settings) during development. This file
// is NOT tracked by default and avoids committing secrets to git.
if (file_exists(__DIR__ . '/dev-config.php')) {
    require_once __DIR__ . '/dev-config.php';
}

// ── Database (fill in your cPanel MySQL details) ──────────────
// DB_PASS must be set in dev-config.php (local) or directly here for production.
// Never commit your real password to git.
if (!defined('DB_HOST'))    define('DB_HOST',    'localhost');
if (!defined('DB_NAME'))    define('DB_NAME',    'your_database_name');
if (!defined('DB_USER'))    define('DB_USER',    'your_database_user');
if (!defined('DB_PASS'))    define('DB_PASS',    '');          // <-- set your real password here on the server
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// ── Site URL (no trailing slash) ──────────────────────────────
// Root install:      define('SITE_URL', 'https://yourdomain.com');
// Subfolder install: define('SITE_URL', 'https://yourdomain.com/sahakari');
if (!defined('SITE_URL')) define('SITE_URL', 'https://example.com');

// ── Site Identity ─────────────────────────────────────────────
define('SITE_NAME', 'Company');

// ── File Uploads ──────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', SITE_URL . '/uploads/');

// ── Session Security ──────────────────────────────────────────
if (!defined('SESSION_SECRET'))
    define('SESSION_SECRET', '59ea8e8e7e61f2cdba8efe416546a540c8605ea0a5d318c3c7cac2602f12f48d');

// ── PHP Settings ──────────────────────────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
date_default_timezone_set('Asia/Kathmandu');

// Start session early (lang.php needs it)
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
             || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
             || ($_SERVER['SERVER_PORT'] ?? 80) == 443;
    session_set_cookie_params([
        'lifetime' => 86400 * 7,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $isHttps,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Auto-load language helpers so isNepali(), __() and cms() work everywhere
// (must come after session_start so lang detection can read $_SESSION['lang'])
require_once __DIR__ . '/lang.php';
