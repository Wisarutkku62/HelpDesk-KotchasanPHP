<?php
namespace Support\Index;

class Model extends \Kotchasan\Model
{
    public static function getTickets()
    {
        try {
            $model = new static;
            return $model->db()->createQuery()
                ->select('*')
                ->from('tickets')
                ->order('id DESC')
                ->execute();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function addTicket($save)
    {
        try {
            $model = new static;
            $db = $model->db();
            $db->query("CREATE TABLE IF NOT EXISTS tickets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                detail TEXT,
                status TEXT DEFAULT 'pending'
            )");
            return $db->insert(static::getTableName('tickets'), $save);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function deleteTicket($id)
    {
        try {
            return static::db()->delete(
                static::getTableName('tickets'),
                array('id', (int)$id)
            );
        } catch (\Throwable $e) {
            return false;
        }
    }
}