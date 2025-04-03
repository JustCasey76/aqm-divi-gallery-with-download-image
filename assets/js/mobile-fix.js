/**
 * Force single column display on mobile devices
 * This script runs after all other scripts to ensure our mobile view is enforced
 */
(function($) {
    'use strict';
    
    // Function to enforce single column on mobile
    function enforceSingleColumnOnMobile() {
        // Check if we're on a mobile device (screen width less than 768px)
        if (window.innerWidth < 768) {
            console.log('AQM Gallery: Enforcing single column on mobile');
            
            // Target all gallery items containers with maximum specificity
            $('.aqm-gallery-container .aqm-gallery-items').each(function() {
                // Force single column through direct style manipulation
                $(this).css({
                    'display': 'grid !important',
                    'grid-template-columns': 'repeat(1, 1fr) !important'
                });
                
                // For extra certainty, also modify through setAttribute which can't be overridden
                this.style.setProperty('display', 'grid', 'important');
                this.style.setProperty('grid-template-columns', 'repeat(1, 1fr)', 'important');
                
                // Add a special class we can target with CSS
                $(this).addClass('aqm-mobile-enforced');
            });
            
            // Also force each gallery item to be full width
            $('.aqm-gallery-item').css({
                'width': '100% !important',
                'max-width': '100% !important'
            });
        }
    }
    
    // Run on document ready
    $(document).ready(function() {
        enforceSingleColumnOnMobile();
    });
    
    // Also run on window resize to catch orientation changes
    $(window).on('resize', function() {
        enforceSingleColumnOnMobile();
    });
    
    // Run after any potential Divi builder events
    $(window).on('et_builder_api_ready', function() {
        setTimeout(enforceSingleColumnOnMobile, 500);
    });
    
    // Also run on any potential content updates
    $(document).on('ajaxComplete', function() {
        setTimeout(enforceSingleColumnOnMobile, 500);
    });
    
})(jQuery);
