<?php
/**
 * The main template file
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

get_header();
?>

<div class="content-area">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
				<header class="entry-header">
					<?php
					if ( is_singular() ) {
						the_title( '<h1 class="entry-title">', '</h1>' );
					} else {
						the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					}
					?>

					<?php if ( 'post' === get_post_type() ) : ?>
						<div class="entry-meta">
							<span class="posted-on">
								<?php
								printf(
									/* translators: %s: post date. */
									esc_html_x( 'Posted on %s', 'post date', 'visaminhquan' ),
									'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . esc_html( get_the_date() ) . '</a>'
								);
								?>
							</span>
							<span class="byline">
								<?php
								printf(
									/* translators: %s: post author. */
									esc_html_x( 'by %s', 'post author', 'visaminhquan' ),
									'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
								);
								?>
							</span>
						</div><!-- .entry-meta -->
					<?php endif; ?>
				</header><!-- .entry-header -->

				<?php if ( has_post_thumbnail() && ! is_singular() ) : ?>
					<div class="post-thumbnail">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'large' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<div class="entry-content">
					<?php
					if ( is_singular() ) {
						the_content();
					} else {
						the_excerpt();
					}

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'visaminhquan' ),
							'after'  => '</div>',
						)
					);
					?>
				</div><!-- .entry-content -->

				<?php if ( ! is_singular() ) : ?>
					<footer class="entry-footer">
						<a href="<?php the_permalink(); ?>" class="read-more">
							<?php esc_html_e( 'Read More', 'visaminhquan' ); ?>
						</a>
					</footer><!-- .entry-footer -->
				<?php endif; ?>
			</article><!-- #post-<?php the_ID(); ?> -->
			<?php
		endwhile;

		// Pagination
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => esc_html__( '&laquo; Previous', 'visaminhquan' ),
				'next_text' => esc_html__( 'Next &raquo;', 'visaminhquan' ),
			)
		);

	else :
		?>
		<div class="no-posts">
			<h2><?php esc_html_e( 'Nothing Found', 'visaminhquan' ); ?></h2>
			<p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'visaminhquan' ); ?></p>
			<?php get_search_form(); ?>
		</div>
		<?php
	endif;
	?>
</div><!-- .content-area -->

<?php
get_footer();

