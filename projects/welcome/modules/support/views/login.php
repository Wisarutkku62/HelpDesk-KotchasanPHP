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
        <body class="auth-body">
            <div class="auth-container">
                <h1 class="auth-title">เข้าสู่ระบบ IT Support</h1>

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
                    <button type="submit" class="btn btn-primary btn-submit">เข้าสู่ระบบ</button>
                </form>

                <p class="auth-footer">
                    ยังไม่มีบัญชี? <a href="index.php?module=support&action=register" class="auth-link">สมัครสมาชิก</a>
                </p>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}