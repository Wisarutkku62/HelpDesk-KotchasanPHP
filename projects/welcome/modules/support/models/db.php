<?php
namespace Support\Index;

class Db
{
    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dir = ROOT_PATH . 'datas';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $dbPath = $dir . '/helpdesk.sql';
        $pdo = new \PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        // สร้างตารางข้อมูล
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

        self::$pdo = $pdo;
        return self::$pdo;
    }
}