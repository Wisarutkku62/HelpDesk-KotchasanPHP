<?php
namespace Support\Index;

class RegisterView
{
    public function render($error = '')
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>สมัครสมาชิก - IT Support</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body style="background: #f8fafc;">
            <div class="auth-container">
                <h1 class="auth-title">สมัครสมาชิกใหม่ IT Support</h1>

                <?php if (!empty($error)): ?>
                    <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="index.php?module=support&action=do_register" method="post">
                    <div class="form-group">
                        <label>ชื่อ-นามสกุล</label>
                        <input type="text" name="name" class="form-control" required placeholder="เช่น สมชาย ใจดี">
                    </div>
                    <div class="form-group">
                        <label>ชื่อผู้ใช้งาน (Username)</label>
                        <input type="text" name="username" class="form-control" required placeholder="ตั้งชื่อผู้ใช้สำหรับ Login">
                    </div>
                    <div class="form-group">
                        <label>รหัสผ่าน (Password)</label>
                        <input type="password" name="password" class="form-control" required placeholder="ตั้งรหัสผ่าน">
                    </div>
                    <div class="form-group">
                        <label>บทบาท (Role)</label>
                        <select name="role" class="form-control">
                            <option value="user">User (ผู้ใช้งานทั่วไป/ผู้แจ้งซ่อม)</option>
                            <option value="staff">Staff (ช่างไอที Support)</option>
                            <option value="admin">Admin (ผู้ดูแลระบบ)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">ยืนยันการสมัคร</button>
                </form>

                <p style="text-align: center; margin-top: 20px; font-size: 0.875rem; color: #64748b;">
                    มีบัญชีอยู่แล้ว? <a href="index.php?module=support&action=login" style="color: #4f46e5; text-decoration: none; font-weight: 600;">เข้าสู่ระบบ</a>
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}