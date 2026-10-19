<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class Controller extends \Kotchasan\Controller
{
    public function index(Request $request)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $action = $_GET['action'] ?? $_POST['action'] ?? $request->request('action')->topic();

        if ($action === 'logout') {
            unset($_SESSION['login_user']);
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support&action=login');
            exit;
        }

        if ($action === 'login') {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/login.php';
            $view = new \Support\Index\LoginView();
            echo $view->render();
            return;
        }

        if ($action === 'do_login' && $request->isPost()) {
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
                return;
            }
        }

        if ($action === 'register') {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/register.php';
            $view = new \Support\Index\RegisterView();
            echo $view->render();
            return;
        }

        if ($action === 'do_register' && $request->isPost()) {
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
            return;
        }

        //Auth Guard
        if (empty($_SESSION['login_user'])) {
            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support&action=login');
            exit;
        }

        // User Login Only
        $loginUser = $_SESSION['login_user'];
        $search = $_GET['search'] ?? $request->get('search')->topic();
        $selectedStatus = $_GET['status'] ?? $request->get('status')->topic();

        $queryParams = [];
        if (!empty($search)) { $queryParams[] = 'search=' . urlencode($search); }
        if (!empty($selectedStatus)) { $queryParams[] = 'status=' . urlencode($selectedStatus); }
        $redirectUrl = 'index.php?module=support' . (!empty($queryParams) ? '&' . implode('&', $queryParams) : '');

        if ($action === 'add') {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/add.php';
            $view = new \Support\Index\AddView();
            echo $view->render($request);
            return;
        }

       if ($action === 'save' && $request->isPost()) {
            $imageName = '';
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'datas/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileTmp   = $_FILES['image']['tmp_name'];
                $fileName  = $_FILES['image']['name'];
                $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($fileExt, $allowed)) {
                    $imageName = 'ticket_' . time() . '_' . uniqid() . '.' . $fileExt;
                    move_uploaded_file($fileTmp, $uploadDir . $imageName);
                }
            }

            $save = [
                'title'  => $request->post('title')->topic(),
                'detail' => $request->post('detail')->topic(),
                'image'  => $imageName,
                'status' => 'pending'
            ];

            if (!empty($save['title'])) {
                \Support\Index\Model::addTicket($save, $loginUser['id']);
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support');
            exit;
        }

        if ($action === 'status') {
            if (in_array($loginUser['role'], ['staff', 'admin'])) {
                $id = $request->request('id')->toInt();
                $status = $request->request('status')->topic();

                if ($id > 0 && in_array($status, ['pending', 'in_progress', 'completed'])) {
                    \Support\Index\Model::updateStatus($id, $status);
                }
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($action === 'delete') {
            $id = $request->request('id')->toInt();
            if ($id > 0) {
                \Support\Index\Model::deleteTicket($id, $loginUser['id'], $loginUser['role']);
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($action === 'view') {
            $id = (int)$request->get('id')->toInt();
            $ticket = \Support\Index\Model::getTicketById($id);

            if (!$ticket) {
                header('Location: index.php?module=support');
                exit;
            }

            if ($loginUser['role'] === 'user' && $ticket['user_id'] != $loginUser['id']) {
                header('Location: index.php?module=support');
                exit;
            }
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/detail.php';
            $view = new \Support\Index\DetailView();
            echo $view->render($request, $ticket, $loginUser);
            return;
        }

        if ($action === 'profile') {
            require_once ROOT_PATH . 'projects/welcome/modules/support/views/profile.php';
            $view = new \Support\Index\ProfileView();
            echo $view->render($request, $loginUser);
            return;
        }

        if ($action === 'save_profile' && $request->isPost()) {
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
                return;
            } else {
                require_once ROOT_PATH . 'projects/welcome/modules/support/views/profile.php';
                $view = new \Support\Index\ProfileView();
                echo $view->render($request, $loginUser, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', false);
                return;
            }
        }

        //Admin
       if ($action === 'manage_users') {
            if ($loginUser['role'] !== 'admin') {
                header('Location: index.php?module=support');
                exit;
            }

            require_once ROOT_PATH . 'projects/welcome/modules/support/views/users.php';
            $users = \Support\Index\Model::getUsers();
            $view = new \Support\Index\UsersView();
            echo $view->render($request, $users, $loginUser);
            return;
        }

        if ($action === 'update_user_role' && $request->isPost()) {
            if ($loginUser['role'] === 'admin') {
                $targetUserId = $request->post('user_id')->toInt();
                $newRole = $request->post('role')->topic();

                if ($targetUserId !== $loginUser['id'] && in_array($newRole, ['user', 'staff', 'admin'])) {
                    \Support\Index\Model::updateUserRole($targetUserId, $newRole);
                }
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support&action=manage_users');
            exit;
        }

        if ($action === 'delete_user') {
            if ($loginUser['role'] === 'admin') {
                $targetUserId = $request->get('id')->toInt();
                if ($targetUserId > 0 && $targetUserId !== $loginUser['id']) {
                    \Support\Index\Model::deleteUser($targetUserId);
                }
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support&action=manage_users');
            exit;
        }

        $summary = \Support\Index\Model::getSummary($loginUser['id'], $loginUser['role']);
        $tickets = \Support\Index\Model::getTickets($search, $selectedStatus, $loginUser['id'], $loginUser['role']);
        $view = new \Support\Index\View();
        echo $view->render($request, $tickets, $summary, $search, $selectedStatus, $loginUser);
    }
}