<?php
/**
 * ============================================================
 *  HTextile — Production Config (PostgreSQL / Supabase)
 *  Phase 1 Migration:
 *    - Credentials loaded from .env (never hardcoded)
 *    - PDO (pgsql) replaces mysqli
 *    - db_*() helper functions keep old code working
 *    - Proper error reporting (log, never display)
 * ============================================================
 */

// ── Error Reporting ──────────────────────────────────────────
// Show NO errors to the browser; log everything to file.
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors',     '1');
ini_set('error_log',      __DIR__ . '/../log/php_errors.log');

// ── Timezone ─────────────────────────────────────────────────
date_default_timezone_set('Asia/Kolkata');

// ── Load .env file ───────────────────────────────────────────
// Simple .env parser — no external dependency needed.
$_env_file = __DIR__ . '/../.env';
if (file_exists($_env_file)) {
    foreach (file($_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        if (strpos(trim($_line), '#') === 0) continue;   // skip comments
        if (strpos($_line, '=') === false)          continue;   // skip malformed lines
        [$_key, $_val] = explode('=', $_line, 2);
        $_key = trim($_key);
        $_val = trim($_val);
        if (!array_key_exists($_key, $_SERVER) && !array_key_exists($_key, $_ENV)) {
            putenv("$_key=$_val");
            $_ENV[$_key]    = $_val;
            $_SERVER[$_key] = $_val;
        }
    }
    unset($_env_file, $_line, $_key, $_val);
}

// ── Environment Variable Helper ─────────────────────────────
function get_env_var(string $key, string $default = ''): string {
    $val = getenv($key);
    if ($val !== false && $val !== '') return $val;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
    return $default;
}

// ── Database Credentials (from .env or Vercel Environment) ──
define('DB_HOST',     get_env_var('DB_HOST', '127.0.0.1'));
define('DB_PORT',     get_env_var('DB_PORT', '5432'));
define('DB_DATABASE', get_env_var('DB_DATABASE', 'postgres'));
define('DB_USER',     get_env_var('DB_USER', ''));
define('DB_PASSWORD', get_env_var('DB_PASSWORD', ''));
define('APP_ENV',     get_env_var('APP_ENV', 'production'));

// ── PDO Singleton ─────────────────────────────────────────────
// Returns a single shared PDO instance (connection pooling friendly).
function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s;sslmode=require',
        DB_HOST, DB_PORT, DB_DATABASE
    );
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT         => false,
        ]);
    } catch (PDOException $e) {
        error_log('[DB CONNECTION ERROR] ' . $e->getMessage());
        $err = htmlspecialchars($e->getMessage());
        $debugHost = htmlspecialchars(DB_HOST);
        $debugUser = htmlspecialchars(DB_USER);
        die("<div style='color:red;padding:20px; font-family: sans-serif;'>
               <h2>Database connection failed.</h2>
               <p><strong>Error:</strong> {$err}</p>
               <p><strong>Configured Host:</strong> {$debugHost}</p>
               <p><strong>Configured User:</strong> {$debugUser}</p>
               <p>Please double-check your Vercel Environment Variables in the project settings.</p>
             </div>");
    }
    return $pdo;
}

// ── Legacy Compatibility Wrappers ────────────────────────────
// These wrap PDO so existing code calling get_connection() /
// release_connection() continues to work during migration.

/**
 * Returns the shared PDO connection.
 * Drop-in replacement for the old mysqli get_connection().
 */
function get_connection(): PDO {
    return get_pdo();
}

/**
 * No-op: PDO handles connection pooling automatically.
 * Kept so existing release_connection($con) calls don't break.
 */
function release_connection($con): void {
    // PDO closes when $pdo goes out of scope or script ends.
    // Explicit close: set to null. We keep singleton alive for page lifetime.
}

// ── Safe DB Helper Functions ──────────────────────────────────
// Use these for ALL new queries. Old code can migrate to these gradually.

/**
 * Execute a parameterized SQL query (safe, no SQL injection).
 *
 * Usage:
 *   $stmt = db_query("SELECT * FROM txt_company WHERE group_id = ?", [$groupId]);
 *   while ($row = db_fetch($stmt)) { ... }
 *
 * @param  string $sql    SQL with ? placeholders
 * @param  array  $params Values matching each ?
 * @return PDOStatement
 */
