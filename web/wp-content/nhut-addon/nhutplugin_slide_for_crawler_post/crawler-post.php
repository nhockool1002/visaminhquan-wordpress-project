<?php
/**
 * Crawler Post Plugin
 * 
 * Crawl bài viết từ visaminhquan.com.vn và insert vào WordPress
 * 
 * Admin page: wp-admin/admin.php?page=nhut-crawler-post
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nhut_Crawler_Post {
    
    private static $instance = null;
    
    // Available sites configuration
    private $available_sites = array(
        'visaminhquan' => array(
            'name' => 'visaminhquan.com.vn',
            'base_url' => 'https://visaminhquan.com.vn',
            'target_url' => 'https://visaminhquan.com.vn/visa-quoc-te',
            'link_pattern' => '/visa-quoc-te/|/visa-[a-z-]+\/?$/'
        ),
        'dulichviet' => array(
            'name' => 'dulichviet.com.vn',
            'base_url' => 'https://dulichviet.com.vn',
            'target_url' => 'https://dulichviet.com.vn/lam-visa',
            'link_pattern' => '/lam-visa/|/visa-[a-z-]+\/?$/'
        )
    );
    
    private $target_url = 'https://visaminhquan.com.vn/visa-quoc-te';
    private $base_url = 'https://visaminhquan.com.vn';
    private $current_site = 'visaminhquan';

    // Used to scope cURL tuning to a single request host (avoid affecting other WP HTTP calls)
    private $curl_tune_host = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_nhut_crawler_scan', array($this, 'ajax_crawler_scan'));
        add_action('wp_ajax_nhut_crawler_start', array($this, 'ajax_crawler_start'));
        add_action('wp_ajax_nhut_crawler_process', array($this, 'ajax_crawler_process'));
        add_action('wp_ajax_nhut_crawler_get_progress', array($this, 'ajax_get_progress'));
        add_action('wp_ajax_nhut_crawler_truncate', array($this, 'ajax_truncate_posts'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_management_page(
            'Crawler Bài Viết',
            'Crawler Bài Viết',
            'manage_options',
            'nhut-crawler-post',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'tools_page_nhut-crawler-post') {
            return;
        }
        
        wp_enqueue_script(
            'nhut-crawler-admin-js',
            content_url('nhut-addon/nhutplugin_slide_for_crawler_post/assets/js/crawler-admin.js'),
            array('jquery'),
            '1.0.0',
            true
        );
        
        wp_localize_script('nhut-crawler-admin-js', 'nhutCrawler', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nhut_crawler_nonce')
        ));
        
        wp_enqueue_style(
            'nhut-crawler-admin-css',
            content_url('nhut-addon/nhutplugin_slide_for_crawler_post/assets/css/crawler-admin.css'),
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Bạn không có quyền truy cập trang này.');
        }
        
        $total_crawled = get_option('nhut_crawler_total_crawled', 0);
        $total_inserted = get_option('nhut_crawler_total_inserted', 0);
        $last_run = get_option('nhut_crawler_last_run', 'Chưa chạy');
        ?>
        <div class="wrap nhut-crawler-wrap">
            <h1>Crawler Bài Viết</h1>
            
            <div class="nhut-crawler-site-selector" style="margin-bottom: 20px;">
                <label for="nhut-crawler-site" style="font-weight: 600; margin-right: 10px;">Chọn site để crawler:</label>
                <select id="nhut-crawler-site" name="crawler_site" style="min-width: 250px; padding: 5px;">
                    <?php 
                    $selected_site = get_option('nhut_crawler_selected_site', 'visaminhquan');
                    foreach ($this->available_sites as $site_key => $site_config) {
                        $selected = ($selected_site === $site_key) ? 'selected' : '';
                        echo '<option value="' . esc_attr($site_key) . '" ' . $selected . '>' . esc_html($site_config['name']) . '</option>';
                    }
                    ?>
                </select>
                <p class="description" style="margin-top: 5px;">
                    Site hiện tại: <strong id="nhut-current-site-name"><?php echo esc_html($this->available_sites[$selected_site]['name']); ?></strong>
                </p>
            </div>
            
            <div class="nhut-crawler-stats">
                <div class="nhut-stat-box">
                    <h3>Tổng số bài đã crawl</h3>
                    <p class="nhut-stat-number"><?php echo esc_html($total_crawled); ?></p>
                </div>
                <div class="nhut-stat-box">
                    <h3>Tổng số bài đã insert</h3>
                    <p class="nhut-stat-number"><?php echo esc_html($total_inserted); ?></p>
                </div>
                <div class="nhut-stat-box">
                    <h3>Lần chạy cuối</h3>
                    <p class="nhut-stat-text"><?php echo esc_html($last_run); ?></p>
                </div>
            </div>
            
            <div class="nhut-crawler-controls">
                <div style="margin-bottom: 15px;">
                    <button id="nhut-crawler-scan" class="button button-secondary button-large">
                        Quét danh sách bài viết
                    </button>
                    <span id="nhut-scan-result" style="margin-left: 15px; font-weight: 600; color: #2271b1;"></span>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="nhut-crawler-limit" style="font-weight: 600; margin-right: 10px;">
                        Số lượng bài viết muốn crawl (0 = tất cả):
                    </label>
                    <input type="number" id="nhut-crawler-limit" name="crawler_limit" value="0" min="0" style="width: 100px; padding: 5px;">
                    <p class="description" style="margin-top: 5px;">
                        Để 0 để crawl tất cả bài viết có thể. Các bài viết đã crawl sẽ tự động bỏ qua.
                    </p>
                </div>
                
                <div>
                    <button id="nhut-crawler-start" class="button button-primary button-large" disabled>
                        Bắt đầu Crawl
                    </button>
                    <button id="nhut-crawler-stop" class="button button-secondary button-large" style="display:none;">
                        Dừng Crawl
                    </button>
                    <button id="nhut-crawler-truncate" class="button button-secondary button-large" style="margin-left: 10px;">
                        Xóa tất cả bài viết Crawler
                    </button>
                </div>
            </div>
            
            <div id="nhut-crawler-progress" class="nhut-crawler-progress" style="display:none;">
                <div class="nhut-progress-bar">
                    <div class="nhut-progress-fill" id="nhut-progress-fill"></div>
                </div>
                <p class="nhut-progress-text" id="nhut-progress-text">Đang khởi tạo...</p>
                <div class="nhut-progress-log" id="nhut-progress-log"></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Scan posts (get list without crawling)
     */
    public function ajax_crawler_scan() {
        check_ajax_referer('nhut_crawler_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền'));
            return;
        }
        
        // Get selected site
        $selected_site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : 'visaminhquan';
        
        if (!isset($this->available_sites[$selected_site])) {
            wp_send_json_error(array('message' => 'Site không hợp lệ'));
            return;
        }
        
        // Set current site configuration
        $this->current_site = $selected_site;
        $site_config = $this->available_sites[$selected_site];
        $this->base_url = $site_config['base_url'];
        $this->target_url = $site_config['target_url'];
        
        // Get list of posts
        $all_posts = $this->get_post_list();
        
        if (is_wp_error($all_posts)) {
            wp_send_json_error(array('message' => $all_posts->get_error_message()));
            return;
        }
        
        // Filter out already crawled posts
        $new_posts = array();
        $already_crawled = 0;
        
        foreach ($all_posts as $post_url) {
            $existing = get_posts(array(
                'post_type' => 'post',
                'meta_query' => array(
                    array(
                        'key' => '_crawled_url',
                        'value' => $post_url,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1,
                'fields' => 'ids'
            ));
            
            if (empty($existing)) {
                $new_posts[] = $post_url;
            } else {
                $already_crawled++;
            }
        }
        
        // Save scan result
        update_option('nhut_crawler_scan_result', array(
            'total' => count($all_posts),
            'new' => count($new_posts),
            'already_crawled' => $already_crawled,
            'posts' => $new_posts,
            'site' => $selected_site
        ));
        
        wp_send_json_success(array(
            'total' => count($all_posts),
            'new' => count($new_posts),
            'already_crawled' => $already_crawled,
            'message' => sprintf(
                'Tìm thấy %d bài viết. Có %d bài mới có thể crawl, %d bài đã crawl trước đó.',
                count($all_posts),
                count($new_posts),
                $already_crawled
            )
        ));
    }
    
    /**
     * AJAX: Start crawler
     */
    public function ajax_crawler_start() {
        check_ajax_referer('nhut_crawler_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền'));
            return;
        }
        
        // Get selected site
        $selected_site = isset($_POST['site']) ? sanitize_text_field($_POST['site']) : 'visaminhquan';
        
        if (!isset($this->available_sites[$selected_site])) {
            wp_send_json_error(array('message' => 'Site không hợp lệ'));
            return;
        }
        
        // Save selected site
        update_option('nhut_crawler_selected_site', $selected_site);
        
        // Set current site configuration
        $this->current_site = $selected_site;
        $site_config = $this->available_sites[$selected_site];
        $this->base_url = $site_config['base_url'];
        $this->target_url = $site_config['target_url'];
        
        // Reset progress
        update_option('nhut_crawler_progress', array(
            'current' => 0,
            'total' => 0,
            'status' => 'initializing',
            'message' => 'Đang lấy danh sách bài viết từ ' . $site_config['name'] . '...',
            'posts' => array(),
            'errors' => array(),
            'log_messages' => array(),
            'site' => $selected_site
        ));
        
        // Get limit from POST
        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 0;
        
        // Get list of posts to crawl (use scan result if available, otherwise scan now)
        $scan_result = get_option('nhut_crawler_scan_result', null);
        
        if ($scan_result && isset($scan_result['site']) && $scan_result['site'] === $selected_site) {
            // Use cached scan result
            $all_posts = $scan_result['posts'];
            $log_messages = array();
            $log_messages[] = array(
                'type' => 'info',
                'message' => sprintf(
                    'Sử dụng kết quả scan: Tổng %d bài, %d bài mới, %d bài đã crawl (bỏ qua)',
                    $scan_result['total'],
                    $scan_result['new'],
                    $scan_result['already_crawled']
                )
            );
        } else {
            // Scan now
            $all_posts_raw = $this->get_post_list();
            
            if (is_wp_error($all_posts_raw)) {
                wp_send_json_error(array('message' => $all_posts_raw->get_error_message()));
                return;
            }
            
            // Filter out already crawled posts
            $new_posts = array();
            $already_crawled = 0;
            
            foreach ($all_posts_raw as $post_url) {
                $existing = get_posts(array(
                    'post_type' => 'post',
                    'meta_query' => array(
                        array(
                            'key' => '_crawled_url',
                            'value' => $post_url,
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => 1,
                    'fields' => 'ids'
                ));
                
                if (empty($existing)) {
                    $new_posts[] = $post_url;
                } else {
                    $already_crawled++;
                }
            }
            
            $all_posts = $new_posts;
            $log_messages = array();
            $log_messages[] = array(
                'type' => 'info',
                'message' => sprintf(
                    'Đã quét: Tổng %d bài viết, %d bài mới có thể crawl, %d bài đã crawl (bỏ qua)',
                    count($all_posts) + $already_crawled,
                    count($all_posts),
                    $already_crawled
                )
            );
        }
        
        if (empty($all_posts)) {
            wp_send_json_error(array('message' => 'Không có bài viết mới nào để crawl. Tất cả đã được crawl trước đó.'));
            return;
        }
        
        // Apply limit if specified
        if ($limit > 0 && $limit < count($all_posts)) {
            $all_posts = array_slice($all_posts, 0, $limit);
            $log_messages[] = array(
                'type' => 'info',
                'message' => sprintf('Giới hạn crawl: %d bài viết đầu tiên', $limit)
            );
        }
        
        // Update progress with post list
        $progress = get_option('nhut_crawler_progress', array());
        $progress['posts'] = $all_posts;
        $progress['total'] = count($all_posts);
        $progress['status'] = 'ready';
        $progress['message'] = 'Đã sẵn sàng crawl ' . count($all_posts) . ' bài viết';
        $progress['log_messages'] = isset($progress['log_messages']) ? array_merge($progress['log_messages'], $log_messages) : $log_messages;
        update_option('nhut_crawler_progress', $progress);
        
        wp_send_json_success(array(
            'total' => count($all_posts),
            'message' => 'Đã sẵn sàng crawl ' . count($all_posts) . ' bài viết',
            'log_messages' => $log_messages
        ));
    }
    
    /**
     * AJAX: Process crawler (process one post at a time)
     */
    public function ajax_crawler_process() {
        check_ajax_referer('nhut_crawler_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền'));
            return;
        }
        
        // Increase execution time
        set_time_limit(300); // 5 minutes
        ini_set('max_execution_time', 300);
        
        $progress = get_option('nhut_crawler_progress', array());
        
        if (empty($progress['posts']) || $progress['current'] >= $progress['total']) {
            // Completed
            $log_messages = isset($progress['log_messages']) ? $progress['log_messages'] : array();
            update_option('nhut_crawler_progress', array(
                'current' => $progress['total'],
                'total' => $progress['total'],
                'status' => 'completed',
                'message' => 'Hoàn thành!',
                'posts' => $progress['posts'],
                'errors' => $progress['errors'],
                'log_messages' => $log_messages
            ));
            
            update_option('nhut_crawler_last_run', current_time('mysql'));
            
            wp_send_json_success(array(
                'status' => 'completed',
                'message' => 'Hoàn thành crawl ' . $progress['total'] . ' bài viết',
                'inserted' => get_option('nhut_crawler_total_inserted', 0)
            ));
            return;
        }
        
        // Process current post
        $current_index = $progress['current'];
        $post_url = $progress['posts'][$current_index];
        
        $result = $this->crawl_and_insert_post($post_url);
        
        $errors = $progress['errors'];
        $log_messages = isset($progress['log_messages']) ? $progress['log_messages'] : array();
        
        if (is_wp_error($result)) {
            $error_msg = $result->get_error_message();
            $errors[] = array(
                'url' => $post_url,
                'message' => $error_msg
            );
            
            // Check if it's because post already exists
            if ($error_msg === 'Bài viết đã tồn tại') {
                $log_messages[] = array(
                    'type' => 'warning',
                    'message' => 'Bỏ qua: ' . basename($post_url) . ' (đã crawl trước đó)'
                );
                $response_message = 'Bỏ qua: ' . basename($post_url) . ' (đã tồn tại)';
            } else {
                $log_messages[] = array(
                    'type' => 'error',
                    'message' => 'Lỗi: ' . $error_msg . ' - ' . basename($post_url)
                );
                $response_message = 'Lỗi: ' . basename($post_url);
            }
            $featured_image_set = false;
        } else {
            // Success - result is array with post_id and featured_image info
            $total_inserted = get_option('nhut_crawler_total_inserted', 0);
            update_option('nhut_crawler_total_inserted', $total_inserted + 1);
            
            $log_message = '✓ Đã insert: ' . basename($post_url);
            $response_message = 'Đã xử lý: ' . basename($post_url);
            
            // Add category information to log
            if (isset($result['matched_categories']) && !empty($result['matched_categories'])) {
                $log_message .= ' → Categories: ' . implode(', ', $result['matched_categories']);
            }
            
            if (isset($result['featured_image_set']) && $result['featured_image_set']) {
                $log_message .= ' ✓ Featured image';
                $response_message .= ' ✓ Featured image';
            } elseif (isset($result['featured_image_failed']) && $result['featured_image_failed']) {
                $log_message .= ' ✗ Featured image failed';
                $response_message .= ' ✗ Featured image failed';
            } elseif (isset($result['featured_image_id']) && !$result['featured_image_id']) {
                $log_message .= ' (Không có featured image)';
            }
            
            $log_messages[] = array(
                'type' => 'success',
                'message' => $log_message
            );
            
            $featured_image_set = isset($result['featured_image_set']) ? $result['featured_image_set'] : false;
        }
        
        // Update progress
        $new_progress = array(
            'current' => $current_index + 1,
            'total' => $progress['total'],
            'status' => 'processing',
            'message' => 'Đang xử lý bài ' . ($current_index + 1) . '/' . $progress['total'],
            'posts' => $progress['posts'],
            'errors' => $errors,
            'log_messages' => $log_messages
        );
        
        update_option('nhut_crawler_progress', $new_progress);
        
        $total_crawled = get_option('nhut_crawler_total_crawled', 0);
        update_option('nhut_crawler_total_crawled', $total_crawled + 1);
        
        wp_send_json_success(array(
            'status' => 'processing',
            'current' => $current_index + 1,
            'total' => $progress['total'],
            'message' => $response_message,
            'progress' => round(($current_index + 1) / $progress['total'] * 100, 2),
            'featured_image_set' => $featured_image_set
        ));
    }
    
    /**
     * AJAX: Get progress
     */
    public function ajax_get_progress() {
        check_ajax_referer('nhut_crawler_nonce', 'nonce');
        
        $progress = get_option('nhut_crawler_progress', array());
        
        if (empty($progress)) {
            wp_send_json_success(array(
                'status' => 'idle',
                'current' => 0,
                'total' => 0,
                'progress' => 0
            ));
            return;
        }
        
        $progress_percent = $progress['total'] > 0 
            ? round($progress['current'] / $progress['total'] * 100, 2) 
            : 0;
        
        wp_send_json_success(array(
            'status' => $progress['status'],
            'current' => $progress['current'],
            'total' => $progress['total'],
            'progress' => $progress_percent,
            'message' => $progress['message'],
            'errors' => count($progress['errors']),
            'log_messages' => isset($progress['log_messages']) ? $progress['log_messages'] : array()
        ));
    }
    
    /**
     * AJAX: Truncate crawled posts
     */
    public function ajax_truncate_posts() {
        check_ajax_referer('nhut_crawler_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền'));
            return;
        }
        
        // Increase execution time and memory
        set_time_limit(600); // 10 minutes
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');
        
        $batch_size = 50; // Process 50 posts at a time
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $deleted_count = isset($_POST['deleted_count']) ? intval($_POST['deleted_count']) : 0;
        $deleted_images = isset($_POST['deleted_images']) ? intval($_POST['deleted_images']) : 0;
        
        // Get crawled posts in batches
        $crawled_posts = get_posts(array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'key' => '_is_crawled_post',
                    'value' => '1',
                    'compare' => '='
                )
            ),
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'fields' => 'ids',
            'orderby' => 'ID',
            'order' => 'ASC'
        ));
        
        if (!empty($crawled_posts)) {
            foreach ($crawled_posts as $post_id) {
                // Delete featured image if exists
                $thumbnail_id = get_post_thumbnail_id($post_id);
                if ($thumbnail_id) {
                    wp_delete_attachment($thumbnail_id, true);
                    $deleted_images++;
                }
                
                // Delete post
                $deleted = wp_delete_post($post_id, true);
                if ($deleted) {
                    $deleted_count++;
                }
            }
            
            // Continue with next batch
            wp_send_json_success(array(
                'status' => 'processing',
                'message' => "Đã xóa {$deleted_count} bài viết...",
                'deleted_posts' => $deleted_count,
                'deleted_images' => $deleted_images,
                'offset' => $offset + $batch_size,
                'continue' => true
            ));
            return;
        }
        
        // All posts deleted, now delete orphaned images
        if ($offset === 0 || isset($_POST['delete_images'])) {
            $image_offset = isset($_POST['image_offset']) ? intval($_POST['image_offset']) : 0;
            
            $crawled_images = get_posts(array(
                'post_type' => 'attachment',
                'meta_query' => array(
                    array(
                        'key' => '_crawled_image_url',
                        'compare' => 'EXISTS'
                    )
                ),
                'posts_per_page' => $batch_size,
                'offset' => $image_offset,
                'fields' => 'ids',
                'orderby' => 'ID',
                'order' => 'ASC'
            ));
            
            if (!empty($crawled_images)) {
                foreach ($crawled_images as $image_id) {
                    wp_delete_attachment($image_id, true);
                    $deleted_images++;
                }
                
                // Continue with next batch of images
                wp_send_json_success(array(
                    'status' => 'processing_images',
                    'message' => "Đã xóa {$deleted_count} bài viết và {$deleted_images} hình ảnh...",
                    'deleted_posts' => $deleted_count,
                    'deleted_images' => $deleted_images,
                    'image_offset' => $image_offset + $batch_size,
                    'delete_images' => true,
                    'continue' => true
                ));
                return;
            }
        }
        
        // Reset stats
        update_option('nhut_crawler_total_crawled', 0);
        update_option('nhut_crawler_total_inserted', 0);
        delete_option('nhut_crawler_progress');
        
        wp_send_json_success(array(
            'status' => 'completed',
            'message' => "Đã xóa {$deleted_count} bài viết và {$deleted_images} hình ảnh",
            'deleted_posts' => $deleted_count,
            'deleted_images' => $deleted_images,
            'continue' => false
        ));
    }
    
    /**
     * Get list of posts from target URL
     */
    private function get_post_list() {
        $html = $this->fetch_url($this->target_url);
        
        if (is_wp_error($html)) {
            return $html;
        }
        
        // Get site configuration
        $site_config = $this->available_sites[$this->current_site];
        $link_pattern = $site_config['link_pattern'];
        
        // Parse HTML to get post links
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        
        $post_urls = array();
        
        // Find all links - different selectors for different sites
        if ($this->current_site === 'visaminhquan') {
            $links = $xpath->query("//a[contains(@href, '/visa-') or contains(@href, '/visa-quoc-te/')]");
        } else {
            // For dulichviet.com.vn
            $links = $xpath->query("//a[contains(@href, '/lam-visa/') or contains(@href, '/visa-')]");
        }
        
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            
            // Convert relative URLs to absolute
            if (strpos($href, 'http') !== 0) {
                if (strpos($href, '//') === 0) {
                    $href = 'https:' . $href;
                } else {
                    $href = $this->base_url . '/' . ltrim($href, '/');
                }
            }
            
            // Filter out non-post URLs based on site pattern
            if (preg_match('#' . $link_pattern . '#', $href)) {
                // Additional filtering to exclude category pages
                if (strpos($href, '/category/') === false && 
                    strpos($href, '/tag/') === false &&
                    strpos($href, '/author/') === false) {
                    $post_urls[] = $href;
                }
            }
        }
        
        // Remove duplicates
        $post_urls = array_unique($post_urls);
        
        return array_values($post_urls);
    }
    
    /**
     * Crawl and insert a single post
     */
    private function crawl_and_insert_post($url) {
        // Fetch post content
        $html = $this->fetch_url($url);
        
        if (is_wp_error($html)) {
            return $html;
        }
        
        // Parse post
        $post_data = $this->parse_post($html, $url);
        
        if (is_wp_error($post_data)) {
            return $post_data;
        }
        
        // Check if post already exists
        $existing = get_posts(array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'key' => '_crawled_url',
                    'value' => $url,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        if (!empty($existing)) {
            error_log('Post already exists, skipping: ' . $url);
            return new WP_Error('exists', 'Bài viết đã tồn tại');
        }
        
        // Download and save images
        $featured_image_id = null;
        $featured_image_set = false;
        $featured_image_failed = false;
        $featured_image_url = isset($post_data['featured_image']) ? $post_data['featured_image'] : '';
        
        if (!empty($featured_image_url)) {
            error_log('Attempting to download featured image: ' . $featured_image_url);
            $featured_image_id = $this->download_image($featured_image_url, $post_data['title']);
            
            if ($featured_image_id) {
                error_log('Featured image downloaded successfully, ID: ' . $featured_image_id);
            } else {
                error_log('Failed to download featured image: ' . $featured_image_url);
                $featured_image_failed = true;
            }
        } else {
            error_log('No featured image URL found for post: ' . $post_data['title']);
            $featured_image_failed = true;
        }
        
        // Insert post
        $post_id = wp_insert_post(array(
            'post_title' => $post_data['title'],
            'post_content' => $post_data['content'],
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_category' => $post_data['categories']
        ));
        
        if (is_wp_error($post_id)) {
            error_log('Failed to insert post: ' . $post_id->get_error_message());
            return $post_id;
        }
        
        error_log('Post inserted with ID: ' . $post_id . ', Categories: ' . implode(', ', $post_data['matched_categories']));
        
        // Set featured image
        if ($featured_image_id) {
            error_log('Attempting to set featured image for post ID: ' . $post_id . ', image ID: ' . $featured_image_id);
            $set_result = set_post_thumbnail($post_id, $featured_image_id);
            
            if ($set_result) {
                $featured_image_set = true;
                error_log('Featured image set successfully for post ID: ' . $post_id);
            } else {
                $featured_image_failed = true;
                error_log('Failed to set featured image for post ID: ' . $post_id . '. set_post_thumbnail returned: ' . var_export($set_result, true));
            }
        } else {
            $featured_image_failed = true;
        }
        
        // Save metadata - mark as crawled post
        update_post_meta($post_id, '_crawled_url', $url);
        update_post_meta($post_id, '_crawled_date', current_time('mysql'));
        update_post_meta($post_id, '_is_crawled_post', '1'); // Flag to identify crawled posts
        
        // Return result with featured image info and category info
        return array(
            'post_id' => $post_id,
            'featured_image_set' => $featured_image_set,
            'featured_image_failed' => $featured_image_failed,
            'featured_image_id' => $featured_image_id,
            'matched_categories' => isset($post_data['matched_categories']) ? $post_data['matched_categories'] : array()
        );
    }
    
    /**
     * Parse post HTML
     */
    private function parse_post($html, $url) {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        
        // Get title - try multiple selectors
        $title = '';
        $title_selectors = array(
            "//h1[contains(@class, 'entry-title')]",
            "//h1[contains(@class, 'post-title')]",
            "//h1[contains(@class, 'title')]",
            "//article//h1",
            "//main//h1",
            "//h1",
            "//title"
        );
        
        foreach ($title_selectors as $selector) {
            $title_nodes = $xpath->query($selector);
            if ($title_nodes->length > 0) {
                $title = trim($title_nodes->item(0)->textContent);
                // Remove site name if present (for both sites)
                $title = preg_replace('/\s*[-|]\s*Visa Minh Quân.*$/i', '', $title);
                $title = preg_replace('/\s*[-|]\s*Du Lịch Việt.*$/i', '', $title);
                $title = preg_replace('/\s*[-|]\s*dulichviet.*$/i', '', $title);
                if (!empty($title)) {
                    break;
                }
            }
        }
        
        if (empty($title)) {
            // Fallback: extract from URL
            $title = basename($url);
            $title = str_replace('-', ' ', $title);
            $title = ucwords($title);
        }
        
        // Get content - try multiple selectors
        $content = '';
        $content_selectors = array(
            "//article[contains(@class, 'post')]",
            "//article[contains(@class, 'entry')]",
            "//div[contains(@class, 'entry-content')]",
            "//div[contains(@class, 'post-content')]",
            "//div[contains(@class, 'content')]",
            "//main//article",
            "//main",
            "//article"
        );
        
        foreach ($content_selectors as $selector) {
            $content_nodes = $xpath->query($selector);
            if ($content_nodes->length > 0) {
                $content_node = $content_nodes->item(0);
                // Remove script and style tags
                $scripts = $xpath->query('.//script | .//style', $content_node);
                foreach ($scripts as $script) {
                    $script->parentNode->removeChild($script);
                }
                $content = $dom->saveHTML($content_node);
                if (!empty($content) && strlen(strip_tags($content)) > 100) {
                    break;
                }
            }
        }
        
        // If still no content, get body text
        if (empty($content) || strlen(strip_tags($content)) < 100) {
            $body_nodes = $xpath->query("//body");
            if ($body_nodes->length > 0) {
                $body = $body_nodes->item(0);
                // Remove header, footer, nav, sidebar
                $remove_selectors = array('header', 'footer', 'nav', '.sidebar', '.widget', '.menu');
                foreach ($remove_selectors as $sel) {
                    $nodes = $xpath->query(".//{$sel}", $body);
                    foreach ($nodes as $node) {
                        $node->parentNode->removeChild($node);
                    }
                }
                $content = $dom->saveHTML($body);
            }
        }
        
        // Get featured image - try multiple strategies
        $featured_image = '';
        
        // Strategy 1: Try to find in meta tags (Open Graph, Twitter Card)
        $meta_tags = $xpath->query("//meta[@property='og:image' or @name='twitter:image' or @property='og:image:url']");
        if ($meta_tags->length > 0) {
            $og_image = $meta_tags->item(0)->getAttribute('content');
            if (!empty($og_image)) {
                $featured_image = $og_image;
                error_log('Found featured image from meta tag: ' . $featured_image);
            }
        }
        
        // Strategy 2: Try to find in structured data (JSON-LD)
        if (empty($featured_image)) {
            $scripts = $xpath->query("//script[@type='application/ld+json']");
            foreach ($scripts as $script) {
                $json_content = $script->textContent;
                $json_data = @json_decode($json_content, true);
                if ($json_data && isset($json_data['image'])) {
                    if (is_string($json_data['image'])) {
                        $featured_image = $json_data['image'];
                    } elseif (is_array($json_data['image']) && isset($json_data['image']['url'])) {
                        $featured_image = $json_data['image']['url'];
                    } elseif (is_array($json_data['image']) && isset($json_data['image'][0])) {
                        $featured_image = is_string($json_data['image'][0]) ? $json_data['image'][0] : (isset($json_data['image'][0]['url']) ? $json_data['image'][0]['url'] : '');
                    }
                    if (!empty($featured_image)) {
                        error_log('Found featured image from JSON-LD: ' . $featured_image);
                        break;
                    }
                }
            }
        }
        
        // Strategy 3: Try multiple CSS selectors
        if (empty($featured_image)) {
            $img_selectors = array(
                "//img[contains(@class, 'featured')]",
                "//img[contains(@class, 'thumbnail')]",
                "//img[contains(@class, 'wp-post-image')]",
                "//img[contains(@class, 'attachment-post-thumbnail')]",
                "//div[contains(@class, 'featured-image')]//img[1]",
                "//div[contains(@class, 'post-thumbnail')]//img[1]",
                "//div[contains(@class, 'entry-thumbnail')]//img[1]",
                "//div[contains(@class, 'post-image')]//img[1]",
                "//figure[contains(@class, 'wp-block-image')]//img[1]",
                "//div[contains(@class, 'banner')]//img[1]",
                "//section[contains(@class, 'hero')]//img[1]",
                "//article//img[1]",
                "//main//img[1]",
                "//div[contains(@class, 'featured')]//img[1]",
                "//img[1]"
            );
            
            foreach ($img_selectors as $selector) {
                $img_nodes = $xpath->query($selector);
                if ($img_nodes->length > 0) {
                    $img = $img_nodes->item(0);
                    $img_src = $img->getAttribute('src');
                    
                    // Try data attributes for lazy loading
                    if (empty($img_src)) {
                        $img_src = $img->getAttribute('data-src');
                    }
                    if (empty($img_src)) {
                        $img_src = $img->getAttribute('data-lazy-src');
                    }
                    if (empty($img_src)) {
                        $img_src = $img->getAttribute('data-original');
                    }
                    if (empty($img_src)) {
                        $img_src = $img->getAttribute('data-lazy');
                    }
                    if (empty($img_src)) {
                        $img_src = $img->getAttribute('data-url');
                    }
                    
                    // Try srcset as fallback
                    if (empty($img_src)) {
                        $srcset = $img->getAttribute('srcset');
                        if (!empty($srcset)) {
                            $srcset_parts = explode(',', $srcset);
                            if (!empty($srcset_parts[0])) {
                                $img_src = trim(explode(' ', $srcset_parts[0])[0]);
                            }
                        }
                    }
                    
                    if (!empty($img_src)) {
                        // Skip icons/logos
                        $img_class = $img->getAttribute('class');
                        if (stripos($img_class, 'icon') !== false || 
                            stripos($img_class, 'logo') !== false ||
                            stripos($img_src, 'icon') !== false ||
                            stripos($img_src, 'logo') !== false) {
                            continue;
                        }
                        
                        // Convert relative URLs to absolute
                        if (strpos($img_src, 'http') !== 0) {
                            if (strpos($img_src, '//') === 0) {
                                $img_src = 'https:' . $img_src;
                            } else {
                                $img_src = $this->base_url . '/' . ltrim($img_src, '/');
                            }
                        }
                        
                        // Remove query strings
                        $img_src = strtok($img_src, '?');
                        
                        // Skip data URIs and validate URL
                        if (strpos($img_src, 'data:') !== 0 && filter_var($img_src, FILTER_VALIDATE_URL)) {
                            // Skip SVG
                            if (stripos($img_src, '.svg') === false) {
                                $featured_image = $img_src;
                                error_log('Found featured image from selector ' . $selector . ': ' . $featured_image);
                                break;
                            }
                        }
                    }
                }
            }
        }
        
        // Strategy 4: Find first large image in content
        if (empty($featured_image)) {
            $all_images = $xpath->query("//img[@src or @data-src or @data-lazy-src]");
            $best_image = '';
            $best_size = 0;
            
            foreach ($all_images as $img) {
                $img_src = $img->getAttribute('src');
                if (empty($img_src)) {
                    $img_src = $img->getAttribute('data-src');
                }
                if (empty($img_src)) {
                    $img_src = $img->getAttribute('data-lazy-src');
                }
                
                if (!empty($img_src)) {
                    // Skip icons/logos/avatars
                    $img_class = $img->getAttribute('class');
                    if (stripos($img_class, 'icon') !== false || 
                        stripos($img_class, 'logo') !== false ||
                        stripos($img_src, 'icon') !== false ||
                        stripos($img_src, 'logo') !== false ||
                        stripos($img_src, 'avatar') !== false) {
                        continue;
                    }
                    
                    // Convert to absolute URL
                    if (strpos($img_src, 'http') !== 0) {
                        if (strpos($img_src, '//') === 0) {
                            $img_src = 'https:' . $img_src;
                        } else {
                            $img_src = $this->base_url . '/' . ltrim($img_src, '/');
                        }
                    }
                    
                    $img_src = strtok($img_src, '?');
                    
                    if (strpos($img_src, 'data:') !== 0 && filter_var($img_src, FILTER_VALIDATE_URL) && stripos($img_src, '.svg') === false) {
                        // Calculate size score
                        $width = intval($img->getAttribute('width')) ?: 0;
                        $height = intval($img->getAttribute('height')) ?: 0;
                        $size_score = $width * $height;
                        
                        // Prefer larger images (at least 200x200 = 40000)
                        if ($size_score > $best_size && $size_score > 40000) {
                            $best_image = $img_src;
                            $best_size = $size_score;
                        } elseif ($size_score == 0 && empty($best_image)) {
                            // If no size info, use first valid image as fallback
                            $best_image = $img_src;
                        }
                    }
                }
            }
            
            if (!empty($best_image)) {
                $featured_image = $best_image;
                error_log('Found featured image from best size calculation: ' . $featured_image);
            }
        }
        
        if (empty($featured_image)) {
            error_log('No featured image found for URL: ' . $url);
            // Debug: log all images found
            $all_imgs = $xpath->query("//img");
            error_log('Total images found on page: ' . $all_imgs->length);
            if ($all_imgs->length > 0) {
                $first_img = $all_imgs->item(0);
                error_log('First image src: ' . $first_img->getAttribute('src'));
                error_log('First image class: ' . $first_img->getAttribute('class'));
            }
        } else {
            error_log('Final featured image URL: ' . $featured_image);
        }
        
        // Download and replace images in content
        $content = $this->process_content_images($content);
        
        // Get categories based on URL or content
        $category_result = $this->get_categories_from_url($url);
        $categories = $category_result['category_ids'];
        $matched_categories = $category_result['matched_categories'];
        
        // Log category information
        if (!empty($matched_categories)) {
            error_log('Categories assigned for ' . $url . ': ' . implode(', ', $matched_categories));
        }
        
        return array(
            'title' => $title,
            'content' => $content,
            'featured_image' => $featured_image,
            'categories' => $categories,
            'matched_categories' => $matched_categories
        );
    }
    
    /**
     * Get categories from URL and content analysis
     */
    private function get_categories_from_url($url) {
        $category_ids = array();
        $matched_categories = array();
        
        // Extract category name from URL - different patterns for different sites
        $category_slug = '';
        $category_name_pattern = '';
        
        if ($this->current_site === 'visaminhquan') {
            // Pattern for visaminhquan.com.vn
            if (preg_match('/\/visa-([a-z-]+)\/?$/', $url, $matches)) {
                $category_slug = 'visa-' . $matches[1];
                $category_name_pattern = 'VISA ' . strtoupper(str_replace('-', ' ', $matches[1]));
            }
        } else {
            // Pattern for dulichviet.com.vn
            if (preg_match('/\/lam-visa\/([a-z-]+)\/?$/', $url, $matches) || 
                preg_match('/\/visa-([a-z-]+)\/?$/', $url, $matches)) {
                $category_slug = 'visa-' . $matches[1];
                $category_name_pattern = 'VISA ' . strtoupper(str_replace('-', ' ', $matches[1]));
            }
        }
        
        // Try to find category by slug first
        if (!empty($category_slug)) {
            $category = get_term_by('slug', $category_slug, 'category');
            if ($category) {
                $category_ids[] = $category->term_id;
                $matched_categories[] = $category->name . ' (từ URL slug)';
            }
        }
        
        // Try to find by name pattern
        if (empty($category_ids) && !empty($category_name_pattern)) {
            $category = get_term_by('name', $category_name_pattern, 'category');
            if ($category) {
                $category_ids[] = $category->term_id;
                $matched_categories[] = $category->name . ' (từ URL pattern)';
            }
        }
        
        // Try partial match with continent categories
        if (empty($category_ids)) {
            $continent_keywords = array(
                'chau-a' => 'VISA CHÂU Á',
                'chau-au' => 'VISA CHÂU ÂU',
                'chau-phi' => 'VISA CHÂU PHI',
                'chau-my' => 'VISA CHÂU MỸ',
                'chau-uc' => 'VISA CHÂU ÚC',
                'asia' => 'VISA CHÂU Á',
                'europe' => 'VISA CHÂU ÂU',
                'africa' => 'VISA CHÂU PHI',
                'america' => 'VISA CHÂU MỸ',
                'oceania' => 'VISA CHÂU ÚC'
            );
            
            foreach ($continent_keywords as $keyword => $continent_name) {
                if (stripos($url, $keyword) !== false) {
                    $category = get_term_by('name', $continent_name, 'category');
                    if ($category) {
                        $category_ids[] = $category->term_id;
                        $matched_categories[] = $continent_name . ' (từ URL keyword)';
                        break;
                    }
                }
            }
        }
        
        // If still no category found, try to analyze URL path
        if (empty($category_ids)) {
            $url_parts = parse_url($url);
            $path = isset($url_parts['path']) ? $url_parts['path'] : '';
            
            // Check for continent indicators in path
            $path_lower = strtolower($path);
            if (strpos($path_lower, 'chau-a') !== false || strpos($path_lower, 'asia') !== false) {
                $category = get_term_by('name', 'VISA CHÂU Á', 'category');
                if ($category) {
                    $category_ids[] = $category->term_id;
                    $matched_categories[] = 'VISA CHÂU Á (từ URL path)';
                }
            } elseif (strpos($path_lower, 'chau-au') !== false || strpos($path_lower, 'europe') !== false) {
                $category = get_term_by('name', 'VISA CHÂU ÂU', 'category');
                if ($category) {
                    $category_ids[] = $category->term_id;
                    $matched_categories[] = 'VISA CHÂU ÂU (từ URL path)';
                }
            } elseif (strpos($path_lower, 'chau-phi') !== false || strpos($path_lower, 'africa') !== false) {
                $category = get_term_by('name', 'VISA CHÂU PHI', 'category');
                if ($category) {
                    $category_ids[] = $category->term_id;
                    $matched_categories[] = 'VISA CHÂU PHI (từ URL path)';
                }
            } elseif (strpos($path_lower, 'chau-my') !== false || strpos($path_lower, 'america') !== false) {
                $category = get_term_by('name', 'VISA CHÂU MỸ', 'category');
                if ($category) {
                    $category_ids[] = $category->term_id;
                    $matched_categories[] = 'VISA CHÂU MỸ (từ URL path)';
                }
            } elseif (strpos($path_lower, 'chau-uc') !== false || strpos($path_lower, 'oceania') !== false) {
                $category = get_term_by('name', 'VISA CHÂU ÚC', 'category');
                if ($category) {
                    $category_ids[] = $category->term_id;
                    $matched_categories[] = 'VISA CHÂU ÚC (từ URL path)';
                }
            }
        }
        
        // If no specific category found, assign to default parent category
        if (empty($category_ids)) {
            $parent_category = get_term_by('name', 'VISA CHÂU Á', 'category');
            if ($parent_category) {
                $category_ids[] = $parent_category->term_id;
                $matched_categories[] = 'VISA CHÂU Á (mặc định)';
            }
        }
        
        // Log category matching result
        if (!empty($matched_categories)) {
            error_log('Category matched for URL ' . $url . ': ' . implode(', ', $matched_categories));
        } else {
            error_log('No category matched for URL: ' . $url);
        }
        
        return array(
            'category_ids' => $category_ids,
            'matched_categories' => $matched_categories
        );
    }
    
    /**
     * Process images in content
     */
    private function process_content_images($content) {
        if (empty($content)) {
            return $content;
        }
        
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        
        $images = $xpath->query("//img");
        
        foreach ($images as $img) {
            // Try multiple attributes (src, data-src, data-lazy-src)
            $src = $img->getAttribute('src');
            if (empty($src)) {
                $src = $img->getAttribute('data-src');
            }
            if (empty($src)) {
                $src = $img->getAttribute('data-lazy-src');
            }
            if (empty($src)) {
                $src = $img->getAttribute('data-original');
            }
            
            if (empty($src)) {
                continue;
            }
            
            // Convert relative URLs to absolute
            if (strpos($src, 'http') !== 0) {
                if (strpos($src, '//') === 0) {
                    $src = 'https:' . $src;
                } else {
                    $src = $this->base_url . '/' . ltrim($src, '/');
                }
            }
            
            // Remove query strings that might cause issues
            $src = strtok($src, '?');
            
            // Skip data URIs and invalid URLs
            if (strpos($src, 'data:') === 0 || !filter_var($src, FILTER_VALIDATE_URL)) {
                continue;
            }
            
            // Download image
            $image_id = $this->download_image($src);
            
            if ($image_id) {
                $new_url = wp_get_attachment_url($image_id);
                if ($new_url) {
                    $img->setAttribute('src', $new_url);
                    // Remove lazy loading attributes
                    $img->removeAttribute('data-src');
                    $img->removeAttribute('data-lazy-src');
                    $img->removeAttribute('data-original');
                }
            } else {
                // Log failed downloads
                error_log('Failed to download image: ' . $src);
            }
        }
        
        return $dom->saveHTML();
    }
    
    /**
     * Download image from URL
     */
    private function download_image($url, $title = '') {
        if (empty($url)) {
            error_log('download_image: Empty URL provided');
            return false;
        }
        
        // Normalize URL - remove query strings and fragments
        $url = strtok($url, '?#');
        
        error_log('download_image: Processing URL: ' . $url);
        
        // Check if image already exists
        $existing = get_posts(array(
            'post_type' => 'attachment',
            'meta_query' => array(
                array(
                    'key' => '_crawled_image_url',
                    'value' => $url,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'fields' => 'ids'
        ));
        
        if (!empty($existing)) {
            error_log('download_image: Image already exists, returning ID: ' . $existing[0]);
            return $existing[0];
        }
        
        // Download image
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        // Set timeout for image download
        $timeout = 30;
        error_log('download_image: Attempting to download from: ' . $url);
        $tmp = download_url($url, $timeout);
        
        if (is_wp_error($tmp)) {
            error_log('download_image: Download URL error: ' . $tmp->get_error_message() . ' - URL: ' . $url);
            return false;
        }
        
        if (empty($tmp) || !file_exists($tmp)) {
            error_log('download_image: Temporary file not created or does not exist');
            return false;
        }
        
        error_log('download_image: File downloaded to: ' . $tmp);
        
        // Get file extension from URL
        $url_info = parse_url($url);
        $path_info = pathinfo($url_info['path']);
        $extension = isset($path_info['extension']) ? strtolower($path_info['extension']) : '';
        
        // Try to detect MIME type from file
        $file_type = wp_check_filetype($tmp);
        if (!empty($file_type['ext'])) {
            $extension = $file_type['ext'];
        } elseif (empty($extension)) {
            // Try to detect from file content
            $image_info = @getimagesize($tmp);
            if ($image_info && isset($image_info[2])) {
                $mime_to_ext = array(
                    IMAGETYPE_JPEG => 'jpg',
                    IMAGETYPE_PNG => 'png',
                    IMAGETYPE_GIF => 'gif',
                    IMAGETYPE_WEBP => 'webp'
                );
                if (isset($mime_to_ext[$image_info[2]])) {
                    $extension = $mime_to_ext[$image_info[2]];
                }
            }
        }
        
        // Validate extension
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (empty($extension) || !in_array(strtolower($extension), $allowed_extensions)) {
            @unlink($tmp);
            error_log('download_image: Invalid or missing image extension: ' . $extension . ' - URL: ' . $url);
            return false;
        }
        
        // Generate filename
        $filename = !empty($title) ? sanitize_file_name($title) : 'image';
        $filename = $filename . '.' . $extension;
        
        // Ensure unique filename
        $upload_dir = wp_upload_dir();
        if (is_wp_error($upload_dir)) {
            @unlink($tmp);
            error_log('download_image: Upload directory error: ' . $upload_dir->get_error_message());
            return false;
        }
        
        $filename = wp_unique_filename($upload_dir['path'], $filename);
        
        $file_array = array(
            'name' => $filename,
            'tmp_name' => $tmp
        );
        
        error_log('download_image: Attempting to sideload file: ' . $filename);
        $id = media_handle_sideload($file_array, 0, $title);
        
        if (is_wp_error($id)) {
            @unlink($tmp);
            error_log('download_image: Media handle sideload error: ' . $id->get_error_message() . ' - URL: ' . $url);
            return false;
        }
        
        if (!$id || !is_numeric($id)) {
            @unlink($tmp);
            error_log('download_image: Invalid attachment ID returned: ' . var_export($id, true));
            return false;
        }
        
        // Save original URL
        update_post_meta($id, '_crawled_image_url', $url);
        
        error_log('download_image: Successfully created attachment ID: ' . $id);
        return $id;
    }
    
    /**
     * Fetch URL with timeout handling
     */
    private function fetch_url($url) {
        $host = parse_url($url, PHP_URL_HOST);

        $args = array(
            // WordPress 'timeout' is total timeout; connect timeout may still be too low in some Docker/DDEV setups
            'timeout' => 45,
            'httpversion' => '1.1',
            'redirection' => 5,
            'sslverify' => true,
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
            'headers' => array(
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'vi-VN,vi;q=0.9,en;q=0.8',
            ),
        );

        // Some environments (notably Docker/DDEV) may have broken IPv6 egress -> cURL connect timeout.
        // Force IPv4 for selected hosts.
        $force_ipv4_hosts = array(
            'dulichviet.com.vn',
            'visaminhquan.com.vn',
        );

        $should_force_ipv4 = $host && in_array(strtolower($host), $force_ipv4_hosts, true);

        if ($should_force_ipv4 && function_exists('add_action')) {
            $this->curl_tune_host = strtolower($host);
            add_action('http_api_curl', array($this, 'nhut_http_api_curl_tune'), 10, 3);
        }

        $response = wp_remote_get($url, $args);

        if ($should_force_ipv4 && function_exists('remove_action')) {
            remove_action('http_api_curl', array($this, 'nhut_http_api_curl_tune'), 10);
            $this->curl_tune_host = null;
        }

        if (is_wp_error($response)) {
            $msg = $response->get_error_message();

            // Make timeout errors more actionable in admin log
            if (stripos($msg, 'cURL error 28') !== false || stripos($msg, 'timeout') !== false) {
                $msg .= ' (Gợi ý: server/container có thể không ra được IPv6/443. Đã thử ép IPv4 cho host này; nếu vẫn lỗi hãy kiểm tra firewall/DNS/proxy từ container.)';
                return new WP_Error('timeout', $msg);
            }

            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $headers = wp_remote_retrieve_headers($response);

        // Cloudflare/WAF blocks: return a clear error so user understands why crawler fails
        if ($code === 403) {
            $cf_mitigated = '';
            if (is_array($headers) && isset($headers['cf-mitigated'])) {
                $cf_mitigated = (string) $headers['cf-mitigated'];
            } elseif (is_object($headers) && isset($headers['cf-mitigated'])) {
                $cf_mitigated = (string) $headers['cf-mitigated'];
            }

            if (!empty($cf_mitigated)) {
                return new WP_Error(
                    'blocked',
                    'Bị chặn bởi Cloudflare/WAF (HTTP 403, cf-mitigated: ' . $cf_mitigated . '). Cần whitelist IP server, tắt challenge/bot protection, hoặc cung cấp nguồn crawl khác (RSS/API).'
                );
            }
        }

        if ($code >= 400) {
            // Retry once for transient 5xx
            if ($code >= 500 && $code <= 599) {
                error_log('fetch_url: HTTP ' . $code . ' for ' . $url . ' - retrying once...');
                $retry = wp_remote_get($url, $args);
                if (!is_wp_error($retry)) {
                    $retry_code = wp_remote_retrieve_response_code($retry);
                    if ($retry_code > 0 && $retry_code < 400) {
                        $body = wp_remote_retrieve_body($retry);
                        if (!empty($body)) {
                            return $body;
                        }
                    }
                    $code = $retry_code ?: $code;
                }
            }

            if ($code === 500) {
                return new WP_Error('http_500', 'Website nguồn đang trả HTTP 500 cho URL: ' . $url . ' (lỗi phía server nguồn hoặc WAF). Vui lòng thử lại sau hoặc đổi nguồn crawl.');
            }

            return new WP_Error('http_error', 'HTTP error ' . $code . ' khi tải URL: ' . $url);
        }

        $body = wp_remote_retrieve_body($response);

        if (empty($body)) {
            return new WP_Error('empty', 'Nội dung trống');
        }

        return $body;
    }

    /**
     * Tune cURL options for specific hosts (mainly to fix IPv6 issues / connect timeout)
     */
    public function nhut_http_api_curl_tune($handle, $r, $url) {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host || !$this->curl_tune_host || strtolower($host) !== $this->curl_tune_host) {
            return;
        }

        // Force IPv4 to avoid IPv6 egress problems in some containers
        if (defined('CURL_IPRESOLVE_V4')) {
            @curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }

        // Increase connect timeout (WordPress timeout is total timeout, connect can still fail early)
        @curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 20);
        @curl_setopt($handle, CURLOPT_TIMEOUT, 45);
    }
}

// Initialize
Nhut_Crawler_Post::get_instance();


