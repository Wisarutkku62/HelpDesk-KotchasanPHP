<?php
namespace Support\Index;

class CommentModel
{
    public static function addComment($ticketId, $userId, $comment)
    {
        try {
            $pdo = Db::connect();
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
            $pdo = Db::connect();
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