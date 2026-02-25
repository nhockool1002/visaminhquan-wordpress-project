<?php
/**
 * The template for displaying search results pages
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

get_header();
?>

<div class="content-area">
	<?php if ( have_posts() ) : ?>
		<header class="page-header">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Search Results for: %s', 'visaminhquan' ),
					'<span>' . get_search_query() . '</span>'
				);
				?>
			</h1>
		</header><!-- .page-header -->

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'search' );
		endwhile;

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
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'visaminhquan' ); ?></p>
			<?php get_search_form(); ?>
		</div>
		<?php
	endif;
	?>
</div><!-- .content-area -->

<?php
get_footer();

