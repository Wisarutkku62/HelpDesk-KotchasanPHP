<?php
namespace Support\Index;

class TicketModel
{
    public static function addTicket($save, $userId = 0)
    {
        try {
            $pdo = Db::connect();
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
            $pdo = Db::connect();
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
            $pdo = Db::connect();
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
            $pdo = Db::connect();
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

    public static function getTicketById($id)
    {
        try {
            $pdo = Db::connect();
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

    public static function getSummary($userId = 0, $role = 'admin')
    {
        try {
            $pdo = Db::connect();
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
}