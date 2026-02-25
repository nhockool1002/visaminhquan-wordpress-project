<?php
/**
 * Template for category archives – hiển thị bài viết dạng card
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

get_header();

$category = get_queried_object();
?>

<div class="vmq-category-archive">
	<div class="vmq-category-archive-inner container">
		<header class="vmq-category-header">
			<h1 class="vmq-category-title"><?php single_cat_title(); ?></h1>
			<?php if ( category_description() ) : ?>
				<div class="vmq-category-description"><?php echo category_description(); ?></div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="vmq-category-cards">
				<?php
				while ( have_posts() ) :
					the_post();
					$permalink = get_permalink();
					$thumb_id  = get_post_thumbnail_id();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'vmq-category-card' ); ?>>
						<a href="<?php echo esc_url( $permalink ); ?>" class="vmq-category-card-link">
							<?php if ( $thumb_id ) : ?>
								<div class="vmq-category-card-image">
									<?php the_post_thumbnail( 'medium_large', array( 'class' => 'vmq-category-card-img' ) ); ?>
								</div>
							<?php else : ?>
								<div class="vmq-category-card-image vmq-category-card-no-image">
									<span class="vmq-category-card-placeholder"><?php echo esc_html( get_the_title() ? wp_trim_words( get_the_title(), 3 ) : __( 'Bài viết', 'visaminhquan' ) ); ?></span>
								</div>
							<?php endif; ?>
							<div class="vmq-category-card-body">
								<?php if ( 'post' === get_post_type() ) : ?>
									<div class="vmq-category-card-meta">
										<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
											<?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?>
										</time>
									</div>
								<?php endif; ?>
								<h2 class="vmq-category-card-title"><?php the_title(); ?></h2>
								<div class="vmq-category-card-excerpt">
									<?php echo wp_kses_post( has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 20 ) ); ?>
								</div>
								<span class="vmq-category-card-more"><?php esc_html_e( 'Xem thêm', 'visaminhquan' ); ?></span>
							</div>
						</a>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'   => 2,
					'prev_text'  => esc_html__( '&laquo; Trước', 'visaminhquan' ),
					'next_text'  => esc_html__( 'Sau &raquo;', 'visaminhquan' ),
					'class'      => 'vmq-category-pagination',
				)
			);
			?>

		<?php else : ?>
			<div class="vmq-category-empty">
				<p><?php esc_html_e( 'Chưa có bài viết nào trong danh mục này.', 'visaminhquan' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
