# Checklist Deploy – Visa Minh Quân

Hướng dẫn từng bước deploy mã nguồn WordPress cho dự án Visa Minh Quân.

---

## Phần 1: Môi trường Local (DDEV)

### Yêu cầu hệ thống

- [ ] **Docker Desktop** đã cài và đang chạy  
- [ ] **DDEV** v1.24+ (`brew install ddev` hoặc [ddev.com](https://ddev.com))  
- [ ] **Git**  
- [ ] **Composer** (hoặc dùng `ddev composer`)

---

### Bước 1: Clone mã nguồn

```bash
git clone <repository-url> visaminhquan
cd visaminhquan
```

- [ ] Đã clone xong mã nguồn  
- [ ] Đã cd vào thư mục dự án  

---

### Bước 2: Khởi động DDEV

```bash
ddev start
```

- [ ] DDEV start thành công  
- [ ] Truy cập được `https://visaminhquan.ddev.site` (hoặc URL từ `ddev describe`)

---

### Bước 3: Cài đặt dependencies (nếu có)

```bash
# Composer (ở thư mục gốc hoặc trong web/)
ddev composer install

# NPM (trong theme/plugin nếu có)
ddev npm install
ddev npm run build
```

- [ ] `composer install` hoàn tất  
- [ ] `npm install` và `npm run build` (nếu có) hoàn tất  

---

### Bước 4: Cấu hình WordPress

#### 4.1. Database

- [ ] Đã import database (nếu có file `.sql`):

```bash
ddev import-db --file=path/to/database.sql
```

- [ ] Hoặc cài mới WordPress:

```bash
ddev wp core install --url=https://visaminhquan.ddev.site \
  --title="Visa Minh Quân" \
  --admin_user=admin \
  --admin_password=<password> \
  --admin_email=admin@example.com
```

#### 4.2. Uploads

- [ ] Đã import thư mục uploads (nếu có):

```bash
ddev import-files --source=path/to/uploads
```

#### 4.3. Cập nhật URL (nếu import từ môi trường khác)

```bash
# Thay old-url bằng URL cũ (vd: https://visaminhquan.com.vn)
# Thay new-url bằng https://visaminhquan.ddev.site
ddev wp search-replace 'https://old-url.com' 'https://visaminhquan.ddev.site'
```

- [ ] Search-replace URL đã chạy xong  
- [ ] Đã xóa cache: `ddev wp cache flush`  

---

### Bước 5: Kiểm tra Local

- [ ] Trang chủ load đúng  
- [ ] Đăng nhập admin hoạt động  
- [ ] Media (ảnh, file) hiển thị đúng  
- [ ] Form liên hệ và các plugin chính hoạt động  

---

## Phần 2: Deploy Production

### Trước khi deploy

- [ ] Đã test kỹ trên local  
- [ ] Đã backup database và files trên server cũ (nếu đang chạy)  
- [ ] Có thông tin hosting: SSH, FTP/SFTP, database, domain  

---

### Bước 1: Chuẩn bị mã nguồn

#### 1.1. Export database từ local

```bash
ddev export-db --file=backup-$(date +%Y%m%d).sql
```

- [ ] Đã export file SQL

#### 1.2. Cập nhật URL trong database (nếu deploy mới hoặc đổi domain)

```bash
# Chạy search-replace trên file SQL trước khi upload
# Hoặc dùng WP-CLI trên server sau khi import
ddev wp search-replace 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn' --export=db-production.sql
```

- [ ] Đã cập nhật URL sang domain production  

#### 1.3. Chuẩn bị files

- [ ] Đã loại trừ: `wp-config.php`, `wp-config-ddev.php`, `.ddev/`, `node_modules/`, `.git/` (nếu không dùng Git deploy)  
- [ ] Đã nén hoặc dùng Git để đẩy lên server  

---

### Bước 2: Upload lên server

#### Cách A: Git

```bash
# Trên server (SSH)
cd /path/to/website
git pull origin main
```

- [ ] Đã pull mã mới nhất  
- [ ] Đã cài dependencies: `composer install --no-dev`, `npm run build` (nếu cần)  

#### Cách B: FTP/SFTP / rsync

```bash
rsync -avz --exclude='.git' --exclude='node_modules' \
  --exclude='.ddev' --exclude='wp-config-ddev.php' \
  ./ user@server:/path/to/web/
```

- [ ] Đã upload đủ files  
- [ ] Phân quyền đúng: dùng script `chmod-on-server.sh` trong thư mục deploy-output (upload lên server, chạy trong thư mục gốc WordPress) hoặc `chmod -R 755 wp-content` (uploads: 775)  

---

### Bước 3: Cấu hình Production

#### 3.1. wp-config.php – **Bắt buộc chỉnh trên production**

File `wp-config.php` trong repo dùng cho DDEV: thông tin DB được nạp từ `wp-config-ddev.php` (chỉ có khi chạy trong DDEV). **Trên server production không có DDEV**, nên bạn phải khai báo lại DB và (tùy chọn) URL.

**Cách 1 – Dùng file riêng (khuyến nghị):** Tạo file `web/wp-config-production.php` **trên server** (không commit vào Git), nội dung ví dụ:

```php
<?php
/** Cấu hình Production – không commit file này lên Git */

define( 'DB_NAME', 'tên_database_trên_hosting' );
define( 'DB_USER', 'user_database' );
define( 'DB_PASSWORD', 'mật_khẩu_database' );
define( 'DB_HOST', 'localhost' ); // hoặc theo hướng dẫn hosting

// Tùy chọn: ép URL production (hoặc để WordPress lấy từ database)
define( 'WP_HOME', 'https://visaminhquan.com.vn' );
define( 'WP_SITEURL', 'https://visaminhquan.com.vn' );

define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
$table_prefix = 'wp_';
```

Trong `wp-config.php` đã có đoạn: nếu **không** chạy trong DDEV thì sẽ `require` file này. Đảm bảo trên server đã tạo đúng tên file (ví dụ `wp-config-production.php`) như trong `wp-config.php`.

**Cách 2 – Sửa trực tiếp wp-config.php trên server:**  
Thêm hoặc sửa các dòng sau **trước** dòng `require wp-settings.php` (và **không** require `wp-config-ddev.php` trên production):

- [ ] `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`  
- [ ] (Tùy chọn) `WP_HOME`, `WP_SITEURL`  
- [ ] `WP_DEBUG` = false, `WP_DEBUG_LOG` = false  
- [ ] Security keys (AUTH_KEY, SECURE_AUTH_KEY, ...) – có thể giữ bộ trong repo hoặc tạo mới từ [WordPress.org](https://api.wordpress.org/secret-key/1.1/salt/)  

- [ ] **Không** upload hoặc **không** require `wp-config-ddev.php` trên production  

#### 3.2. Database

```bash
# Trên server (hoặc qua phpMyAdmin)
mysql -u user -p database_name < backup-YYYYMMDD.sql
```

- [ ] Đã import database  
- [ ] Đã search-replace URL nếu cần:  
  `wp search-replace 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn'`  

---

### Bước 4: Hậu deploy

- [ ] Xóa cache: `wp cache flush`  
- [ ] Kiểm tra permalink: vào Admin → Settings → Permalinks → Save  
- [ ] Kiểm tra SSL (HTTPS)  
- [ ] Kiểm tra file `.htaccess` (Apache) hoặc cấu hình nginx  

---

### Bước 5: Kiểm tra Production

- [ ] Trang chủ load đúng  
- [ ] Đăng nhập admin  
- [ ] Media hiển thị đúng  
- [ ] Form liên hệ gửi email  
- [ ] Plugin Yivic Easy Live Chat hoạt động  
- [ ] Không còn link/redirect về ddev.site  

---

### Khắc phục: 404 CSS/JS (Elementor, Contact Form 7, …) sau deploy

Khi console báo nhiều `404 (Not Found)` với file trong `wp-content/plugins/...` (Elementor, Contact Form 7):

1. **Document root đúng**
   - Site phải trỏ vào thư mục **có chứa `wp-config.php`** (trong repo này là thư mục **`web`**).
   - Nếu bạn upload cả repo (có thư mục `web/`), trong cPanel/hosting cần đặt **Document Root** = `public_html/web` (hoặc `domains/test.visaminhquan.com.vn/web`), **không** để `public_html` (vì khi đó WordPress tìm `wp-content` ở `public_html/wp-content` trong khi file thật nằm trong `public_html/web/wp-content`).

2. **Đã upload đủ `wp-content`**
   - Trên server kiểm tra có đủ: `web/wp-content/plugins/`, `web/wp-content/themes/`, `web/wp-content/uploads/`.
   - Đặc biệt có thư mục `web/wp-content/plugins/elementor/` và `web/wp-content/plugins/contact-form-7/` (và các file .css, .js bên trong). Nếu thiếu → upload lại từ ZIP deploy hoặc copy từ máy local.

3. **Phân quyền**
   - Dùng script **chmod-on-server.sh** (có trong `deploy-output/<ngày-giờ>/` khi chạy `./scripts/deploy-prepare.sh all`): upload file lên server, vào thư mục gốc WordPress (chứa `wp-config.php`), chạy `chmod +x chmod-on-server.sh && ./chmod-on-server.sh`.
   - Hoặc thủ công: thư mục `755`, file `644`; `wp-content/uploads`: thư mục `775`, file `664`. Ví dụ: `chmod -R 755 web/wp-content/plugins`.

4. **Regenerate CSS Elementor**
   - Đăng nhập WP Admin → **Elementor** → **Tools** → **Regenerate CSS** (và Regenerate Files nếu có). Sau đó xóa cache (plugin cache / CDN) nếu đang dùng.

5. **Permalink**
   - Vào **Settings → Permalinks**, bấm **Save** (không đổi gì) để refresh rewrite rules.

6. **Cache / CDN**
   - Xóa cache trình duyệt, xóa cache server/CDN (nếu có) rồi tải lại trang.

---

## Phần 3: Share Local (Tunnel)

Khi cần share site local cho người khác xem tạm thời:

### Cloudflare Quick Tunnel

```bash
ddev describe   # Xem port (vd: 127.0.0.1:55000)
cloudflared tunnel --url http://127.0.0.1:55000
```

- [ ] DDEV đang chạy  
- [ ] Cloudflared đã tạo URL `https://xxx.trycloudflare.com`  
- [ ] `wp-config.php` đã cấu hình cho tunnel domain (xem README / wp-config)  
- [ ] Giữ terminal cloudflared mở trong lúc share  

### Ngrok

```bash
ngrok http 55000
```

- [ ] Dùng URL `https://xxx.ngrok-free.app`  
- [ ] Đảm bảo wp-config chấp nhận domain tunnel  

---

## Script chuẩn bị deploy

Trong thư mục dự án có script `scripts/deploy-prepare.sh` để:

- **Replace URL** trong toàn bộ database  
- **Export DB** ra file SQL  
- **ZIP mã nguồn** (loại trừ `.git`, `.ddev`, `node_modules`, `deploy-output`)

### Cách dùng

```bash
# Cho script quyền thực thi (chỉ lần đầu)
chmod +x scripts/deploy-prepare.sh

# Xem hướng dẫn
./scripts/deploy-prepare.sh

# Chuẩn bị deploy (không đổi DB/DEV): export DB đã thay URL trong file + ZIP
./scripts/deploy-prepare.sh all 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn'

# Chỉ export DB đã thay URL trong file (không đổi DB hiện tại)
./scripts/deploy-prepare.sh export-db-replace 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn'

# Chỉ export database (giữ nguyên URL)
./scripts/deploy-prepare.sh export-db

# Chỉ ZIP mã nguồn
./scripts/deploy-prepare.sh zip

# [Cẩn trọng] Replace URL ngay trong DB hiện tại – chỉ dùng khi cố ý đổi URL trên môi trường hiện tại
./scripts/deploy-prepare.sh replace-url 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn'
```

**Môi trường DEV:** Lệnh `all` và `export-db-replace` **không thay đổi** database hay mã nguồn trên máy bạn; chúng chỉ tạo file SQL (đã thay URL) và file ZIP. Chỉ `replace-url` mới ghi đè URL trong DB hiện tại.

Kết quả nằm trong thư mục **`deploy-output/<ngày-giờ>/`** (file `.sql` và `.zip`).

---

## Lưu ý

1. **Backup** trước mỗi lần deploy production.  
2. **Test trên staging** trước khi deploy lên production.  
3. **URL**: luôn search-replace khi đổi môi trường (local → staging → production).  
4. **Secrets**: không commit `wp-config.php` có password thật vào Git.  
