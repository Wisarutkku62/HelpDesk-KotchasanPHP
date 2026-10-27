## ระบบแจ้งซ่อม IT Support พัฒนาด้วย **Kotchasan PHP Framework** และ **SQLite Database** ออกแบบด้วยสถาปัตยกรรมแบบ **Modular MVC** 

## จัดทำโดย

| ชื่อ | มหาวิทยาลัย | คณะ |  สาขา | ชั้นปี | 
|---|---|---|---|---|
| วิศรุต เครือลุน| มหาวิทยาลัยขอนแก่น | วิทยาลัยการคอมพิวเตอร์ |  สาขาเทคโนโลยีสารสนเทศ |ชั้นปีที่ 2|


##  คุณสมบัติเด่นของระบบ 

###  1. ระบบสิทธิ์และการจัดการผู้ใช้งาน 
* **3 สิทธิ์การใช้งาน:**
  * `USER` (ผู้ใช้งานทั่วไป): แจ้งซ่อม ติดตามสถานะ ดู/แก้ไขตั๋วของตนเอง
  * `STAFF` (ช่างไอที): รับเรื่อง ดำเนินการ และปิดงานซ่อม
  * `ADMIN` (ผู้ดูแลระบบ): จัดการสิทธิ์ผู้ใช้งาน รีเซ็ตรหัสผ่าน และลบข้อมูลตั๋ว/ผู้ใช้
* **Authentication Guard & Session Management:** ป้องกันการเข้าถึงหน้าจัดการหากไม่ได้เข้าสู่ระบบ
* **จัดการโปรไฟล์:** ผู้ใช้งานสามารถเปลี่ยนชื่อและรหัสผ่านของตนเองได้
* ###  2. ระบบตั๋วแจ้งซ่อม 
* **แบบฟอร์มแจ้งซ่อม:** รองรับหัวข้อ รายละเอียด หมวดหมู่ปัญหากรองตามประเภท และการแนบรูปภาพประกอบ (.jpg, .png, .webp)
* **ระบบติดตามสถานะ:**
  * 🟡 `pending` (รอดำเนินการ)
  * 🔵 `in_progress` (กำลังดำเนินการ)
  * 🟢 `completed` (เสร็จสิ้น)
* **ระบบค้นหาและตัวกรองข้อมูล (Filter Bar):** ค้นหาด้วยคำสำคัญ (Keyword), กรองตามหมวดหมู่ หรือกรองตามสถานะงาน
 ###  3. ระบบทรัพย์สิน & อุปกรณ์ 
* ผูกรายการแจ้งซ่อมกับอุปกรณ์/ทรัพย์สินองค์กรผ่าน `asset_id`
* แสดงป้ายรหัสทรัพย์สิน (`[AST-XXX]`) ในหน้า Dashboard และหน้ารายละเอียดตั๋ว
* ค้นหารายการแจ้งซ่อมย้อนหลังตามรหัสทรัพย์สินหรือชื่ออุปกรณ์ได้

###  4. ระบบโต้ตอบ & บันทึกประวัติการซ่อม 
* แสดงประวัติการติดตามงานและโต้ตอบระหว่างผู้แจ้งและทีมช่างแบบ Chronological Timeline
* แสดงป้ายบอกบทบาทของผู้ตอบข้อความ (`ADMIN`, `STAFF`, `USER`) เพื่อความชัดเจน

###  5. สรุปรายงาน & สถิติภาพรวม 
* **Stat Cards (4 ช่อง):** สรุปจำนวนรายการทั้งหมด, รอดำเนินการ, กำลังดำเนินการ และเสร็จสิ้น แบบ Real-time
* จัดรูปการแสดงผลวันที่เป็นรูปแบบไทย (เช่น `21 ต.ค. 2569 00:00 น.`)

---
## เทคโนโลยีที่ใช้ 

* **Language:** PHP 7.4+ / PHP 8.x
* **Framework:** Kotchasan PHP Framework
* **Database:** SQLite3 (จัดเก็บในรูปแบบไฟล์ `datas/helpdesk.sql`)
* **Frontend:** HTML5, Modern CSS3 (CSS Variables, Flexbox, CSS Grid)
* **Typography:** Google Fonts ('Prompt')

---
## โครงสร้างโปรเจกต์ 

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

## วิธีการติดตั้งและรันโปรแกรม (How to Run the Application)

สามารถเลือกวิธีรันโปรแกรมได้ 2 รูปแบบ ทั้งการใช้ **Local Server Package (XAMPP / Laragon / WAMP)** หรือการใช้ **PHP Built-in Development Server** โดยไม่ต้องติดตั้งระบบฐานข้อมูลแยกต่างหาก เนื่องจากระบบใช้ SQLite 3

