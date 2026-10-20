<?php
namespace Support\Index;

class AddView
{
    public function render($request, $categories = [], $assets = [])
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>เพิ่มรายการแจ้งซ่อม</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="support-form-container">
                <div class="support-header">
                    <h1 class="support-title">เพิ่มรายการแจ้งซ่อมใหม่</h1>
                </div>
                
                <form action="index.php?module=support&action=save" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">หัวข้อแจ้งซ่อม <span class="required-asterisk">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="ระบุปัญหา เช่น เครื่องพิมพ์ใช้งานไม่ได้" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label class="form-label">หมวดหมู่ปัญหา <span class="required-asterisk">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">กรุณาเลือกหมวดหมู่ปัญหา</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">อุปกรณ์ / ทรัพย์สินชำรุด (ถ้ามี)</label>
                        <select name="asset_id" class="form-control">
                            <option value="">ไม่ระบุ / อุปกรณ์ทั่วไป</option>
                            <?php if (!empty($assets)): ?>
                                <?php foreach ($assets as $ast): ?>
                                    <option value="<?php echo $ast['id']; ?>">
                                        [<?php echo htmlspecialchars($ast['asset_code']); ?>] <?php echo htmlspecialchars($ast['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">รายละเอียดปัญหา</label>
                        <textarea name="detail" class="form-control" rows="4" placeholder="อธิบายอาการเพิ่มเติม..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">แนบรูปภาพประกอบ (อุปกรณ์ชำรุด)</label>
                        <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                        <small class="form-hint">รองรับไฟล์ .jpg, .jpeg, .png, .webp (ไม่เกิน 5MB)</small>
                    </div>

                    <div class="form-actions">
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