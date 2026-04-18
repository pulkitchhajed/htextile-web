<?php
/**
 * ============================================================
 *  HTextile — CLI Password Hash Generator
 *  Run from command line (no web server needed):
 *
 *    php utility/generate_hashes_cli.php
 *
 *  It will:
 *    1. Connect to your database using .env credentials
 *    2. Print UPDATE SQL statements for all plaintext passwords
 *    3. You copy and run those statements in Supabase SQL Editor
 * ============================================================
 */

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    die("Run this script from the command line only:\n  php utility/generate_hashes_cli.php\n");
}

echo "\n";
echo "============================================================\n";
echo "  HTextile — Password Hash Generator (CLI)\n";
echo "============================================================\n\n";

// Load .env manually for CLI context
$env_file = __DIR__ . '/../.env';
if (!file_exists($env_file)) {
    echo "❌ ERROR: .env file not found at: $env_file\n";
    echo "   Create it first by copying .env.example → .env\n";
    exit(1);
}

foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
    [$key, $val] = explode('=', $line, 2);
    putenv(trim($key) . '=' . trim($val));
}

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

if (empty($host) || empty($db) || empty($user)) {
    echo "❌ ERROR: DB_HOST, DB_DATABASE, or DB_USER is missing in .env\n";
    exit(1);
}

// Connect
echo "🔌 Connecting to: $host:$port/$db as $user ...\n";
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connected successfully!\n\n";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "\nTip: If connection times out, try using the Supabase Pooler host (port 6543).\n";
    exit(1);
}

// Fetch all users
$stmt = $pdo->query("SELECT login_id, login_name, login_password FROM txt_login ORDER BY login_id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "⚠️  No users found in txt_login table.\n";
    exit(0);
}

echo "Found " . count($users) . " user(s).\n\n";
echo "------------------------------------------------------------\n";
echo "  SQL TO RUN IN SUPABASE SQL EDITOR\n";
echo "  Copy everything between the === lines\n";
echo "------------------------------------------------------------\n";
echo "===== START COPY ===========================================\n\n";

$needsMigration = 0;
foreach ($users as $u) {
    $isHashed = str_starts_with($u['login_password'], '$2y$');
    if ($isHashed) {
        echo "-- ✅ SKIP: login_id={$u['login_id']} ({$u['login_name']}) — already bcrypt\n";
    } else {
        $hash = password_hash($u['login_password'], PASSWORD_BCRYPT);
        echo "UPDATE txt_login SET login_password = '$hash' WHERE login_id = {$u['login_id']}; -- {$u['login_name']}\n";
        $needsMigration++;
    }
}

echo "\n===== END COPY =============================================\n\n";

if ($needsMigration === 0) {
    echo "🎉 All passwords are already hashed. No migration needed!\n";
} else {
    echo "⚠️  $needsMigration password(s) need migration.\n";
    echo "   Steps:\n";
    echo "   1. Copy the UPDATE statements above (between === lines)\n";
    echo "   2. Open Supabase Dashboard → SQL Editor → New Query\n";
    echo "   3. Paste and click Run\n";
    echo "   4. Delete this file after use:\n";
    echo "      del utility\\generate_hashes_cli.php\n\n";
}

echo "Done.\n";