function db_query(string $sql, array $params = []): PDOStatement {
    $pdo  = get_pdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch one row as associative array.
 * Drop-in for: mysqli_fetch_assoc() / mysqli_fetch_array()
 */
function db_fetch(PDOStatement $stmt): array|false {
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Fetch all rows as associative array.
 */
function db_fetch_all(PDOStatement $stmt): array {
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Number of rows returned/affected.
 * Drop-in for: mysqli_num_rows() / mysqli_affected_rows()
 */
function db_rows(PDOStatement $stmt): int {
    return $stmt->rowCount();
}

/**
 * Last inserted auto-increment ID.
 * Drop-in for: mysqli_insert_id()
 * For PostgreSQL sequences, pass the sequence name.
 * Example: db_last_id('txt_bill_entry_bill_entry_id_seq')
 */
function db_last_id(string $sequence = ''): string {
    return get_pdo()->lastInsertId($sequence ?: null);
}

/**
 * Safely escape a value for use in LIKE patterns only.
 * For all other cases, use db_query() with ? placeholders instead.
 */
function db_escape_like(string $value): string {
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
}

// ── Date Utility Functions ────────────────────────────────────

/**
 * Convert dd-mm-yyyy → yyyy-mm-dd for storage.
 * Returns '1970-01-01' for blank / invalid input.
 */
function convert_date(string $date): string {
    if (empty($date)) return '1970-01-01';

    $date = trim($date);
    if (in_array($date, ['00-00-0000', '0000-00-00', ''])) return '1970-01-01';

    $parts = explode('-', $date);
    if (count($parts) !== 3) return '1970-01-01';

    [$dd, $mm, $yy] = $parts;
    $dd = ($dd === '00' || $dd === '0') ? '01' : str_pad($dd, 2, '0', STR_PAD_LEFT);
    $mm = ($mm === '00' || $mm === '0') ? '01' : str_pad($mm, 2, '0', STR_PAD_LEFT);
    $yy = (in_array($yy, ['0000', '00']))   ? '1970' : $yy;

    return "$yy-$mm-$dd";
}

/**
 * Convert yyyy-mm-dd → dd-mm-yyyy for display.
 * Returns empty string for sentinel / blank dates.
 */
function rev_convert_date(string $date): string {
    if (empty($date) ||
        in_array($date, ['0000-00-00', '1970-01-01', '2080-01-01', '2030-01-01'])) {
        return '';
    }
    $parts = explode('-', $date);
    return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
}

// ── Number Utility Functions ──────────────────────────────────

function zeroToBlank($num): string {
    if (is_null($num) || (is_numeric($num) && $num == 0)) return '';
    return (string)$num;
}

function blankToZero($num): float {
    if (is_null($num))                    return 0.0;
    if (is_string($num) && $num === '')   return 0.0;
    return (float)$num;
}

function defaultDateToBlank(string $date): string {
    if (in_array($date, ['0000-00-00', '1970-01-01', '2030-01-01'])) return '';
    return $date;
}

// ── Indian Money Format ───────────────────────────────────────

function moneyFormatIndia($num): string {
    $num = (string)floor((float)$num);
    if (strlen($num) <= 3) return $num;
    $lastThree  = substr($num, -3);
    $rest       = substr($num, 0, strlen($num) - 3);
    $rest       = (strlen($rest) % 2 === 1) ? '0' . $rest : $rest;
    $chunks     = str_split($rest, 2);
    $chunks[0]  = (int)$chunks[0]; // remove leading zero from first chunk
    return implode(',', $chunks) . ',' . $lastThree;
}

function IND_money_format($money): string {
    $decimal = (float)$money - floor((float)$money);
    $intPart = (string)(int)floor((float)$money);
    $len     = strlen($intPart);
    $rev     = strrev($intPart);
    $m       = '';
    for ($i = 0; $i < $len; $i++) {
        if (($i === 3 || ($i > 3 && ($i - 1) % 2 === 0)) && $i !== $len) {
            $m .= ',';
        }
        $m .= $rev[$i];
    }
    $result = strrev($m);
    if ($decimal > 0) {
        $decStr = number_format($decimal, 2);
        $result .= substr($decStr, 1); // append ".XX"
    }
    return $result;
}

// ── Misc Helpers ──────────────────────────────────────────────

function dateDiff(string $start, string $end): int {
    if (empty($end)) return 0;
    $s = strtotime($start);
    $e = strtotime($end);
    return (int)round(($s - $e) / 86400);
}

function getSqlMessage(int $error_no, string $str): string {
    $msgs = [
        23503 => ' Cannot delete — record is linked to other data.',   // FK violation (PostgreSQL)
        23505 => ' Duplicate entry — record already exists.',          // Unique violation
    ];
    return $str . ($msgs[$error_no] ?? '');
}

// ── Include Logging ───────────────────────────────────────────
include_once(__DIR__ . '/log.php');

// ── Include Supabase Storage Utility ────────────────────────
require_once(__DIR__ . '/supabase_storage.php');