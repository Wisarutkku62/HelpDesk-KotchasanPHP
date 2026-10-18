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

        try {
            $pdo->exec("ALTER TABLE tickets ADD COLUMN user_id INTEGER DEFAULT 0");
        } catch (\Throwable $e) {}

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            name TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)")
                ->execute(['admin', password_hash('admin1234', PASSWORD_DEFAULT), 'ผู้ดูแลระบบ (Admin)', 'admin']);
            $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)")
                ->execute(['staff', password_hash('staff1234', PASSWORD_DEFAULT), 'ช่างไอที (Staff)', 'staff']);
            $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)")
                ->execute(['user', password_hash('user1234', PASSWORD_DEFAULT), 'สมชาย พนักงาน', 'user']);
        }
        return $pdo;
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
            return false;
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

    public static function getTickets($search = '', $status = '')
    {
        try {
            $pdo = self::connectDB();
            $sql = "SELECT * FROM tickets WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (title LIKE :search OR detail LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            if (!empty($status)) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }

            $sql .= " ORDER BY id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }
     public static function getSummary()
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM tickets");
            return $stmt->fetch() ?: ['total' => 0, 'pending' => 0, 'in_progress' => 0, 'completed' => 0];
        } catch (\Throwable $e) {
            return ['total' => 0, 'pending' => 0, 'in_progress' => 0, 'completed' => 0];
        }
    }
    
    public static function login($username, $password)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function register($username, $password, $name, $role = 'user')
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (:username, :password, :name, :role)");
            return $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':name'     => $name,
                ':role'     => $role
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}