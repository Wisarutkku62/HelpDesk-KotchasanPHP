<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class UserController
{
    private static function checkAdmin(array $loginUser)
    {
        if (($loginUser['role'] ?? '') !== 'admin') {
            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support');
            exit;
        }
    }

    public static function manage(Request $request, array $loginUser)
    {
        self::checkAdmin($loginUser);

        $viewFile = ROOT_PATH . 'projects/welcome/modules/support/views/manage_users.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die('ไม่พบไฟล์ views/manage_users.php กรุณาตรวจสอบการสร้างไฟล์');
        }

        $users = \Support\Index\Model::getUsers();
        $view = new \Support\Index\UsersView();
        echo $view->render($request, $users, $loginUser);
    }

    public static function updateRole(Request $request, array $loginUser)
    {
        self::checkAdmin($loginUser);

        $targetUserId = $request->post('user_id')->toInt();
        $newRole = $request->post('role')->topic();

        if ($targetUserId !== $loginUser['id'] && in_array($newRole, ['user', 'staff', 'admin'])) {
            \Support\Index\Model::updateUserRole($targetUserId, $newRole);
        }

        if (ob_get_length()) { ob_clean(); }
        header('Location: index.php?module=support&action=manage_users');
        exit;
    }

    public static function resetPassword(Request $request, array $loginUser)
    {
        self::checkAdmin($loginUser);

        $targetUserId = $request->post('user_id')->toInt();
        $newPassword = $request->post('new_password')->topic();
        $msg = '';
        $isSuccess = false;

        if (!empty($newPassword) && mb_strlen($newPassword) >= 4) {
            \Support\Index\Model::resetUserPassword($targetUserId, $newPassword);
            $msg = 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว';
            $isSuccess = true;
        } else {
            $msg = 'รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษร';
        }

        $viewFile = ROOT_PATH . 'projects/welcome/modules/support/views/manage_users.php';
        if (file_exists($viewFile)) { require_once $viewFile; }

        $users = \Support\Index\Model::getUsers();
        $view = new \Support\Index\UsersView();
        echo $view->render($request, $users, $loginUser, $msg, $isSuccess);
    }

    public static function delete(Request $request, array $loginUser)
    {
        self::checkAdmin($loginUser);

        $targetUserId = $request->get('id')->toInt();
        if ($targetUserId > 0 && $targetUserId !== $loginUser['id']) {
            \Support\Index\Model::deleteUser($targetUserId);
        }

        if (ob_get_length()) { ob_clean(); }
        header('Location: index.php?module=support&action=manage_users');
        exit;
    }
}