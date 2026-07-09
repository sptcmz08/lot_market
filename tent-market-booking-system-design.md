# เอกสารออกแบบระบบจองล็อคและติดตั้งเต็นท์ตลาด

> แนวทางสำหรับสร้างระบบจริงด้วย **Laravel + MySQL + JavaScript + SVG Map + PWA**  
> เน้นใช้งานบนมือถือเป็นหลัก รองรับ Phone / iPad / PC  
> UX/UI โทนน่ารัก ใช้ง่าย เห็นเลขล็อคชัด ลดการส่งเต็นท์ผิดล็อค

---

## 1. สรุปภาพรวมระบบ

ระบบนี้เป็น Web App สำหรับร้านให้เช่าเต็นท์ในตลาด โดยมีหน้าบ้านให้ลูกค้าเลือกล็อคจากแผนที่ตลาด กรอกข้อมูลจอง และมีหลังบ้านให้ Admin ยืนยันงาน จากนั้นพนักงานส่งเต็นท์เปิดงานผ่านมือถือ เห็นเลขล็อคชัดเจน ถ่ายรูปยืนยันก่อน/หลังติดตั้ง และส่งสถานะกลับเข้าหลังบ้าน

ระบบเวอร์ชันแรก **ยังไม่ใช้ OCR** เพื่อให้ทำได้ไว ใช้งานง่าย และเหมาะกับ Hosting Plesk ทั่วไป แต่จะออกแบบโครงสร้างฐานข้อมูลเผื่อเพิ่ม OCR ภายหลังได้

### เป้าหมายหลัก

1. ลูกค้าดูแผนที่ตลาดและเลือกล็อคได้เอง
2. ลูกค้าดูสถานะว่าว่าง / จองแล้ว / รอยืนยันได้จากแผนที่
3. Admin ยืนยันการจองและมอบหมายงานให้พนักงานได้
4. พนักงานเปิดมือถือแล้วเห็นงานวันนี้พร้อมเลขล็อคตัวใหญ่
5. พนักงานถ่ายรูปเลขล็อคและรูปหลังติดตั้งเพื่อยืนยันงาน
6. ลูกค้าตรวจสอบสถานะติดตั้งได้จากหน้าบ้าน
7. ระบบใช้งานได้ทุกอุปกรณ์โดยไม่ต้องติดตั้งแอปจริง

---

## 2. ภาษาที่ใช้และเทคโนโลยีที่แนะนำ

### Stack หลัก

```txt
Backend Framework: Laravel
Backend Language: PHP
Database: MySQL หรือ MariaDB
Frontend: Blade + JavaScript + Alpine.js หรือ Livewire
Map: SVG Interactive Map
CSS/UI: Tailwind CSS หรือ Bootstrap 5 + Custom CSS
Mobile App Style: PWA
Image Upload: Laravel Storage
Hosting: Plesk Linux Hosting
```

### เหตุผลที่เลือก Stack นี้

| ส่วน | เทคโนโลยี | เหตุผล |
|---|---|---|
| Backend | Laravel / PHP | เหมาะกับ Plesk Hosting ทั่วไป ดูแลง่าย ทำระบบสมาชิก/หลังบ้านเร็ว |
| Database | MySQL/MariaDB | Hosting Plesk รองรับทั่วไป สำรองข้อมูลง่าย |
| Frontend | Blade + JavaScript | ไม่ซับซ้อน โหลดไว ทำหน้ามือถือได้ง่าย |
| Interactive Map | SVG | แต่ละล็อคกดได้ เปลี่ยนสีสถานะได้ เหมาะกับแผนผังตลาด |
| PWA | Web App | เปิดผ่านมือถือเหมือนแอป ไม่ต้องลง Play Store / App Store |
| รูปยืนยัน | HTML Camera Input | มือถือถ่ายรูปแล้วอัปโหลดเข้า Laravel ได้ทันที |

### สิ่งที่ยังไม่ใช้ในเฟสแรก

```txt
Google Vision OCR = ยังไม่ใช้
Native Android/iOS App = ยังไม่ใช้
Realtime WebSocket = ยังไม่จำเป็น
Payment Gateway = ยังไม่จำเป็น ถ้ายังจองผ่าน Admin ยืนยันเอง
```

---

## 3. บทบาทผู้ใช้งาน

ระบบมี 4 บทบาทหลัก

### 3.1 ลูกค้า / Customer

ลูกค้าไม่จำเป็นต้องสมัครสมาชิกในเฟสแรก สามารถจองผ่านฟอร์มได้ทันที โดยกรอกชื่อร้าน เบอร์โทร วันที่ใช้งาน และเลือกล็อคจากแผนที่

สิ่งที่ลูกค้าทำได้:

- เลือกวันที่ใช้งาน
- ดูแผนที่ตลาด
- กดล็อคเพื่อดูสถานะ
- จองล็อค
- ดูสถานะการจองจากรหัสจองหรือเบอร์โทร
- ดูรูปยืนยันเมื่อติดตั้งเสร็จแล้ว หาก Admin อนุญาตให้แสดง

### 3.2 Admin

Admin คือเจ้าของร้านหรือผู้ดูแลระบบ

สิ่งที่ Admin ทำได้:

- ดู Dashboard งานทั้งหมด
- ดูรายการจองใหม่
- ยืนยัน / ปฏิเสธ / แก้ไขการจอง
- แก้ไขข้อมูลล็อคในแผนที่
- มอบหมายงานให้พนักงาน
- ตรวจรูปถ่ายยืนยัน
- เปลี่ยนสถานะงาน
- ดูรายงานรายวัน / รายเดือน
- จัดการผู้ใช้งานพนักงาน

### 3.3 Staff / พนักงานส่งเต็นท์

พนักงานใช้มือถือเป็นหลัก

สิ่งที่พนักงานทำได้:

- Login เข้าหน้างานของตัวเอง
- ดูงานติดตั้งวันนี้
- ดูเลขล็อคตัวใหญ่
- ดูแผนที่ที่ Highlight ล็อคเป้าหมาย
- กดเริ่มติดตั้ง
- ถ่ายรูปเลขล็อค / รูปก่อนติดตั้ง
- ถ่ายรูปหลังติดตั้ง
- กดส่งงาน
- แจ้งปัญหาพร้อมรูปถ่าย

