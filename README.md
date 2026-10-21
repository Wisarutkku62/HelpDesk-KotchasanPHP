 🛠️ IT Helpdesk Support System (ระบบแจ้งซ่อมองค์กร)

ระบบแจ้งซ่อม IT Support พัฒนาด้วย **Kotchasan PHP Framework** และ **SQLite Database** ออกแบบด้วยสถาปัตยกรรมแบบ **Modular MVC** เน้นความเบา มั่นคง และ UI ที่สะอาดสบายตา 

##  คุณสมบัติเด่นของระบบ (Key Features)

###  1. ระบบสิทธิ์และการจัดการผู้ใช้งาน (Role-Based Access Control)
* **3 สิทธิ์การใช้งาน:**
  * `USER` (ผู้ใช้งานทั่วไป): แจ้งซ่อม ติดตามสถานะ ดู/แก้ไขตั๋วของตนเอง
  * `STAFF` (ช่างไอที): รับเรื่อง ดำเนินการ และปิดงานซ่อม
  * `ADMIN` (ผู้ดูแลระบบ): จัดการสิทธิ์ผู้ใช้งาน รีเซ็ตรหัสผ่าน และลบข้อมูลตั๋ว/ผู้ใช้
* **Authentication Guard & Session Management:** ป้องกันการเข้าถึงหน้าจัดการหากไม่ได้เข้าสู่ระบบ
* **จัดการโปรไฟล์:** ผู้ใช้งานสามารถเปลี่ยนชื่อและรหัสผ่านของตนเองได้
* ###  2. ระบบตั๋วแจ้งซ่อม (Ticket Management System)
* **แบบฟอร์มแจ้งซ่อม:** รองรับหัวข้อ รายละเอียด หมวดหมู่ปัญหากรองตามประเภท และการแนบรูปภาพประกอบ (.jpg, .png, .webp)
* **ระบบติดตามสถานะ:**
  * 🟡 `pending` (รอดำเนินการ)
  * 🔵 `in_progress` (กำลังดำเนินการ)
  * 🟢 `completed` (เสร็จสิ้น)
* **ระบบค้นหาและตัวกรองข้อมูล (Filter Bar):** ค้นหาด้วยคำสำคัญ (Keyword), กรองตามหมวดหมู่ หรือกรองตามสถานะงาน
 ###  3. ระบบทรัพย์สิน & อุปกรณ์ (Asset Management)
* ผูกรายการแจ้งซ่อมกับอุปกรณ์/ทรัพย์สินองค์กรผ่าน `asset_id`
* แสดงป้ายรหัสทรัพย์สิน (`[AST-XXX]`) ในหน้า Dashboard และหน้ารายละเอียดตั๋ว
* ค้นหารายการแจ้งซ่อมย้อนหลังตามรหัสทรัพย์สินหรือชื่ออุปกรณ์ได้

###  4. ระบบโต้ตอบ & บันทึกประวัติการซ่อม (Timeline Comments)
* แสดงประวัติการติดตามงานและโต้ตอบระหว่างผู้แจ้งและทีมช่างแบบ Chronological Timeline
* แสดงป้ายบอกบทบาทของผู้ตอบข้อความ (`ADMIN`, `STAFF`, `USER`) เพื่อความชัดเจน

###  5. สรุปรายงาน & สถิติภาพรวม (Dashboard Analytics)
* **Stat Cards (4 ช่อง):** สรุปจำนวนรายการทั้งหมด, รอดำเนินการ, กำลังดำเนินการ และเสร็จสิ้น แบบ Real-time
* จัดรูปการแสดงผลวันที่เป็นรูปแบบไทย (เช่น `21 ต.ค. 2569 00:00 น.`)

---
## 🛠️ เทคโนโลยีที่ใช้ (Tech Stack)

* **Language:** PHP 7.4+ / PHP 8.x
* **Framework:** Kotchasan PHP Framework
* **Database:** SQLite3 (จัดเก็บในรูปแบบไฟล์ `datas/helpdesk.sql`)
* **Frontend:** HTML5, Modern CSS3 (CSS Variables, Flexbox, CSS Grid)
* **Typography:** Google Fonts ('Prompt')

