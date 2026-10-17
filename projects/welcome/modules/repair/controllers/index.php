<?php
namespace Support\Index;

use Kotchasan\Http\Request;
use Kotchasan\Html;

class Controller extends \Kotchasan\Controller
{
    public function render(Request $request)
    {
        $tickets = \Support\Index\Model::getTickets();

        $view = new \Support\Index\View();
        return $view->render($request, $tickets);
    }
}