### 3.4 Viewer / ผู้ตรวจสอบงาน

ถ้าต้องการในอนาคต อาจมี role สำหรับผู้จัดการที่ดูรายงานได้อย่างเดียว แต่แก้ไขข้อมูลไม่ได้

---

## 4. Flow การทำงานหลัก

### 4.1 Flow ลูกค้าจองล็อค

```txt
ลูกค้าเปิดเว็บ
↓
เลือกวันที่ใช้งาน
↓
ระบบโหลดสถานะล็อคของวันนั้น
↓
ลูกค้ากดล็อคบนแผนที่
↓
ระบบแสดงข้อมูลล็อค
↓
ถ้าว่าง กด “จองล็อคนี้”
↓
กรอกข้อมูลร้าน / เบอร์ / เต็นท์ / เคาน์เตอร์
↓
ส่งคำขอจอง
↓
ระบบสร้าง Booking สถานะ “รอแอดมินยืนยัน”
↓
แสดงเลขอ้างอิงการจองให้ลูกค้า
```

### 4.2 Flow Admin ยืนยันงาน

```txt
Admin เข้าหลังบ้าน
↓
เห็นรายการจองใหม่
↓
เปิดดูรายละเอียด
↓
ตรวจล็อค วันที่ ขนาดเต็นท์ ขนาดเคาน์เตอร์
↓
กดยืนยันการจอง
↓
ระบบเปลี่ยนสถานะเป็น “จองแล้ว / รอจัดส่ง”
↓
เลือกพนักงานผู้รับผิดชอบ
↓
ระบบสร้าง Delivery Task
```

### 4.3 Flow พนักงานติดตั้งเต็นท์

```txt
พนักงาน Login ผ่านมือถือ
↓
เห็นงานวันนี้
↓
กดเปิดงาน
↓
เห็นเลขล็อคตัวใหญ่ เช่น GB50-52
↓
กดดูแผนที่ ระบบ Highlight ล็อคเป้าหมาย
↓
กดเริ่มติดตั้ง
↓
ถ่ายรูปเลขล็อค / รูปหน้างาน
↓
ติดตั้งเต็นท์
↓
ถ่ายรูปหลังติดตั้ง
↓
กดส่งงาน
↓
ระบบเปลี่ยนสถานะเป็น “ติดตั้งเสร็จแล้ว”
↓
Admin และลูกค้าเห็นสถานะล่าสุด
```

### 4.4 Flow แจ้งปัญหา

```txt
พนักงานเปิดงาน
↓
พบปัญหา เช่น ล็อคมีร้านอื่นอยู่ / หาเลขไม่เจอ / ลูกค้าเปลี่ยนจุด
↓
กด “แจ้งปัญหา”
↓
เลือกประเภทปัญหา
↓
ถ่ายรูปประกอบ
↓
ส่งให้ Admin
↓
ระบบเปลี่ยนสถานะเป็น “มีปัญหา / รอ Admin ตรวจ”
```

---

## 5. UX/UI Design Concept

### 5.1 Mood & Tone

ระบบควรดูเป็นมิตร น่ารัก และใช้ง่าย ไม่ควรเป็นระบบหลังบ้านแข็ง ๆ แบบองค์กร เพราะกลุ่มใช้งานคือร้านตลาด ลูกค้า และพนักงานที่ใช้มือถือหน้างาน

แนวทางภาพรวม:

```txt
น่ารัก
สดใส
อ่านง่าย
ปุ่มใหญ่
สีสถานะชัด
ใช้ icon ช่วยจำ
โค้งมน
ตัวอักษรใหญ่บนมือถือ
```

### 5.2 สีหลักของระบบ

แนะนำใช้โทน Pastel + สีสถานะชัด

```txt
Primary: #FF8FB1  ชมพูพาสเทล
Secondary: #8BD3DD  ฟ้าอ่อน
Accent: #FFD166  เหลืองสดใส
Background: #FFF7FA  ชมพูอ่อนมาก
Card: #FFFFFF
Text: #2F2F37
Muted Text: #7A7A85
Border: #F1DDE5
```

สีสถานะล็อค:

```txt
ว่าง = #6FD08C เขียว
รอแอดมินยืนยัน = #FFD166 เหลือง
จองแล้ว = #FF6B6B แดง
รอจัดส่ง = #8BD3DD ฟ้า
กำลังติดตั้ง = #A78BFA ม่วงอ่อน
ติดตั้งเสร็จแล้ว = #4ECDC4 เขียวอมฟ้า
มีปัญหา = #FF9F1C ส้ม
ปิดใช้งาน = #BDBDBD เทา
```

### 5.3 Font

แนะนำใช้ฟอนต์ไทยอ่านง่าย เช่น

```txt
Noto Sans Thai
Prompt
Sarabun
```

ถ้าต้องการน่ารักและทันสมัย แนะนำ **Prompt** หรือ **Noto Sans Thai**

### 5.4 รูปแบบปุ่ม

ปุ่มควรใหญ่และกดง่ายบนมือถือ

```css
border-radius: 18px;
padding: 14px 18px;
font-size: 16px;
font-weight: 600;
box-shadow: 0 8px 18px rgba(255, 143, 177, 0.18);
```

ตัวอย่างชื่อปุ่ม:

```txt
จองล็อคนี้
ยืนยันการจอง
เริ่มติดตั้ง
ถ่ายรูปเลขล็อค
ถ่ายรูปหลังติดตั้ง
ส่งงาน
แจ้งปัญหา
```

### 5.5 Card Style

ทุกข้อมูลควรอยู่ใน Card ขาว โค้งมน เงาอ่อน

```css
background: #fff;
border-radius: 24px;
padding: 18px;
box-shadow: 0 10px 30px rgba(47, 47, 55, 0.08);
border: 1px solid #f3e3ea;
```

---

## 6. หน้าจอที่ต้องมี

## 6.1 หน้าลูกค้า: หน้าแรก / แผนที่จอง

### วัตถุประสงค์

