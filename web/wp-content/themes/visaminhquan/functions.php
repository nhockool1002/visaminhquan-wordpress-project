<?php
/**
 * VISAMINHQUAN Theme Functions
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Theme Setup
 */
function visaminhquan_setup() {
	// Add theme support for title tag
	add_theme_support( 'title-tag' );

	// Add theme support for post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add theme support for custom logo
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	// Add theme support for HTML5
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Add theme support for automatic feed links
	add_theme_support( 'automatic-feed-links' );

	// Register navigation menus
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'visaminhquan' ),
		'footer'  => esc_html__( 'Footer Menu', 'visaminhquan' ),
	) );

	// Set content width
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'visaminhquan_setup' );

/**
 * Enqueue Scripts and Styles
 */
function visaminhquan_scripts() {
	// Enqueue theme stylesheet
	wp_enqueue_style( 'visaminhquan-style', get_stylesheet_uri(), array(), '1.0' );

	// Enqueue Elementor homepage styles
	wp_enqueue_style( 'visaminhquan-elementor-homepage', get_template_directory_uri() . '/assets/css/elementor-homepage.css', array(), '1.0' );

	// Enqueue Custom Form CSS (Contact Form 7)
	wp_enqueue_style( 'visaminhquan-custom-form-css', get_template_directory_uri() . '/assets/css/custom-form-css.css', array(), '1.0.2' );
	$vmq_uploads = home_url( '/wp-content/uploads/2026/01/' );
	wp_add_inline_style( 'visaminhquan-custom-form-css', sprintf(
		":root {\n  --vmq-url-bg: url('%s');\n  --vmq-url-bg-hd1: url('%s');\n  --vmq-url-bg-hd2: url('%s');\n  --vmq-url-my1: url('%s');\n  --vmq-url-my2: url('%s');\n  --vmq-url-my3: url('%s');\n  --vmq-url-my4: url('%s');\n  --vmq-url-jk-bg: url('%s');\n  --vmq-url-cauhoi: url('%s');\n}\n",
		esc_url( $vmq_uploads . 'bg.jpg' ),
		esc_url( $vmq_uploads . 'bg-hd1.png' ),
		esc_url( $vmq_uploads . 'bg-hd2.png' ),
		esc_url( $vmq_uploads . 'my1.png' ),
		esc_url( $vmq_uploads . 'my2.png' ),
		esc_url( $vmq_uploads . 'my3.png' ),
		esc_url( $vmq_uploads . 'my4.png' ),
		esc_url( $vmq_uploads . 'jk-bg.png' ),
		esc_url( $vmq_uploads . 'cauhoi.png' )
	) );

	// Enqueue Custom Form JS (Testimonials Slider)
	wp_enqueue_script( 'visaminhquan-custom-form-js', get_template_directory_uri() . '/assets/js/custom-form-js.js', array(), '1.0.0', true );
	wp_localize_script( 'visaminhquan-custom-form-js', 'vmqThemeConfig', array(
		'homeUrl'  => home_url( '/' ),
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
	) );

	// Enqueue theme script
	wp_enqueue_script( 'visaminhquan-script', get_template_directory_uri() . '/js/theme.js', array(), '1.0', true );

	// Google Translate Script (load AFTER theme.js, in footer) so callback + DOM are ready
	wp_enqueue_script(
		'google-translate',
		'//translate.google.com/translate_a/element.js?cb=visaminhquanGoogleTranslateInit',
		array( 'visaminhquan-script' ),
		null,
		true
	);

	// Comment reply script disabled - comments are disabled
	// if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	// 	wp_enqueue_script( 'comment-reply' );
	// }
}
add_action( 'wp_enqueue_scripts', 'visaminhquan_scripts' );

/**
 * Register Widget Areas
 */
function visaminhquan_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'visaminhquan' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'visaminhquan' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'visaminhquan' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Add widgets here.', 'visaminhquan' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'visaminhquan_widgets_init' );

/**
 * Custom Excerpt Length
 */
function visaminhquan_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'visaminhquan_excerpt_length' );

/**
 * Custom Excerpt More
 */
function visaminhquan_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'visaminhquan_excerpt_more' );

/**
 * Meta box: cấu hình dùng full layout Elementor cho bài viết
 */
