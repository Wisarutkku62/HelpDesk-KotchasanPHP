<?php
namespace Support\Index;

use Kotchasan\Http\Request;

class AddView extends \Kotchasan\View
{
    public function render(Request $request)
    {
        ob_start();
        ?>

        <link rel="stylesheet" href="css/style.css">

        <div class="support-form-container">
            <h2 class="support-title"> สร้างรายการแจ้งซ่อมใหม่</h2>
            
            <form method="post" action="index.php?module=support&action=save">
                <div class="form-group">
                    <label class="form-label">หัวข้อแจ้งซ่อม <span style="color:red;">*</span></label>
                    <input type="text" name="title" required placeholder="เช่น ปริ้นเตอร์กระดาษติด, เปิดคอมไม่ติด" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">รายละเอียดปัญหา</label>
                    <textarea name="detail" rows="4" placeholder="อธิบายอาการเสียเพิ่มเติม..." class="form-control"></textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button type="submit" class="btn btn-primary">บันทึกแจ้งซ่อม</button>
                    <a href="index.php?module=support" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>

        <?php
        return ob_get_clean();
    }
}