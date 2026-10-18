<?php
namespace Support\Index;

class View
{
    public function render($request, $tickets = [], $summary = [], $search = '', $selectedStatus = '')
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ระบบแจ้งซ่อม - IT Support Dashboard</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-container">
                <div class="support-header">
                    <h1 class="support-title">ระบบแจ้งซ่อม IT Support</h1>
                    <a href="index.php?module=support&action=add" class="btn btn-primary">+ แจ้งซ่อมใหม่</a>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="stat-title">ทั้งหมด</span>
                        <span class="stat-value"><?php echo number_format($summary['total'] ?? 0); ?></span>
                    </div>
                    <div class="stat-card card-pending">
                        <span class="stat-title">รอดำเนินการ</span>
                        <span class="stat-value"><?php echo number_format($summary['pending'] ?? 0); ?></span>
                    </div>
                    <div class="stat-card card-progress">
                        <span class="stat-title">กำลังดำเนินการ</span>
                        <span class="stat-value"><?php echo number_format($summary['in_progress'] ?? 0); ?></span>
                    </div>
                    <div class="stat-card card-completed">
                        <span class="stat-title">เสร็จสิ้น</span>
                        <span class="stat-value"><?php echo number_format($summary['completed'] ?? 0); ?></span>
                    </div>
                </div>
                <form method="get" action="index.php" class="filter-bar">
                    <input type="hidden" name="module" value="support">
                    <input type="text" name="search" class="filter-input" placeholder="ค้นหาหัวข้อหรือรายละเอียด" value="<?php echo htmlspecialchars($search); ?>">
                    
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="" <?php echo empty($selectedStatus) ? 'selected="selected"' : ''; ?>>-- ทุกสถานะ --</option>
                        <option value="pending" <?php echo ($selectedStatus === 'pending') ? 'selected="selected"' : ''; ?>>รอดำเนินการ</option>
                        <option value="in_progress" <?php echo ($selectedStatus === 'in_progress') ? 'selected="selected"' : ''; ?>>กำลังดำเนินการ</option>
                        <option value="completed" <?php echo ($selectedStatus === 'completed') ? 'selected="selected"' : ''; ?>>เสร็จสิ้น</option>
                    </select>

                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px;">ค้นหา</button>
                    <?php if (!empty($search) || !empty($selectedStatus)): ?>
                        <a href="index.php?module=support" class="btn" style="background: #e2e8f0; color: #475569;">ล้างการค้นหา</a>
                    <?php endif; ?>
                </form>
                <table class="support-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>หัวข้อ</th>
                            <th>รายละเอียด</th>
                            <th style="width: 140px;">วันที่แจ้ง</th>
                            <th style="width: 140px;">สถานะ</th>
                            <th style="width: 160px; text-align: center;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tickets)): ?>
                            <?php foreach ($tickets as $row): ?>
                                <tr>
                                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['detail']); ?></td>
                                    <td style="font-size: 0.85rem;" class="text-muted">
                                        <?php echo !empty($row['created_at']) ? self::formatThaiDate($row['created_at']) : '-'; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status']; ?>">
                                             <?php 
                                                if ($row['status'] === 'pending') echo 'รอดำเนินการ';
                                                elseif ($row['status'] === 'in_progress') echo 'กำลังดำเนินการ';
                                                elseif ($row['status'] === 'completed') echo 'เสร็จสิ้น';
                                                else echo htmlspecialchars($row['status']);
                                            ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php 
                                            // สร้าง URL เปลี่ยนสถานะ/ลบ ให้คงค่า search และ status เดิมไว้
                                            $filterParams = '';
                                            if (!empty($search)) { $filterParams .= '&search=' . urlencode($search); }
                                            if (!empty($selectedStatus)) { $filterParams .= '&status=' . urlencode($selectedStatus); }
                                        ?>
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <a href="index.php?module=support&action=status&id=<?php echo $row['id']; ?>&status=in_progress<?php echo $filterParams; ?>" class="btn-status btn-status-process">รับเรื่อง</a>
                                        <?php elseif ($row['status'] === 'in_progress'): ?>
                                            <a href="index.php?module=support&action=status&id=<?php echo $row['id']; ?>&status=completed<?php echo $filterParams; ?>" class="btn-status btn-status-complete">ปิดงาน</a>
                                        <?php endif; ?>

                                        <a href="index.php?module=support&action=delete&id=<?php echo $row['id']; ?><?php echo $filterParams; ?>" class="btn-delete" onclick="return confirm('ยืนยันการลบรายการนี้?')">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding: 40px 0;">
                                    ไม่พบรายการแจ้งซ่อมที่ตรงกับเงื่อนไข
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