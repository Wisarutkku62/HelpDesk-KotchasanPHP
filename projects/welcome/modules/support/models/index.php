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

        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS departments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS assets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            asset_code TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            location TEXT
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            name TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            department_id INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            detail TEXT,
            image TEXT,
            status TEXT DEFAULT 'pending',
            user_id INTEGER DEFAULT 0,
            category_id INTEGER DEFAULT NULL,
            asset_id INTEGER DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS ticket_comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticket_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            comment TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        try { $pdo->exec("ALTER TABLE tickets ADD COLUMN user_id INTEGER DEFAULT 0"); } catch (\Throwable $e) {}
        try { $pdo->exec("ALTER TABLE tickets ADD COLUMN image TEXT"); } catch (\Throwable $e) {}
        try { $pdo->exec("ALTER TABLE tickets ADD COLUMN category_id INTEGER DEFAULT NULL"); } catch (\Throwable $e) {}
        try { $pdo->exec("ALTER TABLE tickets ADD COLUMN asset_id INTEGER DEFAULT NULL"); } catch (\Throwable $e) {}

        $stmtCat = $pdo->query("SELECT COUNT(*) FROM categories");
        if ($stmtCat->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO categories (name) VALUES 
                ('อุปกรณ์ฮาร์ดแวร์'),
                ('โปรแกรม/ซอฟต์แวร์'),
                ('ระบบเครือข่าย/อินเทอร์เน็ต'),
                ('เครื่องพิมพ์/สแกนเนอร์')");
        }

        $stmtDept = $pdo->query("SELECT COUNT(*) FROM departments");
        if ($stmtDept->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO departments (name) VALUES ('ไอที/ระบบ'), ('บัญชีและการเงิน'), ('การตลาด'), ('ทรัพยากรบุคคล')");
        }

        $stmtAsset = $pdo->query("SELECT COUNT(*) FROM assets");
        if ($stmtAsset->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO assets (asset_code, name, location) VALUES 
                ('AST-001', 'Notebook Dell XPS 13', 'แผนกไอที'),
                ('AST-002', 'PC Office HP ProDesk', 'แผนกบัญชี'),
                ('AST-003', 'Printer Canon LBP6030', 'ส่วนกลาง ชั้น 2'),
                ('AST-004', 'Monitor LG 24 นิ้ว', 'แผนกการตลาด')");
        }

        $stmtUser = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmtUser->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)")
                ->execute(['admin', password_hash('admin1234', PASSWORD_DEFAULT), 'ผู้ดูแลระบบ (Admin)', 'admin']);
            $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)")
                ->execute(['staff', password_hash('staff1234', PASSWORD_DEFAULT), 'ช่างไอที (Staff)', 'staff']);
            $pdo->prepare("INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)")
                ->execute(['user', password_hash('user1234', PASSWORD_DEFAULT), 'สมหมาย นะจ๊ะ', 'user']);
        }

        return $pdo;
    }

    public static function getAssets()
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->query("SELECT * FROM assets ORDER BY asset_code ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function addTicket($save, $userId = 0)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category_id, asset_id, title, detail, image, status, created_at) VALUES (:user_id, :category_id, :asset_id, :title, :detail, :image, :status, :created_at)");
            return $stmt->execute([
                ':user_id'     => $userId,
                ':category_id' => !empty($save['category_id']) ? (int)$save['category_id'] : null,
                ':asset_id'    => !empty($save['asset_id']) ? (int)$save['asset_id'] : null,
                ':title'       => $save['title'],
                ':detail'      => $save['detail'] ?? '',
                ':image'       => $save['image'] ?? '',
                ':status'      => $save['status'] ?? 'pending',
                ':created_at'  => date('Y-m-d H:i:s')
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

    public static function deleteTicket($id, $userId = 0, $role = 'admin')
    {
        try {
            $pdo = self::connectDB();
            if ($role === 'admin') {
                $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
                return $stmt->execute([':id' => (int)$id]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id AND user_id = :user_id AND status = 'pending'");
                return $stmt->execute([':id' => (int)$id, ':user_id' => (int)$userId]);
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getTickets($search = '', $status = '', $userId = 0, $role = 'admin', $categoryId = 0)
    {
        try {
            $pdo = self::connectDB();
            $sql = "SELECT t.*, u.name as user_name, c.name as category_name,
                           a.asset_code, a.name as asset_name
                    FROM tickets t 
                    LEFT JOIN users u ON t.user_id = u.id 
                    LEFT JOIN categories c ON t.category_id = c.id 
                    LEFT JOIN assets a ON t.asset_id = a.id
                    WHERE 1=1";
            $params = [];

            if ($role === 'user') {
                $sql .= " AND t.user_id = :user_id";
                $params[':user_id'] = $userId;
            }

            if (!empty($search)) {
                $sql .= " AND (t.title LIKE :search OR t.detail LIKE :search OR a.asset_code LIKE :search OR a.name LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }

            if (!empty($status)) {
                $sql .= " AND t.status = :status";
                $params[':status'] = $status;
            }

            if ($categoryId > 0) {
                $sql .= " AND t.category_id = :category_id";
                $params[':category_id'] = $categoryId;
            }

            $sql .= " ORDER BY t.id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getSummary($userId = 0, $role = 'admin')
    {
        try {
            $pdo = self::connectDB();
            $where = ($role === 'user') ? " WHERE user_id = " . (int)$userId : "";
            $stmt = $pdo->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM tickets {$where}");
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

    public static function getTicketById($id)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("
                SELECT t.*, u.name AS user_name, u.role AS user_role, c.name AS category_name,
                       a.asset_code, a.name AS asset_name, a.location AS asset_location
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN assets a ON t.asset_id = a.id
                WHERE t.id = :id
                LIMIT 1
            ");
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function updateProfile($userId, $name, $password = '')
    {
        try {
            $pdo = self::connectDB();
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = :name, password = :password WHERE id = :id");
                return $stmt->execute([
                    ':name' => $name,
                    ':password' => $hashedPassword,
                    ':id' => $userId
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
                return $stmt->execute([
                    ':name' => $name,
                    ':id' => $userId
                ]);
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getUsers()
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->query("SELECT id, username, name, role, created_at FROM users ORDER BY id ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function updateUserRole($userId, $role)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
            return $stmt->execute([':role' => $role, ':id' => (int)$userId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function resetUserPassword($userId, $newPassword)
    {
        try {
            $pdo = self::connectDB();
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            return $stmt->execute([':password' => $hashed, ':id' => (int)$userId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function deleteUser($userId)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => (int)$userId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getCategories()
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getDepartments()
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->query("SELECT * FROM departments ORDER BY id ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function addComment($ticketId, $userId, $comment)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment, created_at) VALUES (:ticket_id, :user_id, :comment, :created_at)");
            return $stmt->execute([
                ':ticket_id' => (int)$ticketId,
                ':user_id'   => (int)$userId,
                ':comment'   => $comment,
                ':created_at'=> date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getCommentsByTicketId($ticketId)
    {
        try {
            $pdo = self::connectDB();
            $stmt = $pdo->prepare("
                SELECT c.*, u.name AS user_name, u.role AS user_role
                FROM ticket_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.ticket_id = :ticket_id
                ORDER BY c.id ASC
            ");
            $stmt->execute([':ticket_id' => (int)$ticketId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }
}