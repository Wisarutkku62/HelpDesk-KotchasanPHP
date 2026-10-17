<?php 
namespace Support\Index;

use Kotchasan\Http\Request;

class View extends \Kotchasan\View
{
    public function render(Request $request, $tickets)
    {
        $content = '<div class="content_bg">';
        $content .= '<h1>รายการแจ้งซ่อม (Helpdesk Support)</h1>';
        $content .= '<table class="border fullwidth">';
        $content .= '<thead>';
        $content .= '<tr>';
        $content .= '<th class="center">ID</th>';
        $content .= '<th>หัวข้อแจ้งซ่อม</th>';
        $content .= '<th class="center">สถานะ</th>';
        $content .= '</tr>';
        $content .= '</thead>';
        $content .= '<tbody>';

        if (empty($tickets)) {
            $content .= '<tr><td colspan="3" class="center">ยังไม่มีรายการแจ้งซ่อมในระบบ</td></tr>';
        } else {
            foreach ($tickets as $item) {
                $content .= '<tr>';
                $content .= '<td class="center">' . $item->id . '</td>';
                $content .= '<td>' . htmlspecialchars($item->title ?? '-') . '</td>';
                $content .= '<td class="center">' . htmlspecialchars($item->status ?? '-') . '</td>';
                $content .= '</tr>';
            }
        }

        $content .= '</tbody>';
        $content .= '</table>';
        $content .= '</div>';

        return $content;
    } 
}