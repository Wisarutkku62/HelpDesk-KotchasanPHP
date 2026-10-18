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

    public static function getTicketById($id)
    {
        try {
            $model = new static;
            return $model->db()->createQuery()
                ->select('*')
                ->from('tickets')
                ->where(array('id', (int)$id))
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function addTicket($save)
    {
        return static::db()->insert(static::getTableName('tickets'), $save);
    }

    public static function updateTicket($id, $save)
    {
        return static::db()->update(
            static::getTableName('tickets'),
            array('id', (int)$id),
            $save
        );
    }

    public static function deleteTicket($id)
    {
        return static::db()->delete(
            static::getTableName('tickets'),
            array('id', (int)$id)
        );
    }
}