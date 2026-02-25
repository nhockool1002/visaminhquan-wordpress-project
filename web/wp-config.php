<?php
/**
 * #ddev-generated: Automatically generated WordPress settings file.
 * ddev manages this file and may delete or overwrite the file unless this comment is removed.
 * It is recommended that you leave this file alone.
 *
 * @package ddevapp
 */

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Authentication Unique Keys and Salts. */
define( 'AUTH_KEY', 'aGUNMeDRTWKvCLuKtEfvBKOcFyWdDubLLhttvKrSuxkATUWvXqRqXBgMvNGyIeiX' );
define( 'SECURE_AUTH_KEY', 'iJdbAVnaDiAhmAERQwrAnBZcAXNWjbYvUBEosanDePrsedNjwCapgzcVgrTgPMqD' );
define( 'LOGGED_IN_KEY', 'TmpnqyQTCTLFBSpnTUTwozNCuafaFSuqNpmGpdlAwoayitKQvdbvkztPUOKVHQHh' );
define( 'NONCE_KEY', 'pnKAWbVUaPYMiEPcaQeRamJdjmOdEDCKoCTajwdbLVvQAFQEoHfAJikdeBEACiUr' );
define( 'AUTH_SALT', 'IWHzNdzxWQYslZEhDxjfkxVUTwYqWIJklHyRhLLxPwGCYLdUzOsECkohrycwUXEB' );
define( 'SECURE_AUTH_SALT', 'QsynPlGjFVgwVlVtgLOlVoUgbtamqGZZmxiAtpCcLMZtYeTRkwZUKgeVLeAHupRB' );
define( 'LOGGED_IN_SALT', 'YjQdjBEhwVCxDywRaVLuzwfVqIrJlDzTtiJqJxPqOLBYOISivRiZNvBRcqkjUHHN' );
define( 'NONCE_SALT', 'sQohphSocKQlnMlQUHIoWLFuXsGcQmLXDrRtqJWJxtgraBLAeGFskRJzlDLqIFJQ' );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
defined( 'ABSPATH' ) || define( 'ABSPATH', dirname( __FILE__ ) . '/' );

// Include environment-specific settings.
$ddev_settings       = __DIR__ . '/wp-config-ddev.php';
$production_settings = __DIR__ . '/wp-config-production.php';

if ( ! defined( 'DB_USER' ) ) {
	// Ưu tiên cấu hình local của ddev khi chạy trong môi trường ddev.
	if ( getenv( 'IS_DDEV_PROJECT' ) === 'true' && is_readable( $ddev_settings ) ) {
		require_once $ddev_settings;
	// Khi deploy lên production (không phải ddev), đọc file wp-config-production.php nếu tồn tại.
	} elseif ( is_readable( $production_settings ) ) {
		require_once $production_settings;
	}
}

/** Include wp-settings.php */
if ( file_exists( ABSPATH . '/wp-settings.php' ) ) {
	require_once ABSPATH . '/wp-settings.php';
}