ให้ลูกค้าเปิดมาแล้วเห็นแผนที่ตลาดทันที คลิกหรือแตะล็อคเพื่อดูสถานะและจองได้

### Layout มือถือ

```txt
┌────────────────────────────┐
│  โลโก้ร้าน / ชื่อระบบ      │
│  เช่าเต็นท์ตลาด             │
├────────────────────────────┤
│  เลือกวันที่ใช้งาน          │
│  [ 06/07/2569      📅 ]     │
├────────────────────────────┤
│  สถานะสี                    │
│  🟢 ว่าง  🟡 รอยืนยัน       │
│  🔴 จองแล้ว  🟣 ติดตั้งแล้ว │
├────────────────────────────┤
│                            │
│        SVG MAP             │
│    แตะล็อคเพื่อดูข้อมูล    │
│                            │
├────────────────────────────┤
│  Bottom Sheet เมื่อกดล็อค   │
│  ล็อค: GB50-52             │
│  สถานะ: ว่าง               │
│  [จองล็อคนี้]              │
└────────────────────────────┘
```

### Behavior

- บน PC: Hover ที่ล็อคแล้วแสดง Tooltip
- บนมือถือ: แตะล็อคแล้วเปิด Bottom Sheet
- ถ้าล็อคจองแล้ว ให้แสดงชื่อร้านได้ตามสิทธิ์ที่กำหนด เช่น แสดงเต็มใน Admin แต่หน้าลูกค้าอาจแสดงแค่ “จองแล้ว” หรือ “ร้าน: xxx” ตามนโยบาย

### ข้อมูลที่แสดงเมื่อกดล็อค

```txt
เลขล็อค: GB50-52
สถานะ: ว่าง / จองแล้ว / รอยืนยัน
วันที่ใช้: 06/07/2569
ชื่อร้าน: แสดงเมื่อจองแล้วถ้าอนุญาต
ปุ่ม: จองล็อคนี้ / ดูรายละเอียด
```

---

## 6.2 หน้าฟอร์มจอง

### Fields

```txt
วันที่ใช้
ชื่อร้าน
เบอร์โทร
ล็อคที่เลือก
ขนาดเต็นท์
- 1.5
- 2x2
- 2x3
- 2.5
- 3x4.5
ขนาดเคาน์เตอร์
- 2 ล็อค
- 3 ล็อค
หมายเหตุ
```

### Layout

```txt
┌────────────────────────────┐
│  จองล็อค GB50-52           │
│  วันที่ 06/07/2569          │
├────────────────────────────┤
│  ชื่อร้าน                   │
│  [____________________]     │
│  เบอร์โทร                   │
│  [____________________]     │
│  เต็นท์                     │
│  [ 2x2 ▼ ]                  │
│  เคาน์เตอร์                 │
│  [ 2 ล็อค ▼ ]               │
│  หมายเหตุ                   │
│  [____________________]     │
├────────────────────────────┤
│  [ส่งคำขอจอง]              │
└────────────────────────────┘
```

### Validation

```txt
ชื่อร้าน: required, max 150
เบอร์โทร: required, ตัวเลข 9-10 หลัก
วันที่ใช้: required, ห้ามย้อนหลัง
ล็อค: required, ต้องว่างในวันที่เลือก
เต็นท์: required
เคาน์เตอร์: optional หรือ required ตามนโยบาย
```

---

## 6.3 หน้าตรวจสอบสถานะสำหรับลูกค้า

ลูกค้ากรอกเบอร์โทรหรือรหัสจองเพื่อตรวจสถานะ

```txt
┌────────────────────────────┐
│  ตรวจสอบสถานะการจอง        │
├────────────────────────────┤
│  เบอร์โทร / รหัสจอง         │
│  [____________________]     │
│  [ค้นหา]                    │
└────────────────────────────┘
```

ผลลัพธ์:

```txt
ร้าน: โหยหอย หอยแครงลวกแกะ
วันที่ใช้: 06/07/2569
ล็อค: GB50-52
เต็นท์: 2x2
สถานะ: ติดตั้งเสร็จแล้ว
รูปยืนยัน: ดูรูป
```

---

## 6.4 Admin Dashboard

### เมนูหลัก

```txt
Dashboard
รายการจอง
แผนที่ล็อค
งานพนักงาน
รูปยืนยัน
รายงาน
ตั้งค่า
ผู้ใช้งาน
```

### Layout Dashboard

```txt
┌────────────────────────────┐
│  Dashboard วันนี้           │
├────────────────────────────┤
│  จองใหม่      12 รายการ     │
│  รอจัดส่ง      8 รายการ     │
│  กำลังติดตั้ง   3 รายการ     │
│  เสร็จแล้ว     20 รายการ     │
│  มีปัญหา       1 รายการ      │
├────────────────────────────┤
│  งานด่วนวันนี้               │
│  - GB50-52 ร้าน A            │
│  - GC10 ร้าน B               │
└────────────────────────────┘
```

### Card สรุปสถานะ

```txt
🌱 รอยืนยัน
⛺ รอจัดส่ง
🛠 กำลังติดตั้ง
✅ ติดตั้งเสร็จ
⚠️ มีปัญหา
```

---

## 6.5 Admin: รายการจอง

### Table บน PC

```txt
วันที่ | รหัสจอง | ร้าน | เบอร์ | ล็อค | เต็นท์ | สถานะ | จัดการ
```

### Card บนมือถือ

```txt
┌────────────────────────────┐
│  GB50-52                   │
│  ร้าน: โหยหอย              │
│  วันที่: 06/07/2569         │
│  เต็นท์: 2x2                │
│  สถานะ: รอยืนยัน            │
│  [ดูรายละเอียด] [ยืนยัน]    │
└────────────────────────────┘
```

### Actions

```txt
ดูรายละเอียด
แก้ไขข้อมูล
ยืนยันการจอง
ยกเลิกการจอง
สร้างงานส่งเต็นท์
มอบหมายพนักงาน
ดูรูปยืนยัน
```

---

## 6.6 Admin: แผนที่ล็อค

Admin สามารถดูแผนที่ทุกล็อคพร้อมสีสถานะ และกดแก้ไขแต่ละล็อคได้

