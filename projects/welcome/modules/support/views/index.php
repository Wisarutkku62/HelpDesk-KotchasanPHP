<?php
namespace Support\Index;

class View
{
    public function render($request, $tickets = [], $summary = [], $search = '', $selectedStatus = '', $loginUser = [], $categories = [], $selectedCategory = 0)
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
                <div class="user-bar">
                    <div class="user-info">
                        <span>สวัสดีคุณ <strong><?php echo htmlspecialchars($loginUser['name'] ?? 'ผู้ใช้งาน'); ?></strong></span>
                        <span class="role-badge role-<?php echo $loginUser['role'] ?? 'user'; ?>">
                            <?php echo strtoupper($loginUser['role'] ?? 'user'); ?>
                        </span>
                    </div>
                    <div class="user-actions">
                    <?php if (($loginUser['role'] ?? '') === 'admin'): ?>
                         <a href="index.php?module=support&action=manage_users" class="btn-manage-users">จัดการผู้ใช้งาน</a>
                     <?php endif; ?>
                    <a href="index.php?module=support&action=profile" class="btn-edit-profile">แก้ไขโปรไฟล์</a>
                    <a href="index.php?module=support&action=logout" class="btn-logout" onclick="return confirm('ยืนยันออกจากระบบ?')">ออกจากระบบ</a>
                    </div>
                </div>

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

                    <select name="category_id" class="filter-select" onchange="this.form.submit()">
                        <option value="0">-- ทุกหมวดหมู่ --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $selectedCategory == $cat['id'] ? 'selected="selected"' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>

                    <button type="submit" class="btn btn-primary btn-search">ค้นหา</button>
                    <?php if (!empty($search) || !empty($selectedStatus) || $selectedCategory > 0): ?>
                        <a href="index.php?module=support" class="btn btn-reset">ล้างการค้นหา</a>
                    <?php endif; ?>
                </form>

                <table class="support-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-img">รูปภาพ</th>
                            <th class="col-cat">หมวดหมู่</th>
                            <th class="col-title">หัวข้อ</th>
                            <th class="detail">รายละเอียด</th>
                            <th class="col-user">ผู้แจ้ง</th>
                            <th class="col-date">วันที่แจ้ง</th>
                            <th class="col-status">สถานะ</th>
                            <th class="col-action">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tickets)): ?>
                            <?php foreach ($tickets as $row): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $row['id']; ?></strong>
                                    </td>
                                    <td>
                                        <?php 
                                            $imgRelPath = 'datas/uploads/' . $row['image'];
                                            $projImgRelPath = 'projects/welcome/datas/uploads/' . $row['image'];
                                            $hasImg = false;
                                            $imgUrl = '';

                                            if (!empty($row['image'])) {
                                                if (file_exists(ROOT_PATH . $projImgRelPath)) {
                                                    $hasImg = true;
                                                    $baseUrl = defined('WEB_URL') ? WEB_URL : '';
                                                    $imgUrl = $baseUrl . $projImgRelPath;
                                                } elseif (file_exists(ROOT_PATH . $imgRelPath)) {
                                                    $hasImg = true;
                                                    $baseUrl = defined('WEB_URL') ? WEB_URL : '';
                                                    $imgUrl = $baseUrl . $imgRelPath;
                                                }
                                            }
                                        ?>
                                        <?php if ($hasImg): ?>
                                            <a href="<?php echo $imgUrl; ?>" target="_blank">
                                                <img src="<?php echo $imgUrl; ?>" class="ticket-thumb" alt="อุปกรณ์ชำรุด">
                                            </a>
                                        <?php else: ?>
                                            <span class="no-image-text">ไม่มีรูป</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="category-badge">
                                            <?php echo htmlspecialchars($row['category_name'] ?? 'ทั่วไป'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?module=support&action=view&id=<?php echo $row['id']; ?>" class="ticket-link">
                                            <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                        </a>
                                    </td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['detail']); ?></td>
                                    <td><span class="user-name-text"><?php echo htmlspecialchars($row['user_name'] ?? 'ทั่วไป'); ?></span></td>
                                    <td class="ticket-date text-muted">
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
                                    <td class="text-center">
                                    <div class="action-group">
                                        <?php 
                                            $filterParams = '';
                                            if (!empty($search)) { $filterParams .= '&search=' . urlencode($search); }
                                            if (!empty($selectedStatus)) { $filterParams .= '&status=' . urlencode($selectedStatus); }
                                            if ($selectedCategory > 0) { $filterParams .= '&category_id=' . $selectedCategory; }
                                            $userRole = $loginUser['role'] ?? 'user';
                                        ?>

                                        <?php if (in_array($userRole, ['staff', 'admin'])): ?>
                                            <?php if ($row['status'] === 'pending'): ?>
                                                <a href="index.php?module=support&action=status&id=<?php echo $row['id']; ?>&status=in_progress<?php echo $filterParams; ?>" class="btn-status btn-status-process">รับเรื่อง</a>
                                            <?php elseif ($row['status'] === 'in_progress'): ?>
                                                <a href="index.php?module=support&action=status&id=<?php echo $row['id']; ?>&status=completed<?php echo $filterParams; ?>" class="btn-status btn-status-complete">ปิดงาน</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if ($userRole === 'admin' || ($userRole === 'user' && $row['user_id'] == $loginUser['id'] && $row['status'] === 'pending')): ?>
                                            <a href="index.php?module=support&action=delete&id=<?php echo $row['id']; ?><?php echo $filterParams; ?>" class="btn-delete" onclick="return confirm('ยืนยันการลบรายการนี้?')">ลบ</a>
                                        <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted empty-state">
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