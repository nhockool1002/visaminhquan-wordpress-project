<?php
/**
 * Script test để copy nội dung Elementor từ bài 671 sang một bài cụ thể
 * 
 * Cách sử dụng:
 * Truy cập: https://visaminhquan.ddev.site/wp-content/themes/visaminhquan/test-copy-elementor.php?target_id=629
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if user is admin
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Bạn không có quyền truy cập script này.' );
}

$source_post_id = 671;
$target_post_id = isset( $_GET['target_id'] ) ? intval( $_GET['target_id'] ) : 629;

echo "<h1>Test Copy Elementor Content</h1>";
echo "<p>Copy từ bài <strong>ID: {$source_post_id}</strong> sang bài <strong>ID: {$target_post_id}</strong></p>";

$source_post = get_post( $source_post_id );
$target_post = get_post( $target_post_id );

if ( ! $source_post ) {
	echo "<p style='color: red;'>❌ Không tìm thấy bài nguồn ID: {$source_post_id}</p>";
	exit;
}

if ( ! $target_post ) {
	echo "<p style='color: red;'>❌ Không tìm thấy bài đích ID: {$target_post_id}</p>";
	exit;
}

echo "<h2>Thông tin bài nguồn:</h2>";
echo "<p><strong>Tiêu đề:</strong> {$source_post->post_title}</p>";
echo "<p><strong>Elementor Edit Mode:</strong> " . get_post_meta( $source_post_id, '_elementor_edit_mode', true ) . "</p>";
echo "<p><strong>Elementor Data:</strong> " . ( ! empty( get_post_meta( $source_post_id, '_elementor_data', true ) ) ? 'Có' : 'Không' ) . "</p>";
echo "<p><strong>VMQ Use Elementor Layout:</strong> " . get_post_meta( $source_post_id, '_vmq_use_elementor_layout', true ) . "</p>";

echo "<h2>Thông tin bài đích (TRƯỚC KHI COPY):</h2>";
echo "<p><strong>Tiêu đề:</strong> {$target_post->post_title}</p>";
echo "<p><strong>Elementor Edit Mode:</strong> " . get_post_meta( $target_post_id, '_elementor_edit_mode', true ) . "</p>";
echo "<p><strong>Elementor Data:</strong> " . ( ! empty( get_post_meta( $target_post_id, '_elementor_data', true ) ) ? 'Có' : 'Không' ) . "</p>";
echo "<p><strong>VMQ Use Elementor Layout:</strong> " . get_post_meta( $target_post_id, '_vmq_use_elementor_layout', true ) . "</p>";

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

$source_uses_elementor = get_post_meta( $source_post_id, '_elementor_edit_mode', true ) === 'builder';
$source_has_elementor_data = ! empty( get_post_meta( $source_post_id, '_elementor_data', true ) );

echo "<h2>Bắt đầu copy...</h2>";

$copied_meta = array();
$failed_meta = array();

// Use Elementor's built-in copy method if available
if ( ( $source_uses_elementor || $source_has_elementor_data ) && class_exists( '\Elementor\Plugin' ) ) {
	$elementor = \Elementor\Plugin::$instance;
	
	echo "<p style='color: blue;'>→ Sử dụng Elementor API để copy...</p>";
	
	// Use Elementor's copy_elementor_meta method
	if ( method_exists( $elementor->db, 'copy_elementor_meta' ) ) {
		$elementor->db->copy_elementor_meta( $source_post_id, $target_post_id );
		$copied_meta[] = 'elementor_meta (via Elementor API)';
		echo "<p style='color: green;'>✓ Đã copy Elementor meta qua API</p>";
	} else {
		echo "<p style='color: orange;'>⚠ Elementor API không khả dụng, sử dụng phương pháp thủ công...</p>";
		// Fallback: Copy manually
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
					echo "<p style='color: green;'>✓ Đã copy: {$meta_key}</p>";
				} else {
					$failed_meta[] = $meta_key;
					echo "<p style='color: red;'>✗ Lỗi copy: {$meta_key}</p>";
				}
			} else {
				echo "<p style='color: gray;'>- Bỏ qua: {$meta_key} (không có giá trị)</p>";
			}
		}
	}
	
	// Also copy post_content
	$source_content = $source_post->post_content;
	if ( ! empty( $source_content ) ) {
		$update_result = wp_update_post( array(
			'ID' => $target_post_id,
			'post_content' => $source_content,
		) );
		if ( ! is_wp_error( $update_result ) ) {
			$copied_meta[] = 'post_content';
			echo "<p style='color: green;'>✓ Đã copy: post_content</p>";
		} else {
			$failed_meta[] = 'post_content';
			echo "<p style='color: red;'>✗ Lỗi copy: post_content - " . $update_result->get_error_message() . "</p>";
		}
	}
	
	// Clear cache and regenerate CSS
	echo "<p style='color: blue;'>→ Đang clear cache và regenerate CSS...</p>";
	$elementor->files_manager->clear_cache();
	delete_post_meta( $target_post_id, '_elementor_css' );
	delete_post_meta( $target_post_id, '_elementor_page_assets' );
	
	if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
		$css_file = \Elementor\Core\Files\CSS\Post::create( $target_post_id );
		if ( $css_file ) {
			$css_file->update();
			echo "<p style='color: green;'>✓ Đã regenerate CSS</p>";
		}
	}
}

// Always set _vmq_use_elementor_layout if source uses Elementor
if ( $source_uses_elementor || $source_has_elementor_data ) {
	$update_result = update_post_meta( $target_post_id, '_vmq_use_elementor_layout', '1' );
	if ( $update_result !== false ) {
		$copied_meta[] = '_vmq_use_elementor_layout';
		echo "<p style='color: green;'>✓ Đã set: _vmq_use_elementor_layout = '1'</p>";
	} else {
		$failed_meta[] = '_vmq_use_elementor_layout';
		echo "<p style='color: red;'>✗ Lỗi set: _vmq_use_elementor_layout</p>";
	}
}

echo "<h2>Thông tin bài đích (SAU KHI COPY):</h2>";
$target_post_after = get_post( $target_post_id );
echo "<p><strong>Tiêu đề:</strong> {$target_post_after->post_title} (giữ nguyên)</p>";
echo "<p><strong>Elementor Edit Mode:</strong> " . get_post_meta( $target_post_id, '_elementor_edit_mode', true ) . "</p>";
echo "<p><strong>Elementor Data:</strong> " . ( ! empty( get_post_meta( $target_post_id, '_elementor_data', true ) ) ? 'Có' : 'Không' ) . "</p>";
echo "<p><strong>VMQ Use Elementor Layout:</strong> " . get_post_meta( $target_post_id, '_vmq_use_elementor_layout', true ) . "</p>";

echo "<h2>Kết quả:</h2>";
echo "<p><strong>Đã copy thành công:</strong> " . count( $copied_meta ) . " meta keys</p>";
if ( ! empty( $copied_meta ) ) {
	echo "<ul>";
	foreach ( $copied_meta as $meta ) {
		echo "<li>{$meta}</li>";
	}
	echo "</ul>";
}

if ( ! empty( $failed_meta ) ) {
	echo "<p style='color: red;'><strong>Lỗi:</strong> " . count( $failed_meta ) . " meta keys</p>";
	echo "<ul>";
	foreach ( $failed_meta as $meta ) {
		echo "<li>{$meta}</li>";
	}
	echo "</ul>";
}

echo "<hr>";
echo "<p><a href='" . admin_url( "post.php?post={$target_post_id}&action=edit" ) . "'>Mở bài viết trong editor</a></p>";

