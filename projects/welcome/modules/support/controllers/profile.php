<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class ProfileController
{
    public static function index(Request $request, array $loginUser)
    {
        require_once ROOT_PATH . 'projects/welcome/modules/support/views/profile.php';
        $view = new \Support\Index\ProfileView();
        echo $view->render($request, $loginUser);
    }

    public static function save(Request $request, array $loginUser)
    {
        $name = trim($request->post('name')->topic());
        $password = $request->post('password')->topic();
        $confirmPassword = $request->post('confirm_password')->topic();
        $errorMsg = '';

        if (empty($name)) {
            $errorMsg = 'กรุณากรอกชื่อ-นามสกุล';
        } elseif (!empty($password)) {
            if (mb_strlen($password) < 4) {
                $errorMsg = 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 4 ตัวอักษร';
            } elseif ($password !== $confirmPassword) {
                $errorMsg = 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน';
            }
        }

        if (!empty($errorMsg)) {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/profile.php';
            $view = new \Support\Index\ProfileView();
            echo $view->render($request, $loginUser, $errorMsg, false);
            return;
        }

        $success = \Support\Index\Model::updateProfile($loginUser['id'], $name, $password);
        if ($success) {
            $_SESSION['login_user']['name'] = $name;
            $loginUser['name'] = $name;

            require_once ROOT_PATH . 'projects/welcome/modules/support/views/profile.php';
            $view = new \Support\Index\ProfileView();
            echo $view->render($request, $loginUser, 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว', true);
        } else {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/profile.php';
            $view = new \Support\Index\ProfileView();
            echo $view->render($request, $loginUser, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', false);
        }
    }
}