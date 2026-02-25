<?php
/**
 * Carousel Post Shortcode
 * 
 * Shortcode: [nhut_carousel_post]
 * 
 * Attributes:
 * - columns: Số cột hiển thị (mặc định: 4)
 * - posts_per_page: Số lượng post (mặc định: 10)
 * - ignore_sticky: Bỏ qua sticky post (mặc định: 1)
 * - read_more_text: Text nút "Xem thêm" (mặc định: "Xem thêm")
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nhut_Carousel_Post {
    
    private static $instance = null;
    
    // Cài đặt mặc định
    private $default_columns = 4;
    private $default_posts_per_page = 10;
    private $default_ignore_sticky = true;
    private $default_read_more_text = 'Xem thêm';
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private static $assets_loaded = false;
    
    private function __construct() {
        add_shortcode('nhut_carousel_post', array($this, 'render_carousel'));
    }
    
    /**
     * Load CSS và JS
     */
    private function load_assets() {
        // Chỉ load một lần
        if (self::$assets_loaded) {
            return;
        }
        
        $plugin_url = content_url('nhut-addon/nhutplugin_carousel_post');
        
        wp_enqueue_style(
            'nhut-carousel-post-css',
            $plugin_url . '/assets/css/carousel-post.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'nhut-carousel-post-js',
            $plugin_url . '/assets/js/carousel-post.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        self::$assets_loaded = true;
    }
    
    /**
     * Render carousel shortcode
     */
    public function render_carousel($atts) {
        // Load assets khi shortcode được render
        $this->load_assets();
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'columns' => $this->default_columns,
            'posts_per_page' => $this->default_posts_per_page,
            'ignore_sticky' => $this->default_ignore_sticky ? '1' : '0',
            'read_more_text' => $this->default_read_more_text,
        ), $atts, 'nhut_carousel_post');
        
        // Convert to proper types
        $columns = absint($atts['columns']);
        $posts_per_page = absint($atts['posts_per_page']);
        $ignore_sticky = $atts['ignore_sticky'] === '1' || $atts['ignore_sticky'] === 'true';
        $read_more_text = sanitize_text_field($atts['read_more_text']);
        
        // Query posts
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $posts_per_page,
            'post_status' => 'publish',
            'ignore_sticky_posts' => $ignore_sticky,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<p>Không có bài viết nào.</p>';
        }
        
        // Generate unique ID for this carousel instance
        $carousel_id = 'nhut-carousel-' . uniqid();
        
        // Start output
        ob_start();
        ?>
        <div class="nhut-carousel-post-wrapper" data-columns="<?php echo esc_attr($columns); ?>">
            <div class="nhut-carousel-post-container" id="<?php echo esc_attr($carousel_id); ?>">
                <div class="nhut-carousel-post-slider">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div class="nhut-carousel-post-item">
                            <div class="nhut-carousel-post-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="nhut-carousel-post-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="nhut-carousel-post-content">
                                    <h3 class="nhut-carousel-post-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php echo wp_trim_words(get_the_title(), 15, '...'); ?>
                                        </a>
                                    </h3>
                                    
                                    <a href="<?php the_permalink(); ?>" class="nhut-carousel-post-read-more">
                                        <span><?php echo esc_html($read_more_text); ?></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20" class="nhut-carousel-icon">
                                            <path fill="#0056A4" fill-rule="evenodd" d="M.47 19.53a.75.75 0 0 1 0-1.06l7.72-7.72H4.655a.75.75 0 0 1 0-1.5H10a.75.75 0 0 1 .75.75v5.344a.75.75 0 0 1-1.5 0V11.81l-7.72 7.72a.75.75 0 0 1-1.06 0" clip-rule="evenodd"/>
                                            <path fill="#0056A4" d="m1.518 15.3 3.052-3.052a2.25 2.25 0 0 1 .086-4.498H10A2.25 2.25 0 0 1 12.25 10v5.344a2.25 2.25 0 0 1-4.498.086L4.7 18.482A9.95 9.95 0 0 0 10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10c0 1.947.556 3.763 1.518 5.3"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Navigation arrows -->
                <button class="nhut-carousel-nav nhut-carousel-prev" aria-label="Previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 18l-6-6 6-6"/>
                    </svg>
                </button>
                <button class="nhut-carousel-nav nhut-carousel-next" aria-label="Next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}

// Initialize
Nhut_Carousel_Post::get_instance();

