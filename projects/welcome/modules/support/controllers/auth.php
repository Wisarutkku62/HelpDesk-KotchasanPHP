<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class AuthController
{
    public static function logout()
    {
        unset($_SESSION['login_user']);
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        if (ob_get_length()) { ob_clean(); }
        header('Location: index.php?module=support&action=login');
        exit;
    }

    public static function login()
    {
        require_once ROOT_PATH . 'projects/welcome/modules/support/views/login.php';
        $view = new \Support\Index\LoginView();
        echo $view->render();
    }

    public static function doLogin(Request $request)
    {
        $username = $request->post('username')->topic();
        $password = $request->post('password')->topic();
        $user = \Support\Index\Model::login($username, $password);

        if ($user) {
            $_SESSION['login_user'] = $user;
            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support');
            exit;
        } else {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/login.php';
            $view = new \Support\Index\LoginView();
            echo $view->render('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        }
    }

    public static function register()
    {
        require_once ROOT_PATH . 'projects/welcome/modules/support/views/register.php';
        $view = new \Support\Index\RegisterView();
        echo $view->render();
    }

    public static function doRegister(Request $request)
    {
        $name = $request->post('name')->topic();
        $username = $request->post('username')->topic();
        $password = $request->post('password')->topic();
        $role = $request->post('role')->topic();

        if (!in_array($role, ['user', 'staff'])) {
            $role = 'user';
        }

        if (!empty($username) && !empty($password) && !empty($name)) {
            $success = \Support\Index\Model::register($username, $password, $name, $role);
            if ($success) {
                if (ob_get_length()) { ob_clean(); }
                header('Location: index.php?module=support&action=login');
                exit;
            }
        }

        require_once ROOT_PATH . 'projects/welcome/modules/support/views/register.php';
        $view = new \Support\Index\RegisterView();
        echo $view->render('ไม่สามารถสมัครสมาชิกได้ ชื่อผู้ใช้นี้อาจถูกใช้ไปแล้ว');
    }
}