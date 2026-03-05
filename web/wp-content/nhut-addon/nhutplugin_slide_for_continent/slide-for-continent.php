<?php
/**
 * Slide for Continent - Carousel Bài viết Visa theo châu lục
 * Using Bootstrap 5 Carousel - Fully Responsive
 * * Shortcodes:
 * - [nhut_slide_asia] - Châu Á
 * - [nhut_slide_europe] - Châu Âu
 * - [nhut_slide_africa] - Châu Phi
 * - [nhut_slide_america] - Châu Mỹ
 * - [nhut_slide_oceania] - Châu Úc
 * - [nhut_slide_custom] - Tuỳ chỉnh (VD: [nhut_slide_custom cat="1,2,3" goto="6" title="Tên tuỳ chọn"])
 * * Attributes:
 * - posts_per_page: Số lượng bài viết hiển thị trên 1 slide (mặc định: 4)
 * - total_posts: Tổng số bài viết load từ category (mặc định: 10)
 * - show_all_text: Text nút "Xem tất cả" (mặc định: "Xem tất cả")
 * - cat: (Chỉ dùng cho custom) Danh sách ID category lấy bài viết
 * - goto: (Chỉ dùng cho custom) ID category khi nhấn Xem tất cả
 * - title: (Chỉ dùng cho custom) Tiêu đề của khối slide
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nhut_Slide_For_Continent {
    
    private static $instance = null;
    private static $assets_loaded = false;
    
    // Mapping shortcode -> category name
    private $continent_mapping = array(
        'asia' => 'VISA CHÂU Á',
        'europe' => 'VISA CHÂU ÂU',
        'africa' => 'VISA CHÂU PHI',
        'america' => 'VISA CHÂU MỸ',
        'oceania' => 'VISA CHÂU ÚC'
    );
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('nhut_slide_asia', array($this, 'render_asia'));
        add_shortcode('nhut_slide_europe', array($this, 'render_europe'));
        add_shortcode('nhut_slide_africa', array($this, 'render_africa'));
        add_shortcode('nhut_slide_america', array($this, 'render_america'));
        add_shortcode('nhut_slide_oceania', array($this, 'render_oceania'));
        
        // Thêm shortcode custom mới
        add_shortcode('nhut_slide_custom', array($this, 'render_custom'));
        // Alias phòng trường hợp gõ nhầm chữ "custome"
        add_shortcode('nhut_slide_custome', array($this, 'render_custom'));
    }
    
    /**
     * Load CSS và JS
     */
    private function load_assets() {
        if (self::$assets_loaded) {
            return;
        }
        
        $plugin_url = content_url('nhut-addon/nhutplugin_slide_for_continent');
        
        // Enqueue Bootstrap 5 CSS
        wp_enqueue_style(
            'bootstrap-5-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
            array(),
            '5.3.2'
        );
        
        // Enqueue Bootstrap 5 JS Bundle (includes Popper)
        wp_enqueue_script(
            'bootstrap-5-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
            array(),
            '5.3.2',
            true
        );
        
        wp_enqueue_style(
            'nhut-slide-continent-css',
            $plugin_url . '/assets/css/slide-for-continent.css',
            array('bootstrap-5-css'),
            '2.0.0'
        );
        
        wp_enqueue_script(
            'nhut-slide-continent-js',
            $plugin_url . '/assets/js/slide-for-continent.js',
            array('jquery', 'bootstrap-5-js'),
            '2.0.0',
            true
        );
        
        self::$assets_loaded = true;
    }
    
    /**
     * Render carousel cho châu lục và Custom
     */
    private function render_continent_carousel($continent_key, $atts) {
        $this->load_assets();
        
        // Parse attributes
        $atts = shortcode_atts(array(
            'posts_per_page' => 4,
            'total_posts'    => 10,
            'show_all_text'  => 'Xem tất cả',
            'cat'            => '', // Dành cho custom
            'goto'           => '', // Dành cho custom
            'title'          => ''  // Dành cho custom
        ), $atts);
        
        $posts_per_page = absint($atts['posts_per_page']);
        $total_posts    = absint($atts['total_posts']);
        $show_all_text  = sanitize_text_field($atts['show_all_text']);
        
        $category_ids  = array();
        $category_name = '';
        $category_url  = '';
        $category_id   = 0;

        // XỬ LÝ CHO SHORTCODE CUSTOM
        if ($continent_key === 'custom') {
            if (empty($atts['cat']) || empty($atts['goto'])) {
                return '<p>Vui lòng cung cấp đầy đủ tham số "cat" và "goto" cho shortcode.</p>';
            }

            // Lấy array ID các category cần get bài viết
            $category_ids = array_map('intval', explode(',', $atts['cat']));
            
            // Lấy ID category đích đến cho nút xem tất cả
            $goto_id = intval($atts['goto']);
            $category_url = get_category_link($goto_id);
            
            // Xử lý tiêu đề hiển thị
            $goto_cat = get_term($goto_id, 'category');
            if (!empty($atts['title'])) {
                $category_name = sanitize_text_field($atts['title']);
            } else {
                // Default lấy tên của category goto nếu không truyền title
                $category_name = ($goto_cat && !is_wp_error($goto_cat)) ? $goto_cat->name : 'Chuyên mục';
            }

        } else {
            // XỬ LÝ CHO SHORTCODE CHÂU LỤC CŨ (GIỮ NGUYÊN HOÀN TOÀN TÍNH LOGIC ĐỂ KHÔNG BỊ ẢNH HƯỞNG)
            $category_name = isset($this->continent_mapping[$continent_key]) 
                ? $this->continent_mapping[$continent_key] 
                : '';
            
            if (empty($category_name)) {
                return '<p>Category không tồn tại.</p>';
            }
            
            // Lấy category ID
            $category = get_term_by('name', $category_name, 'category');
            if (!$category) {
                return '<p>Category "' . esc_html($category_name) . '" không tồn tại.</p>';
            }
            
            $category_id = $category->term_id;
            $category_url = get_category_link($category_id);
            
            // Lấy tất cả sub-categories
            $sub_categories = get_terms(array(
                'taxonomy'   => 'category',
                'parent'     => $category_id,
                'hide_empty' => false
            ));
            
            // Tạo array category IDs bao gồm parent và tất cả children
            $category_ids = array($category_id);
            if (!is_wp_error($sub_categories) && !empty($sub_categories)) {
                foreach ($sub_categories as $sub_cat) {
                    $category_ids[] = $sub_cat->term_id;
                }
            }
        }
        
        // Query posts
        $args = array(
            'post_type'           => 'post',
            'posts_per_page'      => $total_posts,
            'post_status'         => 'publish',
            'category__in'        => $category_ids,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true
        );
        
        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '<p>Không có bài viết nào trong category "' . esc_html($category_name) . '".</p>';
        }
        
        // Generate unique ID
        $carousel_id = 'nhut-continent-' . $continent_key . '-' . uniqid();
        
        // Collect all posts
        $posts_array = array();
        while ($query->have_posts()) {
            $query->the_post();
            $posts_array[] = array(
                'id'           => get_the_ID(),
                'title'        => get_the_title(),
                'permalink'    => get_the_permalink(),
                'thumbnail'    => has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '',
                'thumbnail_id' => has_post_thumbnail() ? get_post_thumbnail_id() : 0,
                'categories'   => get_the_category()
            );
        }
        wp_reset_postdata();
        
        // Start output
        ob_start();
        ?>
        <div class="nhut-continent-carousel-wrapper" data-continent="<?php echo esc_attr($continent_key); ?>">
            <div class="nhut-continent-header">
                <h2 class="nhut-continent-title"><?php echo esc_html($category_name); ?></h2>
                <a href="<?php echo esc_url($category_url); ?>" class="nhut-continent-view-all">
                    <?php echo esc_html($show_all_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            
            <div id="<?php echo esc_attr($carousel_id); ?>" class="carousel slide nhut-continent-carousel" data-bs-ride="false" data-bs-wrap="true" data-bs-interval="false" data-bs-pause="false">
                <div class="carousel-inner">
                    <?php 
                    $total_posts_count = count($posts_array);
                    // Dùng thuộc tính posts_per_page cho số lượng item mỗi slide, mặc định nếu lỗi sẽ lấy 4
                    $items_per_slide = $posts_per_page > 0 ? $posts_per_page : 4; 
                    
                    // Calculate number of slides needed
                    $num_slides = ceil($total_posts_count / $items_per_slide);
                    
                    for ($slide_index = 0; $slide_index < $num_slides; $slide_index++) {
                        $is_active = ($slide_index === 0) ? ' active' : '';
                        echo '<div class="carousel-item' . $is_active . '">';
                        echo '<div class="row g-3 g-md-4">';
                        
                        // Display items for this slide
                        for ($item_index = 0; $item_index < $items_per_slide; $item_index++) {
                            $post_index = ($slide_index * $items_per_slide) + $item_index;
                            
                            if ($post_index >= $total_posts_count) {
                                break;
                            }
                            
                            $post = $posts_array[$post_index];
                            
                            // Find sub-category for tag
                            $tag_category = null;
                            if (!empty($post['categories'])) {
                                foreach ($post['categories'] as $cat) {
                                    if ($cat->parent == $category_id && $category_id !== 0) {
                                        $tag_category = $cat;
                                        break;
                                    }
                                }
                                if (!$tag_category && !empty($post['categories'])) {
                                    $tag_category = $post['categories'][0];
                                }
                            }
                            ?>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12">
                                <div class="nhut-continent-card">
                                    <?php if (!empty($post['thumbnail'])) : ?>
                                        <div class="nhut-continent-image">
                                            <a href="<?php echo esc_url($post['permalink']); ?>">
                                                <img src="<?php echo esc_url($post['thumbnail']); ?>" 
                                                     alt="<?php echo esc_attr($post['title']); ?>" 
                                                     class="img-fluid">
                                            </a>
                                            <?php if ($tag_category) : ?>
                                                <span class="nhut-continent-tag"><?php echo esc_html($tag_category->name); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="nhut-continent-content">
                                        <h3 class="nhut-continent-post-title">
                                            <a href="<?php echo esc_url($post['permalink']); ?>">
                                                <?php echo esc_html(wp_trim_words($post['title'], 15, '...')); ?>
                                            </a>
                                        </h3>
                                        
                                        <a href="<?php echo esc_url($post['permalink']); ?>" class="nhut-continent-contact">
                                            Liên hệ
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        echo '</div>'; // Close row
                        echo '</div>'; // Close carousel-item
                    }
                    ?>
                </div>
                
                <button class="carousel-control-prev nhut-continent-nav" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next nhut-continent-nav" type="button" data-bs-target="#<?php echo esc_attr($carousel_id); ?>" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Shortcode handlers
    public function render_asia($atts) {
        return $this->render_continent_carousel('asia', $atts);
    }
    
    public function render_europe($atts) {
        return $this->render_continent_carousel('europe', $atts);
    }
    
    public function render_africa($atts) {
        return $this->render_continent_carousel('africa', $atts);
    }
    
    public function render_america($atts) {
        return $this->render_continent_carousel('america', $atts);
    }
    
    public function render_oceania($atts) {
        return $this->render_continent_carousel('oceania', $atts);
    }

    // Xử lý Custom Shortcode
    public function render_custom($atts) {
        return $this->render_continent_carousel('custom', $atts);
    }
}

// Initialize
Nhut_Slide_For_Continent::get_instance();