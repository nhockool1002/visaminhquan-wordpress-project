<?php
/**
 * The template for displaying all single posts
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

get_header();

/**
 * Một số bài viết (ví dụ các landing page dịch vụ) được thiết kế full bằng Elementor.
 * Những bài này sẽ bật meta "_vmq_use_elementor_layout" trong admin để:
 * - Hiển thị nguyên vẹn layout Elementor
 * - Bỏ qua layout single mặc định (header bài viết, TOC, author box, sidebar, v.v.)
 */
$vmq_use_elementor_layout = false;

if ( have_posts() ) {
	the_post();

	$current_id = get_the_ID();
	$vmq_use_elementor_layout = '1' === get_post_meta( $current_id, '_vmq_use_elementor_layout', true );

	// Tương thích ngược: mặc định bật cho bài VISA Mỹ (ID 671) nếu chưa cấu hình meta.
	if ( 671 === (int) $current_id && ! $vmq_use_elementor_layout ) {
		$vmq_use_elementor_layout = true;
	}

	rewind_posts();
}

if ( $vmq_use_elementor_layout ) {
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;

	get_footer();
	return;
}
?>

<div class="vmq-single-post-wrapper">
	<div class="vmq-single-post-container">
		<div class="vmq-single-post-layout">
			<main class="vmq-single-post-main">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'vmq-single-post-article' ); ?>>
						<header class="vmq-single-post-header">
							<?php if ( 'post' === get_post_type() ) : ?>
								<div class="vmq-single-post-meta">
									<?php
									$categories = get_the_category();
									if ( ! empty( $categories ) ) {
										$category = $categories[0];
										echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="vmq-single-post-category">' . esc_html( $category->name ) . '</a>';
									}
									?>
									<span class="vmq-single-post-date">
										<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
											<?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
										</time>
									</span>
								</div>
							<?php endif; ?>
							
							<?php the_title( '<h1 class="vmq-single-post-title">', '</h1>' ); ?>
							
							<?php if ( 'post' === get_post_type() ) : ?>
								<div class="vmq-single-post-author">
									<span class="vmq-single-post-author-label">Tác giả:</span>
									<span class="vmq-single-post-author-name">
										<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
											<?php echo esc_html( get_the_author() ); ?>
										</a>
									</span>
								</div>
							<?php endif; ?>
						</header><!-- .vmq-single-post-header -->

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="vmq-single-post-thumbnail">
								<?php the_post_thumbnail( 'large', array( 'class' => 'vmq-single-post-featured-image' ) ); ?>
							</div>
						<?php endif; ?>

						<?php // TOC + nội dung bài viết ?>
						<div class="vmq-single-post-content" id="vmq-post-content">
							<section class="vmq-inline-toc-widget" id="vmq-inline-toc-widget" hidden>
								<button type="button" class="vmq-inline-toc-toggle" id="vmq-inline-toc-toggle" aria-expanded="false" aria-controls="vmq-toc">
									<span class="vmq-inline-toc-toggle-label">Mục lục bài viết</span>
									<span class="vmq-inline-toc-toggle-icon" aria-hidden="true"></span>
								</button>
								<nav class="vmq-toc" id="vmq-toc"></nav>
							</section>

							<?php
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="vmq-single-post-pages">' . esc_html__( 'Pages:', 'visaminhquan' ),
									'after'  => '</div>',
									'link_before' => '<span class="vmq-single-post-page-link">',
									'link_after' => '</span>',
								)
							);
							?>
						</div><!-- .vmq-single-post-content -->

						<section class="vmq-post-author-share">
							<div class="vmq-post-author-share-inner">
								<div class="vmq-post-author-info">
									<div class="vmq-post-author-avatar">
										<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/ph-vi.png' ) ); ?>" alt="Ms. Vi - Chuyên gia visa" loading="lazy" />
									</div>
									<div class="vmq-post-author-text">
										<h3 class="vmq-post-author-name">Ms. Vi – Chuyên gia visa</h3>
										<p class="vmq-post-author-bio">
											Ms. Vi – Chuyên gia tư vấn và xử lý hồ sơ visa với hơn 10 năm kinh nghiệm trực tiếp thẩm định hồ sơ, đánh giá rủi ro và theo sát toàn bộ quá trình xử lý visa du lịch, công tác, thăm thân, du học tại nhiều thị trường. Nội dung được biên tập dựa trên kinh nghiệm thực tiễn và quy định lãnh sự cập nhật, đảm bảo tính chính xác, minh bạch và đáng tin cậy.
										</p>
									</div>
								</div>

								<div class="vmq-post-share">
									<span class="vmq-post-share-label">Chia sẻ bài viết:</span>
									<div class="vmq-post-share-icons">
										<a href="https://zalo.me/2705726786452285490" class="vmq-social-icon" aria-label="Zalo" target="_blank" rel="noopener noreferrer">
											<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/zalo.png' ) ); ?>" alt="Zalo" />
										</a>
										<a href="https://www.youtube.com/@VISAMINHQUÂN" class="vmq-social-icon" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
											<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/ytb.png' ) ); ?>" alt="YouTube" />
										</a>
										<a href="https://www.messenger.com/t/663709650160951" class="vmq-social-icon" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
											<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/fb.png' ) ); ?>" alt="Facebook" />
										</a>
										<a href="https://www.tiktok.com/@visa.minh.qun?_r=1&_t=ZS-93czkhKvPR2" class="vmq-social-icon" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
											<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/tiktok.png' ) ); ?>" alt="TikTok" />
										</a>
									</div>
								</div>
							</div>
						</section>

						<?php
						// Slider bài viết mới nhất dưới box tác giả.
						if ( function_exists( 'visaminhquan_render_latest_posts_slider_inline' ) ) {
							visaminhquan_render_latest_posts_slider_inline();
						}
						?>

						<footer class="vmq-single-post-footer">
							<?php
							$categories_list = get_the_category_list( esc_html__( ', ', 'visaminhquan' ) );
							if ( $categories_list ) {
								?>
								<div class="vmq-single-post-categories">
									<span class="vmq-single-post-footer-label">Chuyên mục:</span>
									<span class="vmq-single-post-categories-list"><?php echo $categories_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</div>
								<?php
							}

							$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'visaminhquan' ) );
							if ( $tags_list ) {
								?>
								<div class="vmq-single-post-tags">
									<span class="vmq-single-post-footer-label">Thẻ:</span>
									<span class="vmq-single-post-tags-list"><?php echo $tags_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</div>
								<?php
							}
							?>
						</footer><!-- .vmq-single-post-footer -->
					</article><!-- #post-<?php the_ID(); ?> -->

				<?php endwhile; ?>
			</main><!-- .vmq-single-post-main -->

			<aside class="vmq-single-post-sidebar">
				<div class="vmq-single-post-sidebar-widget vmq-destination-guide-widget">
					<?php echo do_shortcode( '[vmq_destination_guide]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</aside><!-- .vmq-single-post-sidebar -->
		</div><!-- .vmq-single-post-layout -->
	</div><!-- .vmq-single-post-container -->
</div><!-- .vmq-single-post-wrapper -->

<?php
get_footer();

