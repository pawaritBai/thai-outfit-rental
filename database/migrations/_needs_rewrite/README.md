# migration ที่รอเขียนใหม่

โปรเจกต์นี้เดิม **ไม่มี migration ของตารางหลัก** (Users, Shops, ThaiOutfits, Bookings ...)
schema ถูกสร้างตรงในฐานข้อมูลบน cloud ซึ่งข้อมูลหายไปแล้ว

ไฟล์ในโฟลเดอร์นี้คือ migration เดิมที่ยังใช้ไม่ได้ — Laravel ไม่สแกน subfolder จึงไม่ถูกรัน
ย้ายมาไว้ที่นี่เพื่อเก็บเป็นข้อมูลอ้างอิงระหว่างเขียน schema ชุดใหม่ใน `database/migrations/`

| ไฟล์ | ปัญหา |
|---|---|
| `2025_03_13_..._add_size_and_color_to_cart_items` | body ว่างเปล่า ไม่ได้ทำอะไร |
| `2025_03_22_..._create_issues_table` | FK ไป `Users` ก่อนตารางนั้นถูกสร้าง, ขาด column `file_path`, `reply` ควรเป็น nullable |
| `2025_03_22_..._notifications` | ใช้ `constrained()` ซึ่งเดาชื่อตารางเป็น `users`/`id` แต่จริงคือ `Users`/`user_id` |
| `2025_03_22_..._add_timestamps_to_order_details_table` | อ้าง `order_details` (ตัวเล็ก) แต่ตารางจริงชื่อ `OrderDetails` |
| `2025_03_22_..._add_updated_at_to_order_details_table` | ควรรวมเข้าไปในไฟล์ create ของ OrderDetails เลย |

เมื่อเขียน `issues` / `notifications` / `OrderDetails` เวอร์ชันใหม่เสร็จและ migrate ผ่านแล้ว
ลบโฟลเดอร์นี้ทิ้งได้
