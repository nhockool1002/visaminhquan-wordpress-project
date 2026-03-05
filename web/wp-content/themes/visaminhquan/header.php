<?php
/**
 * The header for our theme
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TW93HHDD');</script>
    <!-- End Google Tag Manager -->
	<link rel="preload" as="image" href="https://visaminhquan.com.vn/wp-content/uploads/2026/02/vs1.png" fetchpriority="high">
	<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Manrope-Regular.woff2" type="font/woff2" crossorigin>
<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Manrope-Bold.woff2" type="font/woff2" crossorigin>
<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Manrope-SemiBold.woff2" type="font/woff2" crossorigin>
<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Manrope-Medium.woff2" type="font/woff2" crossorigin>
<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Manrope-ExtraBold.woff2" type="font/woff2" crossorigin>
<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Roboto-Regular.woff2" type="font/woff2" crossorigin>
<link rel="preload" as="font" href="https://visaminhquan.com.vn/wp-content/themes/visaminhquan/assets/font/Roboto-Bold.woff2" type="font/woff2" crossorigin>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TW93HHDD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'visaminhquan' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="vmq-top-contact-bar">
			<div class="container">
				<div class="vmq-top-contact-inner">
					<div class="vmq-top-contact-left">
						<span class="vmq-top-contact-icon" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" focusable="false">
								<path d="M5.5 3A2.5 2.5 0 0 0 3 5.5 15.5 15.5 0 0 0 18.5 21 2.5 2.5 0 0 0 21 18.5v-2a1 1 0 0 0-1-1h-3a1 1 0 0 0-1 .76l-.5 2a1 1 0 0 1-1 .74 9.5 9.5 0 0 1-6.5-6.5 1 1 0 0 1 .74-1l2-.5a1 1 0 0 0 .76-1V4a1 1 0 0 0-1-1h-2Z" />
							</svg>
						</span>
						<span class="vmq-top-contact-phone-label">Hotline:</span>
						<a href="tel:0924727789" class="vmq-top-contact-phone-number">0924 727 789</a>
					</div>
					<div class="vmq-top-contact-right">
						<span class="vmq-top-contact-icon" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" focusable="false">
								<path d="M12 3.5a8.5 8.5 0 1 0 8.5 8.5A8.51 8.51 0 0 0 12 3.5Zm0 2a6.5 6.5 0 1 1-6.5 6.5A6.51 6.51 0 0 1 12 5.5Zm-.75 1.75a1 1 0 0 0-1 1v3.75a1 1 0 0 0 .44.83l2.75 1.83a1 1 0 1 0 1.12-1.66L12.25 11V8.25a1 1 0 0 0-1-1Z" />
							</svg>
						</span>
						<span class="vmq-top-contact-work-label">Giờ làm việc:</span>
						<span class="vmq-top-contact-work-time">8:00 - 21:00</span>
					</div>
				</div>
			</div>
		</div>
		<div class="header-top">
			<div class="container">
				<div class="header-wrapper">
					<div class="site-branding">
						<?php
						if ( has_custom_logo() ) {
							the_custom_logo();
						} else {
							?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-logo-link">
								<div class="site-logo-placeholder">
									<!-- Logo sẽ được thêm vào đây -->
								</div>
							</a>
							<?php
						}
						?>
					</div><!-- .site-branding -->

					<button class="mobile-menu-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'visaminhquan' ); ?>" aria-expanded="false">
						<span class="hamburger-icon">
							<span class="hamburger-line"></span>
							<span class="hamburger-line"></span>
							<span class="hamburger-line"></span>
						</span>
					</button>

					<nav id="site-navigation" class="main-navigation">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'menu_id'        => 'primary-menu',
								'container'      => false,
								'menu_class'     => 'primary-menu',
								'fallback_cb'    => false,
								'depth'          => 3,
							)
						);
						?>
					</nav><!-- #site-navigation -->

					<!-- Mobile Menu V2: Vertical Icon Tabs -->
					<div class="vmq-mobile-menu" id="vmq-mobile-menu" aria-hidden="true">
						<div class="vmq-mobile-menu-backdrop"></div>
						<div class="vmq-mobile-menu-panel" role="dialog" aria-modal="true">
							<div class="vmq-mobile-menu-header">
								<span class="vmq-mobile-menu-title">Menu</span>
								<button type="button" class="vmq-mobile-menu-close" aria-label="<?php esc_attr_e( 'Đóng menu', 'visaminhquan' ); ?>">
									<span></span>
									<span></span>
								</button>
							</div>
							<div class="vmq-mobile-menu-inner">
								<div class="vmq-mobile-menu-tabs" id="vmq-mobile-menu-tabs">
									<!-- JS will inject main menu tabs here -->
								</div>
								<div class="vmq-mobile-menu-content" id="vmq-mobile-menu-content">
									<!-- JS will inject submenu panels here -->
								</div>
							</div>
						</div>
					</div>

					<div class="header-right">
						<div class="header-search">
							<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
								<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Tìm kiếm...', 'placeholder', 'visaminhquan' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
								<button type="submit" class="search-submit">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M7.33333 12.6667C10.2789 12.6667 12.6667 10.2789 12.6667 7.33333C12.6667 4.38781 10.2789 2 7.33333 2C4.38781 2 2 4.38781 2 7.33333C2 10.2789 4.38781 12.6667 7.33333 12.6667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M14 14L11.1 11.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</button>
							</form>
						</div>

						<div class="language-selector">
							<a href="#" class="lang-link active" data-lang="vi" title="Tiếng Việt" id="lang-vi">
								<span class="flag-icon flag-vn">🇻🇳</span>
							</a>
							<a href="#" class="lang-link" data-lang="en" title="English" id="lang-en">
								<span class="flag-icon flag-uk">🇬🇧</span>
							</a>
						</div>
						
						<!-- Google Translate Element (Hidden) -->
						<div id="google_translate_element" style="display: none;"></div>
						
						<!-- Translation Loading Indicator -->
						<div class="translation-loading" id="translation-loading">
							<div class="translation-loading-spinner"></div>
							<span>Đang dịch...</span>
						</div>

						<div class="header-contact">
							<a href="<?php echo esc_url( home_url( '/lien-he' ) ); ?>" class="btn-contact">
								<?php esc_html_e( 'Liên hệ', 'visaminhquan' ); ?>
							</a>
						</div>
					</div><!-- .header-right -->
				</div><!-- .header-wrapper -->
			</div><!-- .container -->
		</div><!-- .header-top -->
	</header><!-- #masthead -->

	<main id="main" class="site-main">
		<div class="container">
			<div class="site-content">

