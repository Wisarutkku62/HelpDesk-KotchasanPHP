<?php
namespace Support\Index;

class AddView
{
    public function render($request)
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>เพิ่มรายการแจ้งซ่อม</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-form-container">
                <div class="support-header">
                    <h1 class="support-title"> เพิ่มรายการแจ้งซ่อมใหม่</h1>
                </div>
                <form action="index.php?module=support&action=save" method="post">
                    <div class="form-group">
                        <label class="form-label">หัวข้อแจ้งซ่อม <span style="color: #ee5d50;">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="ระบุปัญหา เช่น เครื่องพิมพ์ใช้งานไม่ได้" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">รายละเอียดปัญหา</label>
                        <textarea name="detail" class="form-control" rows="4" placeholder="อธิบายอาการเพิ่มเติม..."></textarea>
                    </div>
                    <div style="display: flex; gap: 12px; margin-top: 28px;">
                        <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
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