ข้อมูลล็อค:

```txt
รหัสล็อค
โซน
ตำแหน่งบน SVG
เปิด/ปิดใช้งาน
หมายเหตุ
```

ฟังก์ชัน:

```txt
เพิ่มล็อค
แก้ไขล็อค
ปิดล็อคชั่วคราว
ดูประวัติการจองล็อคนั้น
```

---

## 6.7 หน้าพนักงาน: งานวันนี้

หน้านี้ต้องเน้นมือถือเป็นหลัก และต้องเห็นเลขล็อคเด่นมาก

```txt
┌────────────────────────────┐
│  สวัสดี คุณพนักงาน          │
│  งานติดตั้งวันนี้ 5 งาน      │
├────────────────────────────┤
│  CARD งาน                   │
│  ┌──────────────────────┐   │
│  │       GB50-52         │   │
│  │  ร้าน: โหยหอย         │   │
│  │  เต็นท์: 2x2          │   │
│  │  เคาน์เตอร์: 2 ล็อค   │   │
│  │  [เปิดงาน]            │   │
│  └──────────────────────┘   │
└────────────────────────────┘
```

### Design เฉพาะหน้างาน

- เลขล็อคต้องใหญ่ที่สุดใน Card
- ใช้สีพื้นหลังต่างกันตามสถานะ
- ปุ่มใหญ่ กดง่าย
- ลดข้อมูลที่ไม่จำเป็น
- ใช้ Sticky Bottom Action เช่น ปุ่ม “เริ่มติดตั้ง” / “ส่งงาน”

---

## 6.8 หน้าพนักงาน: รายละเอียดงาน

```txt
┌────────────────────────────┐
│  งานติดตั้ง                │
├────────────────────────────┤
│        GB50-52             │
│  เลขล็อคเป้าหมาย           │
├────────────────────────────┤
│  ร้าน: โหยหอย              │
│  เบอร์: 096xxx             │
│  เต็นท์: 2x2                │
│  เคาน์เตอร์: 2 ล็อค         │
├────────────────────────────┤
│  [ดูตำแหน่งบนแผนที่]       │
│  [เริ่มติดตั้ง]             │
│  [ถ่ายรูปเลขล็อค]           │
│  [ถ่ายรูปหลังติดตั้ง]       │
│  [ส่งงาน]                   │
│  [แจ้งปัญหา]                │
└────────────────────────────┘
```

### ข้อบังคับก่อนส่งงาน

```txt
ต้องมีรูปเลขล็อคอย่างน้อย 1 รูป
ต้องมีรูปหลังติดตั้งอย่างน้อย 1 รูป
ต้องกดเริ่มติดตั้งก่อนกดส่งงาน
ถ้ามีปัญหา ต้องใส่หมายเหตุและรูปประกอบ
```

---

## 6.9 หน้าถ่ายรูป

ใช้ HTML input สำหรับกล้องมือถือ

```html
<input type="file" accept="image/*" capture="environment">
```

รูปที่ต้องเก็บ:

```txt
lot_number = รูปเลขล็อค / หน้าล็อค
before = รูปก่อนติดตั้ง ถ้าต้องการแยก
 after = รูปหลังติดตั้ง
problem = รูปปัญหา
```

ควรบีบอัดรูปก่อนอัปโหลดเพื่อประหยัดพื้นที่ Hosting

```txt
ขนาดแนะนำ: กว้างไม่เกิน 1600px
คุณภาพ JPEG: 70-80%
```

---

## 7. สถานะระบบ

### 7.1 สถานะล็อค

```txt
available = ว่าง
pending = รอแอดมินยืนยัน
booked = จองแล้ว
installing = กำลังติดตั้ง
completed = ติดตั้งเสร็จแล้ว
blocked = ปิดใช้งาน
problem = มีปัญหา
```

### 7.2 สถานะ Booking

```txt
pending_admin = รอแอดมินยืนยัน
confirmed = ยืนยันแล้ว
assigned = มอบหมายพนักงานแล้ว
installing = กำลังติดตั้ง
completed = ติดตั้งเสร็จแล้ว
cancelled = ยกเลิก
problem = มีปัญหา
```

### 7.3 สถานะ Delivery Task

```txt
waiting = รอเริ่มงาน
started = เริ่มติดตั้ง
photo_uploaded = อัปโหลดรูปแล้ว
completed = ส่งงานแล้ว
problem = มีปัญหา
```

---

## 8. Database Design

> หมายเหตุ: ชื่อตารางและ field สามารถปรับได้ตามมาตรฐานทีมพัฒนา

## 8.1 users

เก็บผู้ใช้งานระบบ เช่น Admin และ Staff

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NULL UNIQUE,
    phone VARCHAR(30) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'viewer') NOT NULL DEFAULT 'staff',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## 8.2 zones

เก็บโซนของตลาด เช่น GB, GC, GD

```sql
CREATE TABLE zones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## 8.3 lots

เก็บข้อมูลล็อคแต่ละช่อง

```sql
CREATE TABLE lots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    zone_id BIGINT UNSIGNED NULL,
    lot_code VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NULL,
    svg_element_id VARCHAR(100) NULL,
    position_x DECIMAL(10,2) NULL,
    position_y DECIMAL(10,2) NULL,
    width DECIMAL(10,2) NULL,
    height DECIMAL(10,2) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    note TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (zone_id) REFERENCES zones(id) ON DELETE SET NULL
);
```

## 8.4 bookings

เก็บข้อมูลการจองหลัก

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(50) NOT NULL UNIQUE,
    use_date DATE NOT NULL,
    shop_name VARCHAR(150) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    tent_size VARCHAR(50) NOT NULL,
    counter_size VARCHAR(50) NULL,
    status ENUM(
        'pending_admin',
        'confirmed',
        'assigned',
        'installing',
        'completed',
        'cancelled',
        'problem'
    ) NOT NULL DEFAULT 'pending_admin',
    admin_note TEXT NULL,
    customer_note TEXT NULL,
    confirmed_by BIGINT UNSIGNED NULL,
    confirmed_at DATETIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_use_date (use_date),
    INDEX idx_customer_phone (customer_phone),
    INDEX idx_status (status)
);
```

