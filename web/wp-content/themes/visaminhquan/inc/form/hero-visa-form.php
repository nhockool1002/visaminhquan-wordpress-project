<?php
/**
 * Hero Form - Dịch vụ visa trọn gói
 * Shortcode: [vmq_hero_form]
 *
 * @package VISAMINHQUAN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render hero form HTML
 */
function vmq_hero_form_render() {
	ob_start();
	?>
	<form class="vmq-hero-form-custom" method="post" action="">
		<?php wp_nonce_field( 'vmq_hero_form', 'vmq_hero_form_nonce' ); ?>
		<p class="vmq-form-group">
			<label for="vmq_hero_name">Họ tên</label>
			<input type="text" id="vmq_hero_name" name="vmq_hero_name" placeholder="" required />
		</p>
		<p class="vmq-form-group">
			<label for="vmq_hero_phone">Số điện thoại</label>
			<input type="tel" id="vmq_hero_phone" name="vmq_hero_phone" placeholder="" required />
		</p>
		<p class="vmq-form-group vmq-checkbox-group">
			<label class="vmq-checkbox-label">Dịch vụ cần tư vấn</label>
			<div class="vmq-checkboxes">
				<label><input type="checkbox" name="vmq_visa[]" value="my" /> Visa Mỹ</label>
				<label><input type="checkbox" name="vmq_visa[]" value="uc" /> Visa Úc</label>
				<label><input type="checkbox" name="vmq_visa[]" value="schengen" /> Visa Schengen</label>
				<label><input type="checkbox" name="vmq_visa[]" value="han" /> Visa Hàn</label>
				<label><input type="checkbox" name="vmq_visa[]" value="trung" /> Visa Trung</label>
				<label><input type="checkbox" name="vmq_visa[]" value="canada" /> Visa Canada</label>
			</div>
		</p>
		<p class="vmq-form-group">
			<button type="submit" class="vmq-submit">Nhận tư vấn miễn phí</button>
		</p>
		<p class="vmq-form-note">Thông tin được bảo mật tuyệt đối – Không phát sinh chi phí</p>
	</form>
	<?php
	return ob_get_clean();
}

/**
 * Register shortcode
 */
function vmq_register_hero_form_shortcode() {
	add_shortcode( 'vmq_hero_form', 'vmq_hero_form_render' );
}
add_action( 'init', 'vmq_register_hero_form_shortcode' );
