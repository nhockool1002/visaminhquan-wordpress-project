<?php 
/**
 * The template for displaying the footer
 */
$footer_opts = get_option('vmq_footer_data'); 

if ( ! function_exists( 'get_vmq_val' ) ) {
    function get_vmq_val($key, $default, $opts) {
        return (!empty($opts[$key])) ? $opts[$key] : $default;
    }
}
?>

            </div></div></main>

    <?php if ( function_exists( 'visaminhquan_render_related_news_slider' ) ) : ?>
        <?php visaminhquan_render_related_news_slider(); ?>
    <?php endif; ?>

    <footer class="vmq-footer">
        <div class="vmq-footer-bg"></div>
        <div class="vmq-footer-wrap">
            <div class="vmq-footer-content">
                <div class="vmq-footer-col vmq-footer-col-main">
                    <div class="vmq-footer-logo">
                        <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/logofb2.png' ) ); ?>" alt="Visa Minh Quân Logo" />
                    </div>
                    <h2 class="vmq-footer-company-name"><?php echo esc_html(get_vmq_val('company_name', 'CÔNG TY TNHH VISA MINH QUÂN', $footer_opts)); ?></h2>

                    <div class="vmq-footer-social vmq-footer-social-column">
                        <div class="vmq-footer-social-icons">
                            <a href="<?php echo esc_url(get_vmq_val('social_yt', '#', $footer_opts)); ?>" class="vmq-social-icon" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/ytfb2.png' ) ); ?>" alt="YouTube" />
                            </a>
                            <a href="<?php echo esc_url(get_vmq_val('social_fb', '#', $footer_opts)); ?>" class="vmq-social-icon" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/fbfb2.png' ) ); ?>" alt="Facebook" />
                            </a>
                            <a href="<?php echo esc_url(get_vmq_val('social_tt', '#', $footer_opts)); ?>" class="vmq-social-icon" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/ttfb2.png' ) ); ?>" alt="TikTok" />
                            </a>
                            <a href="<?php echo esc_url(get_vmq_val('social_zl', '#', $footer_opts)); ?>" class="vmq-social-icon" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/02/zlfb2.png' ) ); ?>" alt="Zalo" />
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="vmq-footer-col">
                    <h3 class="vmq-footer-col-title">Liên hệ</h3>
                    <div class="vmq-footer-info">
                        <p>MST: <?php echo esc_html(get_vmq_val('mst', '0318999859', $footer_opts)); ?></p>
                        <p>Địa chỉ: <?php echo esc_html(get_vmq_val('address', '64 Võ Thị Sáu, Tân Định, Hồ Chí Minh', $footer_opts)); ?></p>
                        <p>Hotline: <a href="tel:<?php echo esc_attr(get_vmq_val('hotline_tel', '0924727789', $footer_opts)); ?>"><?php echo esc_html(get_vmq_val('hotline', '0924.727.789', $footer_opts)); ?></a></p>
                        <p>Tổng đài: <a href="tel:<?php echo esc_attr(get_vmq_val('tong_dai_tel', '0928472789', $footer_opts)); ?>"><?php echo esc_html(get_vmq_val('tong_dai', '0928.472.789', $footer_opts)); ?></a></p>
                        <p>Email: <a href="mailto:<?php echo esc_attr(get_vmq_val('email', 'info@visaminhquan.com', $footer_opts)); ?>"><?php echo esc_html(get_vmq_val('email', 'info@visaminhquan.com', $footer_opts)); ?></a></p>
                    </div>
                </div>
                
                <div class="vmq-footer-col vmq-footer-map-col">
                    <h3 class="vmq-footer-col-title">Chính sách</h3>
                    <div class="vmq-footer-links">
                        <a href="<?php echo esc_url(get_vmq_val('link_faq', home_url('/cau-hoi-thuong-gap'), $footer_opts)); ?>" class="vmq-footer-link">Câu hỏi thường gặp</a>
                        <a href="<?php echo esc_url(get_vmq_val('link_policy', home_url('/chinh-sach-bao-mat'), $footer_opts)); ?>" class="vmq-footer-link">Chính sách bảo mật</a>
                        <a href="<?php echo esc_url(get_vmq_val('link_tos', home_url('/dieu-khoan-dich-vu'), $footer_opts)); ?>" class="vmq-footer-link">Điều khoản dịch vụ</a>
                    </div>
                    <br />
                    <div class="vmq-footer-map-wrapper">
                        <?php echo get_vmq_val('maps_iframe', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.2859615378857!2d106.6932605!3d10.789396399999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x65eeb2b9f23d5489%3A0x56507a758c65df02!2sC%C3%94NG%20TY%20TNHH%20VISA%20MINH%20QU%C3%82N!5e0!3m2!1sen!2s!4v1772706226892!5m2!1sen!2s" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', $footer_opts); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="vmq-footer-bottom">
            <div class="vmq-footer-bottom-wrap">
                <div class="vmq-footer-copyright">
                    <p>Copyright © <?php echo date( 'Y' ); ?> <?php echo esc_html(get_vmq_val('company_name', 'Visa Minh Quân', $footer_opts)); ?></p>
                </div>
                <button class="vmq-scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12L12 5L19 12"/></svg>
                </button>
            </div>
        </div>
    </footer>

    <?php 
    $zalo_enable = get_vmq_val('zalo_enable', '', $footer_opts);
    if ( strtolower($zalo_enable) === 'on' ) : 
        $oa_id = get_vmq_val('zalo_oa_id', '2705726786452285490', $footer_opts);
        $welcome = get_vmq_val('zalo_welcome', 'Xin chào! Chúng tôi có thể giúp gì cho bạn?', $footer_opts);
    ?>
        <div class="zalo-chat-widget" data-oaid="<?php echo esc_attr($oa_id); ?>" data-welcome-message="<?php echo esc_attr($welcome); ?>" data-autopopup="7" data-width="300" data-height="350"></div>
        <script src="https://sp.zalo.me/plugins/sdk.js"></script>
    <?php endif; ?>

    <nav class="vmq-sticky-footer">
        <div class="vmq-sticky-footer-inner">
            <a href="<?php echo esc_url(get_vmq_val('sticky_1_link', 'tel:0924727789', $footer_opts)); ?>" class="vmq-sticky-item">
                <?php echo get_vmq_val('sticky_1_svg', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>', $footer_opts); ?>
                <span><?php echo esc_html(get_vmq_val('sticky_1_label', 'Trao đổi chuyên viên', $footer_opts)); ?></span>
            </a>
            <span class="vmq-sticky-sep"></span>
            <a href="<?php echo esc_url(get_vmq_val('sticky_2_link', '#', $footer_opts)); ?>" class="vmq-sticky-item">
                <?php echo get_vmq_val('sticky_2_svg', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>', $footer_opts); ?>
                <span><?php echo esc_html(get_vmq_val('sticky_2_label', 'Đánh giá hồ sơ', $footer_opts)); ?></span>
            </a>
        </div>
    </nav>

    <?php wp_footer(); ?>
</body>
</html>