function visaminhquan_add_elementor_layout_metabox() {
	add_meta_box(
		'vmq_elementor_layout',
		__( 'Giao diện Elementor', 'visaminhquan' ),
		'visaminhquan_elementor_layout_metabox_callback',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'visaminhquan_add_elementor_layout_metabox' );

function visaminhquan_elementor_layout_metabox_callback( $post ) {
	wp_nonce_field( 'vmq_save_elementor_layout', 'vmq_elementor_layout_nonce' );
	$value = get_post_meta( $post->ID, '_vmq_use_elementor_layout', true );
	?>
	<p>
		<label for="vmq_use_elementor_layout">
			<input type="checkbox" id="vmq_use_elementor_layout" name="vmq_use_elementor_layout" value="1" <?php checked( $value, '1' ); ?> />
			<?php esc_html_e( 'Hiển thị full layout Elementor (bỏ qua layout blog mặc định)', 'visaminhquan' ); ?>
		</label>
	</p>
	<p style="font-size:12px;color:#666;">
		<?php esc_html_e( 'Dùng cho các trang dịch vụ/bài viết được thiết kế hoàn toàn bằng Elementor.', 'visaminhquan' ); ?>
	</p>
	<?php
}

function visaminhquan_save_elementor_layout_metabox( $post_id ) {
	// Nonce check.
	if ( ! isset( $_POST['vmq_elementor_layout_nonce'] ) || ! wp_verify_nonce( $_POST['vmq_elementor_layout_nonce'], 'vmq_save_elementor_layout' ) ) {
		return;
	}

	// Autosave?
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Quyền.
	if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Lưu meta.
	$use_elementor = isset( $_POST['vmq_use_elementor_layout'] ) && '1' === $_POST['vmq_use_elementor_layout'] ? '1' : '';

	if ( $use_elementor ) {
		update_post_meta( $post_id, '_vmq_use_elementor_layout', '1' );
	} else {
		delete_post_meta( $post_id, '_vmq_use_elementor_layout' );
	}
}
add_action( 'save_post', 'visaminhquan_save_elementor_layout_metabox' );

/**
 * Add custom CSS class to menu items with children
 */
function visaminhquan_add_menu_parent_class( $items ) {
	$parents = array();
	foreach ( $items as $item ) {
		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parents[] = $item->menu_item_parent;
		}
	}
	foreach ( $items as $item ) {
		if ( in_array( $item->ID, $parents ) ) {
			$item->classes[] = 'menu-item-has-children';
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'visaminhquan_add_menu_parent_class' );

/**
 * Thêm class riêng cho menu "Tin tức" để tùy chỉnh CSS (dropdown 1 cột, không full-width)
 */
function visaminhquan_add_tin_tuc_menu_class( $items, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		foreach ( $items as $item ) {
			if ( isset( $item->title ) && in_array( trim( $item->title ), array( 'Tin tức', 'News' ), true ) ) {
				$item->classes[] = 'menu-item-tin-tuc';
				break;
			}
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'visaminhquan_add_tin_tuc_menu_class', 15, 2 );

/**
 * Thêm class cho các nhóm visa châu lục để sắp xếp thứ tự: Âu - Á - Mỹ - Úc - Phi
 */
function visaminhquan_add_visa_menu_classes( $items, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		foreach ( $items as $item ) {
			$title = isset( $item->title ) ? trim( $item->title ) : '';
			if ( in_array( $title, array( 'Visa châu Âu', 'Visa Europe' ), true ) ) {
				$item->classes[] = 'menu-item-chau-au';
			} elseif ( in_array( $title, array( 'Visa châu Á', 'Visa Asia' ), true ) ) {
				$item->classes[] = 'menu-item-chau-a';
			} elseif ( in_array( $title, array( 'Visa châu Mỹ', 'Visa America' ), true ) ) {
				$item->classes[] = 'menu-item-chau-my';
			} elseif ( in_array( $title, array( 'Visa châu Úc', 'Visa Australia' ), true ) ) {
				$item->classes[] = 'menu-item-chau-uc';
			} elseif ( in_array( $title, array( 'Visa châu Phi', 'Visa Africa' ), true ) ) {
				$item->classes[] = 'menu-item-chau-phi';
			}
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'visaminhquan_add_visa_menu_classes', 15, 2 );

/**
 * Thêm class cho menu "Dịch vụ khác" - layout 4 cột (Hình 1)
 */
function visaminhquan_add_dich_vu_khac_menu_classes( $items, $args ) {
	if ( isset( $args->theme_location ) && 'primary' !== $args->theme_location ) {
		return $items;
	}
	$dich_vu_khac_id = 0;
	foreach ( $items as $item ) {
		if ( isset( $item->title ) && in_array( trim( $item->title ), array( 'Dịch vụ khác', 'Other Services' ), true ) ) {
			$item->classes[] = 'menu-item-dich-vu-khac';
			$dich_vu_khac_id = $item->ID;
			break;
		}
	}
	if ( ! $dich_vu_khac_id ) return $items;

	$col_map = array(
		'Dịch vụ hộ chiếu'  => 'menu-item-dv-col1',
		'Dịch vụ hồ chiếu'  => 'menu-item-dv-col1',
		'DỊCH VỤ HỘ CHIẾU'  => 'menu-item-dv-col1',
		'DỊCH VỤ HỒ CHIẾU'  => 'menu-item-dv-col1',
		'Visa Việt Nam'     => 'menu-item-dv-col2',
		'VISA VIỆT NAM'     => 'menu-item-dv-col2',
		'Visa du học'       => 'menu-item-dv-col3',
		'VISA DU HỌC'       => 'menu-item-dv-col3',
		'Visa định cư'      => 'menu-item-dv-col3',
		'VISA ĐỊNH CƯ'      => 'menu-item-dv-col3',
		'Bảo hiểm du lịch'  => 'menu-item-dv-col4',
		'BẢO HIỂM DU LỊCH'  => 'menu-item-dv-col4',
		'Vé máy bay'        => 'menu-item-dv-col4',
		'VÉ MÁY BAY'        => 'menu-item-dv-col4',
	);

	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent === (int) $dich_vu_khac_id ) {
			$title = isset( $item->title ) ? trim( $item->title ) : '';
			if ( isset( $col_map[ $title ] ) ) {
				$item->classes[] = $col_map[ $title ];
			}
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'visaminhquan_add_dich_vu_khac_menu_classes', 16, 2 );

/**
 * Lấy cài đặt Tin tức liên quan (slide trên footer).
 */
function visaminhquan_get_related_news_settings() {
	$defaults = array(
		'enabled'        => '1',
		'excluded_ids'   => '82',
		'posts_per_page' => 6,
	);

	$settings = get_option( 'visaminhquan_related_news_settings', array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	return wp_parse_args( $settings, $defaults );
}

/**
 * Kiểm tra có hiển thị slide tin tức liên quan không.
 *
 * Hiển thị trên tất cả trang/post đơn lẻ, trừ:
 *  - Trang chủ (front page / blog page)
 *  - Các ID nằm trong danh sách loại trừ (mặc định: 82 – trang chủ hiện tại)
 */
function visaminhquan_should_show_related_news_slider() {
	$settings = visaminhquan_get_related_news_settings();

	// Bật/tắt toàn cục từ wp-admin.
	if ( empty( $settings['enabled'] ) || '0' === (string) $settings['enabled'] ) {
		return false;
	}

	if ( is_front_page() || is_home() ) {
		return false;
	}

	if ( ! is_singular() ) {
		return false;
	}

	$current_id = get_queried_object_id();
	if ( ! $current_id ) {
		return false;
	}

	// Danh sách ID trang không hiển thị slide tin tức (có thể mở rộng qua filter).
	$raw_ids = isset( $settings['excluded_ids'] ) ? (string) $settings['excluded_ids'] : '';

	if ( '' === trim( $raw_ids ) ) {
		$ids = array( 82 );
	} else {
		$ids = array();
		foreach ( preg_split( '/[,]+/', $raw_ids ) as $part ) {
			$part = trim( $part );
			if ( '' === $part ) {
				continue;
			}
			$int_id = (int) $part;
			if ( $int_id > 0 ) {
				$ids[] = $int_id;
			}
		}
		if ( empty( $ids ) ) {
			$ids = array( 82 );
		}
	}

	$excluded_ids = apply_filters( 'visaminhquan_related_news_excluded_ids', $ids );

	if ( in_array( (int) $current_id, array_map( 'intval', (array) $excluded_ids ), true ) ) {
		return false;
	}

	return true;
}

/**
 * Render slide tin tức liên quan (hoặc tin mới nhất) phía trên footer.
 */
function visaminhquan_render_related_news_slider() {
	if ( ! visaminhquan_should_show_related_news_slider() ) {
		return;
	}

	$settings = visaminhquan_get_related_news_settings();
	$posts_per_page = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 6;
	if ( $posts_per_page < 1 ) {
		$posts_per_page = 1;
	}

	$current_id   = get_queried_object_id();
	$current_type = get_post_type( $current_id );

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => $posts_per_page,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	);

	// Nếu đang ở bài viết blog, loại trừ chính nó khỏi danh sách.
	if ( 'post' === $current_type ) {
		$args['post__not_in'] = array( $current_id );
	}

	$related_query = new WP_Query( $args );

	if ( ! $related_query->have_posts() ) {
		return;
	}

	$view_all_url = '';
	$blog_page_id = (int) get_option( 'page_for_posts' );
	if ( $blog_page_id ) {
		$view_all_url = get_permalink( $blog_page_id );
	} else {
		$archive_link = get_post_type_archive_link( 'post' );
		if ( $archive_link && ! is_wp_error( $archive_link ) ) {
			$view_all_url = $archive_link;
		}
	}

	?>
	<section class="vmq-related-news-section">
		<div class="vmq-container vmq-related-news-container">
			<div class="vmq-related-news-header">
				<h2 class="vmq-section-title"><?php esc_html_e( 'Tin tức liên quan', 'visaminhquan' ); ?></h2>
				<?php if ( $view_all_url ) : ?>
					<a class="vmq-related-view-all" href="<?php echo esc_url( $view_all_url ); ?>">
						<?php esc_html_e( 'Xem tất cả', 'visaminhquan' ); ?>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
							<path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				<?php endif; ?>
			</div>

			<div class="vmq-related-slider">
				<button class="vmq-related-arrow vmq-related-prev" type="button" aria-label="<?php esc_attr_e( 'Previous news', 'visaminhquan' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>

				<div class="vmq-related-track">
					<?php
					while ( $related_query->have_posts() ) :
						$related_query->the_post();
						?>
						<div class="vmq-related-item">
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'vmq-related-card' ); ?>>
								<a href="<?php the_permalink(); ?>" class="vmq-related-link">
									<div class="vmq-related-thumb">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail( 'medium_large' );
										}
										?>
									</div>
									<div class="vmq-related-body">
										<h3 class="vmq-related-title"><?php the_title(); ?></h3>
										<a href="<?php the_permalink(); ?>" class="vmq-related-more">
											<?php esc_html_e( 'Xem thêm', 'visaminhquan' ); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20" class="nhut-carousel-icon" aria-hidden="true" focusable="false">
												<path fill="#0056A4" fill-rule="evenodd" d="M.47 19.53a.75.75 0 0 1 0-1.06l7.72-7.72H4.655a.75.75 0 0 1 0-1.5H10a.75.75 0 0 1 .75.75v5.344a.75.75 0 0 1-1.5 0V11.81l-7.72 7.72a.75.75 0 0 1-1.06 0" clip-rule="evenodd"></path>
												<path fill="#0056A4" d="m1.518 15.3 3.052-3.052a2.25 2.25 0 0 1 .086-4.498H10A2.25 2.25 0 0 1 12.25 10v5.344a2.25 2.25 0 0 1-4.498.086L4.7 18.482A9.95 9.95 0 0 0 10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10c0 1.947.556 3.763 1.518 5.3"></path>
											</svg>
										</a>
									</div>
								</a>
							</article>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<button class="vmq-related-arrow vmq-related-next" type="button" aria-label="<?php esc_attr_e( 'Next news', 'visaminhquan' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Slider "Bài viết mới nhất" đặt trong single post (thay cho author box).
 * Tái sử dụng layout của slide tin tức liên quan.
 */
function visaminhquan_render_latest_posts_slider_inline() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$current_id = get_queried_object_id();

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => 6,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	if ( $current_id ) {
		$args['post__not_in'] = array( $current_id );
	}

	$latest_query = new WP_Query( $args );

	if ( ! $latest_query->have_posts() ) {
		return;
	}

	?>
	<section class="vmq-related-news-section vmq-related-news-section--inline">
		<div class="vmq-related-news-container">
			<div class="vmq-related-news-header">
				<h2 class="vmq-section-title"><?php esc_html_e( 'Bài viết mới nhất', 'visaminhquan' ); ?></h2>
			</div>

			<div class="vmq-related-slider">
				<button class="vmq-related-arrow vmq-related-prev" type="button" aria-label="<?php esc_attr_e( 'Previous news', 'visaminhquan' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>

				<div class="vmq-related-track">
					<?php
					while ( $latest_query->have_posts() ) :
						$latest_query->the_post();
						?>
						<div class="vmq-related-item">
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'vmq-related-card' ); ?>>
								<a href="<?php the_permalink(); ?>" class="vmq-related-link">
									<div class="vmq-related-thumb">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail( 'medium_large' );
										}
										?>
									</div>
									<div class="vmq-related-body">
										<h3 class="vmq-related-title"><?php the_title(); ?></h3>
										<a href="<?php the_permalink(); ?>" class="vmq-related-more">
											<?php esc_html_e( 'Xem thêm', 'visaminhquan' ); ?>
											<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20" class="nhut-carousel-icon" aria-hidden="true" focusable="false">
												<path fill="#0056A4" fill-rule="evenodd" d="M.47 19.53a.75.75 0 0 1 0-1.06l7.72-7.72H4.655a.75.75 0 0 1 0-1.5H10a.75.75 0 0 1 .75.75v5.344a.75.75 0 0 1-1.5 0V11.81l-7.72 7.72a.75.75 0 0 1-1.06 0" clip-rule="evenodd"></path>
												<path fill="#0056A4" d="m1.518 15.3 3.052-3.052a2.25 2.25 0 0 1 .086-4.498H10A2.25 2.25 0 0 1 12.25 10v5.344a2.25 2.25 0 0 1-4.498.086L4.7 18.482A9.95 9.95 0 0 0 10 20c5.523 0 10-4.477 10-10S15.523 0 10 0 0 4.477 0 10c0 1.947.556 3.763 1.518 5.3"></path>
											</svg>
										</a>
									</div>
								</a>
							</article>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<button class="vmq-related-arrow vmq-related-next" type="button" aria-label="<?php esc_attr_e( 'Next news', 'visaminhquan' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
						<path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Gắn URL page 313 cho menu "Dịch vụ visa"
 */
function visaminhquan_set_dich_vu_visa_menu_link( $items, $args ) {
	// Chỉ áp dụng cho primary menu
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		foreach ( $items as $item ) {
			if ( isset( $item->title ) && 'Dịch vụ visa' === trim( $item->title ) ) {
				$item->url = get_permalink( 313 );
			}
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'visaminhquan_set_dich_vu_visa_menu_link', 20, 2 );


/**
 * Đăng ký cài đặt Tin tức liên quan trong wp-admin.
 */
function visaminhquan_register_related_news_settings() {
	register_setting(
		'visaminhquan_related_news_group',
		'visaminhquan_related_news_settings',
		'visaminhquan_sanitize_related_news_settings'
	);
}
add_action( 'admin_init', 'visaminhquan_register_related_news_settings' );

/**
 * Sanitize dữ liệu cài đặt Tin tức liên quan.
 */
function visaminhquan_sanitize_related_news_settings( $input ) {
	$output = array();

	$output['enabled'] = ! empty( $input['enabled'] ) ? '1' : '0';

	if ( isset( $input['excluded_ids'] ) ) {
		$output['excluded_ids'] = sanitize_text_field( $input['excluded_ids'] );
	}

	if ( isset( $input['posts_per_page'] ) ) {
		$ppp = (int) $input['posts_per_page'];
		if ( $ppp < 1 ) {
			$ppp = 1;
		}
		$output['posts_per_page'] = $ppp;
	}

	return $output;
}

/**
 * Thêm trang cài đặt Tin tức liên quan vào menu Settings.
 */
function visaminhquan_add_related_news_settings_page() {
	add_options_page(
		__( 'Tin tức liên quan', 'visaminhquan' ),
		__( 'Tin tức liên quan', 'visaminhquan' ),
		'manage_options',
		'visaminhquan-related-news',
		'visaminhquan_render_related_news_settings_page'
	);
}
add_action( 'admin_menu', 'visaminhquan_add_related_news_settings_page' );

/**
 * Giao diện trang cài đặt Tin tức liên quan.
 */
function visaminhquan_render_related_news_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = visaminhquan_get_related_news_settings();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Cài đặt Tin tức liên quan', 'visaminhquan' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'visaminhquan_related_news_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="vmq_related_enabled"><?php esc_html_e( 'Hiển thị slide Tin tức liên quan', 'visaminhquan' ); ?></label>
					</th>
					<td>
						<label>
							<input type="checkbox" id="vmq_related_enabled" name="visaminhquan_related_news_settings[enabled]" value="1" <?php checked( $settings['enabled'], '1' ); ?> />
							<?php esc_html_e( 'Bật hiển thị trên tất cả trang/bài (trừ các trang bị loại trừ).', 'visaminhquan' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="vmq_related_excluded_ids"><?php esc_html_e( 'ID trang không hiển thị', 'visaminhquan' ); ?></label>
					</th>
					<td>
						<input type="text" id="vmq_related_excluded_ids" name="visaminhquan_related_news_settings[excluded_ids]" value="<?php echo esc_attr( $settings['excluded_ids'] ); ?>" class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'Nhập ID page/post, cách nhau bằng dấu phẩy. Ví dụ: 82,123,456', 'visaminhquan' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="vmq_related_posts_per_page"><?php esc_html_e( 'Số lượng bài hiển thị', 'visaminhquan' ); ?></label>
					</th>
					<td>
						<input type="number" id="vmq_related_posts_per_page" name="visaminhquan_related_news_settings[posts_per_page]" value="<?php echo esc_attr( $settings['posts_per_page'] ); ?>" min="1" step="1" />
						<p class="description">
							<?php esc_html_e( 'Tổng số bài trong slide Tin tức liên quan.', 'visaminhquan' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Create default menu structure programmatically
 */
function visaminhquan_create_default_menu() {
	// Check if menu already exists
	$menu_name = 'Primary Menu';
	$menu_exists = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu_exists ) {
		// Create menu
		$menu_id = wp_create_nav_menu( $menu_name );

		// Add menu items
		$menu_items = array(
			array(
				'title' => 'Về Minh Quân',
				'url' => home_url( '/ve-minh-quan' ),
			),
			array(
				'title' => 'Dịch vụ visa',
				'url' => '#',
				'children' => array(
					array(
						'title' => 'Visa châu Á',
						'url' => '#',
						'children' => array(
							array( 'title' => 'Visa Ấn Độ', 'url' => home_url( '/visa-an-do' ) ),
							array( 'title' => 'Visa Nhật Bản', 'url' => home_url( '/visa-nhat-ban' ) ),
							array( 'title' => 'Visa Đài Loan', 'url' => home_url( '/visa-dai-loan' ) ),
							array( 'title' => 'Visa Hồng Kong', 'url' => home_url( '/visa-hong-kong' ) ),
							array( 'title' => 'Visa Trung Quốc', 'url' => home_url( '/visa-trung-quoc' ) ),
							array( 'title' => 'Visa Dubai', 'url' => home_url( '/visa-dubai' ) ),
							array( 'title' => 'Visa Nga', 'url' => home_url( '/visa-nga' ) ),
						),
					),
					array(
						'title' => 'Visa châu Âu',
						'url' => '#',
						'children' => array(
							array( 'title' => 'Visa Bỉ', 'url' => home_url( '/visa-bi' ) ),
							array( 'title' => 'Visa Anh', 'url' => home_url( '/visa-anh' ) ),
							array( 'title' => 'Visa Đức', 'url' => home_url( '/visa-duc' ) ),
							array( 'title' => 'Visa Ý', 'url' => home_url( '/visa-y' ) ),
							array( 'title' => 'Visa Pháp', 'url' => home_url( '/visa-phap' ) ),
							array( 'title' => 'Visa Slovenia', 'url' => home_url( '/visa-slovenia' ) ),
							array( 'title' => 'Visa Bồ Đào Nha', 'url' => home_url( '/visa-bo-dao-nha' ) ),
							array( 'title' => 'Visa Hà Lan', 'url' => home_url( '/visa-ha-lan' ) ),
							array( 'title' => 'Visa Thụy Điển', 'url' => home_url( '/visa-thuy-dien' ) ),
							array( 'title' => 'Visa Đan Mạch', 'url' => home_url( '/visa-dan-mach' ) ),
							array( 'title' => 'Visa Ireland', 'url' => home_url( '/visa-ireland' ) ),
							array( 'title' => 'Visa Phần Lan', 'url' => home_url( '/visa-phan-lan' ) ),
							array( 'title' => 'Visa Áo', 'url' => home_url( '/visa-ao' ) ),
							array( 'title' => 'Visa Hy Lạp', 'url' => home_url( '/visa-hy-lap' ) ),
							array( 'title' => 'Visa Iceland', 'url' => home_url( '/visa-iceland' ) ),
							array( 'title' => 'Visa Na Uy', 'url' => home_url( '/visa-na-uy' ) ),
							array( 'title' => 'Visa Bulgaria', 'url' => home_url( '/visa-bulgaria' ) ),
							array( 'title' => 'Visa Hungary', 'url' => home_url( '/visa-hungary' ) ),
							array( 'title' => 'Visa Ba Lan', 'url' => home_url( '/visa-ba-lan' ) ),
							array( 'title' => 'Visa Lithuania', 'url' => home_url( '/visa-lithuania' ) ),
							array( 'title' => 'Visa Thụy Sĩ', 'url' => home_url( '/visa-thuy-si' ) ),
							array( 'title' => 'Visa Liechtenstein', 'url' => home_url( '/visa-liechtenstein' ) ),
							array( 'title' => 'Visa Cộng Hòa Síp', 'url' => home_url( '/visa-cong-hoa-sip' ) ),
						),
					),
					array(
						'title' => 'Visa châu Mỹ',
						'url' => '#',
						'children' => array(
							array( 'title' => 'Visa Úc', 'url' => home_url( '/visa-uc' ) ),
							array( 'title' => 'Visa Argentina', 'url' => home_url( '/visa-argentina' ) ),
							array( 'title' => 'Visa Fiji', 'url' => home_url( '/visa-fiji' ) ),
							array( 'title' => 'Visa Peru', 'url' => home_url( '/visa-peru' ) ),
							array( 'title' => 'Visa Mỹ', 'url' => home_url( '/visa-my' ) ),
							array( 'title' => 'Visa Canada', 'url' => home_url( '/visa-canada' ) ),
						),
					),
					array(
						'title' => 'Visa châu Phi',
						'url' => '#',
						'children' => array(
							array( 'title' => 'Visa Ai Cập', 'url' => home_url( '/visa-ai-cap' ) ),
						),
					),
					array(
						'title' => 'Visa châu Úc',
						'url' => '#',
						'children' => array(
							array( 'title' => 'Visa Úc', 'url' => home_url( '/visa-uc' ) ),
							array( 'title' => 'Visa New Zealand', 'url' => home_url( '/visa-new-zealand' ) ),
						),
					),
				),
			),
			array(
				'title' => 'Dịch vụ khác',
				'url' => '#',
				'children' => array(
					array( 'title' => 'Dịch vụ hộ chiếu', 'url' => home_url( '/dich-vu-ho-chieu' ), 'children' => array(
						array( 'title' => 'Hộ chiếu Úc', 'url' => home_url( '/ho-chieu-uc' ) ),
						array( 'title' => 'Hộ chiếu Canada', 'url' => home_url( '/ho-chieu-canada' ) ),
						array( 'title' => 'Hộ chiếu Mỹ', 'url' => home_url( '/ho-chieu-my' ) ),
						array( 'title' => 'Hộ chiếu Việt Nam', 'url' => home_url( '/ho-chieu-viet-nam' ) ),
					) ),
					array( 'title' => 'Visa Việt Nam', 'url' => home_url( '/visa-viet-nam' ), 'children' => array(
						array( 'title' => 'Làm mới visa Việt Nam', 'url' => home_url( '/lam-moi-visa-viet-nam' ) ),
						array( 'title' => 'Thẻ tạm trú', 'url' => home_url( '/the-tam-tru' ) ),
						array( 'title' => 'Giấy phép lao động', 'url' => home_url( '/giay-phep-lao-dong' ) ),
					) ),
					array( 'title' => 'Visa du học', 'url' => home_url( '/visa-du-hoc' ) ),
					array( 'title' => 'Visa định cư', 'url' => home_url( '/visa-dinh-cu' ), 'children' => array(
						array( 'title' => 'Visa định cư Úc', 'url' => home_url( '/visa-dinh-cu-uc' ) ),
						array( 'title' => 'Visa định cư Mỹ', 'url' => home_url( '/visa-dinh-cu-my' ) ),
					) ),
					array( 'title' => 'Bảo hiểm du lịch', 'url' => home_url( '/bao-hiem-du-lich' ) ),
					array( 'title' => 'Vé máy bay', 'url' => home_url( '/ve-may-bay' ) ),
				),
			),
			array(
				'title' => 'Tin tức',
				'url' => '#',
				'children' => array(
					array( 'title' => 'Cẩm nang xuất nhập cảnh', 'url' => home_url( '/cam-nang-xuat-nhap-canh' ) ),
					array( 'title' => 'Danh sách đại sứ quán', 'url' => home_url( '/danh-sach-dai-su-quan' ) ),
					array( 'title' => 'Danh sách các nước yêu cầu visa', 'url' => home_url( '/danh-sach-cac-nuoc-yeu-cau-visa' ) ),
					array( 'title' => 'Danh sách các nước không yêu cầu visa', 'url' => home_url( '/danh-sach-cac-nuoc-khong-yeu-cau-visa' ) ),
					array( 'title' => 'Kiểm tra tỷ lệ đậu visa', 'url' => home_url( '/kiem-tra-ty-le-dau-visa' ) ),
					array( 'title' => 'Thông báo & cập nhật', 'url' => home_url( '/thong-bao-cap-nhat' ) ),
				),
			),
		);

		// Helper function to add menu items recursively
		$add_menu_item = function( $parent_id, $item ) use ( &$add_menu_item, $menu_id ) {
			$menu_item_data = array(
				'menu-item-title' => $item['title'],
				'menu-item-url' => $item['url'],
				'menu-item-status' => 'publish',
			);

			if ( $parent_id ) {
				$menu_item_data['menu-item-parent-id'] = $parent_id;
			}

			$item_id = wp_update_nav_menu_item( $menu_id, 0, $menu_item_data );

			if ( isset( $item['children'] ) && is_array( $item['children'] ) ) {
				foreach ( $item['children'] as $child ) {
					$add_menu_item( $item_id, $child );
				}
			}

			return $item_id;
		};

		// Add all menu items
		foreach ( $menu_items as $item ) {
			$add_menu_item( 0, $item );
		}

		// Assign menu to location
		$locations = get_theme_mod( 'nav_menu_locations' );
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
add_action( 'after_setup_theme', 'visaminhquan_create_default_menu', 20 );

/**
 * Hide WordPress Admin Bar
 */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Load Carousel Post Addon
 */
$carousel_post_file = WP_CONTENT_DIR . '/nhut-addon/nhutplugin_carousel_post/carousel-post.php';
if ( file_exists( $carousel_post_file ) ) {
	require_once $carousel_post_file;
}

/**
 * Load Check Visa Pass Rate Addon
 */
$check_visa_file = WP_CONTENT_DIR . '/nhut-addon/nhutplugin_check_visa_pass_rate/check-visa-pass-rate.php';
if ( file_exists( $check_visa_file ) ) {
	require_once $check_visa_file;
}

/**
 * Load Slide for Continent Addon
 */
$slide_continent_file = WP_CONTENT_DIR . '/nhut-addon/nhutplugin_slide_for_continent/slide-for-continent.php';
if ( file_exists( $slide_continent_file ) ) {
	require_once $slide_continent_file;
}

/**
 * Load Crawler Post Addon
 */
$crawler_post_file = WP_CONTENT_DIR . '/nhut-addon/nhutplugin_slide_for_crawler_post/crawler-post.php';
if ( file_exists( $crawler_post_file ) ) {
	require_once $crawler_post_file;
}

/**
 * Create Visa Categories (chạy một lần)
 * Có thể chạy bằng cách thêm ?run_create_categories=1 vào URL admin
 * hoặc chạy file create-categories.php trực tiếp
 */
function nhut_create_visa_categories_on_demand() {
    // Chỉ chạy khi được yêu cầu và user có quyền
    if (isset($_GET['run_create_categories']) && current_user_can('manage_categories')) {
        $create_categories_file = WP_CONTENT_DIR . '/nhut-addon/nhutplugin_slide_for_continent/create-categories.php';
        if (file_exists($create_categories_file)) {
            require_once $create_categories_file;
            nhut_create_visa_categories();
            echo '<div class="notice notice-success"><p>Đã tạo categories thành công!</p></div>';
        }
    }
}
add_action('admin_init', 'nhut_create_visa_categories_on_demand');

/**
 * Disable Comments Completely
 */
// Close comments on the front-end
function visaminhquan_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'visaminhquan_disable_comments_status', 20, 2);
add_filter('pings_open', 'visaminhquan_disable_comments_status', 20, 2);

// Hide existing comments
function visaminhquan_disable_comments_hide_existing_comments($comments) {
	$comments = array();
	return $comments;
}
add_filter('comments_array', 'visaminhquan_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function visaminhquan_disable_comments_admin_menu() {
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'visaminhquan_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function visaminhquan_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url());
		exit;
	}
}
add_action('admin_init', 'visaminhquan_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function visaminhquan_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'visaminhquan_disable_comments_dashboard');

// Remove comments links from admin bar
function visaminhquan_disable_comments_admin_bar() {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}
add_action('init', 'visaminhquan_disable_comments_admin_bar');

// Remove comments support from all post types
function visaminhquan_disable_comments_post_types_support() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}
add_action('admin_init', 'visaminhquan_disable_comments_post_types_support', 100);

// Close comments for existing posts/pages
function visaminhquan_disable_comments_close_comments($open, $post_id) {
	return false;
}
add_filter('comments_open', 'visaminhquan_disable_comments_close_comments', 20, 2);

/**
 * Load Schema Markup Functions
 */
require_once get_template_directory() . '/inc/schema-markup.php';
require_once get_template_directory() . '/inc/form/hero-visa-form.php';

/**
 * Copy Elementor content from source post to target posts
 * 
 * @param int   $source_post_id Source post ID (default: 671 - VISA Mỹ)
 * @param array $target_post_ids Array of target post IDs to copy to
 * @return array Results with success/failure status
 */
function vmq_copy_elementor_content( $source_post_id = 671, $target_post_ids = array() ) {
	if ( empty( $target_post_ids ) ) {
		return array(
			'success' => false,
			'message' => 'Vui lòng cung cấp danh sách ID các trang cần copy nội dung.',
		);
	}

	// Verify source post exists
	$source_post = get_post( $source_post_id );
	if ( ! $source_post ) {
		return array(
			'success' => false,
			'message' => sprintf( 'Không tìm thấy trang nguồn với ID: %d', $source_post_id ),
		);
	}

	// List of Elementor meta keys to copy
	$elementor_meta_keys = array(
		'_elementor_data',
		'_elementor_edit_mode',
		'_elementor_template_type',
		'_elementor_page_settings',
		'_elementor_version',
		'_elementor_pro_version',
		'_elementor_css',
		'_elementor_controls_usage',
		'_elementor_page_assets',
	);

	// Also copy custom meta for Elementor layout
	$custom_meta_keys = array(
		'_vmq_use_elementor_layout',
	);

	$all_meta_keys = array_merge( $elementor_meta_keys, $custom_meta_keys );

	$results = array();
	$copied_count = 0;
	$failed_count = 0;

	foreach ( $target_post_ids as $target_post_id ) {
		$target_post = get_post( $target_post_id );
		
		if ( ! $target_post ) {
			$results[] = array(
				'post_id' => $target_post_id,
				'success' => false,
				'message' => sprintf( 'Không tìm thấy trang với ID: %d', $target_post_id ),
			);
			$failed_count++;
			continue;
		}

		$copied_meta = array();
		$failed_meta = array();

		// Check if source post uses Elementor
		$source_uses_elementor = get_post_meta( $source_post_id, '_elementor_edit_mode', true ) === 'builder';
		$source_has_elementor_data = ! empty( get_post_meta( $source_post_id, '_elementor_data', true ) );

		// Use Elementor's built-in copy method if available (handles JSON properly)
		if ( ( $source_uses_elementor || $source_has_elementor_data ) && class_exists( '\Elementor\Plugin' ) ) {
			$elementor = \Elementor\Plugin::$instance;
			
			// Use Elementor's copy_elementor_meta method which handles wp_slash correctly
			if ( method_exists( $elementor->db, 'copy_elementor_meta' ) ) {
				$elementor->db->copy_elementor_meta( $source_post_id, $target_post_id );
				$copied_meta[] = 'elementor_meta (via Elementor API)';
			} else {
				// Fallback: Copy manually with proper handling
				foreach ( $elementor_meta_keys as $meta_key ) {
					$meta_value = get_post_meta( $source_post_id, $meta_key, true );
					
					if ( '' !== $meta_value && $meta_value !== false ) {
						// For _elementor_data, use wp_slash
						if ( '_elementor_data' === $meta_key ) {
							$meta_value = wp_slash( $meta_value );
						}
						
						$update_result = update_metadata( 'post', $target_post_id, $meta_key, $meta_value );
						
						if ( $update_result !== false ) {
							$copied_meta[] = $meta_key;
						} else {
							$failed_meta[] = $meta_key;
						}
					}
				}
			}
			
			// Also copy post_content
			$source_content = $source_post->post_content;
			if ( ! empty( $source_content ) ) {
				wp_update_post( array(
					'ID' => $target_post_id,
					'post_content' => $source_content,
				) );
				$copied_meta[] = 'post_content';
			}
			
			// Clear Elementor cache and regenerate CSS
			$elementor->files_manager->clear_cache();
			delete_post_meta( $target_post_id, '_elementor_css' );
			delete_post_meta( $target_post_id, '_elementor_page_assets' );
			
			// Regenerate CSS for the post
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = \Elementor\Core\Files\CSS\Post::create( $target_post_id );
				if ( $css_file ) {
					$css_file->update();
				}
			}
		}

		// Always set _vmq_use_elementor_layout if source uses Elementor
		if ( $source_uses_elementor || $source_has_elementor_data ) {
			$update_result = update_post_meta( $target_post_id, '_vmq_use_elementor_layout', '1' );
			if ( $update_result !== false ) {
				$copied_meta[] = '_vmq_use_elementor_layout';
			} else {
				$failed_meta[] = '_vmq_use_elementor_layout';
			}
		}

		if ( ! empty( $copied_meta ) ) {
			$results[] = array(
				'post_id' => $target_post_id,
				'post_title' => $target_post->post_title,
				'success' => true,
				'copied_meta' => $copied_meta,
				'failed_meta' => $failed_meta,
				'message' => sprintf( 
					'Đã copy %d meta keys thành công cho trang "%s" (ID: %d)',
					count( $copied_meta ),
					$target_post->post_title,
					$target_post_id
				),
			);
			$copied_count++;
		} else {
			$results[] = array(
				'post_id' => $target_post_id,
				'post_title' => $target_post->post_title,
				'success' => false,
				'message' => sprintf( 'Không có dữ liệu Elementor nào để copy cho trang "%s" (ID: %d)', $target_post->post_title, $target_post_id ),
			);
			$failed_count++;
		}
	}

	return array(
		'success' => true,
		'source_post_id' => $source_post_id,
		'source_post_title' => $source_post->post_title,
		'total_targets' => count( $target_post_ids ),
		'copied_count' => $copied_count,
		'failed_count' => $failed_count,
		'results' => $results,
		'message' => sprintf( 
			'Hoàn thành! Đã copy nội dung Elementor từ "%s" (ID: %d) sang %d/%d trang thành công.',
			$source_post->post_title,
			$source_post_id,
			$copied_count,
			count( $target_post_ids )
		),
	);
}

/**
 * Copy Elementor content to ALL posts in the system (except source post)
 * 
 * @param int $source_post_id Source post ID (default: 671 - VISA Mỹ)
 * @return array Results with success/failure status
 */
function vmq_copy_elementor_content_to_all_posts( $source_post_id = 671 ) {
	// Verify source post exists
	$source_post = get_post( $source_post_id );
	if ( ! $source_post ) {
		return array(
			'success' => false,
			'message' => sprintf( 'Không tìm thấy trang nguồn với ID: %d', $source_post_id ),
		);
	}

	// Get all posts except the source post
	$all_posts = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'any', // Include all statuses
		'posts_per_page' => -1, // Get all posts
		'exclude'        => array( $source_post_id ),
		'fields'         => 'ids', // Only get IDs for performance
	) );

	if ( empty( $all_posts ) ) {
		return array(
			'success' => false,
			'message' => 'Không tìm thấy bài viết nào để copy nội dung.',
		);
	}

	// Use existing function to copy
	return vmq_copy_elementor_content( $source_post_id, $all_posts );
}

