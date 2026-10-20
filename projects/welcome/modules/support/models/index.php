<?php
namespace Support\Index;

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/user.php';
require_once __DIR__ . '/ticket.php';
require_once __DIR__ . '/comment.php';
require_once __DIR__ . '/asset.php';
require_once __DIR__ . '/category.php';

class Model
{
    // User Methods
    public static function login($username, $password) { return UserModel::login($username, $password); }
    public static function register($username, $password, $name, $role = 'user') { return UserModel::register($username, $password, $name, $role); }
    public static function updateProfile($userId, $name, $password = '') { return UserModel::updateProfile($userId, $name, $password); }
    public static function getUsers() { return UserModel::getUsers(); }
    public static function updateUserRole($userId, $role) { return UserModel::updateUserRole($userId, $role); }
    public static function resetUserPassword($userId, $newPassword) { return UserModel::resetUserPassword($userId, $newPassword); }
    public static function deleteUser($userId) { return UserModel::deleteUser($userId); }

    // Ticket Methods
    public static function addTicket($save, $userId = 0) { return TicketModel::addTicket($save, $userId); }
    public static function updateStatus($id, $status) { return TicketModel::updateStatus($id, $status); }
    public static function deleteTicket($id, $userId = 0, $role = 'admin') { return TicketModel::deleteTicket($id, $userId, $role); }
    public static function getTickets($search = '', $status = '', $userId = 0, $role = 'admin', $categoryId = 0) { return TicketModel::getTickets($search, $status, $userId, $role, $categoryId); }
    public static function getTicketById($id) { return TicketModel::getTicketById($id); }
    public static function getSummary($userId = 0, $role = 'admin') { return TicketModel::getSummary($userId, $role); }

    // Comment Methods
    public static function addComment($ticketId, $userId, $comment) { return CommentModel::addComment($ticketId, $userId, $comment); }
    public static function getCommentsByTicketId($ticketId) { return CommentModel::getCommentsByTicketId($ticketId); }

    // Asset Methods
    public static function getAssets() { return AssetModel::getAssets(); }

    // Category & Department Methods
    public static function getCategories() { return CategoryModel::getCategories(); }
    public static function getDepartments() { return CategoryModel::getDepartments(); }
}