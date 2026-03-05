<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

get_header();
?>

<div class="content-area">
	<section class="error-404 not-found">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'visaminhquan' ); ?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'visaminhquan' ); ?></p>

			<?php get_search_form(); ?>

			<?php
			the_widget( 'WP_Widget_Recent_Posts' );
			?>

			<div class="widget widget_categories">
				<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'visaminhquan' ); ?></h2>
				<ul>
					<?php
					wp_list_categories(
						array(
							'orderby'    => 'count',
							'order'      => 'DESC',
							'show_count' => 1,
							'title_li'   => '',
							'number'     => 10,
						)
					);
					?>
				</ul>
			</div><!-- .widget -->

			<?php
			the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
			the_widget( 'WP_Widget_Tag_Cloud' );
			?>
		</div><!-- .entry-content -->
	</section><!-- .error-404 -->
</div><!-- .content-area -->

<?php
get_footer();

