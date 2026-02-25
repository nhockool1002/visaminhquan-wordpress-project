<?php
/**
 * Schema Markup Functions
 * 
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get Organization Schema
 */
function visaminhquan_get_organization_schema() {
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Organization',
		'@id' => home_url() . '#organization',
		'name' => 'CÔNG TY TNHH VISA MINH QUÂN',
		'legalName' => 'CÔNG TY TNHH VISA MINH QUÂN',
		'url' => home_url(),
		'logo' => 'https://visaminhquan.ddev.site/wp-content/uploads/2026/01/logo-ft.png',
		'image' => 'https://visaminhquan.ddev.site/wp-content/uploads/2026/01/logo-ft.png',
		'description' => 'Dịch vụ tư vấn và làm visa chuyên nghiệp, uy tín tại Việt Nam',
		'address' => array(
			'@type' => 'PostalAddress',
			'streetAddress' => 'Tòa nhà VietPhone Building, Phòng RA9, 64 Võ Thị Sáu, Tân Định',
			'addressLocality' => 'Hồ Chí Minh',
			'addressCountry' => 'VN',
			'addressRegion' => 'Hồ Chí Minh',
		),
		'contactPoint' => array(
			array(
				'@type' => 'ContactPoint',
				'telephone' => '+84-924-727-789',
				'contactType' => 'customer service',
				'areaServed' => 'VN',
				'availableLanguage' => array( 'Vietnamese', 'English' ),
			),
			array(
				'@type' => 'ContactPoint',
				'telephone' => '+84-928-472-789',
				'contactType' => 'customer service',
				'areaServed' => 'VN',
				'availableLanguage' => array( 'Vietnamese', 'English' ),
			),
		),
		'email' => 'info.visaminhquan@gmail.com',
		'sameAs' => array(
			'https://zalo.me/2705726786452285490',
			'https://www.youtube.com/@VISAMINHQUÂN',
			'https://www.messenger.com/t/663709650160951',
			'https://www.tiktok.com/@visa.minh.qun',
		),
		'vatID' => '0318999859',
	);

	return $schema;
}

/**
 * Get LocalBusiness Schema
 */
function visaminhquan_get_local_business_schema() {
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => array( 'LocalBusiness', 'Organization' ),
		'@id' => home_url() . '#business',
		'name' => 'CÔNG TY TNHH VISA MINH QUÂN',
		'image' => 'https://visaminhquan.ddev.site/wp-content/uploads/2026/01/logo-ft.png',
		'logo' => 'https://visaminhquan.ddev.site/wp-content/uploads/2026/01/logo-ft.png',
		'url' => home_url(),
		'description' => 'Dịch vụ tư vấn và làm visa chuyên nghiệp, uy tín tại Việt Nam',
		'address' => array(
			'@type' => 'PostalAddress',
			'streetAddress' => 'Tòa nhà VietPhone Building, Phòng RA9, 64 Võ Thị Sáu, Tân Định',
			'addressLocality' => 'Hồ Chí Minh',
			'addressRegion' => 'Hồ Chí Minh',
			'postalCode' => '',
			'addressCountry' => 'VN',
		),
		'geo' => array(
			'@type' => 'GeoCoordinates',
			'latitude' => '10.7890',
			'longitude' => '106.6912',
		),
		'telephone' => '+84-924-727-789',
		'email' => 'info.visaminhquan@gmail.com',
		'priceRange' => '$$',
		'openingHoursSpecification' => array(
			array(
				'@type' => 'OpeningHoursSpecification',
				'dayOfWeek' => array(
					'Monday',
					'Tuesday',
					'Wednesday',
					'Thursday',
					'Friday',
					'Saturday',
					'Sunday',
				),
				'opens' => '08:00',
				'closes' => '21:00',
			),
		),
		'sameAs' => array(
			'https://zalo.me/2705726786452285490',
			'https://www.youtube.com/@VISAMINHQUÂN',
			'https://www.messenger.com/t/663709650160951',
			'https://www.tiktok.com/@visa.minh.qun',
		),
	);

	return $schema;
}

/**
 * Get WebSite Schema
 */
function visaminhquan_get_website_schema() {
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'WebSite',
		'@id' => home_url() . '#website',
		'url' => home_url(),
		'name' => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'publisher' => array(
			'@id' => home_url() . '#organization',
		),
		'potentialAction' => array(
			array(
				'@type' => 'SearchAction',
				'target' => array(
					'@type' => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		),
	);

	return $schema;
}

/**
 * Get Article Schema for single posts
 */
function visaminhquan_get_article_schema() {
	if ( ! is_single() ) {
		return null;
	}

	global $post;
	
	$author = get_the_author_meta( 'display_name' );
	$published_date = get_the_date( 'c' );
	$modified_date = get_the_modified_date( 'c' );
	$image = has_post_thumbnail() ? get_the_post_thumbnail_url( $post->ID, 'full' ) : '';
	
	$categories = get_the_category();
	$article_section = ! empty( $categories ) ? $categories[0]->name : '';

	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Article',
		'headline' => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ?: wp_trim_words( get_the_content(), 30 ) ),
		'image' => $image ? array( $image ) : array(),
		'datePublished' => $published_date,
		'dateModified' => $modified_date,
		'author' => array(
			'@type' => 'Person',
			'name' => $author,
		),
		'publisher' => array(
			'@id' => home_url() . '#organization',
		),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id' => get_permalink(),
		),
	);

	if ( $article_section ) {
		$schema['articleSection'] = $article_section;
	}

	return $schema;
}

