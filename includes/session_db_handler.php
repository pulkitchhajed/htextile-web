<?php
/**
 * ============================================================
 *  HTextile — Database Session Handler
 *  Overrides PHP default sessions to save in PostgreSQL.
 *  Crucial for Vercel/Serverless environments where local
 *  filesystem is ephemeral and shared across multiple instances.
 * ============================================================
 */

require_once(__DIR__ . '/config.php');

class PostgresSessionHandler implements SessionHandlerInterface {
    private $pdo;

    public function __construct() {
        $this->pdo = get_pdo();
    }

    public function open(string $path, string $name): bool {
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $id): string|false {
        try {
            $stmt = $this->pdo->prepare("SELECT data FROM txt_sessions WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row['data'];
            }
        } catch (PDOException $e) {
            error_log("Session read error: " . $e->getMessage());
        }
        return '';
    }

    public function write(string $id, string $data): bool {
        $timestamp = time();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO txt_sessions (id, data, timestamp) 
                VALUES (?, ?, ?) 
                ON CONFLICT (id) 
                DO UPDATE SET data = EXCLUDED.data, timestamp = EXCLUDED.timestamp
            ");
            $stmt->execute([$id, $data, $timestamp]);
            return true;
        } catch (PDOException $e) {
            error_log("Session write error: " . $e->getMessage());
            return false;
        }
    }

    public function destroy(string $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM txt_sessions WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log("Session destroy error: " . $e->getMessage());
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false {
        $oldest = time() - $max_lifetime;
        try {
            $stmt = $this->pdo->prepare("DELETE FROM txt_sessions WHERE timestamp < ?");
            $stmt->execute([$oldest]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Session garbage collection error: " . $e->getMessage());
            return false;
        }
    }
}

// Register the custom session handler
$handler = new PostgresSessionHandler();
session_set_save_handler($handler, true);
