<?php
/**
 * Hero Form - Dịch vụ visa trọn gói
 * Shortcode: [vmq_hero_form]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Xử lý submit form
 */
function vmq_hero_form_process() {
	if ( isset( $_POST['vmq_hero_form_nonce'] ) && wp_verify_nonce( $_POST['vmq_hero_form_nonce'], 'vmq_hero_form' ) ) {
		
		$name  = isset($_POST['vmq_hero_name']) ? sanitize_text_field( $_POST['vmq_hero_name'] ) : '';
		$phone = isset($_POST['vmq_hero_phone']) ? sanitize_text_field( $_POST['vmq_hero_phone'] ) : '';
		$visas = isset( $_POST['vmq_visa'] ) ? (array) $_POST['vmq_visa'] : array();
		
		// 1. MAPPING DỊCH VỤ (Chuyển mã sang tên hiển thị)
		$map_visas = array(
			'my'       => 'Visa Mỹ',
			'uc'       => 'Visa Úc',
			'schengen' => 'Visa Schengen',
			'han'      => 'Visa Hàn',
			'trung'    => 'Visa Trung',
			'canada'   => 'Visa Canada'
		);

		$selected_visas = array();
		foreach ( $visas as $v ) {
			if ( isset( $map_visas[$v] ) ) {
				$selected_visas[] = $map_visas[$v];
			}
		}
		$visa_display = !empty( $selected_visas ) ? implode( ', ', $selected_visas ) : 'Không chọn';

		// 2. TEMPLATE HTML MỚI
		$current_time = date('H:i d/m/Y');
		$subject      = "Yêu cầu tư vấn mới từ: " . mb_strtoupper($name);
		
		$body_html = '
		<div style="background-color: #f4f7f6; padding: 20px; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.5;">
			<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
				<div style="background-color: #0056b3; padding: 15px; text-align: center;">
					<h2 style="color: #ffffff; margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 1px;">Yêu Cầu Tư Vấn Dịch Vụ</h2>
				</div>
				<div style="padding: 25px;">
					<p style="margin: 0 0 10px 0; font-size: 15px; font-weight: bold; color: #0056b3;">Thông tin từ VISAMINHQUAN.COM.VN</p>
					<p style="margin: 0 0 20px 0; font-size: 14px; color: #666;">Khách hàng vừa chọn các dịch vụ tư vấn sau:</p>
					<table style="width: 100%; border-collapse: collapse;">
						<tr>
							<td style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold; width: 40%;">👤 Họ và tên:</td>
							<td style="padding: 10px 0; border-bottom: 1px solid #eee;">'. mb_strtoupper($name) .'</td>
						</tr>
						<tr>
							<td style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold;">📞 Số điện thoại:</td>
							<td style="padding: 10px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #0056b3;">'. $phone .'</td>
						</tr>
						<tr>
							<td colspan="2" style="padding: 15px 0 5px 0; font-weight: bold;">🌍 Dịch vụ quan tâm:</td>
						</tr>
						<tr>
							<td colspan="2" style="padding: 12px; background-color: #fff9e6; border-left: 4px solid #f0ad4e; color: #856404; font-weight: bold;">'. $visa_display .'</td>
						</tr>
					</table>
					<div style="margin-top: 25px; text-align: center;">
						<a href="tel:'. $phone .'" style="background-color: #28a745; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 14px;">GỌI ĐIỆN HỖ TRỢ NGAY</a>
					</div>
				</div>
				<div style="background-color: #f9f9f9; padding: 12px; text-align: center; border-top: 1px solid #eee; font-size: 11px; color: #999;">
					<p style="margin: 0;">Email gửi tự động từ hệ thống Website vào lúc '. $current_time .'</p>
				</div>
			</div>
		</div>';

		// Gửi mail
		vmq_send_mailjet_api( $subject, $body_html );

		// Redirect để reset form
		wp_redirect( home_url( $_SERVER['REQUEST_URI'] ) );
		exit;
	}
}
add_action( 'template_redirect', 'vmq_hero_form_process' );

/**
 * Render hero form HTML (Giữ nguyên class của bạn)
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
	
	<script>
		// Nếu URL có tham số vmq_submit=success thì hiện modal
		document.addEventListener("DOMContentLoaded", function() {
			const urlParams = new URLSearchParams(window.location.search);
			if (urlParams.get('vmq_submit') === 'success') {
				if (typeof jQuery !== 'undefined') {
					jQuery('#vmq-modal-success').fadeIn();
				} else {
					document.getElementById('vmq-modal-success').style.display = 'flex';
				}
				// Xóa tham số trên URL cho sạch
				window.history.replaceState({}, document.title, window.location.pathname);
			}
		});
	</script>
	<?php
	return ob_get_clean();
}

function vmq_register_hero_form_shortcode() {
	add_shortcode( 'vmq_hero_form', 'vmq_hero_form_render' );
}
add_action( 'init', 'vmq_register_hero_form_shortcode' );