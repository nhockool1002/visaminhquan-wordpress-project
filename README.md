# Hướng dẫn sử dụng DDEV cho dự án WordPress

## Cài đặt và Khởi động

### Khởi động dự án
```bash
ddev start
```
Khởi động tất cả các container DDEV cho dự án.

### Dừng dự án
```bash
ddev stop
```
Dừng tất cả các container nhưng giữ lại dữ liệu.

### Dừng và xóa dữ liệu
```bash
ddev stop --unlist
```
Dừng và xóa dự án khỏi danh sách.

### Khởi động lại dự án
```bash
ddev restart
```
Khởi động lại tất cả các container.

### Kiểm tra trạng thái
```bash
ddev status
```
Hiển thị trạng thái hiện tại của dự án.

## Quản lý Database

### Import database
```bash
ddev import-db --file=path/to/database.sql
```
Import file SQL vào database.

### Export database
```bash
ddev export-db --file=database-backup.sql
```
Export database ra file SQL.

### Truy cập MySQL
```bash
ddev mysql
```
Mở MySQL command line.

### Truy cập MySQL với database cụ thể
```bash
ddev mysql -e "SELECT * FROM wp_posts LIMIT 10;"
```
Chạy câu lệnh SQL trực tiếp.

### Xem thông tin database
```bash
ddev describe
```
Hiển thị thông tin chi tiết về dự án, bao gồm database credentials.

## Truy cập Container

### SSH vào container web
```bash
ddev ssh
```
Truy cập vào container web chính.

### Chạy lệnh trong container
```bash
ddev exec <command>
```
Chạy lệnh bất kỳ trong container web.

### Chạy lệnh trong container database
```bash
ddev exec -s db <command>
```
Chạy lệnh trong container database.

### Chạy lệnh với quyền root
```bash
ddev exec -u root <command>
```
Chạy lệnh với quyền root.

## WordPress CLI (WP-CLI)

### Cài đặt WordPress
```bash
ddev wp core download
ddev wp core install --url=https://visaminhquan.ddev.site --title="Visa Minh Quan" --admin_user=admin --admin_password=admin --admin_email=admin@example.com
```

### Cập nhật WordPress core
```bash
ddev wp core update
```

### Cập nhật plugins
```bash
ddev wp plugin update --all
```

### Cài đặt plugin
```bash
ddev wp plugin install <plugin-name> --activate
```

### Cài đặt theme
```bash
ddev wp theme install <theme-name> --activate
```

### Xem danh sách plugins
```bash
ddev wp plugin list
```

### Xem danh sách themes
```bash
ddev wp theme list
```

### Tìm kiếm và thay thế URL trong database
```bash
ddev wp search-replace 'old-url.com' 'new-url.com'
```

### Xóa cache
```bash
ddev wp cache flush
```

### Export database
```bash
ddev wp db export backup.sql
```

### Import database
```bash
ddev wp db import backup.sql
```

## Xem Logs

### Xem logs của tất cả services
```bash
ddev logs
```

### Xem logs của web server
```bash
ddev logs -s web
```

### Xem logs của database
```bash
ddev logs -s db
```

### Xem logs theo thời gian thực
```bash
ddev logs -f
```

### Xem logs PHP
```bash
ddev logs -s web | grep php
```

## Quản lý Files

### Import files
```bash
ddev import-files --source=path/to/files
```
Import files vào thư mục uploads.

### Export files
```bash
ddev export-files --destination=path/to/backup
```
Export files từ thư mục uploads.

## Cấu hình và Thông tin

### Xem thông tin dự án
```bash
ddev describe
```
Hiển thị URL, database credentials, và thông tin khác.

### Xem cấu hình
```bash
ddev config
```
Hiển thị cấu hình hiện tại của dự án.

### Chỉnh sửa cấu hình
```bash
ddev config --project-name=visaminhquan --docroot=web --php-version=8.2
```

### Xem danh sách tất cả dự án
```bash
ddev list
```

## Power và Cleanup

### Xóa tất cả dữ liệu và khởi động lại
```bash
ddev poweroff
```
Dừng tất cả các dự án DDEV.

### Xóa dữ liệu và khởi động lại từ đầu
```bash
ddev stop --remove-data
ddev start
```

