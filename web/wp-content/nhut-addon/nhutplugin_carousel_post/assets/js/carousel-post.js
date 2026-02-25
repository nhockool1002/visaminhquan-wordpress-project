/**
 * Carousel Post JavaScript
 * Rewritten to ensure items are not cut off
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initCarousels();
    });
    
    // Re-initialize on AJAX content load (for Elementor)
    $(document).on('elementor/popup/show', function() {
        setTimeout(initCarousels, 100);
    });
    
    function initCarousels() {
        $('.nhut-carousel-post-container').each(function() {
            const $container = $(this);
            const $slider = $container.find('.nhut-carousel-post-slider');
            const $items = $slider.find('.nhut-carousel-post-item');
            const $prevBtn = $container.find('.nhut-carousel-prev');
            const $nextBtn = $container.find('.nhut-carousel-next');
            const $wrapper = $container.closest('.nhut-carousel-post-wrapper');
            
            if ($items.length === 0) {
                return;
            }
            
            // Get number of columns from data attribute
            const columns = parseInt($wrapper.data('columns')) || 4;
            
            let currentIndex = 0;
            let itemsPerView = columns;
            let totalItems = $items.length;
            
            // Calculate items per view based on screen size
            function updateItemsPerView() {
                const width = $(window).width();
                
                if (width <= 576) {
                    itemsPerView = 1;
                } else if (width <= 768) {
                    itemsPerView = 2;
                } else if (width <= 992) {
                    itemsPerView = Math.min(2, columns);
                } else if (width <= 1200) {
                    itemsPerView = Math.min(3, columns);
                } else {
                    itemsPerView = columns;
                }
                
                // Recalculate carousel position
                updateCarousel();
            }
            
            // Update carousel position
            function updateCarousel() {
                const maxIndex = Math.max(0, totalItems - itemsPerView);
                currentIndex = Math.min(currentIndex, maxIndex);
                currentIndex = Math.max(0, currentIndex);
                
                if ($items.length === 0) {
                    return;
                }
                
                const sliderElement = $slider[0];
                const containerElement = $container[0];
                
                if (!sliderElement || !containerElement) {
                    return;
                }
                
                let translateX = 0;
                
                if (currentIndex > 0) {
                    // Reset transform temporarily to get accurate measurements
                    const savedTransform = $slider.css('transform');
                    $slider.css('transform', 'translateX(0px)');
                    
                    // Force reflow
                    sliderElement.offsetHeight;
                    
                    // Get first item to calculate width
                    const firstItem = $items.eq(0)[0];
                    const targetItem = $items.eq(currentIndex)[0];
                    
                    if (firstItem && targetItem) {
                        // Get actual item width (includes padding, border, but not margin)
                        const itemWidth = firstItem.offsetWidth;
                        const gap = 20; // Match CSS gap
                        
                        // Calculate base translateX
                        translateX = -(currentIndex * (itemWidth + gap));
                        
                        // Verify and adjust to prevent cutoff
                        const containerWidth = containerElement.offsetWidth;
                        const lastVisibleIndex = Math.min(currentIndex + itemsPerView - 1, totalItems - 1);
                        const lastItem = $items.eq(lastVisibleIndex)[0];
                        
                        if (lastItem) {
                            // Get position of last item
                            const sliderRect = sliderElement.getBoundingClientRect();
                            const lastItemRect = lastItem.getBoundingClientRect();
                            
                            // Calculate where last item would be after transform
                            const lastItemRight = lastItemRect.right - sliderRect.left + translateX;
                            
                            // If last item extends beyond container, adjust
                            if (lastItemRight > containerWidth) {
                                const overflow = lastItemRight - containerWidth;
                                translateX -= overflow;
                            }
                        }
                    }
                }
                
                // Apply the transform
                $slider.css('transform', `translateX(${translateX}px)`);
                
                // Update button states
                updateButtonStates();
            }
            
            // Update navigation button states
            function updateButtonStates() {
                const maxIndex = Math.max(0, totalItems - itemsPerView);
                
                if (currentIndex <= 0) {
                    $prevBtn.addClass('disabled');
                } else {
                    $prevBtn.removeClass('disabled');
                }
                
                if (currentIndex >= maxIndex) {
                    $nextBtn.addClass('disabled');
                } else {
                    $nextBtn.removeClass('disabled');
                }
            }
            
            // Previous button
            $prevBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!$prevBtn.hasClass('disabled') && currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            });
            
            // Next button
            $nextBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const maxIndex = Math.max(0, totalItems - itemsPerView);
                
                if (!$nextBtn.hasClass('disabled') && currentIndex < maxIndex) {
                    currentIndex++;
                    updateCarousel();
                }
            });
            
            // Handle window resize
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const oldItemsPerView = itemsPerView;
                    updateItemsPerView();
                    
                    // Reset to start if items per view changed significantly
                    if (oldItemsPerView !== itemsPerView) {
                        currentIndex = 0;
                    }
                    
                    updateCarousel();
                }, 250);
            });
            
            // Touch/swipe support for mobile
            let touchStartX = 0;
            let touchEndX = 0;
            let isDragging = false;
            
            $slider.on('touchstart', function(e) {
                touchStartX = e.originalEvent.touches[0].clientX;
                isDragging = true;
            });
            
            $slider.on('touchmove', function(e) {
                if (isDragging) {
                    e.preventDefault();
                }
            });
            
            $slider.on('touchend', function(e) {
                if (isDragging) {
                    touchEndX = e.originalEvent.changedTouches[0].clientX;
                    handleSwipe();
                    isDragging = false;
                }
            });
            
            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        // Swipe left - next
                        const maxIndex = Math.max(0, totalItems - itemsPerView);
                        if (currentIndex < maxIndex) {
                            currentIndex++;
                            updateCarousel();
                        }
                    } else {
                        // Swipe right - previous
                        if (currentIndex > 0) {
                            currentIndex--;
                            updateCarousel();
                        }
                    }
                }
            }
            
            // Initialize after DOM is ready
            setTimeout(function() {
                updateItemsPerView();
            }, 100);
        });
    }
    
})(jQuery);
