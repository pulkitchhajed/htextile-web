<?php
/**
 * ============================================================
 *  HTextile — Password Hash Migration Utility
 *  Run this ONCE from the browser or CLI to get bcrypt hashes
 *  for your existing plaintext passwords.
 *
 *  STEP 1: Open this page in browser (local/dev environment only)
 *  STEP 2: Copy the generated hashes
 *  STEP 3: Paste the UPDATE statements into Supabase SQL Editor
 *  STEP 4: DELETE this file from the server immediately after use
 * ============================================================
 */

// ── Security Gate: Block in production ───────────────────────
// Remove this block only if running locally
if (getenv('APP_ENV') === 'production') {
    http_response_code(404);
    die('Not Found');
}

require_once('../includes/config.php');

// ── Fetch all users ───────────────────────────────────────────
$stmt = db_query(
    "SELECT login_id, login_name, login_password FROM txt_login ORDER BY login_id"
);
$users = db_fetch_all($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Password Migration — HTextile</title>
<style>
  body { font-family: monospace; background: #1a1a2e; color: #e0e0e0; padding: 30px; }
  h2   { color: #f39c12; }
  table { border-collapse: collapse; width: 100%; }
  th, td { border: 1px solid #444; padding: 10px; text-align: left; }
  th { background: #16213e; color: #f39c12; }
  .hash  { color: #2ecc71; font-size: 11px; word-break: break-all; }
  .plain { color: #e74c3c; }
  .sql   { background: #0d0d0d; padding: 15px; margin-top: 5px;
           border-left: 3px solid #f39c12; overflow-x: auto; }
  .warn { background: #c0392b; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
</style>
</head>
<body>
<div class="warn">
  ⚠️ SECURITY: Delete this file (<code>utility/hash_passwords.php</code>)
  from the server immediately after completing migration!
</div>
<h2>🔐 Password Hash Migration Utility</h2>
<p>Copy the SQL statements below and run them in the <strong>Supabase SQL Editor</strong>.</p>

<h3>SQL Statements to Run in Supabase:</h3>
<div class="sql">
<?php foreach ($users as $user):
    $isAlreadyHashed = str_starts_with($user['login_password'], '$2y$');
    if ($isAlreadyHashed):
?>
-- ✅ User ID <?= (int)$user['login_id'] ?> (<?= htmlspecialchars($user['login_name']) ?>) — already bcrypt hashed. SKIP.
<?php else:
    $bcrypt = password_hash($user['login_password'], PASSWORD_BCRYPT);
?>UPDATE txt_login SET login_password = '<?= $bcrypt ?>' WHERE login_id = <?= (int)$user['login_id'] ?>; -- <?= htmlspecialchars($user['login_name']) ?>

<?php endif; endforeach; ?>
</div>

<h3>User Status Table:</h3>
<table>
  <tr><th>ID</th><th>Login Name</th><th>Current Password</th><th>Status</th><th>New Hash</th></tr>
  <?php foreach ($users as $user):
    $isAlreadyHashed = str_starts_with($user['login_password'], '$2y$');
  ?>
  <tr>
    <td><?= (int)$user['login_id'] ?></td>
    <td><?= htmlspecialchars($user['login_name']) ?></td>
    <td class="<?= $isAlreadyHashed ? 'hash' : 'plain' ?>">
        <?= $isAlreadyHashed ? '[bcrypt hash]' : htmlspecialchars($user['login_password']) ?>
    </td>
    <td><?= $isAlreadyHashed ? '✅ Already Hashed' : '❌ Plaintext — NEEDS MIGRATION' ?></td>
    <td class="hash">
        <?= $isAlreadyHashed ? 'No change needed' : password_hash($user['login_password'], PASSWORD_BCRYPT) ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<br>
<p style="color:#e74c3c; font-weight:bold;">
  ⚠️ DELETE this file from the server now:
  <code>utility/hash_passwords.php</code>
</p>
</body>
</html>
