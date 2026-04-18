<?php
/**
 * Quick connection test — delete this file after verifying!
 * Run: php utility/test_connection.php
 */

// Only CLI
if (php_sapi_name() !== 'cli') {
    // Browser fallback — block in production
    if (getenv('APP_ENV') === 'production') { http_response_code(404); die(); }
}

// Load .env
$env_file = __DIR__ . '/../.env';
foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    putenv(trim($k) . '=' . trim($v));
}

$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

echo "\n=== HTextile — Supabase Connection Test ===\n";
echo "Host     : $host\n";
echo "Port     : $port\n";
echo "Database : $db\n";
echo "User     : $user\n";
echo "Password : " . str_repeat('*', strlen($pass)) . "\n\n";

// Test 1: Direct connection
echo "Test 1: Direct connection (port 5432)...\n";
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "  ✅ Connected!\n";

    // Test 2: Query
    echo "Test 2: Reading tables...\n";
    $tables = $pdo->query(
        "SELECT table_name FROM information_schema.tables
         WHERE table_schema = 'public' ORDER BY table_name"
    )->fetchAll(PDO::FETCH_COLUMN);

    echo "  ✅ Found " . count($tables) . " tables:\n";
    foreach ($tables as $t) echo "     - $t\n";

    // Test 3: Check txt_login
    echo "\nTest 3: Checking txt_login...\n";
    $count = $pdo->query("SELECT COUNT(*) FROM txt_login")->fetchColumn();
    echo "  ✅ txt_login has $count user(s)\n";

    echo "\n🎉 ALL TESTS PASSED — Your Supabase connection is working!\n";
    echo "   Delete this file now: del utility\\test_connection.php\n\n";

} catch (PDOException $e) {
    echo "  ❌ FAILED: " . $e->getMessage() . "\n\n";
    echo "Trying pooler connection (port 6543)...\n";
    // Try pooler
    $pooler_host = str_replace('db.', 'aws-0-ap-south-1.pooler.supabase.com', $host);
    try {
        $dsn2 = "pgsql:host=$pooler_host;port=6543;dbname=$db;sslmode=require";
        $pdo2 = new PDO($dsn2, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "  ✅ Pooler connection works!\n";
        echo "  👉 Update your .env: DB_HOST=$pooler_host and DB_PORT=6543\n\n";
    } catch (PDOException $e2) {
        echo "  ❌ Pooler also failed: " . $e2->getMessage() . "\n";
        echo "  Check credentials and try resetting DB password in Supabase.\n\n";
    }
}
