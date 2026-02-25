/**
 * Slide for Continent - Bootstrap 5 Carousel JavaScript
 * Fully responsive with infinite loop
 */

(function($) {
    'use strict';
    
    // Initialize carousels when DOM is ready
    $(document).ready(function() {
        initContinentCarousels();
    });
    
    // Re-initialize on AJAX content load (for Elementor)
    $(document).on('elementor/popup/show', function() {
        setTimeout(initContinentCarousels, 100);
    });
    
    function initContinentCarousels() {
        // Check if Bootstrap is loaded
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap 5 is not loaded. Waiting...');
            // Retry after a short delay
            setTimeout(initContinentCarousels, 200);
            return;
        }
        
        $('.nhut-continent-carousel').each(function() {
            const $carousel = $(this);
            const carouselElement = this;
            
            // Skip if already initialized
            if ($carousel.data('bs.carousel')) {
                return;
            }
            
            try {
                // Initialize Bootstrap carousel with infinite loop - NO autoplay
                const carousel = new bootstrap.Carousel(carouselElement, {
                    wrap: true,
                    interval: false, // Disable autoplay
                    keyboard: true,
                    pause: false, // Don't pause on hover
                    ride: false, // Don't auto-start
                    touch: true
                });
                
                // Store carousel instance
                $carousel.data('bs.carousel', carousel);
                
                // Handle responsive adjustments
                handleResponsive($carousel, carousel);
                
                // Listen for window resize
                let resizeTimer;
                $(window).on('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        handleResponsive($carousel, carousel);
                    }, 250);
                });
                
            } catch (error) {
                console.error('Error initializing carousel:', error);
            }
        });
    }
    
    function handleResponsive($carousel, carouselInstance) {
        // This function can be used for responsive adjustments if needed
        // Bootstrap handles most responsive behavior automatically
    }
    
})(jQuery);
