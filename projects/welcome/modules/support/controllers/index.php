<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class Controller extends \Kotchasan\Controller
{
    public function index(Request $request)
    {
        $action = $request->request('action')->topic();
        $search = $request->get('search')->topic();
        $selectedStatus = $request->get('status')->topic();
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
            $save = [
                'title'  => $request->post('title')->topic(),
                'detail' => $request->post('detail')->topic(),
                'status' => 'pending'
            ];

            if (!empty($save['title'])) {
                \Support\Index\Model::addTicket($save);
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support');
            exit;
        }

        if ($action === 'status') {
            $id = $request->request('id')->toInt();
            $status = $request->request('status')->topic();

            if ($id > 0 && in_array($status, ['pending', 'in_progress', 'completed'])) {
                \Support\Index\Model::updateStatus($id, $status);
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($action === 'delete') {
            $id = $request->request('id')->toInt();
            if ($id > 0) {
                \Support\Index\Model::deleteTicket($id);
            }

            if (ob_get_length()) { ob_clean(); }
            header('Location: ' . $redirectUrl);
            exit;

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

            if (!empty($username) && !empty($password) && !empty($name)) {
                $success = \Support\Index\Model::register($username, $password, $name, $role);
                if ($success) {
                    header('Location: index.php?module=support&action=login');
                    exit;
                }
            }

            require_once ROOT_PATH . 'projects/welcome/modules/support/views/register.php';
            $view = new \Support\Index\RegisterView();
            echo $view->render('ไม่สามารถสมัครสมาชิกได้ ชื่อผู้ใช้นี้อาจถูกใช้ไปแล้ว');
            return;
        }

        if ($action === 'logout') {
            unset($_SESSION['login_user']);
            session_destroy();
            header('Location: index.php?module=support&action=login');
            exit;
        }
        if (empty($_SESSION['login_user'])) {
            header('Location: index.php?module=support&action=login');
            exit;
        }
        $loginUser = $_SESSION['login_user'];
        $search = $request->get('search')->topic();
        $selectedStatus = $request->get('status')->topic();

        }
        $summary = \Support\Index\Model::getSummary();
        $tickets = \Support\Index\Model::getTickets($search, $selectedStatus);
        $view = new \Support\Index\View();
        echo $view->render($request, $tickets, $summary, $search, $selectedStatus);
    }
}