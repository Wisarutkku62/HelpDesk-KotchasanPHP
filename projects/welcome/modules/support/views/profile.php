<?php
namespace Support\Index;

class ProfileView
{
    public function render($request, $loginUser = [], $msg = '', $isSuccess = false)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>จัดการโปรไฟล์ส่วนตัว - IT Support</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-form-container">
                <div class="support-header">
                    <h1 class="support-title">จัดการโปรไฟล์ส่วนตัว</h1>
                </div>

                <?php if (!empty($msg)): ?>
                    <div class="<?php echo $isSuccess ? 'alert-success' : 'alert-error'; ?>">
                        <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?module=support&action=save_profile">
                    <div class="form-group">
                        <label class="form-label">ชื่อผู้ใช้ (Username)</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($loginUser['username'] ?? ''); ?>" disabled style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">ชื่อ - นามสกุล <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($loginUser['name'] ?? ''); ?>" required placeholder="ระบุชื่อ-นามสกุล">
                    </div>

                    <hr style="margin: 24px 0; border: 0; border-top: 1px solid #e2e8f0;">

                    <div class="form-group">
                        <label class="form-label">รหัสผ่านใหม่ <span style="color: #64748b; font-weight: normal;">(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="ระบุรหัสผ่านใหม่อย่างน้อย 4 ตัวอักษร">
                    </div>

                    <div class="form-group">
                        <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="ระบุรหัสผ่านใหม่อีกครั้ง">
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                        <a href="index.php?module=support" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}