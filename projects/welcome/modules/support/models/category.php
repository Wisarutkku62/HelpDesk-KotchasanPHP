<?php
namespace Support\Index;

class CategoryModel
{
    public static function getCategories()
    {
        try {
            $pdo = Db::connect();
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getDepartments()
    {
        try {
            $pdo = Db::connect();
            $stmt = $pdo->query("SELECT * FROM departments ORDER BY id ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }
}