/**
 * Đồng bộ Primary Menu: tạo category tương ứng cho từng item (Custom Link) chưa có, rồi gắn link menu vào category.
 * Chỉ xử lý các menu item có type = 'custom'.
 *
 * @return array{created: array, updated: array, skipped: array, errors: array}
 */
function visaminhquan_sync_primary_menu_to_categories() {
	$report = array(
		'created' => array(),
		'updated' => array(),
		'skipped' => array(),
		'errors'  => array(),
	);

	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) ) {
		$report['errors'][] = 'Chưa gán Primary Menu tại Appearance → Menus.';
		return $report;
	}

	$menu_id = (int) $locations['primary'];
	$items   = wp_get_nav_menu_items( $menu_id );
	if ( ! is_array( $items ) ) {
		$report['errors'][] = 'Không lấy được danh sách menu.';
		return $report;
	}

	foreach ( $items as $item ) {
		if ( $item->type !== 'custom' ) {
			$report['skipped'][] = array(
				'title' => $item->title,
				'reason' => 'Đã là ' . $item->type . ', bỏ qua.',
			);
			continue;
		}

		$title = trim( $item->title );
		if ( $title === '' ) {
			$report['skipped'][] = array( 'title' => '(không tiêu đề)', 'reason' => 'Tiêu đề trống.' );
			continue;
		}

		$slug = sanitize_title( $title );
		$term = get_term_by( 'slug', $slug, 'category' );

		if ( ! $term ) {
			$insert = wp_insert_term( $title, 'category', array( 'slug' => $slug ) );
			if ( is_wp_error( $insert ) ) {
				$report['errors'][] = $title . ': ' . $insert->get_error_message();
				continue;
			}
			$term_id = (int) $insert['term_id'];
			$report['created'][] = array( 'title' => $title, 'slug' => $slug, 'term_id' => $term_id );
		} else {
			$term_id = (int) $term->term_id;
		}

		$args = array(
			'menu-item-db-id'       => (int) $item->ID,
			'menu-item-type'        => 'taxonomy',
			'menu-item-object'      => 'category',
			'menu-item-object-id'   => $term_id,
			'menu-item-title'       => $title,
			'menu-item-position'    => (int) $item->menu_order,
			'menu-item-parent-id'   => (int) $item->menu_item_parent,
			'menu-item-status'      => 'publish',
		);

		$updated = wp_update_nav_menu_item( $menu_id, $item->ID, $args );
		if ( is_wp_error( $updated ) ) {
			$report['errors'][] = $title . ': ' . $updated->get_error_message();
			continue;
		}

		$report['updated'][] = array( 'title' => $title, 'url' => get_category_link( $term_id ) );
	}

	return $report;
}

