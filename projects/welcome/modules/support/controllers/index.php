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
        }
        $summary = \Support\Index\Model::getSummary();
        $tickets = \Support\Index\Model::getTickets($search, $selectedStatus);
        $view = new \Support\Index\View();
        echo $view->render($request, $tickets, $summary, $search, $selectedStatus);
    }
}