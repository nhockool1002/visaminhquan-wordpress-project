/**
 * VISAMINHQUAN Theme JavaScript
 *
 * @package VISAMINHQUAN
 * @author Nhựt Nguyễn
 * @version 1.0
 */

// Define Google Translate callback in global scope BEFORE the IIFE
window.visaminhquanGoogleTranslateInit = function() {
    console.log('Google Translate callback called');
    // This will be redefined inside the IIFE with full implementation
};

(function() {
    'use strict';

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNavigation = document.querySelector('.main-navigation');
    const body = document.body;

    if (mobileMenuToggle && mainNavigation) {
        mobileMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = mainNavigation.classList.toggle('active');
            this.setAttribute('aria-expanded', isActive);
            
            // Prevent body scroll when menu is open
            if (isActive) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (mainNavigation.classList.contains('active') && 
                !mainNavigation.contains(e.target) && 
                !mobileMenuToggle.contains(e.target)) {
                mainNavigation.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });

        // Close mobile menu on window resize if it's larger than mobile
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mainNavigation.classList.contains('active')) {
                mainNavigation.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
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
        if (!content || !tocContainer) return;

        const headings = content.querySelectorAll('h2, h3');
        if (!headings.length) {
            tocContainer.parentElement.style.display = 'none';
            return;
        }

        const list = document.createElement('ul');
        list.className = 'vmq-toc-list';

        headings.forEach((heading, index) => {
            // Tạo id nếu chưa có
            if (!heading.id) {
                heading.id = 'vmq-heading-' + (index + 1);
            }

            const li = document.createElement('li');
            li.className = 'vmq-toc-item vmq-toc-item-' + heading.tagName.toLowerCase();

            const a = document.createElement('a');
            a.href = '#' + heading.id;
            a.textContent = heading.textContent.trim();
            a.className = 'vmq-toc-link';

            li.appendChild(a);
            list.appendChild(li);
        });

        tocContainer.appendChild(list);
    })();

    // ============================================
    // GOOGLE TRANSLATE LANGUAGE SELECTOR
    // ============================================
    
    let googleTranslateInitialized = false;
    let translateSelectElement = null;
    
    // Clear translation cookie to always start with Vietnamese (original language)
    function clearTranslationCookie() {
        document.cookie = 'googtrans=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        document.cookie = 'googtrans=/vi/en; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    }
    
    // IMPORTANT: Do NOT persist language across refresh/navigation.
    // Mặc định: mỗi lần load trang sẽ về tiếng Việt.
    // Ngoại lệ DUY NHẤT: lần reload ngay sau khi user bấm EN (được đánh dấu bằng sessionStorage).
    const VMQ_TRANSLATE_FLAG = 'vmq_translate_to_en_once';
    const isTranslateReload = sessionStorage.getItem(VMQ_TRANSLATE_FLAG) === '1';

    if (!isTranslateReload) {
        clearTranslationCookie();
    }

    // Ensure Google Translate script is loaded (no reload fallback, because refresh must return to VI)
    function ensureGoogleTranslateScriptLoaded() {
        if (typeof window.google !== 'undefined' && window.google.translate) {
            return;
        }
        if (document.getElementById('vmq-google-translate-script')) {
            return;
        }
        const s = document.createElement('script');
        s.id = 'vmq-google-translate-script';
        s.src = '//translate.google.com/translate_a/element.js?cb=visaminhquanGoogleTranslateInit';
        s.async = true;
        s.defer = true;
        document.head.appendChild(s);
    }
    
    // Initialize Google Translate (wait for DOM container first)
    window.visaminhquanGoogleTranslateInit = function() {
        if (typeof google === 'undefined' || !google.translate) {
            console.error('Google Translate API not loaded');
            return;
        }

        // Ensure container exists (it is in header.php body)
        const waitForContainer = setInterval(function() {
            const container = document.getElementById('google_translate_element');
            if (!container) return;
            clearInterval(waitForContainer);

            // Initialize Google Translate widget (hidden)
            new google.translate.TranslateElement({
                pageLanguage: 'vi',
                includedLanguages: 'en,vi',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');

            // Wait for select element to be created
            const waitForSelect = setInterval(function() {
                translateSelectElement = document.querySelector('.goog-te-combo');
                if (translateSelectElement) {
                    clearInterval(waitForSelect);
                    googleTranslateInitialized = true;

                    // If this load is from EN click, force apply EN immediately
                    if (isTranslateReload) {
                        translateSelectElement.value = '';
                        setTimeout(function() {
                            translateSelectElement.value = 'en';
                            const ev1 = new Event('change', { bubbles: true, cancelable: true });
                            translateSelectElement.dispatchEvent(ev1);
                            setTimeout(function() {
                                const ev2 = new Event('change', { bubbles: true, cancelable: true });
                                translateSelectElement.dispatchEvent(ev2);
                            }, 50);
                        }, 50);
                    }
                }
            }, 100);

            setTimeout(function() {
                clearInterval(waitForSelect);
            }, 8000);
        }, 50);

        setTimeout(function() {
            clearInterval(waitForContainer);
        }, 8000);
            
            // Hide Google Translate widget UI completely
            const hideTranslateUI = function() {
                const elementsToHide = [
                    '.goog-te-banner-frame',
                    '.goog-te-menu-frame',
                    'body > .goog-te-banner-frame',
                    '#google_translate_element > div',
                    '.goog-te-banner',
                    '.goog-te-menu-value'
                ];
                
                elementsToHide.forEach(selector => {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(el => {
                        el.style.display = 'none';
                        el.style.visibility = 'hidden';
                        el.style.opacity = '0';
                        el.style.height = '0';
                        el.style.overflow = 'hidden';
                        el.style.position = 'absolute';
                        el.style.left = '-9999px';
                    });
                });
                
                // Fix body position
                document.body.style.top = '0';
                document.body.style.position = 'static';
            };
            
            // Hide immediately and keep checking
            hideTranslateUI();
            setInterval(hideTranslateUI, 500);
            
            // Hide on any DOM changes
            const observer = new MutationObserver(hideTranslateUI);
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
    };
    
    // Language selector click handlers
    const langLinks = document.querySelectorAll('.language-selector .lang-link');
    const translationLoading = document.getElementById('translation-loading');
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguageSelector);
    } else {
        initLanguageSelector();
    }
    
    function initLanguageSelector() {
    langLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
                const targetLang = this.getAttribute('data-lang');
                
                if (targetLang === 'vi') {
                    // Vietnamese: Clear cookie and reload to show original text
                    sessionStorage.removeItem(VMQ_TRANSLATE_FLAG);
                    clearTranslationCookie();
                    
                    // Update active state
                    document.getElementById('lang-en')?.classList.remove('active');
                    document.getElementById('lang-vi')?.classList.add('active');
                    
                    // Reload page to show original Vietnamese text
                    location.reload();
                    
                } else if (targetLang === 'en') {
                    // English: Translate using Google Website Translator
                    // Cách ổn định nhất là set cookie + reload 1 lần để Google áp ngôn ngữ.
                    
                    // Show loading indicator
                    if (translationLoading) {
                        translationLoading.style.display = 'flex';
                    }
                    
                    // Update active state
                    document.getElementById('lang-vi')?.classList.remove('active');
                    document.getElementById('lang-en')?.classList.add('active');
                    
                    // Mark this reload as an intentional translate reload (so we don't clear cookie on load)
                    sessionStorage.setItem(VMQ_TRANSLATE_FLAG, '1');

                    // Set session cookie and reload (NO persistence after normal refresh)
                    document.cookie = 'googtrans=/vi/en; path=/;';
                    location.reload();
                }
            });
        });
    }
    
    // Translate to English
    function translateToEnglish() {
        // In case the script was blocked or not enqueued, inject it on demand
        ensureGoogleTranslateScriptLoaded();

        // Get or wait for select element
        let translateSelect = translateSelectElement || document.querySelector('.goog-te-combo');
        
        if (translateSelect && googleTranslateInitialized) {
            // Use select element
            performEnglishTranslation(translateSelect);
        } else {
            // Wait for Google Translate to initialize
            let attempts = 0;
            const maxAttempts = 100; // 10 seconds
            
            const checkInterval = setInterval(function() {
                attempts++;
                translateSelect = document.querySelector('.goog-te-combo');
                
                if (translateSelect && (googleTranslateInitialized || typeof google !== 'undefined')) {
                    clearInterval(checkInterval);
                    translateSelectElement = translateSelect;
                    googleTranslateInitialized = true;
                    performEnglishTranslation(translateSelect);
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    console.error('Không thể khởi tạo Google Translate để dịch sang tiếng Anh.');
                    if (translationLoading) {
                        translationLoading.style.display = 'none';
                    }
                }
            }, 100);
        }
    }
    
    // Perform English translation
    function performEnglishTranslation(translateSelect) {
        if (!translateSelect) {
            console.error('Không tìm thấy .goog-te-combo để dịch.');
            if (translationLoading) {
                translationLoading.style.display = 'none';
            }
            return;
        }
        
        try {
            // Set cookie first
            // No persistence: session-only cookie
            document.cookie = 'googtrans=/vi/en; path=/;';
            
            // Reset and set value to force translation
            translateSelect.value = '';
            
            setTimeout(function() {
                translateSelect.value = 'en';
                
                // Trigger change event
                if (translateSelect.fireEvent) {
                    translateSelect.fireEvent('onchange');
                } else {
                    const changeEvent = new Event('change', { bubbles: true, cancelable: true });
                    translateSelect.dispatchEvent(changeEvent);
                }

                // Fire twice (Google widget thường cần 2 lần)
                setTimeout(function() {
                    if (translateSelect.fireEvent) {
                        translateSelect.fireEvent('onchange');
                    } else {
                        const changeEvent2 = new Event('change', { bubbles: true, cancelable: true });
                        translateSelect.dispatchEvent(changeEvent2);
                    }
                }, 50);
                
                // Also try direct method
                if (translateSelect.onchange) {
                    translateSelect.onchange();
                }
                
                // Try input event
                const inputEvent = new Event('input', { bubbles: true });
                translateSelect.dispatchEvent(inputEvent);
                
            }, 50);
            
            // Hide loading after translation
            setTimeout(function() {
                if (translationLoading) {
                    translationLoading.style.display = 'none';
                }
            }, 2000);
            
        } catch (error) {
            console.error('Error translating to English:', error);
            if (translationLoading) {
                translationLoading.style.display = 'none';
            }
        }
    }
    
    // Flag states: default Vietnamese on load; English only after user clicks EN
    function updateFlagStates() {
        if (isTranslateReload) {
            document.getElementById('lang-vi')?.classList.remove('active');
            document.getElementById('lang-en')?.classList.add('active');
        } else {
            document.getElementById('lang-en')?.classList.remove('active');
            document.getElementById('lang-vi')?.classList.add('active');
        }
    }
    
    // Update flag states on page load
    updateFlagStates();

    // If this page load was triggered by EN click, allow translation to apply then clear the flag.
    // Any subsequent refresh will revert to Vietnamese (cookie cleared on load).
    if (isTranslateReload) {
        setTimeout(function() {
            sessionStorage.removeItem(VMQ_TRANSLATE_FLAG);
            if (translationLoading) {
                translationLoading.style.display = 'none';
            }
        }, 2500);
    }

})();