/**
 * Trang admin: Đồng bộ Menu với Categories
 */
function vmq_add_sync_menu_categories_admin_page() {
	add_submenu_page(
		'themes.php',
		'Đồng bộ Menu với Categories',
		'Menu → Categories',
		'manage_options',
		'vmq-sync-menu-categories',
		'vmq_sync_menu_categories_page_callback'
	);
}
add_action( 'admin_menu', 'vmq_add_sync_menu_categories_admin_page' );

/**
 * Callback trang Đồng bộ Menu với Categories
 */
function vmq_sync_menu_categories_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Bạn không có quyền truy cập trang này.' );
	}

	$report = null;
	if ( isset( $_POST['vmq_sync_menu_categories'] ) && check_admin_referer( 'vmq_sync_menu_categories_action', 'vmq_sync_menu_categories_nonce' ) ) {
		$report = visaminhquan_sync_primary_menu_to_categories();
	}
	?>
	<div class="wrap">
		<h1>Đồng bộ Primary Menu với Categories</h1>
		<p>Tool này kiểm tra từng mục trong <strong>Primary Menu</strong>. Với mục đang là <strong>Custom Link</strong>, sẽ tạo category trùng tên (nếu chưa có) và chuyển link menu sang trỏ tới category đó.</p>

		<?php if ( $report !== null ) : ?>
			<?php if ( ! empty( $report['errors'] ) ) : ?>
				<div class="notice notice-error is-dismissible">
					<p><strong>Lỗi:</strong></p>
					<ul><li><?php echo esc_html( implode( '</li><li>', $report['errors'] ) ); ?></li></ul>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $report['created'] ) ) : ?>
				<div class="notice notice-success">
					<p><strong>Đã tạo category mới (<?php echo count( $report['created'] ); ?>):</strong></p>
					<ul>
						<?php foreach ( $report['created'] as $r ) : ?>
							<li><?php echo esc_html( $r['title'] ); ?> → slug: <code><?php echo esc_html( $r['slug'] ); ?></code>, term_id: <?php echo (int) $r['term_id']; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $report['updated'] ) ) : ?>
				<div class="notice notice-success">
					<p><strong>Đã chuyển link menu sang category (<?php echo count( $report['updated'] ); ?>):</strong></p>
					<ul>
						<?php foreach ( $report['updated'] as $r ) : ?>
							<li><?php echo esc_html( $r['title'] ); ?> → <a href="<?php echo esc_url( $r['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $r['url'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $report['skipped'] ) ) : ?>
				<div class="notice notice-info">
					<p><strong>Đã bỏ qua (<?php echo count( $report['skipped'] ); ?>):</strong></p>
					<ul>
						<?php foreach ( $report['skipped'] as $r ) : ?>
							<li><?php echo esc_html( $r['title'] ); ?> — <?php echo esc_html( $r['reason'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( empty( $report['created'] ) && empty( $report['updated'] ) && empty( $report['errors'] ) && ! empty( $report['skipped'] ) ) : ?>
				<p>Tất cả các mục menu đã được xử lý trước đó hoặc không phải Custom Link. Không có thay đổi.</p>
			<?php endif; ?>
		<?php endif; ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'vmq_sync_menu_categories_action', 'vmq_sync_menu_categories_nonce' ); ?>
			<p>
				<input type="submit" name="vmq_sync_menu_categories" class="button button-primary" value="Chạy đồng bộ Primary Menu → Categories" />
			</p>
		</form>
		<p class="description">Sau khi đồng bộ, vào <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">Appearance → Menus</a> để kiểm tra và nhấn <strong>Save Menu</strong> nếu cần.</p>
	</div>
	<?php
}

