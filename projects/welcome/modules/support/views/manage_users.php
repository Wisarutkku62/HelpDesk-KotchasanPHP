<?php
namespace Support\Index;

class UsersView
{
    public function render($request, $users = [], $loginUser = [], $msg = '', $isSuccess = false)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>จัดการผู้ใช้งาน - IT Support Admin</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-container">
                <div class="support-header">
                    <h1 class="support-title">จัดการผู้ใช้งานในระบบ</h1>
                    <a href="index.php?module=support" class="btn btn-secondary">กลับหน้าหลัก</a>
                </div>

                <?php if (!empty($msg)): ?>
                    <div class="<?php echo $isSuccess ? 'alert-success' : 'alert-error'; ?>">
                        <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endif; ?>

                <table class="support-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>ชื่อผู้ใช้ (Username)</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th class="th-role">บทบาท (Role)</th>
                            <th class="th-reset-pwd">รีเซ็ตรหัสผ่าน</th>
                            <th style="width: 80px;" class="text-center">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong>#<?php echo $u['id']; ?></strong></td>
                                    <td><code><?php echo htmlspecialchars($u['username']); ?></code></td>
                                    <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                                    <td>
                                        <?php if ($u['id'] == $loginUser['id']): ?>
                                            <span class="role-badge role-<?php echo $u['role']; ?>"><?php echo strtoupper($u['role']); ?></span>
                                            <small class="current-user-tag">(ตัวคุณเอง)</small>
                                        <?php else: ?>
                                            <form method="post" action="index.php?module=support&action=update_user_role" class="form-inline">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <select name="role" class="filter-select role-select" onchange="this.form.submit()">
                                                    <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>USER</option>
                                                    <option value="staff" <?php echo $u['role'] === 'staff' ? 'selected' : ''; ?>>STAFF</option>
                                                    <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>ADMIN</option>
                                                </select>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="post" action="index.php?module=support&action=reset_user_password" class="reset-pwd-form">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <input type="password" name="new_password" class="form-control input-reset-pwd" placeholder="รหัสใหม่" required>
                                            <button type="submit" class="btn btn-secondary btn-reset-pwd">ยืนยัน</button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u['id'] != $loginUser['id']): ?>
                                            <a href="index.php?module=support&action=delete_user&id=<?php echo $u['id']; ?>" class="btn-delete" onclick="return confirm('ยืนยันลบผู้ใช้งาน <?php echo htmlspecialchars($u['username']); ?> ?')">ลบ</a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}