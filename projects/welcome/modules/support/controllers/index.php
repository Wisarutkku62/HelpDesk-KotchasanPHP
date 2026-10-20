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
        require_once __DIR__ . '/auth.php';
        require_once __DIR__ . '/profile.php';
        require_once __DIR__ . '/user.php';
        require_once __DIR__ . '/ticket.php';

        // Auth (Logout, Login, Register)
        if ($action === 'logout') {
            return AuthController::logout();
        }
        if ($action === 'login') {
            return AuthController::login();
        }
        if ($action === 'do_login' && $request->isPost()) {
            return AuthController::doLogin($request);
        }
        if ($action === 'register') {
            return AuthController::register();
        }
        if ($action === 'do_register' && $request->isPost()) {
            return AuthController::doRegister($request);
        }

        //Auth Guard Check
        if (empty($_SESSION['login_user'])) {
            if (ob_get_length()) { ob_clean(); }
            header('Location: index.php?module=support&action=login');
            exit;
        }

        $loginUser = $_SESSION['login_user'];

        $search = $_GET['search'] ?? $request->get('search')->topic();
        $selectedStatus = $_GET['status'] ?? $request->get('status')->topic();
        $selectedCategory = (int)($_GET['category_id'] ?? $request->get('category_id')->toInt());

        $queryParams = [];
        if (!empty($search)) { $queryParams[] = 'search=' . urlencode($search); }
        if (!empty($selectedStatus)) { $queryParams[] = 'status=' . urlencode($selectedStatus); }
        if ($selectedCategory > 0) { $queryParams[] = 'category_id=' . $selectedCategory; }
        $redirectUrl = 'index.php?module=support' . (!empty($queryParams) ? '&' . implode('&', $queryParams) : '');

        //Profile
        if ($action === 'profile') {
            return ProfileController::index($request, $loginUser);
        }
        if ($action === 'save_profile' && $request->isPost()) {
            return ProfileController::save($request, $loginUser);
        }

        // User Management (Admin Only)
        if ($action === 'manage_users') {
            return UserController::manage($request, $loginUser);
        }
        if ($action === 'update_user_role' && $request->isPost()) {
            return UserController::updateRole($request, $loginUser);
        }
        if ($action === 'reset_user_password' && $request->isPost()) {
            return UserController::resetPassword($request, $loginUser);
        }
        if ($action === 'delete_user') {
            return UserController::delete($request, $loginUser);
        }

        //Tickets & Comments
        if ($action === 'add') {
            return TicketController::add($request);
        }
        if ($action === 'save' && $request->isPost()) {
            return TicketController::save($request, $loginUser);
        }
        if ($action === 'view') {
            return TicketController::view($request, $loginUser);
        }
        if ($action === 'status') {
            return TicketController::status($request, $loginUser, $redirectUrl);
        }
        if ($action === 'delete') {
            return TicketController::delete($request, $loginUser, $redirectUrl);
        }
        if ($action === 'add_comment' && $request->isPost()) {
            return TicketController::addComment($request, $loginUser);
        }

        return TicketController::dashboard($request, $loginUser);
    }
}