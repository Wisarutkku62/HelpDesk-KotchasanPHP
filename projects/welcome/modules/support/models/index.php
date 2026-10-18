<?php
namespace Support\Index;

class Model
{
    private static function connectDB()
    {
        $dir = ROOT_PATH . 'datas';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $dbPath = $dir . '/helpdesk.sql';
        $pdo = new \PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $pdo->exec("CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            detail TEXT,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        return $pdo;
    }

    public static function getTickets()
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->query("SELECT * FROM tickets ORDER BY id DESC");
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function addTicket($save)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("INSERT INTO tickets (title, detail, status, created_at) VALUES (:title, :detail, :status, :created_at)");
            return $stmt->execute([
                ':title'      => $save['title'],
                ':detail'     => $save['detail'] ?? '',
                ':status'     => $save['status'] ?? 'pending',
                ':created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            exit('เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
        }
    }

    public static function updateStatus($id, $status)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("UPDATE tickets SET status = :status WHERE id = :id");
            return $stmt->execute([
                ':status' => $status,
                ':id'     => (int)$id
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function deleteTicket($id)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
            return $stmt->execute([':id' => (int)$id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}