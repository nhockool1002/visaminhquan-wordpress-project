<?php
/**
 * Check Visa Pass Rate Form Plugin
 * Shortcode: [nhut_check_visa_pass_rate]
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nhut_Check_Visa_Pass_Rate {
    
    private static $instance = null;
    private static $assets_loaded = false;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('nhut_check_visa_pass_rate', array($this, 'render_form'));
        add_action('wp_ajax_nhut_submit_visa_form', array($this, 'handle_form_submit'));
        add_action('wp_ajax_nopriv_nhut_submit_visa_form', array($this, 'handle_form_submit'));
        add_action('wp_ajax_nhut_load_visa_form', array($this, 'load_form_ajax'));
        add_action('wp_ajax_nopriv_nhut_load_visa_form', array($this, 'load_form_ajax'));
    }
    
    private function load_assets() {
        if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) return;
        if (self::$assets_loaded) return;
        
        $plugin_url = content_url('nhut-addon/nhutplugin_check_visa_pass_rate');
        wp_enqueue_style('nhut-check-visa-css', $plugin_url . '/assets/css/check-visa-pass-rate.css', array(), '1.0.0');
        wp_enqueue_script('nhut-check-visa-js', $plugin_url . '/assets/js/check-visa-pass-rate.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('nhut-check-visa-js', 'nhutVisaForm', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nhut_visa_form_nonce')
        ));
        self::$assets_loaded = true;
    }
    
    public function render_form($atts) {
        $this->load_assets();
        $form_id = 'nhut-visa-form-' . uniqid();
        $fb_icon_url   = content_url( 'uploads/2026/01/fbic.png' );
        $zalo_icon_url = content_url( 'uploads/2026/01/zlic.png' );
        
        ob_start();
        ?>
        <div class="nhut-visa-form-wrapper" id="<?php echo esc_attr($form_id); ?>">
            <div class="nhut-visa-form-container">
                <h1 class="nhut-visa-form-title">KIỂM TRA TỈ LỆ ĐẬU VISA</h1>
                <div class="nhut-visa-progress">
                    <div class="nhut-progress-step nhut-step-active" data-step="1"><div class="nhut-progress-circle">1</div></div>
                    <div class="nhut-progress-line"></div>
                    <div class="nhut-progress-step" data-step="2"><div class="nhut-progress-circle">2</div></div>
                    <div class="nhut-progress-line"></div>
                    <div class="nhut-progress-step" data-step="3"><div class="nhut-progress-circle">3</div></div>
                </div>
                <form class="nhut-visa-form" id="<?php echo esc_attr($form_id); ?>-form">
                    <div class="nhut-form-step nhut-step-active" data-step="1">
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">Bạn muốn làm VISA đi nước nào? <span class="nhut-required">*</span></label>
                            <select name="visa_country" class="nhut-form-select" required>
                                <option value="">-- Chọn quốc gia --</option>
                                <option value="Visa Úc">Visa Úc</option><option value="Visa New Zealand">Visa New Zealand</option>
                                <option value="Visa Mỹ">Visa Mỹ</option><option value="Visa Canada">Visa Canada</option>
                                <option value="Visa Anh">Visa Anh</option><option value="Visa Pháp">Visa Pháp</option>
                            </select>
                        </div>
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">Bạn đã đi nước nào chưa? <span class="nhut-required">*</span></label>
                            <select name="traveled_before" class="nhut-form-select" required>
                                <option value="">-- Chọn --</option><option value="Rồi">Rồi</option><option value="Chưa">Chưa</option>
                            </select>
                        </div>
                        <div class="nhut-form-actions"><button type="button" class="nhut-btn nhut-btn-next">Tiếp theo</button></div>
                    </div>
                    <div class="nhut-form-step" data-step="2">
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">Tài chính & Công việc <span class="nhut-required">*</span></label>
                            <select name="savings" class="nhut-form-select" required>
                                <option value="">-- Sổ tiết kiệm --</option>
                                <option value="Sổ tiết kiệm > 100 triệu">Sổ tiết kiệm > 100 triệu</option>
                                <option value="Sổ tiết kiệm < 100 triệu">Sổ tiết kiệm < 100 triệu</option>
                            </select>
                        </div>
                        <div class="nhut-form-field">
                            <select name="property_car" class="nhut-form-select" required>
                                <option value="">-- Có sổ đỏ/oto không? --</option>
                                <option value="Có">Có</option><option value="Không">Không</option>
                            </select>
                        </div>
                        <div class="nhut-form-field">
                            <select name="current_job" class="nhut-form-select" required>
                                <option value="">-- Công việc hiện tại --</option>
                                <option value="Nhân viên">Nhân viên</option><option value="Tự doanh">Tự doanh</option>
                                <option value="Học sinh/Sinh viên">Học sinh/Sinh viên</option>
                            </select>
                        </div>
                        <div class="nhut-form-actions">
                            <button type="button" class="nhut-btn nhut-btn-back">Quay lại</button>
                            <button type="button" class="nhut-btn nhut-btn-next">Tiếp theo</button>
                        </div>
                    </div>
                    <div class="nhut-form-step" data-step="3">
                        <div class="nhut-form-field"><input type="text" name="full_name" class="nhut-form-input" required placeholder="Họ và tên" /></div>
                        <div class="nhut-form-field"><input type="email" name="email" class="nhut-form-input" required placeholder="Địa chỉ email" /></div>
                        <div class="nhut-form-field"><input type="tel" name="phone" class="nhut-form-input" required placeholder="Số điện thoại" /></div>
                        <div class="nhut-form-field">
                            <div class="nhut-captcha-wrapper">
                                <span class="nhut-captcha-question"><span class="nhut-captcha-num1">6</span> + <span class="nhut-captcha-num2">10</span></span>
                                <input type="number" name="captcha_answer" class="nhut-form-input nhut-captcha-input" required placeholder="Đáp án" />
                            </div>
                        </div>
                        <div class="nhut-form-actions">
                            <button type="button" class="nhut-btn nhut-btn-back">Quay lại</button>
                            <button type="submit" class="nhut-btn nhut-btn-submit">Gửi yêu cầu</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function handle_form_submit() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'nhut_visa_form_nonce')) {
            wp_send_json_error(array('message' => 'Invalid security token'));
            return;
        }
        
        $visa_country = sanitize_text_field($_POST['visa_country']);
        $traveled_before = sanitize_text_field($_POST['traveled_before']);
        $savings = sanitize_text_field($_POST['savings']);
        $property_car = sanitize_text_field($_POST['property_car']);
        $current_job = sanitize_text_field($_POST['current_job']);
        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        
        $services_info = "Quốc gia: $visa_country | Đi nước ngoài: $traveled_before | Tiết kiệm: $savings | Tài sản: $property_car | Việc làm: $current_job";
        $current_time = date('H:i d/m/Y');
        $subject = '[VISAMINHQUAN.COM.VN] Yêu cầu kiểm tra tỉ lệ đậu VISA - ' . mb_strtoupper($full_name);
        
        $body_html = '
        <div style="background-color: #f4f7f6; padding: 20px; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.5;">
            <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0;">
                <div style="background-color: #0056b3; padding: 15px; text-align: center;"><h2 style="color: #ffffff; margin: 0; font-size: 18px; text-transform: uppercase;">Kiểm Tra Tỉ Lệ Đậu</h2></div>
                <div style="padding: 25px;">
                    <p style="font-weight: bold; color: #0056b3;">Thông tin từ VISAMINHQUAN.COM.VN</p>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold; width: 40%;">👤 Họ và tên:</td><td style="padding: 10px 0; border-bottom: 1px solid #eee;">'.mb_strtoupper($full_name).'</td></tr>
                        <tr><td style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold;">📞 Số điện thoại:</td><td style="padding: 10px 0; border-bottom: 1px solid #eee; color: #0056b3; font-weight: bold;">'.$phone.'</td></tr>
                        <tr><td style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold;">📧 Email:</td><td style="padding: 10px 0; border-bottom: 1px solid #eee;">'.$email.'</td></tr>
                        <tr><td colspan="2" style="padding: 15px 0 5px 0; font-weight: bold;">🌍 Chi tiết hồ sơ:</td></tr>
                        <tr><td colspan="2" style="padding: 12px; background-color: #fff9e6; border-left: 4px solid #f0ad4e; color: #856404; font-weight: bold; font-size: 13px;">'.$services_info.'</td></tr>
                    </table>
                    <div style="margin-top: 25px; text-align: center;"><a href="tel:'.$phone.'" style="background-color: #28a745; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">GỌI ĐIỆN HỖ TRỢ NGAY</a></div>
                </div>
                <div style="background-color: #f9f9f9; padding: 12px; text-align: center; font-size: 11px; color: #999;"><p>Email gửi tự động từ Website vào lúc '.$current_time.'</p></div>
            </div>
        </div>';
        
        $mail_sent = vmq_send_mailjet_api($subject, $body_html, $email);
        if ($mail_sent) { wp_send_json_success(array('message' => 'Gửi thành công')); } 
        else { wp_send_json_error(array('message' => 'Gửi thất bại')); }
    }
    
    public function load_form_ajax() {
        wp_send_json_success(array('html' => $this->render_form(array())));
    }
}
Nhut_Check_Visa_Pass_Rate::get_instance();