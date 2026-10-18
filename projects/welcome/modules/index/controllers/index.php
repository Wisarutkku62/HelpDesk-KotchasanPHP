<?php
/**
 * @filesource modules/index/controllers/index.php
 *
 * Controller for the Index module.
 * This class handles the default actions for the Index module, including rendering the index page.
 * For more information, please visit: https://www.kotchasan.com/
 *
 * @copyright 2024 Goragod.com
 * @license https://www.kotchasan.com/license/
 * @author Goragod Wiriya <admin@goragod.com>
 */

namespace Index\Index;

use Kotchasan\Http\Request;

/**
 * Render the index page.
 *
 * This method is responsible for rendering the index page of the Index module.
 */
class Controller extends \Kotchasan\Controller
{
    /**
     * Render the index page.
     *
     * This method is responsible for rendering the index page of the Index module.
     *
     * @param Request $request The HTTP request object.
     */
  public function index(Request $request)
    {
        $modelFile = ROOT_PATH . 'projects/welcome/modules/support/models/index.php';
        $viewFile  = ROOT_PATH . 'projects/welcome/modules/support/views/index.php';
        $ctrlFile  = ROOT_PATH . 'projects/welcome/modules/support/controllers/index.php';

        if (!file_exists($modelFile)) {
            exit('หาไฟล์ Model ไม่เจอที่: ' . $modelFile);
        }
        if (!file_exists($viewFile)) {
            exit('หาไฟล์ View ไม่เจอที่: ' . $viewFile);
        }
        if (!file_exists($ctrlFile)) {
            exit('หาไฟล์ Controller ไม่เจอที่: ' . $ctrlFile);
        }

        require_once $modelFile;
        require_once $viewFile;
        require_once $ctrlFile;

        $controller = new \Support\Index\Controller();
        $controller->index($request);
    }
}
