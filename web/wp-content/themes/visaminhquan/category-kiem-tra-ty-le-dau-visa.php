<?php
/**
 * Template for category "Kiểm tra tỷ lệ đậu visa" – chỉ hiển thị form, không hiển thị bài viết
 *
 * @package VISAMINHQUAN
 */

get_header();

$category = get_queried_object();
?>

<div class="vmq-category-archive vmq-category-check-visa-form">
	<div class="vmq-category-archive-inner container">
		<header class="vmq-category-header">
			<h1 class="vmq-category-title"><?php single_cat_title(); ?></h1>
			<?php if ( category_description() ) : ?>
				<div class="vmq-category-description"><?php echo category_description(); ?></div>
			<?php endif; ?>
		</header>

		<div class="vmq-category-check-visa-form-content">
			<?php echo do_shortcode( '[nhut_check_visa_pass_rate]' ); ?>
		</div>
	</div>
</div>

<?php
get_footer();
