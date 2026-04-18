<?php
/**
 * ============================================================
 *  HTextile — Secure Session Check
 *  Fixes:
 *    - Session cookie now has secure, httponly, samesite flags
 *    - Session ID regenerated on each page to prevent fixation
 * ============================================================
 */

// Configure secure cookie BEFORE session_start()
session_set_cookie_params([
    'lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 3600),
    'path'     => '/',
    'secure'   => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'), // HTTPS only in prod
    'httponly' => true,    // Not accessible via JavaScript — prevents XSS theft
    'samesite' => 'Strict' // CSRF protection
]);

// Use database-backed sessions instead of local file sessions (for Vercel serverless)
require_once(__DIR__ . '/session_db_handler.php');

session_start();

include_once(__DIR__ . '/settings.php');

if (isset($_SESSION['APP'])) {
    if ($_SESSION['APP'] === 'HT') {
        if (isset($_SESSION['LOGID'])) {
            // Valid session — regenerate ID periodically to prevent fixation.
            // Only regenerate every 5 minutes to avoid performance hit.
            if (!isset($_SESSION['_last_regen']) ||
                (time() - $_SESSION['_last_regen']) > 300) {
                session_regenerate_id(true);
                $_SESSION['_last_regen'] = time();
            }
            // Logged in — proceed normally.
        } else {
            // No login ID — redirect to login page
            echo "<script>location.href='" . $web_path . "index.php';</script>";
            exit;
        }
    } else {
        // Wrong application session
        echo "<script>location.href='" . $web_path . "index.php?err=2';</script>";
        exit;
    }
} else {
    // No session at all — redirect to login page
    echo "<script>location.href='" . $web_path . "index.php?err=2';</script>";
    exit;
}