### Xóa cache và khởi động lại
```bash
ddev restart --clean
```

## Composer và NPM

### Chạy Composer
```bash
ddev composer install
ddev composer update
ddev composer require <package>
```

### Chạy NPM
```bash
ddev npm install
ddev npm run build
ddev npm run dev
```

## Xdebug

### Bật Xdebug
```bash
ddev xdebug on
```

### Tắt Xdebug
```bash
ddev xdebug off
```

### Kiểm tra trạng thái Xdebug
```bash
ddev xdebug status
```

## MailHog (Email Testing)

### Truy cập MailHog
Mở trình duyệt và truy cập: `https://visaminhquan.ddev.site:8025`

Hoặc xem URL trong:
```bash
ddev describe
```

## Các lệnh hữu ích khác

### Xem version DDEV
```bash
ddev version
```

### Kiểm tra sức khỏe dự án
```bash
ddev healthcheck
```

### Xem thông tin chi tiết về service
```bash
ddev describe -s web
ddev describe -s db
```

### Mở trình duyệt
```bash
ddev launch
```
Mở URL chính của dự án trong trình duyệt.

### Mở phpMyAdmin
```bash
ddev launch -p
```
Mở phpMyAdmin trong trình duyệt.

## Troubleshooting

### Khởi động lại với clean state
```bash
ddev stop --remove-data
ddev start
```

### Xem logs lỗi
```bash
ddev logs -s web --tail=100
```

### Kiểm tra kết nối database
```bash
ddev mysql -e "SHOW DATABASES;"
```

### Kiểm tra PHP version
```bash
ddev exec php -v
```

### Kiểm tra WordPress version
```bash
ddev wp core version
```

### Lỗi kết nối WordPress.org (SSL Error)

Nếu gặp lỗi:
```
Warning: wp_version_check(): An unexpected error occurred. 
WordPress could not establish a secure connection to WordPress.org.
```

**Giải pháp 1: Thêm constants vào wp-config.php**

Thêm các dòng sau vào file `web/wp-config.php` (trước dòng `/* That's all, stop editing! */`):

```php
// Tắt SSL verification cho WordPress.org (chỉ dùng trong môi trường development)
define('WP_HTTP_BLOCK_EXTERNAL', false);
define('WP_ACCESSIBLE_HOSTS', '*.wordpress.org,*.github.com');
add_filter('https_ssl_verify', '__return_false');
add_filter('https_local_ssl_verify', '__return_false');
```

**Giải pháp 2: Sửa wp-config-ddev.php (Khuyến nghị)**

Thêm vào file `web/wp-config-ddev.php` (trong block `if ( getenv( 'IS_DDEV_PROJECT' ) == 'true' )`):

```php
// Cho phép WordPress kết nối đến WordPress.org
define('WP_HTTP_BLOCK_EXTERNAL', false);
define('WP_ACCESSIBLE_HOSTS', '*.wordpress.org,*.github.com');
add_filter('https_ssl_verify', '__return_false');
add_filter('https_local_ssl_verify', '__return_false');
```

**Giải pháp 3: Kiểm tra kết nối mạng**

```bash
# Kiểm tra kết nối đến WordPress.org
ddev exec curl -I https://api.wordpress.org

# Kiểm tra DNS
ddev exec nslookup wordpress.org

# Kiểm tra SSL certificate
ddev exec openssl s_client -connect api.wordpress.org:443 -showcerts
```

**Giải pháp 4: Tắt update checks tạm thời**

Thêm vào `wp-config.php`:

```php
// Tắt automatic update checks
define('WP_AUTO_UPDATE_CORE', false);
define('AUTOMATIC_UPDATER_DISABLED', true);
```

**Giải pháp 5: Khởi động lại DDEV**

```bash
ddev restart
```

Sau khi áp dụng, khởi động lại:
```bash
ddev restart
```

## Lưu ý

- URL mặc định của dự án thường là: `https://visaminhquan.ddev.site`
- Database credentials có thể xem bằng lệnh `ddev describe`
- Tất cả các lệnh cần chạy từ thư mục gốc của dự án
- Để xem thêm thông tin về một lệnh cụ thể, sử dụng: `ddev <command> --help`

