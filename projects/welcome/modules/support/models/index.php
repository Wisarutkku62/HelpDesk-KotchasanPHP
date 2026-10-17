<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class Model extends \Kotchasan\Model
{
    public static function getTickets()
    {
        return \Kotchasan\DB::createQuery()
            ->select('*')
            ->from('tickets')
            ->order('id DESC')
            ->execute();
    }

    public static function getTicketById($id)
    {
        return \Kotchasan\DB::createQuery()
            ->select('*')
            ->from('tickets')
            ->where(array('id', (int)$id))
            ->first();
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