## 8.5 booking_lots

รองรับการจองหลายล็อค เช่น GB50-52 อาจผูกกับ GB50, GB51, GB52

```sql
CREATE TABLE booking_lots (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    lot_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (lot_id) REFERENCES lots(id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_lot (booking_id, lot_id)
);
```

## 8.6 delivery_tasks

เก็บงานส่งเต็นท์

```sql
CREATE TABLE delivery_tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    staff_id BIGINT UNSIGNED NULL,
    task_date DATE NOT NULL,
    status ENUM('waiting', 'started', 'photo_uploaded', 'completed', 'problem') NOT NULL DEFAULT 'waiting',
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    problem_note TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_task_date (task_date),
    INDEX idx_staff_status (staff_id, status)
);
```

## 8.7 delivery_photos

เก็บรูปถ่ายยืนยัน

```sql
CREATE TABLE delivery_photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_task_id BIGINT UNSIGNED NOT NULL,
    photo_type ENUM('lot_number', 'before', 'after', 'problem') NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    taken_at DATETIME NULL,
    uploaded_by BIGINT UNSIGNED NULL,
    note TEXT NULL,
    -- เผื่อเพิ่ม OCR ในอนาคต
    ocr_text TEXT NULL,
    ocr_status VARCHAR(50) NULL,
    ocr_confidence DECIMAL(5,2) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (delivery_task_id) REFERENCES delivery_tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_photo_type (photo_type)
);
```

## 8.8 status_logs

เก็บประวัติการเปลี่ยนสถานะ

```sql
CREATE TABLE status_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loggable_type VARCHAR(100) NOT NULL,
    loggable_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(50) NULL,
    new_status VARCHAR(50) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    note TEXT NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_loggable (loggable_type, loggable_id)
);
```

## 8.9 settings

เก็บค่าตั้งค่าระบบ

```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 9. Laravel Model Relationship

### User

```php
class User extends Authenticatable
{
    public function deliveryTasks()
    {
        return $this->hasMany(DeliveryTask::class, 'staff_id');
    }
}
```

### Zone

```php
class Zone extends Model
{
    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
```

### Lot

```php
class Lot extends Model
{
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_lots');
    }
}
```

### Booking

```php
class Booking extends Model
{
    public function lots()
    {
        return $this->belongsToMany(Lot::class, 'booking_lots');
    }

    public function deliveryTask()
    {
        return $this->hasOne(DeliveryTask::class);
    }
}
```

### DeliveryTask

```php
class DeliveryTask extends Model
{
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function photos()
    {
        return $this->hasMany(DeliveryPhoto::class);
    }
}
```

---

## 10. Route Design

## 10.1 Public Routes

```php
Route::get('/', [PublicMapController::class, 'index'])->name('public.map');
Route::get('/lots/status', [PublicMapController::class, 'lotStatus'])->name('public.lots.status');
Route::get('/booking/create', [PublicBookingController::class, 'create'])->name('public.booking.create');
Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/booking/check', [PublicBookingController::class, 'checkForm'])->name('public.booking.check');
Route::post('/booking/check', [PublicBookingController::class, 'check'])->name('public.booking.check.submit');
```

## 10.2 Auth Routes

ใช้ Laravel Auth เช่น Breeze หรือทำ Login เอง

```php
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

## 10.3 Admin Routes

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('/bookings', AdminBookingController::class);
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/assign', [AdminBookingController::class, 'assignStaff'])->name('bookings.assign');

    Route::resource('/lots', AdminLotController::class);
    Route::get('/map', [AdminMapController::class, 'index'])->name('map.index');

    Route::get('/tasks', [AdminDeliveryTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [AdminDeliveryTaskController::class, 'show'])->name('tasks.show');

    Route::resource('/users', AdminUserController::class);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});
