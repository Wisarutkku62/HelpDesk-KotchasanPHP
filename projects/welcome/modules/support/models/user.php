<?php
namespace Support\Index;

class UserModel
{
    public static function login($username, $password)
    {
        try {
            $pdo = Db::connect();
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
            $pdo = Db::connect();
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

    public static function updateProfile($userId, $name, $password = '')
    {
        try {
            $pdo = Db::connect();
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
            $pdo = Db::connect();
            $stmt = $pdo->query("SELECT id, username, name, role, created_at FROM users ORDER BY id ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function updateUserRole($userId, $role)
    {
        try {
            $pdo = Db::connect();
            $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
            return $stmt->execute([':role' => $role, ':id' => (int)$userId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function resetUserPassword($userId, $newPassword)
    {
        try {
            $pdo = Db::connect();
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
            $pdo = Db::connect();
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => (int)$userId]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}