/**
 * Get WebPage Schema for pages
 */
function visaminhquan_get_webpage_schema() {
	if ( ! is_page() && ! is_single() ) {
		return null;
	}

	global $post;
	
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'WebPage',
		'@id' => get_permalink() . '#webpage',
		'url' => get_permalink(),
		'name' => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ?: wp_trim_words( get_the_content(), 30 ) ),
		'inLanguage' => 'vi-VN',
		'isPartOf' => array(
			'@id' => home_url() . '#website',
		),
		'about' => array(
			'@id' => home_url() . '#organization',
		),
		'datePublished' => get_the_date( 'c' ),
		'dateModified' => get_the_modified_date( 'c' ),
	);

	if ( has_post_thumbnail() ) {
		$schema['primaryImageOfPage'] = array(
			'@type' => 'ImageObject',
			'url' => get_the_post_thumbnail_url( $post->ID, 'full' ),
		);
		$schema['image'] = get_the_post_thumbnail_url( $post->ID, 'full' );
	}

	return $schema;
}

/**
 * Get BreadcrumbList Schema
 */
function visaminhquan_get_breadcrumb_schema() {
	$breadcrumbs = array();
	$position = 1;

	// Home
	$breadcrumbs[] = array(
		'@type' => 'ListItem',
		'position' => $position++,
		'name' => 'Trang chủ',
		'item' => home_url(),
	);

	// Current page/post
	if ( is_single() || is_page() ) {
		global $post;
		$breadcrumbs[] = array(
			'@type' => 'ListItem',
			'position' => $position++,
			'name' => get_the_title(),
			'item' => get_permalink(),
		);
	} elseif ( is_category() ) {
		$category = get_queried_object();
		$breadcrumbs[] = array(
			'@type' => 'ListItem',
			'position' => $position++,
			'name' => $category->name,
			'item' => get_category_link( $category->term_id ),
		);
	} elseif ( is_search() ) {
		$breadcrumbs[] = array(
			'@type' => 'ListItem',
			'position' => $position++,
			'name' => 'Tìm kiếm: ' . get_search_query(),
			'item' => get_search_link(),
		);
	}

	if ( count( $breadcrumbs ) < 2 ) {
		return null;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => $breadcrumbs,
	);

	return $schema;
}

/**
 * Get Service Schema for visa services
 */
function visaminhquan_get_service_schema( $service_name = '', $service_description = '' ) {
	if ( empty( $service_name ) ) {
		$service_name = get_the_title();
	}
	if ( empty( $service_description ) ) {
		$service_description = wp_strip_all_tags( get_the_excerpt() ?: wp_trim_words( get_the_content(), 30 ) );
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => 'Service',
		'serviceType' => $service_name,
		'description' => $service_description,
		'provider' => array(
			'@id' => home_url() . '#organization',
		),
		'areaServed' => array(
			'@type' => 'Country',
			'name' => 'Vietnam',
		),
		'availableChannel' => array(
			'@type' => 'ServiceChannel',
			'serviceUrl' => get_permalink(),
			'servicePhone' => '+84-924-727-789',
		),
	);

	return $schema;
}

/**
 * Output Schema Markup in JSON-LD format
 */
function visaminhquan_output_schema_markup() {
	$schemas = array();

	// Organization Schema (always)
	$schemas[] = visaminhquan_get_organization_schema();

	// LocalBusiness Schema (always)
	$schemas[] = visaminhquan_get_local_business_schema();

	// WebSite Schema (always)
	$schemas[] = visaminhquan_get_website_schema();

	// WebPage Schema (for pages and posts)
	$webpage_schema = visaminhquan_get_webpage_schema();
	if ( $webpage_schema ) {
		$schemas[] = $webpage_schema;
	}

	// Article Schema (for single posts)
	if ( is_single() ) {
		$article_schema = visaminhquan_get_article_schema();
		if ( $article_schema ) {
			$schemas[] = $article_schema;
		}
	}

	// BreadcrumbList Schema
	$breadcrumb_schema = visaminhquan_get_breadcrumb_schema();
	if ( $breadcrumb_schema ) {
		$schemas[] = $breadcrumb_schema;
	}

	// Output all schemas
	foreach ( $schemas as $schema ) {
		if ( ! empty( $schema ) ) {
			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo "\n" . '</script>' . "\n";
		}
	}
}
add_action( 'wp_head', 'visaminhquan_output_schema_markup', 5 );

