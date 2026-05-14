/**
 * VISAMINHQUAN Theme JavaScript
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

(function() {
    'use strict';

    // Mobile menu toggle - Version 2 (Vertical Icon Tabs)
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.getElementById('vmq-mobile-menu');
    const mobileMenuTabs = document.getElementById('vmq-mobile-menu-tabs');
    const mobileMenuContent = document.getElementById('vmq-mobile-menu-content');
    const body = document.body;

    function closeMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.remove('is-open');
        body.style.overflow = '';
        if (mobileMenuToggle) {
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
    }

    function openMobileMenu() {
        if (!mobileMenu) return;
        mobileMenu.classList.add('is-open');
        body.style.overflow = 'hidden';
        if (mobileMenuToggle) {
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
        }
    }

    // Accordion cho submenu trong mobile menu
    function setupMobileAccordion(listElement) {
        if (!listElement) return;

        const parents = listElement.querySelectorAll('li.menu-item-has-children, li:has(> ul)');

        parents.forEach(function (li) {
            const submenu = li.querySelector(':scope > ul');
            const triggerLink = li.querySelector(':scope > a');
            if (!submenu || !triggerLink) return;

            li.classList.add('vmq-mobile-accordion');

            const headerBtn = document.createElement('button');
            headerBtn.type = 'button';
            headerBtn.className = 'vmq-mobile-acc-header';
            headerBtn.innerHTML = '<span class="vmq-mobile-acc-label">' + triggerLink.textContent.trim() + '</span><span class="vmq-mobile-acc-icon"></span>';

            li.insertBefore(headerBtn, triggerLink);
            triggerLink.remove();

            submenu.classList.add('vmq-mobile-acc-panel');
            submenu.style.display = 'none';

            headerBtn.addEventListener('click', function () {
                const isOpen = li.classList.toggle('is-open');
                submenu.style.display = isOpen ? 'block' : 'none';
            });
        });
    }

    function buildMobileMenuFromPrimary() {
        const primaryMenu = document.getElementById('primary-menu');
        if (!primaryMenu || !mobileMenuTabs || !mobileMenuContent) return;

        // Reset containers
        mobileMenuTabs.innerHTML = '';
        mobileMenuContent.innerHTML = '';

        const topItems = Array.prototype.filter.call(primaryMenu.children, function (el) {
            return el.tagName === 'LI';
        });

        topItems.forEach(function (item, index) {
            const link = item.querySelector('a');
            if (!link) return;

            const title = link.textContent.trim();
            const tabId = 'vmq-mobile-tab-' + index;

            // Create tab button
            const tabButton = document.createElement('button');
            tabButton.type = 'button';
            tabButton.className = 'vmq-mobile-tab';
            tabButton.dataset.tab = tabId;
            tabButton.innerHTML = '<span class="vmq-mobile-tab-label">' + title + '</span>';

            // Create panel
            const panel = document.createElement('div');
            panel.className = 'vmq-mobile-panel';
            panel.dataset.tab = tabId;

            const submenu = item.querySelector('ul');
            let list;

            if (submenu) {
                list = submenu.cloneNode(true);
                list.classList.add('vmq-mobile-submenu');
                // Thiết lập accordion cho các nhóm bên trong
                setupMobileAccordion(list);
            } else {
                list = document.createElement('ul');
                list.className = 'vmq-mobile-submenu';
                const li = document.createElement('li');
                li.appendChild(link.cloneNode(true));
                list.appendChild(li);
            }

            panel.appendChild(list);
            mobileMenuTabs.appendChild(tabButton);
            mobileMenuContent.appendChild(panel);

            tabButton.addEventListener('click', function () {
                const allTabs = mobileMenuTabs.querySelectorAll('.vmq-mobile-tab');
                const allPanels = mobileMenuContent.querySelectorAll('.vmq-mobile-panel');

                allTabs.forEach(function (btn) {
                    btn.classList.remove('active');
                });
                allPanels.forEach(function (p) {
                    p.classList.remove('active');
                });

                tabButton.classList.add('active');
                panel.classList.add('active');
            });
        });

        // Kích hoạt tab đầu tiên
        const firstTab = mobileMenuTabs.querySelector('.vmq-mobile-tab');
        const firstPanel = mobileMenuContent.querySelector('.vmq-mobile-panel');
        if (firstTab && firstPanel) {
            firstTab.classList.add('active');
            firstPanel.classList.add('active');
        }
    }

    if (mobileMenuToggle && mobileMenu) {
        buildMobileMenuFromPrimary();

        mobileMenuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (mobileMenu.classList.contains('is-open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Click backdrop hoặc nút đóng để đóng
        mobileMenu.addEventListener('click', function (e) {
            if (e.target.classList.contains('vmq-mobile-menu-backdrop')) {
                closeMobileMenu();
            }
        });

        const closeBtn = mobileMenu.querySelector('.vmq-mobile-menu-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                closeMobileMenu();
            });
        }

        // Đóng khi resize lên desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 992 && mobileMenu.classList.contains('is-open')) {
                closeMobileMenu();
            }
        });
    }

    // Auto columns for submenu based on item count
    function setupMegaMenuColumns() {
        const submenuGroups = document.querySelectorAll('.main-navigation ul ul > li');
        
        submenuGroups.forEach(group => {
            const submenu = group.querySelector('ul');
            if (!submenu) return;

            // Bỏ qua menu Tin tức: luôn 1 cột, do CSS xử lý
            const tinTucMenu = group.closest('.menu-item-tin-tuc');
            if (tinTucMenu) return;

            // Bỏ qua menu Dịch vụ khác: layout 4 cột do CSS xử lý
            const dichVuKhacMenu = group.closest('.menu-item-dich-vu-khac');
            if (dichVuKhacMenu) return;

            const items = submenu.querySelectorAll('li');
            const itemCount = items.length;
            
            // Châu Âu: 2 cột (không dùng 3 cột)
            if (itemCount > 15) {
                group.style.gridColumn = 'span 2';
                submenu.style.columnCount = '2';
                submenu.style.maxHeight = '500px';
                submenu.style.columnGap = '1.5rem';
            } else if (itemCount > 8) {
                group.style.gridColumn = 'span 1';
                submenu.style.columnCount = '2';
                submenu.style.maxHeight = '500px';
                submenu.style.columnGap = '1.5rem';
            } else {
                group.style.gridColumn = 'span 1';
                submenu.style.columnCount = '1';
                submenu.style.maxHeight = 'none';
            }
        });
    }
    
    // Run on page load and when menu is hovered
    setupMegaMenuColumns();
    
    // Re-run when menu items are hovered (in case menu is loaded dynamically)
    const menuItems = document.querySelectorAll('.main-navigation > ul > li');
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            setTimeout(setupMegaMenuColumns, 100);
        });
    });

    // Handle dropdown menus on mobile
    const menuItemsWithChildren = document.querySelectorAll('.main-navigation .menu-item-has-children > a');
    
    menuItemsWithChildren.forEach(item => {
        item.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const parent = this.parentElement;
                const submenu = parent.querySelector('ul');
                
                if (submenu) {
                    parent.classList.toggle('submenu-open');
                    submenu.style.display = parent.classList.contains('submenu-open') ? 'block' : 'none';
                }
            }
        });
    });

    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href === '#' || href.length <= 1) return;

            const hash = href.substring(1);
            const target = document.getElementById(hash) || document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
            }
        });
    });

    // ============================================
    // SINGLE POST - TOC (TABLE OF CONTENTS)
    // ============================================
    (function initPostToc() {
        const content = document.getElementById('vmq-post-content');
        const tocContainer = document.getElementById('vmq-toc');
        const tocWidget = document.getElementById('vmq-inline-toc-widget');
        const tocToggle = document.getElementById('vmq-inline-toc-toggle');
        if (!content || !tocContainer || !tocWidget || !tocToggle) return;

        const headings = content.querySelectorAll('h2');
        if (!headings.length) return;

        const list = document.createElement('ul');
        list.className = 'vmq-toc-list';

        const getCleanHeadingText = (headingEl) => {
            // 1. Lấy nội dung thô (bao gồm cả mã lỗi nếu có)
            let rawContent = headingEl.innerHTML;

            // 2. Dùng Regex để xóa bỏ toàn bộ các thẻ HTML lồng bên trong (như <img>, <span>...)
            // Đồng thời xóa luôn các chuỗi văn bản trông giống thẻ HTML (lỗi hiển thị mã)
            let cleanText = rawContent.replace(/<[^>]*>?/gm, '').trim();

            // 3. Nếu vẫn còn sót các ký tự thực thể HTML (như &lt;img...)
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = cleanText;
            cleanText = tempDiv.textContent || tempDiv.innerText || '';

            // Cắt bớt nếu tiêu đề quá dài (tránh trường hợp lỗi render làm nát layout)
            if (cleanText.length > 200) {
                cleanText = cleanText.substring(0, 150) + '...';
            }

            return cleanText;
        };

        const createTocLinkItem = (headingEl, cleanText, index) => {
            const li = document.createElement('li');
            li.className = 'vmq-toc-item vmq-toc-item-h2';

            const a = document.createElement('a');
            a.href = '#' + headingEl.id;
            a.dataset.tocIndex = String(index);
            a.textContent = cleanText;
            a.className = 'vmq-toc-link';

            li.appendChild(a);
            return li;
        };

        headings.forEach((heading, index) => {
            if (!heading.id) {
                heading.id = 'vmq-heading-' + (index + 1);
            }

            const cleanText = getCleanHeadingText(heading);
            if (!cleanText) return;

            list.appendChild(createTocLinkItem(heading, cleanText, index));
        });

        const tocLinks = list.querySelectorAll('.vmq-toc-link');
        tocLinks.forEach((link) => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const targetId = this.getAttribute('href').replace('#', '');
                const target = document.getElementById(targetId);
                if (!target) return;
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        tocContainer.innerHTML = '';
        tocContainer.appendChild(list);

        // Đặt mục lục xuống dưới đoạn mở bài đầu tiên để đọc intro trước.
        const firstParagraph = content.querySelector('p');
        if (firstParagraph && firstParagraph.parentNode) {
            firstParagraph.insertAdjacentElement('afterend', tocWidget);
        } else {
            content.insertBefore(tocWidget, content.firstChild);
        }

        tocWidget.hidden = false;

        const setTocExpanded = (expanded) => {
            tocWidget.classList.toggle('is-open', expanded);
            tocToggle.setAttribute('aria-expanded', String(expanded));
        };

        setTocExpanded(false);

        tocToggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            setTocExpanded(!expanded);
        });
    })();

    // ============================================
    // GOOGLE TRANSLATE – Đơn giản, không lưu cài đặt
    // ============================================
    // - Reload trang luôn về tiếng Việt gốc (xóa cookie khi load).
    // - Click VI: xóa cookie + reload → hiển thị văn bản gốc (không dịch).
    // - Click EN: dịch sang tiếng Anh bằng Google Translate (không reload).

    const langLinks = document.querySelectorAll('.language-selector .lang-link');
    const translationLoading = document.getElementById('translation-loading');
    let googleTranslateScriptLoaded = false;

    // Xóa cookie Google Translate để luôn hiển thị bản gốc tiếng Việt
    function clearTranslateCookie() {
        document.cookie = 'googtrans=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        document.cookie = 'googtrans=/vi/en; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    }

    // Khi load trang: xóa cookie → trang luôn là tiếng Việt gốc, không lưu ngôn ngữ
    clearTranslateCookie();

    function showLoading() {
        if (translationLoading) {
            translationLoading.style.display = 'flex';
        }
    }

    function hideLoading() {
        if (translationLoading) {
            translationLoading.style.display = 'none';
        }
    }

    // Nạp script Google Translate (nếu chưa có)
    function loadGoogleTranslateScript() {
        if (googleTranslateScriptLoaded) {
            return;
        }
        if (typeof window.google !== 'undefined' && window.google.translate) {
            googleTranslateScriptLoaded = true;
            return;
        }
        if (document.getElementById('vmq-google-translate-script')) {
            return;
        }

        const s = document.createElement('script');
        s.id = 'vmq-google-translate-script';
        s.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        s.async = true;
        s.defer = true;
        document.head.appendChild(s);
    }

    // Callback toàn cục cho Google
    window.googleTranslateElementInit = function() {
        const container = document.getElementById('google_translate_element');
        if (!container || typeof google === 'undefined' || !google.translate) {
            return;
        }

        new google.translate.TranslateElement({
            pageLanguage: 'vi',
            includedLanguages: 'vi,en',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false
        }, 'google_translate_element');
    };

    // Chỉ dùng để chuyển sang tiếng Anh (set select .goog-te-combo = 'en')
    function translateToEnglish() {
        showLoading();
        loadGoogleTranslateScript();

        let attempts = 0;
        const maxAttempts = 100; // ~10s

        const tryApply = function() {
            const combo = document.querySelector('#google_translate_element select.goog-te-combo');
            attempts++;

            if (combo) {
                combo.value = 'en';
                combo.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
                setTimeout(hideLoading, 1500);
                return true;
            }

            if (attempts >= maxAttempts) {
                hideLoading();
                return true;
            }
            return false;
        };

        if (tryApply()) return;
        const interval = setInterval(function() {
            if (tryApply()) clearInterval(interval);
        }, 100);
    }

    function initLanguageSelector() {
        if (!langLinks.length) return;

        langLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetLang = this.getAttribute('data-lang');
                if (!targetLang) return;

                if (targetLang === 'vi') {
                    // Tiếng Việt: không dịch, chỉ xóa cookie và reload → hiển thị văn bản gốc
                    clearTranslateCookie();
                    location.reload();
                    return;
                }

                if (targetLang === 'en') {
                    // Cập nhật trạng thái active
                  var elVi = document.getElementById('lang-vi');
if (elVi) elVi.classList.remove('active');
var elEn = document.getElementById('lang-en');
if (elEn) elEn.classList.remove('active');
                    this.classList.add('active');
                    translateToEnglish();
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguageSelector);
    } else {
        initLanguageSelector();
    }

})();
