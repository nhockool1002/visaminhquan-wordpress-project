<?php
/**
 * Check Visa Pass Rate Form Plugin
 * 
 * Shortcode: [nhut_check_visa_pass_rate]
 * 
 * Form kiểm tra tỉ lệ đậu VISA với 3 bước
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
    
    /**
     * Load CSS và JS
     */
    private function load_assets() {
        // Chỉ load trên frontend, không load trong admin hoặc widgets editor
        if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }
        
        if (self::$assets_loaded) {
            return;
        }
        
        $plugin_url = content_url('nhut-addon/nhutplugin_check_visa_pass_rate');
        
        wp_enqueue_style(
            'nhut-check-visa-css',
            $plugin_url . '/assets/css/check-visa-pass-rate.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'nhut-check-visa-js',
            $plugin_url . '/assets/js/check-visa-pass-rate.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Localize script để truyền AJAX URL
        wp_localize_script('nhut-check-visa-js', 'nhutVisaForm', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nhut_visa_form_nonce')
        ));
        
        self::$assets_loaded = true;
    }
    
    /**
     * Render form shortcode
     */
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
                
                <!-- Progress Indicator -->
                <div class="nhut-visa-progress">
                    <div class="nhut-progress-step nhut-step-active" data-step="1">
                        <div class="nhut-progress-circle">1</div>
                    </div>
                    <div class="nhut-progress-line"></div>
                    <div class="nhut-progress-step" data-step="2">
                        <div class="nhut-progress-circle">2</div>
                    </div>
                    <div class="nhut-progress-line"></div>
                    <div class="nhut-progress-step" data-step="3">
                        <div class="nhut-progress-circle">3</div>
                    </div>
                </div>
                
                <form class="nhut-visa-form" id="<?php echo esc_attr($form_id); ?>-form">
                    <!-- Step 1 -->
                    <div class="nhut-form-step nhut-step-active" data-step="1">
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Bạn muốn làm VISA đi nước nào?
                                <span class="nhut-required">*</span>
                            </label>
                            <div class="nhut-select-wrapper">
                                <select name="visa_country" class="nhut-form-select" required>
                                    <option value="">-- Chọn quốc gia --</option>
                                    <option value="Visa Úc">Visa Úc</option>
                                    <option value="Visa New Zealand">Visa New Zealand</option>
                                    <option value="Visa Cuba">Visa Cuba</option>
                                    <option value="Visa Brazil">Visa Brazil</option>
                                    <option value="Visa Mỹ">Visa Mỹ</option>
                                    <option value="Visa Mexico">Visa Mexico</option>
                                    <option value="Visa Canada">Visa Canada</option>
                                    <option value="Visa Anh">Visa Anh</option>
                                    <option value="Visa Đức">Visa Đức</option>
                                    <option value="Visa Ý">Visa Ý</option>
                                    <option value="Visa Pháp">Visa Pháp</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Bạn đã đi nước nào chưa?
                                <span class="nhut-required">*</span>
                            </label>
                            <div class="nhut-select-wrapper">
                                <select name="traveled_before" class="nhut-form-select" required>
                                    <option value="">-- Chọn --</option>
                                    <option value="Rồi">Rồi</option>
                                    <option value="Chưa">Chưa</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="nhut-form-actions">
                            <button type="button" class="nhut-btn nhut-btn-next">Tiếp theo</button>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="nhut-form-step" data-step="2">
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Bạn có số tiết kiệm không?
                                <span class="nhut-required">*</span>
                            </label>
                            <div class="nhut-select-wrapper">
                                <select name="savings" class="nhut-form-select" required>
                                    <option value="">-- Chọn --</option>
                                    <option value="Sổ tiết kiệm > 100 triệu">Sổ tiết kiệm > 100 triệu</option>
                                    <option value="Sổ tiết kiệm < 100 triệu">Sổ tiết kiệm < 100 triệu</option>
                                    <option value="Bạn chưa có số tiết kiệm">Bạn chưa có số tiết kiệm</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Bạn có sổ đỏ hay oto không?
                                <span class="nhut-required">*</span>
                            </label>
                            <div class="nhut-select-wrapper">
                                <select name="property_car" class="nhut-form-select" required>
                                    <option value="">-- Chọn --</option>
                                    <option value="Có">Có</option>
                                    <option value="Không">Không</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Công việc hiện tại của bạn là gì?
                                <span class="nhut-required">*</span>
                            </label>
                            <div class="nhut-select-wrapper">
                                <select name="current_job" class="nhut-form-select" required>
                                    <option value="">-- Chọn --</option>
                                    <option value="Nhân viên (Tư nhân)">Nhân viên (Tư nhân)</option>
                                    <option value="Nhân viên (Nhà nước)">Nhân viên (Nhà nước)</option>
                                    <option value="Tự doanh">Tự doanh</option>
                                    <option value="Học sinh/Sinh viên">Học sinh/Sinh viên</option>
                                    <option value="Nghỉ hưu">Nghỉ hưu</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="nhut-form-actions">
                            <button type="button" class="nhut-btn nhut-btn-back">Quay lại</button>
                            <button type="button" class="nhut-btn nhut-btn-next">Tiếp theo</button>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="nhut-form-step" data-step="3">
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Họ và tên
                                <span class="nhut-required">*</span>
                            </label>
                            <input type="text" name="full_name" class="nhut-form-input" required placeholder="Nhập họ và tên" />
                        </div>
                        
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Địa chỉ email
                                <span class="nhut-required">*</span>
                            </label>
                            <input type="email" name="email" class="nhut-form-input" required placeholder="Nhập địa chỉ email" />
                        </div>
                        
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Số điện thoại
                                <span class="nhut-required">*</span>
                            </label>
                            <input type="tel" name="phone" class="nhut-form-input" required placeholder="09x xxxx xxxx" />
                        </div>
                        
                        <div class="nhut-form-field">
                            <label class="nhut-form-label">
                                Vui lòng điền đáp án bằng số
                            </label>
                            <div class="nhut-captcha-wrapper">
                                <span class="nhut-captcha-question">
                                    <span class="nhut-captcha-num1">6</span> + <span class="nhut-captcha-num2">10</span>
                                </span>
                                <input type="number" name="captcha_answer" class="nhut-form-input nhut-captcha-input" required placeholder="Nhập kết quả" />
                            </div>
                        </div>
                        
                        <div class="nhut-form-actions">
                            <button type="button" class="nhut-btn nhut-btn-back">Quay lại</button>
                            <button type="submit" class="nhut-btn nhut-btn-submit">Gửi yêu cầu</button>
                        </div>
                    </div>
                    
                    <!-- Social Icons -->
                    <div class="nhut-form-social">
                        <a href="#" class="nhut-social-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <img src="<?php echo esc_url( $fb_icon_url ); ?>" alt="Facebook" />
                        </a>
                        <a href="#" class="nhut-social-link" target="_blank" rel="noopener noreferrer" aria-label="Zalo">
                            <img src="<?php echo esc_url( $zalo_icon_url ); ?>" alt="Zalo" />
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Handle form submission via AJAX
     */
    public function handle_form_submit() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'nhut_visa_form_nonce')) {
            wp_send_json_error(array('message' => 'Invalid security token'));
            return;
        }
        
        // Get form data
        $visa_country = isset($_POST['visa_country']) ? sanitize_text_field($_POST['visa_country']) : '';
        $traveled_before = isset($_POST['traveled_before']) ? sanitize_text_field($_POST['traveled_before']) : '';
        $savings = isset($_POST['savings']) ? sanitize_text_field($_POST['savings']) : '';
        $property_car = isset($_POST['property_car']) ? sanitize_text_field($_POST['property_car']) : '';
        $current_job = isset($_POST['current_job']) ? sanitize_text_field($_POST['current_job']) : '';
        $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $captcha_answer = isset($_POST['captcha_answer']) ? intval($_POST['captcha_answer']) : 0;
        $captcha_num1 = isset($_POST['captcha_num1']) ? intval($_POST['captcha_num1']) : 0;
        $captcha_num2 = isset($_POST['captcha_num2']) ? intval($_POST['captcha_num2']) : 0;
        
        // Validate captcha
        if ($captcha_answer !== ($captcha_num1 + $captcha_num2)) {
            wp_send_json_error(array('message' => 'Captcha không đúng'));
            return;
        }
        
        // Validate required fields
        if (empty($visa_country) || empty($traveled_before) || empty($savings) || 
            empty($property_car) || empty($current_job) || empty($full_name) || 
            empty($email) || empty($phone)) {
            wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ thông tin'));
            return;
        }
        
        // Get email recipient from WP Mail SMTP settings or use admin email
        $to_email = get_option('admin_email');
        
        // Try to get from WP Mail SMTP settings
        // WP Mail SMTP stores settings in different formats depending on version
        $smtp_settings = get_option('wp_mail_smtp', array());
        
        // Check for new format (v2.0+)
        if (!empty($smtp_settings['mail']['from_email'])) {
            $to_email = $smtp_settings['mail']['from_email'];
        }
        // Check for old format
        elseif (!empty($smtp_settings['mail']['mail_from_email'])) {
            $to_email = $smtp_settings['mail']['mail_from_email'];
        }
        // Check for general settings
        elseif (!empty($smtp_settings['general']['mail_from_email'])) {
            $to_email = $smtp_settings['general']['mail_from_email'];
        }
        
        // Email subject
        $subject = 'Yêu cầu kiểm tra tỉ lệ đậu VISA - ' . $full_name;
        
        // Email body
        $message = "YÊU CẦU KIỂM TRA TỈ LỆ ĐẬU VISA\n\n";
        $message .= "THÔNG TIN KHÁCH HÀNG:\n";
        $message .= "Họ và tên: " . $full_name . "\n";
        $message .= "Email: " . $email . "\n";
        $message .= "Số điện thoại: " . $phone . "\n\n";
        
        $message .= "THÔNG TIN VISA:\n";
        $message .= "Bạn muốn làm VISA đi nước nào: " . $visa_country . "\n";
        $message .= "Bạn đã đi nước nào chưa: " . $traveled_before . "\n\n";
        
        $message .= "THÔNG TIN TÀI CHÍNH VÀ CÔNG VIỆC:\n";
        $message .= "Bạn có số tiết kiệm không: " . $savings . "\n";
        $message .= "Bạn có sổ đỏ hay oto không: " . $property_car . "\n";
        $message .= "Công việc hiện tại của bạn là gì: " . $current_job . "\n";
        
        // Email headers
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        // Send email
        $mail_sent = wp_mail($to_email, $subject, $message, $headers);
        
        if ($mail_sent) {
            wp_send_json_success(array('message' => 'Gửi thành công'));
        } else {
            wp_send_json_error(array('message' => 'Gửi thất bại. Vui lòng thử lại sau.'));
        }
    }
    
    /**
     * Load form via AJAX
     */
    public function load_form_ajax() {
        // Load assets
        $this->load_assets();
        
        // Render form
        $form_html = $this->render_form(array());
        
        wp_send_json_success(array('html' => $form_html));
    }
}

// Initialize
Nhut_Check_Visa_Pass_Rate::get_instance();

