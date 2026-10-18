<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class View extends \Kotchasan\View
{
    public function render(Request $request, $tickets)
    {
        ob_start(); 
        ?>
        <link rel="stylesheet" href="css/style.css">

        <div class="support-container">
            <div class="support-header">
                <h1 class="support-title">ระบบแจ้งซ่อม</h1>
                <a href="index.php?module=support&action=add" class="btn btn-success">+ แจ้งซ่อมใหม่</a>
            </div>

            <table class="support-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px;">ID</th>
                        <th>หัวข้อแจ้งซ่อม</th>
                        <th class="text-center" style="width:140px;">สถานะ</th>
                        <th class="text-center" style="width:90px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding:30px;">ยังไม่มีรายการแจ้งซ่อมในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $item): ?>
                            <tr>
                                <td class="text-center text-bold text-muted"><?= $item->id ?></td>
                                <td><?= htmlspecialchars($item->title ?? '-') ?></td>
                                <td class="text-center">
                                    <?php
                                    $status = $item->status ?? 'pending';
                                    if ($status === 'in_progress') {
                                        echo '<span class="badge badge-progress">กำลังดำเนินการ</span>';
                                    } elseif ($status === 'completed') {
                                        echo '<span class="badge badge-completed">เสร็จสิ้น</span>';
                                    } else {
                                        echo '<span class="badge badge-pending">รอแก้ไข</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?module=support&action=delete&id=<?= $item->id ?>" 
                                       onclick="return confirm('คุณต้องการลบรายการนี้ใช่หรือไม่?')" 
                                       class="btn-delete">ลบ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        return ob_get_clean(); 
    }
}