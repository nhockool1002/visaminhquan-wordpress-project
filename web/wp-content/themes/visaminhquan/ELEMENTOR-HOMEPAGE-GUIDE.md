# Hướng dẫn thiết kế trang chủ với Elementor

## Cách sử dụng template

### Bước 1: Tạo trang mới trong WordPress
1. Vào **Pages > Add New**
2. Đặt tên trang: "Trang chủ" hoặc "Home"
3. Click **Edit with Elementor**

### Bước 2: Sử dụng HTML Widget trong Elementor

#### Option 1: Copy từng section
1. Mở file `templates/elementor-homepage-template.html`
2. Copy từng section (Hero, Services, etc.)
3. Trong Elementor, thêm widget **HTML**
4. Paste code vào widget
5. Lặp lại cho từng section

#### Option 2: Sử dụng Shortcode (nếu cần)
Tạo shortcode trong `functions.php` để load template

### Bước 3: Cấu trúc trang chủ

Thứ tự các section từ trên xuống:

1. **Hero Section** - Phần đầu trang với heading và form đăng ký
2. **Dịch vụ Visa trọn gói** - 6 service cards
3. **Dịch vụ khác** - 4 service items
4. **Vì sao chọn Visa Minh Quân** - Process steps
5. **Quy trình làm việc** - Vertical flow
6. **Hồ sơ đã hoàn thành** - Image grid
7. **Testimonials** - Client reviews
8. **Liên hệ** - Contact form
9. **Tin tức** - News section

### Bước 4: Tùy chỉnh trong Elementor

#### Thay đổi màu sắc:
- Primary color: `#2c5282` (Blue)
- Secondary color: `#ffa500` (Orange)
- Background: `#f8f9fa` (Light gray)

#### Thay đổi font:
- Font family: "Segoe UI" (đã có sẵn trong theme)

#### Thay đổi spacing:
- Section padding: `80px 0`
- Container max-width: `1200px`

### Bước 5: Thêm hình ảnh

Thêm các hình ảnh vào thư mục:
- `/wp-content/themes/visaminhquan/assets/images/`
- `hero-illustration.png`
- `process-illustration.png`
- `contact-illustration.png`
- `case-1.jpg` đến `case-9.jpg`
- `testimonial-1.jpg`

### Bước 6: Tùy chỉnh form

Các form trong template cần được kết nối với:
- Contact Form 7 plugin, hoặc
- Elementor Forms widget

### Bước 7: Responsive

Template đã responsive, tự động điều chỉnh:
- Desktop: 3 cột
- Tablet: 2 cột  
- Mobile: 1 cột

## Lưu ý

1. **CSS đã được load tự động** trong `functions.php`
2. **Thay đổi màu sắc** trong file `assets/css/elementor-homepage.css`
3. **Thêm JavaScript** nếu cần slider cho testimonials
4. **Kết nối form** với email hoặc CRM system

## Troubleshooting

- Nếu CSS không load: Kiểm tra file path trong `functions.php`
- Nếu hình ảnh không hiển thị: Kiểm tra đường dẫn trong HTML
- Nếu layout bị lỗi: Kiểm tra Elementor > Settings > Custom CSS

