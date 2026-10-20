<?php
namespace Support\Index;

class DetailView
{
    public function render($request, $ticket = [], $comments = [], $loginUser = [])
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>รายละเอียดการแจ้งซ่อม #<?php echo $ticket['id']; ?></title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-form-container detail-container">
                <div class="detail-header">
                    <div>
                        <span class="detail-id">#<?php echo $ticket['id']; ?></span>
                        <h1 class="detail-title"><?php echo htmlspecialchars($ticket['title']); ?></h1>
                    </div>
                    <span class="badge badge-<?php echo $ticket['status']; ?>">
                        <?php 
                            if ($ticket['status'] === 'pending') echo 'รอดำเนินการ';
                            elseif ($ticket['status'] === 'in_progress') echo 'กำลังดำเนินการ';
                            elseif ($ticket['status'] === 'completed') echo 'เสร็จสิ้น';
                            else echo htmlspecialchars($ticket['status']);
                        ?>
                    </span>
                </div>

                <div class="detail-info-grid">
                    <div class="info-item">
                        <span class="info-label">ผู้แจ้งซ่อม</span>
                        <span class="info-value"><?php echo htmlspecialchars($ticket['user_name'] ?? 'ทั่วไป'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">หมวดหมู่</span>
                        <span class="info-value">
                            <span class="category-badge"><?php echo htmlspecialchars($ticket['category_name'] ?? 'ทั่วไป'); ?></span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">อุปกรณ์ / รหัสทรัพย์สิน</span>
                        <span class="info-value">
                            <?php if (!empty($ticket['asset_code'])): ?>
                                <span class="asset-badge">[<?php echo htmlspecialchars($ticket['asset_code']); ?>]</span>
                                <?php echo htmlspecialchars($ticket['asset_name']); ?>
                            <?php else: ?>
                                <span class="text-muted">ไม่ระบุ</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">วันที่แจ้ง</span>
                        <span class="info-value"><?php echo !empty($ticket['created_at']) ? self::formatThaiDate($ticket['created_at']) : '-'; ?></span>
                    </div>
                </div>

                <div class="detail-section">
                    <h3 class="section-subtitle">รายละเอียดปัญหา</h3>
                    <div class="detail-box">
                        <?php echo nl2br(htmlspecialchars($ticket['detail'] ?? 'ไม่มีรายละเอียดเพิ่มเติม')); ?>
                    </div>
                </div>

                <?php 
                    $imgRelPath = 'datas/uploads/' . $ticket['image'];
                    $projImgRelPath = 'projects/welcome/datas/uploads/' . $ticket['image'];
                    $hasImg = false;
                    $imgUrl = '';

                    if (!empty($ticket['image'])) {
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
                    <div class="detail-section">
                        <h3 class="section-subtitle">รูปภาพประกอบ</h3>
                        <div class="detail-image-wrapper">
                            <a href="<?php echo $imgUrl; ?>" target="_blank">
                                <img src="<?php echo $imgUrl; ?>" class="detail-full-image" alt="รูปภาพอุปกรณ์ชำรุด">
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="detail-section comments-section">
                    <h3 class="section-subtitle">ประวัติการติดตามงาน / บันทึกการซ่อม</h3>

                    <div class="comment-timeline">
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $c): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <div class="comment-author">
                                            <strong><?php echo htmlspecialchars($c['user_name']); ?></strong>
                                            <span class="role-badge role-<?php echo $c['user_role']; ?>"><?php echo strtoupper($c['user_role']); ?></span>
                                        </div>
                                        <span class="comment-time"><?php echo self::formatThaiDate($c['created_at']); ?></span>
                                    </div>
                                    <div class="comment-body">
                                        <?php echo nl2br(htmlspecialchars($c['comment'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="comment-empty">
                                ยังไม่มีประวัติการโต้ตอบหรืออัปเดตงานสำหรับรายการนี้
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="index.php?module=support&action=add_comment" method="post" class="comment-form">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                        <div class="form-group">
                            <label class="form-label">เขียนข้อความโต้ตอบ / บันทึกความคืบหน้า:</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="พิมพ์รายละเอียดเพิ่มเติม..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
                    </form>
                </div>
                <div class="detail-actions">
                    <a href="index.php?module=support" class="btn btn-secondary">กลับหน้าหลัก</a>

                    <?php $userRole = $loginUser['role'] ?? 'user'; ?>
                    <?php if (in_array($userRole, ['staff', 'admin'])): ?>
                        <?php if ($ticket['status'] === 'pending'): ?>
                            <a href="index.php?module=support&action=status&id=<?php echo $ticket['id']; ?>&status=in_progress" class="btn btn-status-process">รับเรื่องรายการนี้</a>
                        <?php elseif ($ticket['status'] === 'in_progress'): ?>
                            <a href="index.php?module=support&action=status&id=<?php echo $ticket['id']; ?>&status=completed" class="btn btn-status-complete">ปิดงานรายการนี้</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
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