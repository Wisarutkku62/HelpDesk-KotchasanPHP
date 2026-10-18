<?php
namespace Support\Index;

class LoginView
{
    public function render($error = '')
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>เข้าสู่ระบบ - IT Support</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body style="background: #f8fafc;">
            <div class="auth-container">
                <h1 class="auth-title">🔑 เข้าสู่ระบบ IT Support</h1>

                <?php if (!empty($error)): ?>
                    <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="index.php?module=support&action=do_login" method="post">
                    <div class="form-group">
                        <label>ชื่อผู้ใช้งาน (Username)</label>
                        <input type="text" name="username" class="form-control" required autofocus placeholder="กรอกชื่อผู้ใช้">
                    </div>
                    <div class="form-group">
                        <label>รหัสผ่าน (Password)</label>
                        <input type="password" name="password" class="form-control" required placeholder="กรอกรหัสผ่าน">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">เข้าสู่ระบบ</button>
                </form>

                <p style="text-align: center; margin-top: 20px; font-size: 0.875rem; color: #64748b;">
                    ยังไม่มีบัญชี? <a href="index.php?module=support&action=register" style="color: #4f46e5; text-decoration: none; font-weight: 600;">สมัครสมาชิก</a>
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}