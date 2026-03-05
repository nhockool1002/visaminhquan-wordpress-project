<?php
/**
 * Script để copy nội dung Elementor từ bài 671 sang TẤT CẢ các bài viết khác
 * 
 * Cách sử dụng:
 * 1. Truy cập: https://visaminhquan.ddev.site/wp-content/themes/visaminhquan/copy-elementor-to-all.php
 * 2. Hoặc chạy từ command line: php copy-elementor-to-all.php
 * 
 * LƯU Ý: Xóa file này sau khi sử dụng xong để bảo mật!
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if user is admin (for web access)
if ( ! is_admin() && ! current_user_can( 'manage_options' ) ) {
	// For CLI, allow execution
	if ( php_sapi_name() !== 'cli' ) {
		wp_die( 'Bạn không có quyền truy cập script này.' );
	}
}

// Source post ID
$source_post_id = 671;

echo "=== Bắt đầu copy nội dung Elementor ===\n";
echo "Trang nguồn: VISA Mỹ (ID: {$source_post_id})\n\n";

// Get all posts except source
$all_posts = get_posts( array(
	'post_type'      => 'post',
	'post_status'    => 'any',
	'posts_per_page' => -1,
	'exclude'        => array( $source_post_id ),
	'fields'         => 'ids',
) );

if ( empty( $all_posts ) ) {
	echo "❌ Không tìm thấy bài viết nào để copy.\n";
	exit;
}

echo "Tìm thấy " . count( $all_posts ) . " bài viết cần copy.\n\n";

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

$custom_meta_keys = array(
	'_vmq_use_elementor_layout',
);

$all_meta_keys = array_merge( $elementor_meta_keys, $custom_meta_keys );

$copied_count = 0;
$failed_count = 0;
$results = array();

foreach ( $all_posts as $target_post_id ) {
	$target_post = get_post( $target_post_id );
	
	if ( ! $target_post ) {
		echo "❌ Không tìm thấy bài viết ID: {$target_post_id}\n";
		$failed_count++;
		continue;
	}

	$copied_meta = array();
	$failed_meta = array();

	// Check if source post uses Elementor
	$source_uses_elementor = get_post_meta( $source_post_id, '_elementor_edit_mode', true ) === 'builder';
	$source_has_elementor_data = ! empty( get_post_meta( $source_post_id, '_elementor_data', true ) );

	// Use Elementor's built-in copy method if available
	if ( ( $source_uses_elementor || $source_has_elementor_data ) && class_exists( '\Elementor\Plugin' ) ) {
		$elementor = \Elementor\Plugin::$instance;
		
		// Use Elementor's copy_elementor_meta method
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
		$source_post_obj = get_post( $source_post_id );
		$source_content = $source_post_obj->post_content;
		if ( ! empty( $source_content ) ) {
			wp_update_post( array(
				'ID' => $target_post_id,
				'post_content' => $source_content,
			) );
			$copied_meta[] = 'post_content';
		}
		
		// Clear cache and regenerate CSS
		$elementor->files_manager->clear_cache();
		delete_post_meta( $target_post_id, '_elementor_css' );
		delete_post_meta( $target_post_id, '_elementor_page_assets' );
		
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = \Elementor\Core\Files\CSS\Post::create( $target_post_id );
			if ( $css_file ) {
				$css_file->update();
			}
		}
	}

	// Always set _vmq_use_elementor_layout if source uses Elementor
	if ( $source_uses_elementor || $source_has_elementor_data ) {
		update_post_meta( $target_post_id, '_vmq_use_elementor_layout', '1' );
		$copied_meta[] = '_vmq_use_elementor_layout';
	}

	if ( ! empty( $copied_meta ) ) {
		echo "✓ Đã copy bài viết: \"{$target_post->post_title}\" (ID: {$target_post_id})\n";
		$copied_count++;
	} else {
		echo "⚠ Không có dữ liệu Elementor để copy cho bài: \"{$target_post->post_title}\" (ID: {$target_post_id})\n";
		$failed_count++;
	}
}

echo "\n=== Kết quả ===\n";
echo "Tổng số bài viết: " . count( $all_posts ) . "\n";
echo "Thành công: {$copied_count}\n";
echo "Thất bại: {$failed_count}\n";
echo "\n✅ Hoàn thành!\n";
echo "⚠️ LƯU Ý: Hãy xóa file này sau khi sử dụng để bảo mật!\n";

