# HƯỚNG DẪN SỬ DỤNG YELLOWCLOAKER + YOURLS

**Phiên bản:** 2.0
**Domain:** https://thugonl.ink
**Ngày cập nhật:** 29/11/2025

---

## 📋 MỤC LỤC

1. [Tổng Quan Hệ Thống](#1-tổng-quan-hệ-thống)
2. [YellowCloaker - Hệ Thống Cloaking](#2-yellowcloaker---hệ-thống-cloaking)
3. [YOURLS - Rút Gọn Link](#3-yourls---rút-gọn-link)
4. [Cấu Hình Nâng Cao](#4-cấu-hình-nâng-cao)
5. [Xử Lý Sự Cố](#5-xử-lý-sự-cố)

---

## 1. TỔNG QUAN HỆ THỐNG

### 1.1 Kiến Trúc Hệ Thống

```
┌─────────────────────────────────────────┐
│     https://thugonl.ink                 │
├─────────────────────────────────────────┤
│                                         │
│  ┌────────────────────────────────┐    │
│  │   YellowCloaker (/)            │    │
│  │   - Lọc bot/người thật         │    │
│  │   - Thống kê truy cập          │    │
│  │   - Admin: /admin?password=... │    │
│  └────────────────────────────────┘    │
│                                         │
│  ┌────────────────────────────────┐    │
│  │   YOURLS (/yourls/)            │    │
│  │   - Rút gọn link               │    │
│  │   - Quản lý link               │    │
│  │   - Admin: /yourls/admin/      │    │
│  └────────────────────────────────┘    │
│                                         │
└─────────────────────────────────────────┘
```

### 1.2 Thông Tin Đăng Nhập

**YellowCloaker Admin:**
- URL: https://thugonl.ink/admin?password=12345
- Password: `12345`

**YOURLS Admin:**
- URL: https://thugonl.ink/yourls/admin/
- Username: `admin`
- Password: `12345`

**MySQL Database:**
- Database: `yourls`
- User: `yourls`
- Password: `YourLs#2024Pass`
- Host: `localhost`

---

## 2. YELLOWCLOAKER - HỆ THỐNG CLOAKING

### 2.1 YellowCloaker Là Gì?

YellowCloaker giúp phân biệt giữa:
- ✅ **White Traffic:** Người dùng thật (được phép vào)
- ❌ **Black Traffic:** Bot, crawler, spy tool (bị chặn)

**Mục đích:** Bảo vệ website khỏi:
- Bot của đối thủ
- Spy tool
- Crawler tự động
- DDoS attacks

### 2.2 Truy Cập Admin Panel

```
https://thugonl.ink/admin?password=12345
```

**Lưu ý:** Không có form đăng nhập, password truyền qua URL

### 2.3 Cấu Hình Bộ Lọc

#### File cấu hình: `/var/www/yellowcloaker/settings.json`

```json
{
    "tds": {
        "mode": "on",
        "filters": {
            "allowed": {
                "countries": [
                    "UA",
                    "BY",
                    "SG"
                ],
                "os": [
                    "Android",
                    "iOS",
                    "Windows",
                    "OS X"
                ]
            },
            "blocked": {
                "isps": [],
                "useragents": [
                    "facebook",
                    "Facebot",
                    "curl",
                    "gce-spider",
                    "yandex.com/bots",
                    "OdklBot"
                ],
                "vpntor": false
            }
        }
    },
    "statistics": {
        "password": "12345"
    }
}
```

#### Thêm/Xóa Quốc Gia Được Phép

**Thêm quốc gia:**
```json
"countries": [
    "UA",      // Ukraine
    "BY",      // Belarus
    "SG",      // Singapore
    "VN",      // Vietnam (thêm mới)
    "TH"       // Thailand (thêm mới)
]
```

**Danh sách mã quốc gia phổ biến:**
- VN: Vietnam
- TH: Thailand
- SG: Singapore
- US: United States
- GB: United Kingdom
- JP: Japan
- KR: South Korea

#### Thêm/Xóa User-Agent Bị Chặn

```json
"useragents": [
    "facebook",
    "Facebot",
    "curl",
    "wget",           // Thêm
    "python-requests" // Thêm
]
```

#### Chặn ISP Cụ Thể

```json
"isps": [
    "AS15169",  // Google
    "AS16509"   // Amazon
]
```

### 2.4 Xem Thống Kê

**Admin Panel hiển thị:**
- Tổng số click White/Black
- Phân bổ theo quốc gia
- Phân bổ theo OS
- Phân bổ theo Browser

**Xóa log cũ (nếu chậm):**
```bash
cd /var/www/yellowcloaker
rm -rf logs/whiteclicks/* logs/blackclicks/*
```

### 2.5 Các Trang Trong YellowCloaker

- `/white/` - Trang hiển thị cho người dùng thật
- `/black/` - Trang hiển thị cho bot (thường là trang fake)
- `/admin?password=12345` - Admin panel

**Tùy chỉnh trang White:**
Sửa file `/var/www/yellowcloaker/white/index.php`

**Tùy chỉnh trang Black:**
Sửa file `/var/www/yellowcloaker/black/index.php`

---

## 3. YOURLS - RÚT GỌN LINK

### 3.1 YOURLS Là Gì?

YOURLS = **Y**our **O**wn **URL** **S**hortener

Tự tạo link rút gọn như bit.ly, tinyurl.com nhưng:
- ✅ Tự host, toàn quyền kiểm soát
- ✅ Tùy chỉnh keyword
- ✅ Thống kê chi tiết
- ✅ Không giới hạn số link

### 3.2 Đăng Nhập Admin

```
URL: https://thugonl.ink/yourls/admin/
Username: admin
Password: 12345
```

### 3.3 Tạo Link Rút Gọn

#### Cách 1: Qua Admin Panel

1. Vào https://thugonl.ink/yourls/admin/
2. Nhập URL gốc vào ô "Enter the URL"
3. (Tùy chọn) Nhập keyword tùy chỉnh vào ô "Custom short URL"
4. Click "Shorten The URL"

**Ví dụ:**
```
URL gốc: https://www.google.com/search?q=hello
Keyword: gg
→ Link rút gọn: https://thugonl.ink/gg
```

#### Cách 2: Qua API

**Tạo link tự động:**
```bash
curl "https://thugonl.ink/yourls/yourls-api.php" \
  -d "username=admin" \
  -d "password=12345" \
  -d "action=shorturl" \
  -d "url=https://example.com" \
  -d "keyword=example" \
  -d "format=json"
```

**Response:**
```json
{
  "status": "success",
  "shorturl": "https://thugonl.ink/example",
  "url": {
    "keyword": "example",
    "url": "https://example.com",
    "title": "Example Domain"
  }
}
```

### 3.4 Quản Lý Link

**Admin Panel cho phép:**
- ✏️ Edit link (đổi URL đích)
- 📊 Xem thống kê click
- 🗑️ Xóa link
- 📝 Thêm title tùy chỉnh

**Tìm kiếm link:**
- Vào tab "Manage Plugins" > Search box
- Gõ keyword hoặc URL để tìm

### 3.5 Xem Thống Kê

**Thống kê tổng quan:**
- Trang chủ admin hiển thị top links
- Click vào "Stats" bên cạnh mỗi link

**Thống kê chi tiết:**
```
https://thugonl.ink/yourls/admin/index.php?page=stats&id=keyword
```

**Thông tin thống kê:**
- Số lượt click
- Click theo thời gian
- Click theo quốc gia
- Referrer (từ đâu vào)

### 3.6 Plugins & Features

**Cài plugin:**
1. Upload plugin vào `/var/www/yellowcloaker/yourls/user/plugins/`
2. Vào Admin > "Manage Plugins"
3. Activate plugin

**Plugins phổ biến:**
- QR Code Generator
- Password Protection
- Redirect According to Device
- Link Preview

---

## 4. CẤU HÌNH NÂNG CAO

### 4.1 Cấu Hình Nginx

**File:** `/etc/nginx/sites-available/thugonl.ink`

**Tăng timeout nếu chậm:**
```nginx
location ~ \.php$ {
    fastcgi_read_timeout 300s;
    fastcgi_send_timeout 300s;
}
```

**Reload Nginx:**
```bash
nginx -t
systemctl reload nginx
```

### 4.2 Cấu Hình PHP-FPM

**File:** `/etc/php/8.1/fpm/pool.d/www.conf`

**Tăng số worker nếu bị 502:**
```ini
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
request_terminate_timeout = 300
```

**Restart PHP-FPM:**
```bash
systemctl restart php8.1-fpm
```

### 4.3 Cấu Hình MySQL

**Truy cập MySQL:**
```bash
mysql -u yourls -p
# Password: YourLs#2024Pass
```

**Xem database:**
```sql
USE yourls;
SHOW TABLES;
SELECT * FROM yourls_url LIMIT 10;
```

**Backup database:**
```bash
mysqldump -u yourls -p yourls > yourls_backup_$(date +%Y%m%d).sql
```

**Restore database:**
```bash
mysql -u yourls -p yourls < yourls_backup_20251129.sql
```

### 4.4 SSL Certificate

**Kiểm tra SSL:**
```bash
certbot certificates
```

**Renew SSL (tự động):**
```bash
certbot renew
```

**Renew SSL (thủ công):**
```bash
certbot renew --force-renewal
```

**SSL expires:** 26/02/2026

### 4.5 Cron Jobs (Tự Động Hóa)

**Thêm cron job:**
```bash
crontab -e
```

**Ví dụ: Xóa log cũ mỗi tuần:**
```cron
0 0 * * 0 rm -rf /var/www/yellowcloaker/logs/whiteclicks/* /var/www/yellowcloaker/logs/blackclicks/*
```

**Ví dụ: Backup database hàng ngày:**
```cron
0 2 * * * mysqldump -u yourls -pYourLs#2024Pass yourls > /backup/yourls_$(date +\%Y\%m\%d).sql
```

---

## 5. XỬ LÝ SỰ CỐ

### 5.1 Admin Panel Chậm/Timeout

**Nguyên nhân:** Quá nhiều log file

**Cách fix:**
```bash
cd /var/www/yellowcloaker
rm -rf logs/whiteclicks/* logs/blackclicks/*
systemctl restart php8.1-fpm
```

### 5.2 Lỗi 502 Bad Gateway

**Nguyên nhân:** PHP-FPM workers bị đầy

**Kiểm tra:**
```bash
systemctl status php8.1-fpm
```

**Cách fix:**
```bash
# Cách 1: Restart PHP-FPM
systemctl restart php8.1-fpm

# Cách 2: Kill force (nếu restart không được)
pkill -9 php-fpm
systemctl start php8.1-fpm
```

### 5.3 Lỗi 403 Forbidden

**Nguyên nhân:** Sai permission hoặc thiếu index file

**Kiểm tra permission:**
```bash
ls -la /var/www/yellowcloaker
```

**Fix permission:**
```bash
chown -R www-data:www-data /var/www/yellowcloaker
chmod -R 755 /var/www/yellowcloaker
```

### 5.4 YOURLS Không Vào Được

**Kiểm tra database:**
```bash
systemctl status mysql
mysql -u yourls -p
```

**Fix database:**
```bash
systemctl restart mysql
```

**Kiểm tra config:**
```bash
cat /var/www/yellowcloaker/yourls/user/config.php
```

### 5.5 Link Rút Gọn Không Hoạt Động

**Kiểm tra .htaccess:**
```bash
cat /var/www/yellowcloaker/yourls/.htaccess
```

**Kiểm tra Nginx rewrite:**
```nginx
location /yourls/ {
    try_files $uri $uri/ /yourls/yourls-loader.php?$args;
}
```

### 5.6 Xem Log Lỗi

**Nginx error log:**
```bash
tail -f /var/log/nginx/thugonl.ink_error.log
```

**PHP-FPM log:**
```bash
tail -f /var/log/php8.1-fpm.log
```

**MySQL log:**
```bash
tail -f /var/log/mysql/error.log
```

### 5.7 Tối Ưu Performance

**1. Tăng PHP memory:**
```bash
# Edit /etc/php/8.1/fpm/php.ini
memory_limit = 256M
```

**2. Enable Nginx cache:**
```nginx
# Thêm vào /etc/nginx/sites-available/thugonl.ink
location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
    expires 7d;
    add_header Cache-Control "public, immutable";
}
```

**3. Enable MySQL query cache:**
```sql
# Edit /etc/mysql/my.cnf
[mysqld]
query_cache_type = 1
query_cache_size = 16M
```

**4. Sử dụng Cloudflare CDN:**
- Bật Cloudflare proxy (orange cloud)
- Cache static assets
- Enable Brotli compression

---

## 6. WORKFLOW SỬ DỤNG THỰC TẾ

### 6.1 Kịch Bản 1: Chạy Ads Với Cloaking

```
1. Tạo landing page thật → /white/index.php
2. Tạo landing page fake → /black/index.php
3. Cấu hình filter:
   - Cho phép: VN, TH, SG (target audience)
   - Chặn: Facebook bot, Google bot
4. Dùng YOURLS tạo link rút gọn
5. Chạy ads với link rút gọn
6. Bot ads → thấy trang black (safe)
   User thật → thấy trang white (real offer)
```

### 6.2 Kịch Bản 2: Bảo Vệ Affiliate Link

```
1. Tạo short link cho affiliate URL
   VD: https://thugonl.ink/offer123
2. Cấu hình cloaking:
   - Chặn bot spy tool
   - Chặn đối thủ (theo ISP/IP)
3. Chia sẻ link rút gọn
4. Theo dõi thống kê:
   - YOURLS: click count
   - YellowCloaker: white/black ratio
```

### 6.3 Kịch Bản 3: A/B Testing

```
1. Tạo 2 version trang white:
   - /white/version-a.php
   - /white/version-b.php
2. Dùng YellowCloaker split traffic
3. Tạo 2 link YOURLS:
   - https://thugonl.ink/test-a
   - https://thugonl.ink/test-b
4. So sánh conversion rate
```

---

## 7. BẢO MẬT & LƯU Ý

### 7.1 ĐỔI PASSWORD (Quan Trọng!)

**YellowCloaker:**
```bash
nano /var/www/yellowcloaker/settings.json
# Đổi "password": "12345" → "your_strong_password"
```

**YOURLS:**
```bash
nano /var/www/yellowcloaker/yourls/user/config.php
# Đổi 'admin' => '12345' → 'admin' => 'your_strong_password'
```

**MySQL:**
```sql
ALTER USER 'yourls'@'localhost' IDENTIFIED BY 'new_strong_password';
FLUSH PRIVILEGES;
```

### 7.2 Ẩn Admin Panel

**Đổi URL admin (YellowCloaker):**
Sửa trong code, thay `/admin` → `/secret-admin-xyz`

**Bảo vệ bằng IP whitelist (Nginx):**
```nginx
location /admin {
    allow 123.45.67.89;  # IP của bạn
    deny all;
}
```

### 7.3 Backup Định Kỳ

**Backup toàn bộ:**
```bash
# Code
tar -czf backup_code_$(date +%Y%m%d).tar.gz /var/www/yellowcloaker

# Database
mysqldump -u yourls -p yourls > backup_db_$(date +%Y%m%d).sql
```

**Upload lên đâu đó an toàn:**
- Google Drive
- Dropbox
- AWS S3
- GitHub (private repo)

---

## 8. LIÊN HỆ & HỖ TRỢ

**GitHub Repository:**
https://github.com/sashiminakamoto/cloack

**YellowCloaker Official:**
https://github.com/dvygolov/YellowCloaker

**YOURLS Official:**
https://yourls.org/

**Thông Tin Server:**
- OS: Ubuntu 22.04
- Web Server: Nginx 1.18.0
- PHP: 8.1-FPM
- Database: MySQL 8.0
- SSL: Let's Encrypt

---

## 9. CHANGELOG

### Version 2.0 (29/11/2025)
- ✅ Thêm YOURLS v1.9.2
- ✅ Tích hợp cả cloaking + URL shortener
- ✅ Tối ưu PHP-FPM workers (5 → 20)
- ✅ Fix 502 Bad Gateway issues
- ✅ Thêm auto-redirect cho /yourls/
- ✅ Upload code lên GitHub

### Version 1.0 (28/11/2025)
- ✅ Cài đặt YellowCloaker
- ✅ Cấu hình Nginx + SSL
- ✅ Cho phép SG traffic
- ✅ Đổi password admin

---

**LƯU Ý CUỐI CÙNG:**

⚠️ Password mặc định `12345` rất yếu, hãy đổi ngay!
⚠️ GitHub token đã lộ trong chat, nên revoke và tạo mới
⚠️ Cloaking có thể vi phạm policy của một số ad network, cẩn thận!

**Chúc bạn sử dụng hiệu quả!** 🚀
