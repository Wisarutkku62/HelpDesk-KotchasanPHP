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
        <body class="auth-body">
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
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">ยืนยันการสมัคร</button>
                </form>

                <p class="auth-footer">
                    มีบัญชีอยู่แล้ว? <a href="index.php?module=support&action=login" class="auth-link">เข้าสู่ระบบ</a>
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}