<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class Controller extends \Kotchasan\Controller
{
    public function index(Request $request)
    {
        $action = $request->request('action')->topic();

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
            if (ob_get_length()) {
                ob_clean();
            }
            header('Location: index.php');
            exit;
        }

        if ($action === 'delete') {
            $id = $request->request('id')->toInt();
            if ($id > 0) {
                \Support\Index\Model::deleteTicket($id);
            }

            if (ob_get_length()) {
                ob_clean();
            }

            header('Location: index.php');
            exit;
        }

        $tickets = \Support\Index\Model::getTickets();
        $view = new \Support\Index\View();
        echo $view->render($request, $tickets);
    }
}