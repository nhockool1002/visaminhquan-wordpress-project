<?php
/**
 * The template for displaying the footer
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */
?>

			</div><!-- .site-content -->
		</div><!-- .container -->
	</main><!-- #main -->

	<?php if ( function_exists( 'visaminhquan_render_related_news_slider' ) ) : ?>
		<?php visaminhquan_render_related_news_slider(); ?>
	<?php endif; ?>

	<footer class="vmq-footer">
		<!-- Background Image -->
		<div class="vmq-footer-bg"></div>
		
		<div class="vmq-footer-wrap">
			<!-- Content: 3 Columns (theo thiết kế) -->
			<div class="vmq-footer-content">
				<!-- Column 1: Logo + Company + Social -->
				<div class="vmq-footer-col vmq-footer-col-main">
					<div class="vmq-footer-logo">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/logofb2.png' ) ); ?>" alt="Visa Minh Quân Logo" />
					</div>
					<h2 class="vmq-footer-company-name">CÔNG TY TNHH VISA MINH QUÂN</h2>

					<div class="vmq-footer-social vmq-footer-social-column">
						<div class="vmq-footer-social-icons">
							<a href="https://www.youtube.com/@VISAMINHQUÂN" class="vmq-social-icon" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/ytfb2.png' ) ); ?>" alt="YouTube" />
							</a>
							<a href="https://www.facebook.com/visaminhquan" class="vmq-social-icon" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/fbfb2.png' ) ); ?>" alt="Facebook" />
							</a>
							<a href="https://www.tiktok.com/@visa.minh.qun?_r=1&_t=ZS-93czkhKvPR2" class="vmq-social-icon" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/ttfb2.png' ) ); ?>" alt="TikTok" />
							</a>
							<a href="https://zalo.me/2705726786452285490" class="vmq-social-icon" aria-label="Zalo" target="_blank" rel="noopener noreferrer">
								<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/zlfb2.png' ) ); ?>" alt="Zalo" />
							</a>
						</div>
					</div>
				</div>
				
				<!-- Column 2: Liên hệ -->
				<div class="vmq-footer-col">
					<h3 class="vmq-footer-col-title">Liên hệ</h3>
					<div class="vmq-footer-info">
						<p>MST: 0318999859</p>
						<p>Địa chỉ: Tòa nhà VietPhone Building, Phòng RA9, 64 Võ Thị Sáu, Tân Định, Hồ Chí Minh, Việt Nam</p>
						<p>Hotline: <a href="tel:0924727789">0924.727.789</a></p>
						<p>Tổng đài: <a href="tel:0928472789">0928.472.789</a></p>
						<p>Email: <a href="mailto:info.visaminhquan@gmail.com">info.visaminhquan@gmail.com</a></p>
						<p>Website: <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( preg_replace( '#^https?://#', '', home_url() ) ); ?></a></p>
					</div>
				</div>
				
				<!-- Column 3: Chính sách + Google Maps -->
				<div class="vmq-footer-col vmq-footer-map-col">
					<h3 class="vmq-footer-col-title">Chính sách</h3>
					<div class="vmq-footer-links">
						<a href="<?php echo esc_url( home_url( '/cau-hoi-thuong-gap' ) ); ?>" class="vmq-footer-link">Câu hỏi thường gặp</a>
						<a href="<?php echo esc_url( home_url( '/chinh-sach-bao-mat' ) ); ?>" class="vmq-footer-link">Chính sách bảo mật</a>
					</div>
					<br />
					<div class="vmq-footer-map-wrapper">
						<iframe 
							src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.1234567890!2d106.6912!3d10.7890!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3a8b8b8b8b%3A0x8b8b8b8b8b8b8b8b!2zVMOyYSBuaMOgIFZpZXRQaG9uZSBCdWlsZGluZywgUGjhu5FuZyBSQTksIDY0IFbDtSBUaOG7iSBTw6B1LCBUw6JuIMSQ4bqnaSwgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1234567890123!5m2!1svi!2s" 
							width="100%" 
							height="100%" 
							style="border:0;" 
							allowfullscreen="" 
							loading="lazy" 
							referrerpolicy="no-referrer-when-downgrade"
							title="Vị trí Công ty TNHH Visa Minh Quân">
						</iframe>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Bottom Footer: Copyright và Social Media -->
		<div class="vmq-footer-bottom">
			<div class="vmq-footer-bottom-wrap">
				<div class="vmq-footer-copyright">
					<p>Copyright © <?php echo date( 'Y' ); ?> Visa Minh Quân</p>
				</div>
				<button class="vmq-scroll-top" aria-label="Scroll to top" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 19V5M5 12L12 5L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</button>
			</div>
		</div>
	</footer>

	<!-- Modal Success & Error for Contact Form 7 -->
	<!-- Modal Success -->
	<div id="vmq-modal-success" class="vmq-modal" style="display: none;">
		<div class="vmq-modal-overlay"></div>
		<div class="vmq-modal-content vmq-modal-success">
			<button class="vmq-modal-close" aria-label="Close">&times;</button>
			<div class="vmq-modal-logo">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/cropped-LOGOVISAMINHQUAN.png' ) ); ?>" alt="Visa Minh Quân Logo" />
			</div>
			<h2 class="vmq-modal-company">VISA MINH QUÂN</h2>
			<div class="vmq-modal-message vmq-modal-success-message">
				<p>YÊU CẦU CỦA BẠN ĐÃ GỬI THÀNH CÔNG</p>
			</div>
			<div class="vmq-modal-cooldown">
				<div class="vmq-cooldown-bar">
					<div class="vmq-cooldown-progress"></div>
				</div>
				<span class="vmq-cooldown-text">Tự động đóng sau <span class="vmq-cooldown-time">5</span> giây</span>
			</div>
		</div>
	</div>

	<!-- Modal Error -->
	<div id="vmq-modal-error" class="vmq-modal" style="display: none;">
		<div class="vmq-modal-overlay"></div>
		<div class="vmq-modal-content vmq-modal-error">
			<button class="vmq-modal-close" aria-label="Close">&times;</button>
			<div class="vmq-modal-logo">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/01/cropped-LOGOVISAMINHQUAN.png' ) ); ?>" alt="Visa Minh Quân Logo" />
			</div>
			<h2 class="vmq-modal-company">VISA MINH QUÂN</h2>
			<div class="vmq-modal-message vmq-modal-error-message">
				<p>YÊU CẦU CỦA BẠN</p>
				<p>ĐÃ GỬI THẤT BẠI</p>
			</div>
			<div class="vmq-modal-cooldown">
				<div class="vmq-cooldown-bar">
					<div class="vmq-cooldown-progress"></div>
				</div>
				<span class="vmq-cooldown-text">Tự động đóng sau <span class="vmq-cooldown-time">5</span> giây</span>
			</div>
		</div>
	</div>

	<!-- Form kiểm tra tỷ lệ đậu visa (ẩn, sẽ được di chuyển vào modal khi cần) -->
	<div style="display: none; position: absolute; left: -9999px; visibility: hidden;">
		<?php echo do_shortcode('[nhut_check_visa_pass_rate]'); ?>
	</div>

	<!-- Sticky footer bar -->
	<nav class="vmq-sticky-footer" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'visaminhquan' ); ?>">
		<div class="vmq-sticky-footer-inner">
			<a href="tel:0924727789" class="vmq-sticky-item">
				<svg class="vmq-sticky-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<span>0924 727 789</span>
			</a>
			<span class="vmq-sticky-sep" aria-hidden="true"></span>
			<a href="tel:0928472789" class="vmq-sticky-item">
				<svg class="vmq-sticky-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<span>0928.472.789</span>
			</a>
			<span class="vmq-sticky-sep" aria-hidden="true"></span>
			<a href="#" class="vmq-sticky-item mq-check-rate-btn" id="vmq-sticky-btn-visa">
				<svg class="vmq-sticky-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<polyline points="10 9 9 9 8 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<span>Kiểm tra tỷ lệ đậu visa</span>
			</a>
			<span class="vmq-sticky-sep" aria-hidden="true"></span>
			<a href="https://zalo.me/2705726786452285490" class="vmq-sticky-item" target="_blank" rel="noopener noreferrer">
				<svg class="vmq-sticky-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<line x1="12" y1="6" x2="12" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<line x1="9" y1="8" x2="15" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
				<span>Tư vấn miễn phí 1:1</span>
			</a>
		</div>
	</nav>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