---

###  1. ความต้องการของระบบ (System Requirements)

* **PHP:** เวอร์ชั่น 7.4 ขึ้นไป หรือ PHP 8.x
* **PHP Extensionsที่ต้องเปิดใช้งาน:**
  * `pdo_sqlite`
  * `sqlite3`
  * `fileinfo` (สำหรับการอัปโหลดรูปภาพ)

---

###  2. การเตรียมสภาพแวดล้อม (PHP Configuration)

ก่อนรันโปรแกรม กรุณาตรวจสอบให้แน่ใจว่าได้เปิดใช้งาน Extension สำคัญในไฟล์ `php.ini` เรียบร้อยแล้ว:

1. เปิดไฟล์ `php.ini` ในเครื่องของคุณ
2. ค้นหาบรรทัดด้านล่าง และเอาเครื่องหมาย `;` ด้านหน้าออก (Uncomment):
   
   ```ini
   extension=pdo_sqlite
   extension=sqlite3
   extension=fileinfo

 ### 3. วิธีการรันโปรแกรม
 
### วิธีที่ 1: รันผ่าน Local Web Server (XAMPP / Laragon / WAMP)
คัดลอกโฟลเดอร์โปรเจกต์ ไปวางในโฟลเดอร์ Web Root ของคุณ:

- XAMPP: C:/xampp/htdocs/

- Laragon: C:/laragon/www/

- WampServer: C:/wamp64/www/

ตรวจสอบสิทธิ์โฟลเดอร์ (Permissions):

ตรวจสอบให้แน่ใจว่าโฟลเดอร์ datas/ และ datas/uploads/ สามารถอ่าน-เขียนไฟล์ได้

เปิดเว็บเบราว์เซอร์ แล้วเข้าไปที่ URL:
```ini

http://localhost/index.php?module=support
```

### วิธีที่ 2: รันผ่าน PHP Built-in Server (ไม่ต้องใช้ XAMPP)

- เปิด Terminal หรือ Command Prompt (cmd)

- ชี้ Path ไปยัง Directory หลักของโปรเจกต์:
```ini

cd /path/to/your/project
```
- พิมพ์คำสั่งเริ่มการรัน Web Server:
```ini
php -S localhost:8000
```
- เปิดเว็บเบราว์เซอร์ แล้วเข้าไปที่ URL:
```ini
http://localhost:8000/index.php?module=support
```

##  โครงสร้างฐานข้อมูล (Database Schema)

<img width="1048" height="940" alt="er diagram itsupport" src="https://github.com/user-attachments/assets/77ca8540-ddf4-45fb-b3d6-60e969910038" />

## อธิบายโครงสร้าง
| ตารางฝั่งหลัก (1) | 	ตารางฝั่งอ้างอิง (N) | 	ชนิดความสัมพันธ์ | ฟิลด์ที่ใช้เชื่อม (FK) | การทำงานในระบบ |
|---|---|---|---|---|
departments |	users |	1 : N |	users.department_id |	1 แผนก มีผู้ใช้งานสังกัดได้หลายคน แต่ผู้ใช้งาน 1 คน จะสังกัดได้เพียง 1 แผนก |
users	| tickets |	1 : N |	tickets.user_id	| ผู้ใช้ 1 คน สามารถเปิดแจ้งซ่อมได้หลายตั๋ว แต่ตั๋วแจ้งซ่อม 1 ใบ ถูกสร้างโดยผู้แจ้งคนเดียว |
categories |	tickets |	1 : N	| tickets.category_id |	1 หมวดหมู่ปัญหา มีตั๋วแจ้งซ่อมระบุเข้ามาได้หลายใบ แต่ตั๋ว 1 ใบ ถูกจัดไว้เพียง 1 หมวดหมู่ |
assets |	tickets |	1 : N |	tickets.asset_id |	อุปกรณ์ 1 ชิ้น แจ้งซ่อมได้หลายครั้ง/หลายตั๋ว แต่ตั๋ว 1 ใบ จะอ้างอิงอุปกรณ์ที่เสียได้ 1 ชิ้น |
tickets |	ticket_comments |	1 : N |	ticket_comments.ticket_id |	ตั๋วแจ้งซ่อม 1 ใบ มีคอมเมนต์โต้ตอบได้หลายข้อความ แต่คอมเมนต์ 1 ข้อความ จะผูกอยู่กับตั๋วใบเดียว |
users |	ticket_comments |	1 : N |	ticket_comments.user_id |	ผู้ใช้ 1 คน สามารถพิมพ์ตอบคอมเมนต์ได้หลายข้อความ แต่คอมเมนต์ 1 ข้อความ ถูกพิมพ์โดยคนเดียว |

  
