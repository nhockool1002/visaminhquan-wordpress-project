/**
 * Custom JavaScript for Testimonials Slider
 * Visa Minh Quân - Testimonials Section
 */

(function() {
    'use strict';

    // Khởi tạo slider, FAQ accordion, related news khi DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initTestimonialsSlider();
        initFaqAccordion();
        initRelatedNewsSlider();
    });

    /**
     * FAQ Accordion - Click mở/đóng câu trả lời, chặn scroll to top (href="#")
     * Dùng delegation trên document để hoạt động cả khi FAQ được load sau (Elementor, AJAX).
     */
    function initFaqAccordion() {
        document.addEventListener('click', function(e) {
            var item = e.target.closest('.mq-faq-item');
            if (!item) return;

            var grid = item.closest('.mq-faq-grid');
            if (!grid) return;

            e.preventDefault();
            e.stopPropagation();

            var cell = item.closest('.mq-faq-cell');
            var container = cell || item;
            var isOpen = container.classList.contains('mq-faq-open');

            grid.querySelectorAll('.mq-faq-cell').forEach(function(c) { c.classList.remove('mq-faq-open'); });
            grid.querySelectorAll('.mq-faq-item').forEach(function(el) {
                if (!el.closest('.mq-faq-cell')) el.classList.remove('mq-faq-open');
            });

            if (!isOpen) container.classList.add('mq-faq-open');
        });
    }

    function initTestimonialsSlider() {
        const sliderContainer = document.querySelector('.vmq-testimonials-slider');
        if (!sliderContainer) return;

        const slides = sliderContainer.querySelectorAll('.vmq-slide');
        const thumbnails = sliderContainer.querySelectorAll('.vmq-thumbnail');
        const prevBtn = sliderContainer.querySelector('.vmq-arrow-prev');
        const nextBtn = sliderContainer.querySelector('.vmq-arrow-next');

        if (slides.length === 0) return;

        let currentSlide = 0;
        const totalSlides = slides.length;

        // Hàm hiển thị slide
        function showSlide(index) {
            // Đảm bảo index trong phạm vi hợp lệ
            if (index < 0) {
                currentSlide = totalSlides - 1;
            } else if (index >= totalSlides) {
                currentSlide = 0;
            } else {
                currentSlide = index;
            }

            // Ẩn tất cả slides
            slides.forEach((slide, idx) => {
                if (idx === currentSlide) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });

            // Cập nhật thumbnails
            thumbnails.forEach((thumb, idx) => {
                if (idx === currentSlide) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
        }

        // Xử lý nút Previous
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showSlide(currentSlide - 1);
            });
        }

        // Xử lý nút Next
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showSlide(currentSlide + 1);
            });
        }

        // Xử lý click vào thumbnail
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', function(e) {
                e.preventDefault();
                showSlide(index);
            });
        });

        // Auto-play (tùy chọn - có thể bật/tắt)
        let autoPlayInterval = null;
        const autoPlayDelay = 5000; // 5 giây

        function startAutoPlay() {
            autoPlayInterval = setInterval(function() {
                showSlide(currentSlide + 1);
            }, autoPlayDelay);
        }

        function stopAutoPlay() {
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval);
                autoPlayInterval = null;
            }
        }

        // Bắt đầu auto-play
        startAutoPlay();

        // Dừng auto-play khi hover vào slider
        sliderContainer.addEventListener('mouseenter', stopAutoPlay);
        sliderContainer.addEventListener('mouseleave', startAutoPlay);

        // Dừng auto-play khi focus vào nút điều hướng (accessibility)
        if (prevBtn) {
            prevBtn.addEventListener('focus', stopAutoPlay);
            prevBtn.addEventListener('blur', startAutoPlay);
        }
        if (nextBtn) {
            nextBtn.addEventListener('focus', stopAutoPlay);
            nextBtn.addEventListener('blur', startAutoPlay);
        }

        // Hỗ trợ keyboard navigation
        sliderContainer.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                showSlide(currentSlide - 1);
                stopAutoPlay();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                showSlide(currentSlide + 1);
                stopAutoPlay();
            }
        });

        // Khởi tạo slide đầu tiên
        showSlide(0);
    }

    /**
     * Slider tin tức liên quan phía trên footer
     * Dùng flex + scroll thay vì thư viện ngoài.
     */
    function initRelatedNewsSlider() {
        const sliders = document.querySelectorAll('.vmq-related-slider');
        if (!sliders.length) return;

        sliders.forEach(function(slider) {
            const track = slider.querySelector('.vmq-related-track');
            if (!track) return;

            const prevBtn = slider.querySelector('.vmq-related-prev');
            const nextBtn = slider.querySelector('.vmq-related-next');

            const getScrollAmount = () => track.clientWidth * 0.9;

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    track.scrollBy({
                        left: -getScrollAmount(),
                        behavior: 'smooth'
                    });
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    track.scrollBy({
                        left: getScrollAmount(),
                        behavior: 'smooth'
                    });
                });
            }
        });
    }

    // =========================================
    // MODAL: SUCCESS & ERROR FOR CONTACT FORM 7
    // =========================================
    function initCF7Modals() {
        const successModal = document.getElementById('vmq-modal-success');
        const errorModal = document.getElementById('vmq-modal-error');
        
        if (!successModal || !errorModal) return;

        let autoCloseTimer = null;
        let countdownInterval = null;
        const COOLDOWN_TIME = 5; // 5 giây

        // Hàm hiển thị modal
        function showModal(modal, type) {
            // Ẩn tất cả modal trước
            document.querySelectorAll('.vmq-modal').forEach(m => {
                m.style.display = 'none';
            });

            // Reset cooldown bar
            const progressBar = modal.querySelector('.vmq-cooldown-progress');
            const timeText = modal.querySelector('.vmq-cooldown-time');
            
            if (progressBar) {
                progressBar.style.animation = 'none';
                progressBar.offsetHeight; // Trigger reflow
                progressBar.style.animation = 'vmq-cooldown-countdown 5s linear forwards';
            }

            // Hiển thị modal được chọn
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Chặn scroll

            // Clear timer cũ nếu có
            if (autoCloseTimer) {
                clearTimeout(autoCloseTimer);
            }
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            // Cập nhật countdown text
            let remainingTime = COOLDOWN_TIME;
            if (timeText) {
                timeText.textContent = remainingTime;
            }

            // Cập nhật số giây còn lại mỗi giây
            countdownInterval = setInterval(function() {
                remainingTime--;
                if (timeText) {
                    timeText.textContent = remainingTime;
                }
                if (remainingTime <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);

            // Auto close sau 5 giây
            autoCloseTimer = setTimeout(function() {
                closeModal(modal);
            }, COOLDOWN_TIME * 1000);
        }

        // Hàm đóng modal
        function closeModal(modal) {
            if (!modal) return;
            
            modal.style.display = 'none';
            document.body.style.overflow = ''; // Cho phép scroll lại
            
            // Clear timers
            if (autoCloseTimer) {
                clearTimeout(autoCloseTimer);
                autoCloseTimer = null;
            }
            if (countdownInterval) {
                clearInterval(countdownInterval);
                countdownInterval = null;
            }

            // Reset cooldown bar
            const progressBar = modal.querySelector('.vmq-cooldown-progress');
            const timeText = modal.querySelector('.vmq-cooldown-time');
            if (progressBar) {
                progressBar.style.animation = 'none';
            }
            if (timeText) {
                timeText.textContent = COOLDOWN_TIME;
            }
        }

        // Xử lý nút close
        document.querySelectorAll('.vmq-modal-close').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                const modal = this.closest('.vmq-modal');
                closeModal(modal);
            });
        });

        // Đóng modal khi click vào overlay
        document.querySelectorAll('.vmq-modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function() {
                const modal = this.closest('.vmq-modal');
                closeModal(modal);
            });
        });

        // Đóng modal khi nhấn ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.vmq-modal').forEach(modal => {
                    if (modal.style.display === 'flex') {
                        closeModal(modal);
                    }
                });
            }
        });

        // Tích hợp với Contact Form 7
        // Lắng nghe sự kiện submit thành công
        document.addEventListener('wpcf7mailsent', function(event) {
            showModal(successModal, 'success');
        }, false);

        // Lắng nghe sự kiện submit thất bại
        document.addEventListener('wpcf7mailfailed', function(event) {
            showModal(errorModal, 'error');
        }, false);

        // Lắng nghe validation errors
        document.addEventListener('wpcf7invalid', function(event) {
            // Không hiển thị modal cho validation errors, chỉ hiển thị thông báo inline
        }, false);

        // Lắng nghe spam
        document.addEventListener('wpcf7spam', function(event) {
            showModal(errorModal, 'error');
        }, false);
    }

    // Khởi tạo modal khi DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initCF7Modals();
        hideCF7Messages();
    });

    // Ẩn tất cả thông báo mặc định của Contact Form 7
    function hideCF7Messages() {
        // Ẩn tất cả response output
        const hideMessages = function() {
            document.querySelectorAll('.wpcf7-response-output').forEach(function(el) {
                el.style.display = 'none';
                el.style.visibility = 'hidden';
                el.style.opacity = '0';
                el.style.height = '0';
                el.style.margin = '0';
                el.style.padding = '0';
                el.style.overflow = 'hidden';
            });

            // Ẩn screen-reader-response
            document.querySelectorAll('.screen-reader-response').forEach(function(el) {
                el.style.display = 'none';
                el.style.visibility = 'hidden';
                el.style.opacity = '0';
                el.style.height = '0';
                el.style.width = '0';
                el.style.margin = '0';
                el.style.padding = '0';
                el.style.overflow = 'hidden';
                el.style.position = 'absolute';
                el.style.left = '-9999px';
            });
        };

        // Chạy ngay lập tức
        hideMessages();

        // Chạy lại sau khi form submit
        document.addEventListener('wpcf7mailsent', hideMessages, false);
        document.addEventListener('wpcf7mailfailed', hideMessages, false);
        document.addEventListener('wpcf7spam', hideMessages, false);

        // Sử dụng MutationObserver để theo dõi thay đổi DOM
        const observer = new MutationObserver(function(mutations) {
            hideMessages();
        });

        // Quan sát tất cả các form CF7
        document.querySelectorAll('.wpcf7').forEach(function(form) {
            observer.observe(form, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'data-status']
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Khai báo các biến
        const grid = document.querySelector('.mq-pricing-grid');
        const cards = document.querySelectorAll('.mq-price-card');
        const defaultCard = document.querySelector('.mq-price-card.mq-default-active');
    
        // Kiểm tra nếu các phần tử tồn tại
        if (grid && cards.length > 0 && defaultCard) {
    
            // 2. Sự kiện khi rê chuột vào BẤT KỲ card nào
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    // Xóa class active khỏi thẻ mặc định (để nó trở về màu tối)
                    defaultCard.classList.remove('mq-default-active');
                });
            });
    
            // 3. Sự kiện khi chuột RỜI KHỎI khu vực Grid (không hover vào card nào nữa)
            grid.addEventListener('mouseleave', function() {
                // Thêm lại class active cho thẻ mặc định (để nó sáng lên lại)
                defaultCard.classList.add('mq-default-active');
            });
        }
    });

    /* --- SCRIPT XỬ LÝ MODAL KIỂM TRA TỶ LỆ ĐẬU --- */
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy các element
        var modal = document.getElementById("mq-visa-modal");
        var btn = document.getElementById("mq-btn-check-rate");
        var span = document.getElementsByClassName("mq-close-modal")[0];

        // Kiểm tra tồn tại để tránh lỗi
        if (modal && btn && span) {
            
            // Khi click nút mở modal
            btn.onclick = function(e) {
                e.preventDefault(); // Ngăn chặn hành vi mặc định nếu là thẻ a
                modal.classList.add("show"); // Thêm class để hiện modal
                modal.style.display = "block"; // Fallback display
                setTimeout(function(){
                    modal.style.opacity = "1";
                }, 10);
            }

            // Khi click nút đóng (x)
            span.onclick = function() {
                closeModal();
            }

            // Khi click ra ngoài vùng content thì đóng modal
            window.onclick = function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            }

            // Hàm đóng modal
            function closeModal() {
                modal.style.opacity = "0";
                setTimeout(function(){
                    modal.classList.remove("show");
                    modal.style.display = "none";
                }, 300); // Chờ hiệu ứng mờ dần kết thúc
            }
        }
    });

    /* --- SCRIPT XỬ LÝ BUTTON VMQ-MAIN-BTN, VMQ-BTN-CTA VÀ MQ-CHECK-RATE-BTN MỞ MODAL KIỂM TRA TỶ LỆ ĐẬU VISA --- */
    document.addEventListener("DOMContentLoaded", function() {
        // Tìm tất cả button có class vmq-main-btn, vmq-btn-cta hoặc mq-check-rate-btn
        const allButtons = document.querySelectorAll('.vmq-main-btn, .vmq-btn-cta, .mq-check-rate-btn');
        
        // Lọc bỏ button có ID mq-btn-check-rate (đã được xử lý bởi code cũ ở trên)
        const mainButtons = Array.from(allButtons).filter(function(btn) {
            return btn.id !== 'mq-btn-check-rate';
        });
        
        if (mainButtons.length === 0) return;

        // Tìm hoặc tạo modal chứa form
        let visaModal = document.getElementById('vmq-visa-check-modal');
        let formContainer = null;
        let formLoaded = false;
        
        // Hàm khởi tạo modal
        function initVisaModal() {
            // Nếu chưa có modal, tạo mới
            if (!visaModal) {
                visaModal = document.createElement('div');
                visaModal.id = 'vmq-visa-check-modal';
                visaModal.className = 'vmq-modal';
                visaModal.style.display = 'none';
                
                // Tạo cấu trúc modal
                visaModal.innerHTML = `
                    <div class="vmq-modal-overlay"></div>
                    <div class="vmq-modal-content vmq-modal-visa-form">
                        <button class="vmq-modal-close" aria-label="Close">&times;</button>
                        <div class="vmq-visa-form-container"></div>
                    </div>
                `;
                
                // Thêm modal vào body
                document.body.appendChild(visaModal);
                formContainer = visaModal.querySelector('.vmq-visa-form-container');
            } else {
                formContainer = visaModal.querySelector('.vmq-visa-form-container');
            }
        }

        // Hàm load form vào modal
        function loadFormIntoModal() {
            if (!formContainer) return;
            
            // Kiểm tra xem form đã có trong modal chưa
            const existingFormInModal = formContainer.querySelector('.nhut-visa-form-wrapper');
            if (existingFormInModal) {
                formLoaded = true;
                return;
            }
            
            // Tìm form trên trang (có thể đã được render)
            const existingForm = document.querySelector('.nhut-visa-form-wrapper');
            
            if (existingForm) {
                // Kiểm tra xem form có nằm trong modal khác không
                const isInModal = existingForm.closest('.vmq-modal');
                if (!isInModal) {
                    // Di chuyển form vào modal
                    formContainer.appendChild(existingForm);
                    formLoaded = true;
                    
                    // Khởi tạo lại form với jQuery nếu có
                    if (typeof jQuery !== 'undefined' && typeof initVisaForm === 'function') {
                        setTimeout(function() {
                            jQuery(existingForm).each(function() {
                                initVisaForm(jQuery(this));
                            });
                        }, 100);
                    }
                } else {
                    // Form đã ở trong modal khác, không di chuyển
                    formLoaded = true;
                }
            } else {
                // Form chưa tồn tại, cần load qua AJAX
                loadFormViaAjax();
            }
        }
        
        // Hàm load form qua AJAX
        function loadFormViaAjax() {
            if (!formContainer || formLoaded) return;
            
            // Hiển thị loading indicator
            formContainer.innerHTML = '<div style="text-align: center; padding: 40px; color: #fff;">Đang tải form...</div>';
            
            // Thử tìm form trên trang trước (có thể đã được render ẩn)
            const existingForm = document.querySelector('.nhut-visa-form-wrapper');
            if (existingForm && formContainer) {
                // Di chuyển form vào modal
                formContainer.innerHTML = '';
                formContainer.appendChild(existingForm);
                formLoaded = true;
                
                // Khởi tạo form
                if (typeof jQuery !== 'undefined') {
                    setTimeout(function() {
                        jQuery(existingForm).each(function() {
                            // Trigger lại initialization của plugin
                            if (typeof initVisaForm === 'function') {
                                initVisaForm(jQuery(this));
                            }
                        });
                    }, 100);
                }
                return;
            }
            
            // Nếu không tìm thấy form trên trang, load qua AJAX
            if (typeof jQuery !== 'undefined') {
                const ajaxUrl = (typeof vmqThemeConfig !== 'undefined' && vmqThemeConfig.ajaxUrl)
                    ? vmqThemeConfig.ajaxUrl
                    : (typeof nhutVisaForm !== 'undefined' && nhutVisaForm.ajaxUrl)
                        ? nhutVisaForm.ajaxUrl
                        : '/wp-admin/admin-ajax.php';
                
                jQuery.ajax({
                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'nhut_load_visa_form'
                    },
                    success: function(response) {
                        if (response && response.success && response.data && response.data.html) {
                            formContainer.innerHTML = response.data.html;
                            formLoaded = true;
                            
                            // Khởi tạo form
                            setTimeout(function() {
                                const newForm = formContainer.querySelector('.nhut-visa-form-wrapper');
                                if (newForm) {
                                    // Trigger lại initialization của plugin
                                    if (typeof jQuery !== 'undefined') {
                                        jQuery(newForm).each(function() {
                                            if (typeof initVisaForm === 'function') {
                                                initVisaForm(jQuery(this));
                                            } else {
                                                // Nếu không có function, trigger event để plugin tự init
                                                jQuery(document).trigger('nhut-visa-form-ready');
                                            }
                                        });
                                    }
                                }
                            }, 100);
                        } else {
                            // Thử tìm lại form trên trang một lần nữa
                            setTimeout(function() {
                                const form = document.querySelector('.nhut-visa-form-wrapper');
                                if (form && formContainer) {
                                    formContainer.innerHTML = '';
                                    formContainer.appendChild(form);
                                    formLoaded = true;
                                    
                                    if (typeof jQuery !== 'undefined') {
                                        setTimeout(function() {
                                            jQuery(form).each(function() {
                                                if (typeof initVisaForm === 'function') {
                                                    initVisaForm(jQuery(this));
                                                }
                                            });
                                        }, 100);
                                    }
                                } else {
                                    formContainer.innerHTML = '<div style="text-align: center; padding: 40px; color: #fff;">Không thể tải form. Vui lòng thử lại sau.</div>';
                                }
                            }, 500);
                        }
                    },
                    error: function() {
                        // Thử tìm lại form trên trang
                        setTimeout(function() {
                            const form = document.querySelector('.nhut-visa-form-wrapper');
                            if (form && formContainer) {
                                formContainer.innerHTML = '';
                                formContainer.appendChild(form);
                                formLoaded = true;
                                
                                if (typeof jQuery !== 'undefined') {
                                    setTimeout(function() {
                                        jQuery(form).each(function() {
                                            if (typeof initVisaForm === 'function') {
                                                initVisaForm(jQuery(this));
                                            }
                                        });
                                    }, 100);
                                }
                            } else {
                                formContainer.innerHTML = '<div style="text-align: center; padding: 40px; color: #fff;">Lỗi khi tải form. Vui lòng thử lại sau.</div>';
                            }
                        }, 500);
                    }
                });
            } else {
                // Fallback: tìm lại form trên trang
                setTimeout(function() {
                    const form = document.querySelector('.nhut-visa-form-wrapper');
                    if (form && formContainer) {
                        formContainer.innerHTML = '';
                        formContainer.appendChild(form);
                        formLoaded = true;
                    } else {
                        formContainer.innerHTML = '<div style="text-align: center; padding: 40px; color: #fff;">Form chưa được cấu hình. Vui lòng đảm bảo shortcode [nhut_check_visa_pass_rate] đã được thêm vào trang.</div>';
                    }
                }, 500);
            }
        }

        // Khởi tạo modal ngay khi DOM ready
        initVisaModal();

        // Hàm mở modal
        function openVisaModal() {
            if (!visaModal) {
                initVisaModal();
            }
            
            if (!visaModal) return;
            
            // Load form vào modal nếu chưa load
            if (!formLoaded) {
                loadFormIntoModal();
            }
            
            // Hiển thị modal
            visaModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Hàm đóng modal
        function closeVisaModal() {
            if (!visaModal) return;
            
            visaModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Gắn sự kiện click cho tất cả button có class vmq-main-btn, vmq-btn-cta hoặc mq-check-rate-btn
        mainButtons.forEach(function(btn) {
            // Kiểm tra xem button đã có sự kiện click chưa (tránh xung đột)
            if (btn.onclick || btn.getAttribute('data-modal-handled')) {
                return; // Bỏ qua button đã được xử lý
            }
            
            // Đánh dấu button đã được xử lý
            btn.setAttribute('data-modal-handled', 'true');
            
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation(); // Ngăn chặn các sự kiện khác
                openVisaModal();
            }, true); // Sử dụng capture phase để chạy trước
        });

        // Đóng modal khi click vào overlay
        if (visaModal) {
            const overlay = visaModal.querySelector('.vmq-modal-overlay');
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeVisaModal();
                });
            }

            // Đóng modal khi click vào nút close
            const closeBtn = visaModal.querySelector('.vmq-modal-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeVisaModal();
                });
            }
        }

        // Đóng modal khi nhấn ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && visaModal && visaModal.style.display === 'flex') {
                closeVisaModal();
            }
        });

        // Theo dõi khi form được thêm vào DOM (nếu form được load sau)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Kiểm tra nếu node là form hoặc chứa form
                            const form = node.classList && node.classList.contains('nhut-visa-form-wrapper') 
                                ? node 
                                : node.querySelector && node.querySelector('.nhut-visa-form-wrapper');
                            
                            if (form && formContainer && form.parentNode !== formContainer) {
                                // Di chuyển form vào modal nếu chưa ở trong đó
                                formContainer.appendChild(form);
                            }
                        }
                    });
                });
            });

            // Quan sát body để phát hiện khi form được thêm vào
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    });

})();

