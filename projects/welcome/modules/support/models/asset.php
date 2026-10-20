<?php
namespace Support\Index;

class AssetModel
{
    public static function getAssets()
    {
        try {
            $pdo = Db::connect();
            $stmt = $pdo->query("SELECT * FROM assets ORDER BY asset_code ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }
}