---
## 📂 โครงสร้างโปรเจกต์ (Directory Structure)

โปรเจกต์ได้รับการปรับปรุงสถาปัตยกรรมเป็นแบบ **Modular Controller & Model** เพื่อให้อ่านง่าย แก้ไขตรงจุด และไม่เกิดความสับสนเมื่อระบบขยายตัว

```text
projects/welcome/
├── css/
│   └── style.css                 # CSS 
└── modules/support/
    ├── controllers/              # Controller ตามกลุ่มหน้าที่
    │   ├── index.php             # Main Router / Dispatcher
    │   ├── auth.php              # Login, Register, Logout
    │   ├── profile.php           # ดู/แก้ไขโปรไฟล์ส่วนตัว
    │   ├── user.php              # จัดการสิทธิ์และสมาชิกระบบ (Admin Only)
    │   └── ticket.php            # Dashboard, CRUD ตั๋วซ่อม, คอมเมนต์โต้ตอบ
    ├── models/                   # Model ตามตาราง entrity (SQLite)
    │   ├── db.php                # เชื่อมต่อ SQLite Database, Create Tables & Seeders
    │   ├── user.php              # SQL จัดการตาราง users
    │   ├── ticket.php            # SQL จัดการตาราง tickets & statistic
    │   ├── comment.php           # SQL จัดการตาราง ticket_comments
    │   ├── asset.php             # SQL จัดการตาราง assets
    │   ├── category.php          # SQL จัดการตาราง categories & departments
    │   └── index.php             # Model Facade รวมทุก Sub-model
    └── views/                    # หน้าแสดงผล HTML
        ├── index.php             # Dashboard หน้าหลัก & รายการตั๋วซ่อม
        ├── add.php               # ฟอร์มเพิ่มรายการแจ้งซ่อมใหม่
        ├── detail.php            # หน้ารายละเอียดตั๋วซ่อม & Timeline โต้ตอบ
        ├── login.php             # หน้าเข้าสู่ระบบ
        ├── register.php          # หน้าสมัครสมาชิก
        ├── profile.php           # หน้าแก้ไขโปรไฟล์
        └── manage_users.php      # หน้าจัดการผู้ใช้งาน (Admin)
```
##  การติดตั้งและใช้งาน (Installation Guide)

1. **คัดลอกไฟล์โปรเจกต์** ไปไว้ในไดเรกทอรีของ Web Server (เช่น `htdocs` สำหรับ XAMPP/Laragon หรือ `www` สำหรับ WampServer)
2. **ตรวจสอบการเปิดใช้งาน PHP SQLite Extension** ในไฟล์ `php.ini` (โดยปกติระบบจำลองส่วนใหญ่จะเปิดใช้งานไว้แล้ว):
   ```ini
   extension=sqlite3
   extension=pdo_sqlite

##  บัญชีผู้ใช้ทดสอบเริ่มต้น (Default Accounts)

รหัสผ่านถูกเข้ารหัสความปลอดภัยด้วย `password_hash()` มาตรฐาน PHP (`PASSWORD_DEFAULT`):

| Username | Password | ชื่อ - นามสกุล | บทบาท (`role`) | สิทธิ์การใช้งานระบบ |
| :--- | :--- | :--- | :---: | :--- |
| `admin` | `admin1234` | ผู้ดูแลระบบ (Admin) | `admin` | สิทธิ์สูงสุด: จัดการผู้ใช้, รีเซ็ตรหัสผ่าน, ลบตั๋วแจ้งซ่อม |
| `staff` | `staff1234` | ช่างไอที (Staff) | `staff` | สิทธิ์เจ้าหน้าที่: กดรับเรื่อง (`in_progress`), ปิดงาน (`completed`), ตอบคอมเมนต์ |
| `user` | `user1234` | สมหมาย นะจ๊ะ | `user` | สิทธิ์ผู้ใช้ทั่วไป: สร้างตั๋วแจ้งซ่อม, ติดตามสถานะตั๋วตนเอง, ตอบคอมเมนต์ |

---

##  โครงสร้างฐานข้อมูล (Database Schema)

ฐานข้อมูล SQLite (`datas/helpdesk.sql`) ประกอบด้วย 6 ตารางหลัก ดังนี้:


  
