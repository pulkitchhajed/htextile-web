<?php
/**
 * ============================================================
 *  HTextile — Secure Login Handler
 *  Fixes applied:
 *    1. Uses PDO prepared statement (no SQL injection)
 *    2. Uses password_verify() (bcrypt) — no plaintext comparison
 *    3. Secure session: regenerate ID on login (prevents fixation)
 *    4. Active check: only users with active=1 can login
 *    5. Captcha check retained from original
 * ============================================================
 *
 *  ⚠️  ONE-TIME MIGRATION REQUIRED IN DATABASE:
 *  Run this in Supabase SQL Editor to hash existing passwords:
 *
 *    -- Check existing passwords first
 *    SELECT login_id, login_name, login_password FROM txt_login;
 *
 *  Then for each user, run (replace values):
 *    UPDATE txt_login
 *    SET login_password = '$2y$10$...'   -- bcrypt hash from PHP
 *    WHERE login_id = 1;
 *
 *  Generate hashes using the helper script: utility/hash_passwords.php
 * ============================================================
 */

session_set_cookie_params([
    'lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 3600),
    'path'     => '/',
    'secure'   => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
    'httponly' => true,
    'samesite' => 'Strict',
]);

// Use database-backed sessions instead of local file sessions
require_once(__DIR__ . '/../includes/session_db_handler.php');

session_start();

// ── Captcha Check ─────────────────────────────────────────────
if (isset($_POST['captcha'], $_SESSION['captcha'])) {
    if ($_SESSION['captcha'] !== $_POST['captcha']) {
        echo 'Security Characters is not valid; Please fill the form again.<br>';
        echo "<a href='" . htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '../index.php') . "'>Back</a>";
        exit;
    }
} else {
    // Captcha fields missing — possible direct POST attack
    echo "<script>location.href='../index.php?err=400';</script>";
    exit;
}

require_once('../includes/config.php');

$login_name     = trim($_POST['login_name']     ?? '');
$login_password = trim($_POST['login_password'] ?? '');

// Basic validation
if (empty($login_name) || empty($login_password)) {
    echo "<script>location.href='../index.php?err=201';</script>";
    exit;
}

// ── Secure DB Query (prepared statement) ─────────────────────
// Fetch user by name only — password is verified in PHP via bcrypt
$stmt = db_query(
    "SELECT login_id, application, login_name, login_password, login_type,
            user_name, region, email, active
     FROM txt_login
     WHERE login_name = ?
       AND delete_tag = 'FALSE'
     LIMIT 1",
    [$login_name]
);
$user = db_fetch($stmt);

// ── Authentication ────────────────────────────────────────────
$authenticated = false;

if ($user) {
    // Check account is active
    if ((int)$user['active'] !== 1) {
        // Account disabled
        echo "<script>location.href='../index.php?err=203';</script>";
        exit;
    }

    // Verify password (bcrypt)
    if (password_verify($login_password, $user['login_password'])) {
        $authenticated = true;
    }
    // ── TEMPORARY FALLBACK (remove after migration) ───────────
    // If the stored password is NOT a bcrypt hash yet (plain text),
    // compare directly AND immediately upgrade to bcrypt.
    elseif (!str_starts_with($user['login_password'], '$2y$') &&
             $login_password === $user['login_password']) {
        // Auto-upgrade plaintext → bcrypt
        $new_hash = password_hash($login_password, PASSWORD_BCRYPT);
        db_query(
            "UPDATE txt_login SET login_password = ? WHERE login_id = ?",
            [$new_hash, $user['login_id']]
        );
        error_log('[SECURITY] Auto-upgraded password hash for user: ' . $login_name);
        $authenticated = true;
    }
}

// ── Login Failed ──────────────────────────────────────────────
if (!$authenticated) {
    // Log failed attempt (helps detect brute force)
    error_log('[LOGIN FAIL] User: ' . $login_name . ' IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    echo "<script>location.href='../index.php?err=201';</script>";
    exit;
}

// ── Login Success ─────────────────────────────────────────────
// Regenerate session ID to prevent session fixation attack
session_regenerate_id(true);

$_SESSION['APP']        = $user['application'];
$_SESSION['LOGID']      = $user['login_id'];
$_SESSION['ROLEID']     = $user['login_type'];
$_SESSION['LOGIN_NAME'] = $user['login_name'];
$_SESSION['USER_NAME']  = $user['user_name'];
$_SESSION['_last_regen'] = time();

// Clear captcha from session after successful use
unset($_SESSION['captcha']);

// Redirect based on role
if ($user['login_type'] === 'admin' || $user['login_type'] === 'user') {
    echo "<script>location.href='../home/index.php';</script>";
} else {
    // Unknown role — send to home, let routing decide
    echo "<script>location.href='../home/index.php';</script>";
}
exit;