/**
 * Admin page to copy Elementor content
 */
function vmq_add_elementor_copy_admin_page() {
	add_submenu_page(
		'tools.php',
		'Copy Nội dung Elementor',
		'Copy Elementor',
		'manage_options',
		'vmq-copy-elementor',
		'vmq_elementor_copy_admin_page_callback'
	);
}
add_action( 'admin_menu', 'vmq_add_elementor_copy_admin_page' );

/**
 * Admin page callback
 */
function vmq_elementor_copy_admin_page_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Bạn không có quyền truy cập trang này.' );
	}

	$source_post_id = 671; // VISA Mỹ
	$result = null;

	// Handle form submission - Copy to all posts
	if ( isset( $_POST['vmq_copy_elementor_to_all_submit'] ) && check_admin_referer( 'vmq_copy_elementor_to_all_action', 'vmq_copy_elementor_to_all_nonce' ) ) {
		// Confirm action
		if ( isset( $_POST['vmq_confirm_copy_all'] ) && 'yes' === $_POST['vmq_confirm_copy_all'] ) {
			$result = vmq_copy_elementor_content_to_all_posts( $source_post_id );
		} else {
			$result = array(
				'success' => false,
				'message' => 'Vui lòng xác nhận bằng cách tích vào ô "Tôi đã hiểu và muốn tiếp tục".',
			);
		}
	}

	// Handle form submission - Copy to specific posts
	if ( isset( $_POST['vmq_copy_elementor_submit'] ) && check_admin_referer( 'vmq_copy_elementor_action', 'vmq_copy_elementor_nonce' ) ) {
		$target_ids_input = sanitize_text_field( $_POST['vmq_target_post_ids'] );
		
		// Parse comma-separated IDs
		$target_post_ids = array_map( 'trim', explode( ',', $target_ids_input ) );
		$target_post_ids = array_map( 'intval', $target_post_ids );
		$target_post_ids = array_filter( $target_post_ids );

		if ( ! empty( $target_post_ids ) ) {
			$result = vmq_copy_elementor_content( $source_post_id, $target_post_ids );
		} else {
			$result = array(
				'success' => false,
				'message' => 'Vui lòng nhập ít nhất một ID trang.',
			);
		}
	}

	// Get source post info
	$source_post = get_post( $source_post_id );
	?>
	<div class="wrap">
		<h1>Copy Nội dung Elementor</h1>
		
		<?php if ( $result ) : ?>
			<div class="notice notice-<?php echo $result['success'] ? 'success' : 'error'; ?> is-dismissible">
				<p><strong><?php echo esc_html( $result['message'] ); ?></strong></p>
			</div>

			<?php if ( isset( $result['results'] ) ) : ?>
				<h2>Chi tiết kết quả:</h2>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Tên trang</th>
							<th>Trạng thái</th>
							<th>Chi tiết</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $result['results'] as $item ) : ?>
							<tr>
								<td><?php echo esc_html( $item['post_id'] ); ?></td>
								<td><strong><?php echo esc_html( $item['post_title'] ?? 'N/A' ); ?></strong></td>
								<td>
									<span style="color: <?php echo $item['success'] ? 'green' : 'red'; ?>;">
										<?php echo $item['success'] ? '✓ Thành công' : '✗ Thất bại'; ?>
									</span>
								</td>
								<td>
									<?php echo esc_html( $item['message'] ); ?>
									<?php if ( isset( $item['copied_meta'] ) && ! empty( $item['copied_meta'] ) ) : ?>
										<br><small>Meta đã copy: <?php echo esc_html( implode( ', ', $item['copied_meta'] ) ); ?></small>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>

		<?php
		// Count total posts (except source)
		$total_posts = wp_count_posts( 'post' );
		$total_posts_count = array_sum( (array) $total_posts ) - 1; // Exclude source post
		?>

		<div class="card" style="border-left: 4px solid #d63638; margin-bottom: 20px;">
			<h2 style="color: #d63638;">⚠️ Copy Nội dung sang TẤT CẢ Bài viết</h2>
			<p><strong>Chức năng này sẽ copy nội dung Elementor từ bài viết <strong>VISA Mỹ (ID: <?php echo esc_html( $source_post_id ); ?>)</strong> sang <strong>TẤT CẢ</strong> các bài viết khác trong hệ thống.</strong></p>
			<p><strong>Số lượng bài viết sẽ bị ảnh hưởng: <?php echo esc_html( $total_posts_count ); ?> bài viết</strong></p>
			
			<form method="post" action="" style="margin-top: 20px;">
				<?php wp_nonce_field( 'vmq_copy_elementor_to_all_action', 'vmq_copy_elementor_to_all_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="vmq_source_post_id_all">Trang nguồn (Source)</label>
						</th>
						<td>
							<input type="number" id="vmq_source_post_id_all" value="<?php echo esc_attr( $source_post_id ); ?>" readonly class="regular-text" />
							<p class="description">
								<?php if ( $source_post ) : ?>
									<strong><?php echo esc_html( $source_post->post_title ); ?></strong>
								<?php else : ?>
									Trang không tồn tại
								<?php endif; ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="vmq_confirm_copy_all">Xác nhận</label>
						</th>
						<td>
							<label>
								<input type="checkbox" id="vmq_confirm_copy_all" name="vmq_confirm_copy_all" value="yes" required />
								<strong>Tôi đã hiểu và muốn copy nội dung Elementor sang TẤT CẢ các bài viết khác (<?php echo esc_html( $total_posts_count ); ?> bài viết). Tiêu đề của từng bài sẽ được giữ nguyên.</strong>
							</label>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" name="vmq_copy_elementor_to_all_submit" class="button button-primary" value="Copy Nội dung sang TẤT CẢ Bài viết" style="background: #d63638; border-color: #d63638;" />
				</p>
			</form>

			<div class="notice notice-warning">
				<p><strong>⚠️ CẢNH BÁO:</strong></p>
				<ul style="list-style: disc; margin-left: 20px;">
					<li>Nội dung Elementor hiện tại của <strong>TẤT CẢ</strong> các bài viết sẽ bị ghi đè.</li>
					<li>Chỉ copy nội dung Elementor, <strong>tiêu đề bài viết sẽ được giữ nguyên</strong>.</li>
					<li>Hãy <strong>backup dữ liệu</strong> trước khi thực hiện!</li>
					<li>Hành động này không thể hoàn tác tự động.</li>
				</ul>
			</div>
		</div>

		<div class="card">
			<h2>Copy Nội dung sang Bài viết Cụ thể</h2>
			<p>Tool này sẽ copy toàn bộ nội dung Elementor từ trang <strong>VISA Mỹ (ID: <?php echo esc_html( $source_post_id ); ?>)</strong> sang các trang được chỉ định.</p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'vmq_copy_elementor_action', 'vmq_copy_elementor_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="vmq_source_post_id">Trang nguồn (Source)</label>
						</th>
						<td>
							<input type="number" id="vmq_source_post_id" value="<?php echo esc_attr( $source_post_id ); ?>" readonly class="regular-text" />
							<p class="description">
								<?php if ( $source_post ) : ?>
									<strong><?php echo esc_html( $source_post->post_title ); ?></strong>
								<?php else : ?>
									Trang không tồn tại
								<?php endif; ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="vmq_target_post_ids">ID các trang đích (Target)</label>
						</th>
						<td>
							<input type="text" id="vmq_target_post_ids" name="vmq_target_post_ids" class="regular-text" placeholder="Ví dụ: 672, 673, 674" required />
							<p class="description">
								Nhập các ID trang cần copy nội dung, cách nhau bởi dấu phẩy (ví dụ: 672, 673, 674)
							</p>
						</td>
					</tr>
				</table>

				<p class="submit">
					<input type="submit" name="vmq_copy_elementor_submit" class="button button-primary" value="Copy Nội dung Elementor" />
				</p>
			</form>

			<div class="notice notice-info">
				<p><strong>Lưu ý:</strong></p>
				<ul style="list-style: disc; margin-left: 20px;">
					<li>Nội dung Elementor hiện tại của các trang đích sẽ bị ghi đè.</li>
					<li>Hãy backup dữ liệu trước khi thực hiện copy.</li>
					<li>Các meta keys được copy: <code>_elementor_data</code>, <code>_elementor_edit_mode</code>, <code>_elementor_template_type</code>, <code>_elementor_page_settings</code>, và các meta khác liên quan đến Elementor.</li>
					<li>Meta <code>_vmq_use_elementor_layout</code> cũng sẽ được copy để bật chế độ Elementor layout.</li>
				</ul>
			</div>
		</div>
	</div>
	<?php
}

