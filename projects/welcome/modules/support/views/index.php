<?php
namespace Support\Index;

class View
{
    public function render($request, $tickets = [])
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>รายการแจ้งซ่อม - IT Support</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-container">
                <div class="support-header">
                    <h1 class="support-title"> รายการแจ้งซ่อม</h1>
                    <a href="index.php?module=support&action=add" class="btn btn-primary">+ แจ้งซ่อมใหม่</a>
                </div>
                <table class="support-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>หัวข้อ</th>
                            <th>รายละเอียด</th>
                            <th style="width: 150px;">สถานะ</th>
                            <th style="width: 100px; text-align: center;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tickets)): ?>
                            <?php foreach ($tickets as $row): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['detail']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status']; ?>">
                                            ● <?php echo $row['status'] === 'pending' ? 'รอดำเนินการ' : htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="index.php?module=support&action=delete&id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('ยืนยันการลบรายการนี้?')">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="padding: 40px 0;">
                                    ยังไม่มีรายการแจ้งซ่อมในขณะนี้
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    private static function formatThaiDate($dateStr)
    {
        $time = strtotime($dateStr);
        if (!$time) return $dateStr;

        $thaiMonths = [
            1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];

        $day = date('j', $time);
        $month = $thaiMonths[(int)date('n', $time)];
        $year = date('Y', $time) + 543;
        $hourMin = date('H:i', $time);

        return "{$day} {$month} {$year} {$hourMin} น.";
    }
}