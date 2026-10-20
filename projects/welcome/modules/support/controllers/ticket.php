<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class TicketController
{
    public static function dashboard(Request $request, array $loginUser)
    {
        $search = $_GET['search'] ?? $request->get('search')->topic();
        $selectedStatus = $_GET['status'] ?? $request->get('status')->topic();
        $selectedCategory = (int)($_GET['category_id'] ?? $request->get('category_id')->toInt());

        $summary = \Support\Index\Model::getSummary($loginUser['id'], $loginUser['role']);
        $categories = \Support\Index\Model::getCategories();
        $tickets = \Support\Index\Model::getTickets($search, $selectedStatus, $loginUser['id'], $loginUser['role'], $selectedCategory);

        require_once ROOT_PATH . 'projects/welcome/modules/support/views/index.php';
        $view = new \Support\Index\View();
        echo $view->render($request, $tickets, $summary, $search, $selectedStatus, $loginUser, $categories, $selectedCategory);
    }

    public static function add(Request $request)
    {
        $categories = \Support\Index\Model::getCategories();
        $assets = \Support\Index\Model::getAssets();

        require_once ROOT_PATH . 'projects/welcome/modules/support/views/add.php';
        $view = new \Support\Index\AddView();
        echo $view->render($request, $categories, $assets);
    }

    public static function save(Request $request, array $loginUser)
    {
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
            'title'       => $request->post('title')->topic(),
            'detail'      => $request->post('detail')->topic(),
            'category_id' => $request->post('category_id')->toInt(),
            'asset_id'    => $request->post('asset_id')->toInt(),
            'image'       => $imageName,
            'status'      => 'pending'
        ];

        if (!empty($save['title'])) {
            \Support\Index\Model::addTicket($save, $loginUser['id']);
        }

        if (ob_get_length()) { ob_clean(); }
        header('Location: index.php?module=support');
        exit;
    }

    public static function view(Request $request, array $loginUser)
    {
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

        $comments = \Support\Index\Model::getCommentsByTicketId($id);

        require_once ROOT_PATH . 'projects/welcome/modules/support/views/detail.php';
        $view = new \Support\Index\DetailView();
        echo $view->render($request, $ticket, $comments, $loginUser);
    }

    public static function status(Request $request, array $loginUser, string $redirectUrl)
    {
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

    public static function delete(Request $request, array $loginUser, string $redirectUrl)
    {
        $id = $request->request('id')->toInt();
        if ($id > 0) {
            \Support\Index\Model::deleteTicket($id, $loginUser['id'], $loginUser['role']);
        }

        if (ob_get_length()) { ob_clean(); }
        header('Location: ' . $redirectUrl);
        exit;
    }

    public static function addComment(Request $request, array $loginUser)
    {
        $ticketId = $request->post('ticket_id')->toInt();
        $comment = trim($request->post('comment')->textarea());

        if ($ticketId > 0 && !empty($comment)) {
            $ticket = \Support\Index\Model::getTicketById($ticketId);
            if ($ticket && ($loginUser['role'] !== 'user' || $ticket['user_id'] == $loginUser['id'])) {
                \Support\Index\Model::addComment($ticketId, $loginUser['id'], $comment);
            }
        }

        if (ob_get_length()) { ob_clean(); }
        header('Location: index.php?module=support&action=view&id=' . $ticketId);
        exit;
    }
}