```

## 10.4 Staff Routes

```php
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tasks', [StaffTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [StaffTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [StaffTaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/upload-photo', [StaffTaskController::class, 'uploadPhoto'])->name('tasks.upload_photo');
    Route::post('/tasks/{task}/complete', [StaffTaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{task}/problem', [StaffTaskController::class, 'reportProblem'])->name('tasks.problem');
});
```

---

## 11. API / AJAX Endpoints

### ดึงสถานะล็อคตามวันที่

```http
GET /lots/status?date=2026-07-06
```

Response:

```json
{
  "date": "2026-07-06",
  "lots": [
    {
      "lot_code": "GB50",
      "status": "booked",
      "shop_name": "โหยหอย",
      "booking_code": "BK202607060001"
    },
    {
      "lot_code": "GB51",
      "status": "booked",
      "shop_name": "โหยหอย",
      "booking_code": "BK202607060001"
    },
    {
      "lot_code": "GB60",
      "status": "available",
      "shop_name": null,
      "booking_code": null
    }
  ]
}
```

### ดึงรายละเอียดงานพนักงาน

```http
GET /staff/tasks/{task}
```

Response:

```json
{
  "id": 1,
  "lot_text": "GB50-52",
  "shop_name": "โหยหอย หอยแครงลวกแกะ",
  "phone": "0969421398",
  "tent_size": "2x2",
  "counter_size": "2 ล็อค",
  "status": "waiting",
  "photos": []
}
```

---

## 12. Business Rules

### 12.1 กฎการจองล็อค

1. หนึ่งล็อคสามารถมีได้แค่หนึ่ง Booking ต่อหนึ่งวันที่ใช้งาน
2. ถ้า Booking ยังเป็น pending_admin ให้ล็อคแสดงเป็นสีเหลือง
3. ถ้า Admin ยืนยันแล้ว ให้ล็อคแสดงเป็นสีแดงหรือสถานะจองแล้ว
4. ถ้า Booking ยกเลิก ให้ปลดล็อคกลับเป็นว่าง
5. ถ้าจองหลายล็อค ต้องตรวจทุกล็อคในช่วงว่าพร้อมใช้งาน

### 12.2 กฎการมอบหมายพนักงาน

1. Booking ที่ยืนยันแล้วเท่านั้นถึงจะสร้างงานส่งเต็นท์ได้
2. หนึ่ง Booking มีหนึ่ง Delivery Task หลัก
3. Delivery Task ต้องมี task_date ตรงกับวันใช้งานหรือวันที่นัดติดตั้ง
4. Admin สามารถเปลี่ยนพนักงานได้ก่อนงาน completed

### 12.3 กฎการส่งงาน

1. ต้องมีรูปประเภท lot_number อย่างน้อย 1 รูป
2. ต้องมีรูปประเภท after อย่างน้อย 1 รูป
3. ต้องกดเริ่มงานก่อนส่งงาน
4. เมื่อส่งงานแล้ว Booking เปลี่ยนเป็น completed
5. หากแจ้งปัญหา Booking เปลี่ยนเป็น problem และ Admin ต้องตรวจ

### 12.4 กฎการแสดงชื่อร้านบนหน้าลูกค้า

เลือกได้ 2 นโยบาย

```txt
แบบ A: แสดงแค่สถานะ “จองแล้ว” เพื่อรักษาความเป็นส่วนตัว
แบบ B: แสดงชื่อร้านที่จอง เพื่อให้ตลาดเห็นว่าใครจองล็อคไหน
```

แนะนำให้ทำเป็น setting ในระบบ

```txt
show_shop_name_public = true/false
```

---

## 13. SVG Map Design

### 13.1 แนวคิด

แผนที่ตลาดควรทำเป็น SVG ที่แต่ละล็อคมี `id` ตรงกับรหัสล็อค เช่น

```html
<rect id="lot-GB50" data-lot-code="GB50" x="100" y="200" width="40" height="30" />
<rect id="lot-GB51" data-lot-code="GB51" x="142" y="200" width="40" height="30" />
<rect id="lot-GB52" data-lot-code="GB52" x="184" y="200" width="40" height="30" />
```

ระบบ JavaScript จะดึงสถานะจาก API แล้วเปลี่ยน class ของแต่ละล็อค

```js
function applyLotStatus(lotCode, status) {
    const el = document.querySelector(`[data-lot-code="${lotCode}"]`);
    if (!el) return;

    el.classList.remove(
        'lot-available',
        'lot-pending',
        'lot-booked',
        'lot-installing',
        'lot-completed',
        'lot-blocked',
        'lot-problem'
    );

    el.classList.add(`lot-${status}`);
}
```

### 13.2 CSS สี SVG

```css
.lot-available { fill: #6FD08C; }
.lot-pending { fill: #FFD166; }
.lot-booked { fill: #FF6B6B; }
.lot-installing { fill: #A78BFA; }
.lot-completed { fill: #4ECDC4; }
.lot-blocked { fill: #BDBDBD; }
.lot-problem { fill: #FF9F1C; }

.market-lot {
    stroke: #ffffff;
    stroke-width: 2;
    cursor: pointer;
    transition: all .18s ease;
}

.market-lot:hover {
    filter: brightness(1.08);
    transform: translateY(-1px);
}
```

### 13.3 การเลือกหลายล็อค

กรณีลูกค้าจองเป็นช่วง เช่น GB50-52 ระบบควรรองรับการเลือกหลายช่อง

วิธีที่ง่าย:

1. ลูกค้ากดล็อคเริ่มต้น
2. ลูกค้ากดล็อคสุดท้าย
3. ระบบเลือกช่วงระหว่างนั้นอัตโนมัติ ถ้าอยู่โซนเดียวกัน

ตัวอย่าง:

```txt
เลือก GB50 ถึง GB52
ระบบบันทึก GB50, GB51, GB52
แสดงข้อความรวมเป็น GB50-52
```

หรือในเฟสแรกอาจให้ Admin เป็นคนแก้ไขช่วงล็อคหลังลูกค้ากรอกข้อมูลได้

---

## 14. Folder Structure Laravel

```txt
app/
  Http/
    Controllers/
      PublicMapController.php
      PublicBookingController.php
      Admin/
        AdminDashboardController.php
        AdminBookingController.php
        AdminLotController.php
        AdminDeliveryTaskController.php
        AdminUserController.php
        AdminReportController.php
      Staff/
        StaffTaskController.php
    Middleware/
      RoleMiddleware.php
  Models/
    User.php
    Zone.php
    Lot.php
    Booking.php
    DeliveryTask.php
    DeliveryPhoto.php
    StatusLog.php
  Services/
    BookingService.php
    LotAvailabilityService.php
    PhotoUploadService.php
    StatusLogService.php
    MapStatusService.php

resources/
  views/
    layouts/
      public.blade.php
      admin.blade.php
      staff.blade.php
    public/
      map.blade.php
      booking-create.blade.php
      booking-check.blade.php
    admin/
      dashboard.blade.php
      bookings/
      lots/
      tasks/
      users/
      reports/
    staff/
      tasks-index.blade.php
      task-show.blade.php
  js/
    market-map.js
    image-compress.js
  css/
    app.css

public/
  images/
  icons/
  manifest.json

storage/
  app/
    public/
      delivery-photos/
```

---

## 15. Service Class ที่ควรมี

### 15.1 LotAvailabilityService

หน้าที่ตรวจว่าล็อคว่างหรือไม่

```php
class LotAvailabilityService
{
    public function isAvailable(array $lotIds, string $useDate, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('use_date', $useDate)
            ->whereIn('status', ['pending_admin', 'confirmed', 'assigned', 'installing', 'completed'])
            ->whereHas('lots', function ($q) use ($lotIds) {
                $q->whereIn('lots.id', $lotIds);
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }
}
```

### 15.2 BookingService

หน้าที่สร้างรหัสจองและ Booking

```php
class BookingService
{
    public function generateBookingCode(): string
    {
        return 'BK' . now()->format('YmdHis') . random_int(100, 999);
    }

    public function createBooking(array $data, array $lotIds): Booking
    {
        return DB::transaction(function () use ($data, $lotIds) {
            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'use_date' => $data['use_date'],
                'shop_name' => $data['shop_name'],
                'customer_phone' => $data['customer_phone'],
                'tent_size' => $data['tent_size'],
                'counter_size' => $data['counter_size'] ?? null,
                'customer_note' => $data['customer_note'] ?? null,
                'status' => 'pending_admin',
            ]);

            $booking->lots()->sync($lotIds);

            return $booking;
        });
    }
}
```

### 15.3 PhotoUploadService

หน้าที่รับรูป บีบอัด และเก็บลง storage

```php
class PhotoUploadService
{
    public function upload($file, string $folder = 'delivery-photos'): string
    {
        return $file->store($folder, 'public');
    }
}
```

ใน production ควรเพิ่ม image resize/compress ด้วย Intervention Image หรือ library ที่เหมาะสม

---

## 16. PWA Requirements

### 16.1 manifest.json

```json
{
  "name": "ระบบจองเต็นท์ตลาด",
  "short_name": "จองเต็นท์",
  "start_url": "/staff/tasks",
  "display": "standalone",
  "background_color": "#FFF7FA",
  "theme_color": "#FF8FB1",
  "icons": [
    {
      "src": "/icons/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

### 16.2 Service Worker ขั้นต่ำ

```js
self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(clients.claim());
});
```

เฟสแรกยังไม่จำเป็นต้อง Offline เต็มรูปแบบ แต่ควรทำ Add to Home Screen ได้

---

## 17. Responsive Design

### Breakpoints

```txt
Mobile: 320px - 767px
Tablet: 768px - 1023px
Desktop: 1024px ขึ้นไป
```

### Mobile First Rules

1. ปุ่มสูงอย่างน้อย 48px
2. ตัวเลขล็อคในหน้าพนักงานขนาด 42-64px
3. Card งานเว้นระยะชัด
4. หลีกเลี่ยง Table ในมือถือ ใช้ Card แทน
5. ใช้ Bottom Sheet สำหรับรายละเอียดล็อค
6. ปุ่มหลักควรอยู่ด้านล่างนิ้วโป้งกดง่าย

---

## 18. ตัวอย่าง Component UI

### 18.1 Status Badge

```html
<span class="status-badge status-confirmed">จองแล้ว</span>
```

```css
.status-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 700;
}
.status-confirmed {
    background: #FFE1E1;
    color: #D83A3A;
}
```

### 18.2 Cute Card

```html
<div class="cute-card">
    <div class="lot-big">GB50-52</div>
    <div class="shop-name">ร้านโหยหอย</div>
    <button class="btn-primary">เปิดงาน</button>
</div>
```

```css
.cute-card {
    background: #fff;
    border-radius: 24px;
    padding: 18px;
    box-shadow: 0 10px 30px rgba(47, 47, 55, 0.08);
    border: 1px solid #f3e3ea;
}
.lot-big {
    font-size: 48px;
    font-weight: 900;
    color: #FF6B9A;
    text-align: center;
    letter-spacing: 1px;
}
.btn-primary {
    width: 100%;
    border: none;
    border-radius: 18px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #FF8FB1, #FF6B9A);
    color: white;
    font-size: 16px;
    font-weight: 800;
}
```

---

## 19. Security Requirements

1. ใช้ Login สำหรับ Admin/Staff
2. Password ต้อง Hash ด้วย Laravel Hash
3. ตรวจ role ทุก route หลังบ้าน
4. Validate input ทุกฟอร์ม
5. จำกัดชนิดไฟล์รูปเฉพาะ jpg/png/webp
6. จำกัดขนาดรูป เช่น ไม่เกิน 5MB ก่อนบีบอัด
7. เก็บรูปใน `storage/app/public` และใช้ `php artisan storage:link`
8. ไม่ให้ Staff แก้งานของคนอื่น ถ้าไม่ได้รับสิทธิ์
9. CSRF protection ต้องเปิดใช้งาน
10. Backup Database และรูปสม่ำเสมอ

---

## 20. Deployment บน Plesk Hosting

### 20.1 สิ่งที่ Hosting ต้องรองรับ

```txt
PHP 8.1+ หรือเวอร์ชันที่ Laravel ที่เลือกต้องการ
Composer
MySQL/MariaDB
File Upload
Cron Job
SSL Let's Encrypt
```

### 20.2 ขั้นตอน Deploy แบบทั่วไป

```txt
1. สร้าง Domain/Subdomain ใน Plesk เช่น tent.example.com
2. สร้าง Database MySQL
3. Upload Laravel Project ไปยัง hosting
4. ตั้ง Document Root ไปที่ /public
5. ตั้งค่า .env
6. รัน composer install --no-dev
7. รัน php artisan key:generate
8. รัน php artisan migrate --seed
9. รัน php artisan storage:link
10. ตั้ง Cron สำหรับ Laravel Scheduler
11. เปิด SSL Let's Encrypt
12. ทดสอบ Login Admin และหน้าลูกค้า
```

### 20.3 ตัวอย่าง .env

```env
APP_NAME="Tent Booking"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tent.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tent_booking
DB_USERNAME=tent_user
DB_PASSWORD=secret

FILESYSTEM_DISK=public
```

### 20.4 Cron Laravel Scheduler

ใน Plesk ตั้ง Cron ทุก 1 นาที

```bash
php /var/www/vhosts/example.com/tent.example.com/artisan schedule:run >> /dev/null 2>&1
```

ถ้าเฟสแรกไม่มี job อัตโนมัติ อาจยังไม่ต้องใช้ก็ได้ แต่เตรียมไว้รองรับอนาคต

---

## 21. Seed Data ที่ควรมี

### 21.1 Admin เริ่มต้น

```txt
Name: Admin
Email: admin@example.com
Password: ตั้งเองตอนติดตั้ง
Role: admin
```

### 21.2 Tent Sizes

อาจเก็บใน settings หรือ config

```php
return [
    'tent_sizes' => ['1.5', '2x2', '2x3', '2.5', '3x4.5'],
    'counter_sizes' => ['2 ล็อค', '3 ล็อค'],
];
```

### 21.3 Zones

```txt
GB
GC
GD
GY
```

### 21.4 Lots

ตัวอย่าง

```txt
GB50
GB51
GB52
GC10
GC11
GD01
```

---

## 22. Feature Phase Plan

### Phase 1: ใช้งานจริงขั้นต่ำ

```txt
ระบบ Login Admin/Staff
หน้าลูกค้าเลือกวันที่และดูแผนที่
ระบบกดล็อคและส่งฟอร์มจอง
หลังบ้านรายการจอง
Admin ยืนยัน / ยกเลิก
สร้างงานพนักงาน
หน้าพนักงานดูงานวันนี้
ถ่ายรูปเลขล็อคและรูปหลังติดตั้ง
ส่งงานและเปลี่ยนสถานะ
ลูกค้าตรวจสอบสถานะ
```

### Phase 2: ปรับ UX/UI และรายงาน

```txt
Dashboard สวยขึ้น
รายงานจำนวนงานรายวัน/เดือน
ค้นหาตามร้าน/เบอร์/ล็อค
Export Excel
แจ้งเตือน LINE Notify หรือ LINE OA ถ้าต้องการ
ระบบจัดการสีสถานะในแผนที่
```

### Phase 3: เพิ่ม OCR ภายหลัง

```txt
เพิ่ม Google Vision OCR
ให้ระบบอ่านเลขจากรูป
เทียบกับเลขล็อคที่จอง
ผ่าน / ไม่ผ่าน / รอ Admin ตรวจ
เก็บ OCR log
```

### Phase 4: เพิ่มความสามารถขั้นสูง

```txt
ระบบชำระเงิน
Slip Verification
Customer Login
Staff GPS Tracking
Offline Mode บางส่วน
ระบบสัญญาเช่า/ใบเสร็จ
```

---

## 23. Checklist สำหรับสร้างระบบ

### Backend

```txt
[ ] ติดตั้ง Laravel
[ ] สร้าง Auth
[ ] สร้าง Role Middleware
[ ] สร้าง Migration ทั้งหมด
[ ] สร้าง Model Relationship
[ ] สร้าง Booking Service
[ ] สร้าง Lot Availability Service
[ ] สร้าง Photo Upload Service
[ ] สร้าง Admin Controllers
[ ] สร้าง Staff Controllers
[ ] สร้าง Public Controllers
```

### Frontend

```txt
[ ] ทำ Layout Public
[ ] ทำ Layout Admin
[ ] ทำ Layout Staff Mobile
[ ] ทำ SVG Map
[ ] ทำ JavaScript เปลี่ยนสีล็อค
[ ] ทำ Bottom Sheet บนมือถือ
[ ] ทำฟอร์มจอง
[ ] ทำหน้า Staff Task Card
[ ] ทำหน้าถ่ายรูป
[ ] ทำ PWA manifest
```

### Testing

```txt
[ ] ลูกค้าเลือกวันที่แล้วสถานะล็อคเปลี่ยนถูกต้อง
[ ] ลูกค้าจองล็อคที่ว่างได้
[ ] ลูกค้าจองล็อคที่ไม่ว่างไม่ได้
[ ] Admin ยืนยันการจองได้
[ ] Admin มอบหมายพนักงานได้
[ ] Staff เห็นเฉพาะงานของตัวเอง
[ ] Staff อัปโหลดรูปได้
[ ] Staff ส่งงานไม่ได้ถ้ายังไม่มีรูป
[ ] ส่งงานแล้วสถานะลูกค้าเปลี่ยนเป็นติดตั้งเสร็จแล้ว
[ ] รูปแสดงในหลังบ้านได้
[ ] มือถือ iPhone/Android เปิดกล้องได้
[ ] iPad/PC ใช้งานแผนที่ได้
```

---

## 24. Prompt สำหรับส่งให้ Developer หรือ AI เขียนโค้ด

ใช้ข้อความนี้เป็น prompt หลักได้

```txt
ต้องการสร้างระบบ Web App / PWA สำหรับร้านเช่าเต็นท์ตลาด ด้วย Laravel + MySQL + JavaScript + SVG Map โดยเน้นใช้งานบนมือถือเป็นหลัก รองรับ Phone, iPad, PC

ระบบมี 3 ฝั่งหลัก:
1. ลูกค้า: เปิดเว็บ ดูแผนที่ตลาด กดเลือกล็อค ดูสถานะ ว่าง/รอยืนยัน/จองแล้ว และส่งฟอร์มจอง
2. Admin: Login หลังบ้าน ดูรายการจอง ยืนยัน/ยกเลิก/แก้ไข มอบหมายพนักงาน ดูแผนที่ ดูรูปยืนยัน และรายงาน
3. Staff: Login ผ่านมือถือ ดูงานติดตั้งวันนี้ เห็นเลขล็อคตัวใหญ่ ดูแผนที่ highlight ล็อค ถ่ายรูปเลขล็อค ถ่ายรูปหลังติดตั้ง แจ้งปัญหา และส่งงาน

ยังไม่ใช้ OCR ในเฟสแรก แต่ต้องเผื่อ field ocr_text, ocr_status, ocr_confidence ใน delivery_photos ไว้สำหรับอนาคต

UX/UI ต้องน่ารัก สีพาสเทล Card โค้งมน ปุ่มใหญ่ อ่านง่ายบนมือถือ สีสถานะชัดเจน และหน้า Staff ต้องแสดงเลขล็อคใหญ่มากเพื่อลดการส่งผิดล็อค

ให้สร้างระบบตามเอกสาร Database, Routes, Models, Controllers, Views, Services และ Business Rules ที่กำหนดไว้
```

---

## 25. สรุปภาษาที่ใช้

```txt
PHP = เขียน Backend ด้วย Laravel
MySQL/MariaDB = ฐานข้อมูล
JavaScript = ควบคุม SVG Map, กล้อง, UX บนมือถือ
HTML/CSS = ทำหน้าเว็บ
SVG = ทำแผนที่ล็อคแบบกดได้
PWA = ทำให้เปิดบนมือถือเหมือนแอป
```

สรุป Stack สุดท้ายของเวอร์ชันแรก:

```txt
Laravel + MySQL + JavaScript + SVG Map + PWA + Cute Mobile UX/UI
```

ระบบนี้สามารถเริ่มจาก Hosting Plesk ทั่วไปได้ และออกแบบไว้ให้เพิ่ม OCR / LINE แจ้งเตือน / ระบบชำระเงิน ในอนาคตได้โดยไม่ต้องรื้อระบบใหม่
