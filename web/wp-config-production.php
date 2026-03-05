<?php
/**
 * Mẫu cấu hình Production.
 * Copy thành wp-config-production.php trên server và điền thông tin thật.
 * Không commit wp-config-production.php (chứa mật khẩu).
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Cho phép load độc lập từ wp-config.php
}

define( 'DB_NAME', 'nhviszvu_test' );
define( 'DB_USER', 'nhviszvu' );
define( 'DB_PASSWORD', 'P4zNR3@?Z@!k4!6' );
define( 'DB_HOST', 'localhost' ); // hoặc theo hướng dẫn hosting (vd: mysql.hosting.com)

// URL site production (tùy chọn; nếu bỏ qua, WordPress lấy từ bảng wp_options)
define( 'WP_HOME', 'https://visaminhquan.com.vn' );
define( 'WP_SITEURL', 'https://visaminhquan.com.vn' );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

if ( ! isset( $table_prefix ) || empty( $table_prefix ) ) {
	$table_prefix = 'wp_';
}
