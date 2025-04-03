/**
 * Enhanced Lightbox Navigation for AQM Divi Gallery
 * Adds next/previous arrows to the default Divi lightbox
 */
(function($) {
    'use strict';
    
    // Wait for Divi's lightbox to initialize first
    $(window).on('load', function() {
        setTimeout(initLightboxNavigation, 1000);
    });
    
    function initLightboxNavigation() {
        console.log('AQM Divi Gallery: Initializing lightbox navigation');
        
        // Save all lightbox links for navigation
        var galleryImages = [];
        
        // Function to create and show navigation arrows
        function addNavigationArrows() {
            // First, collect all gallery images
            galleryImages = [];
            $('.et_pb_gallery_image').each(function() {
                galleryImages.push($(this).attr('href'));
            });
            $('.aqm-gallery-lightbox').each(function() {
                galleryImages.push($(this).attr('href'));
            });
            
            // If no arrows exist and lightbox is open, add them
            if ($('.mfp-wrap').length > 0 && $('.aqm-lightbox-nav').length === 0) {
                // Create previous arrow
                var prevBtn = $('<button class="aqm-lightbox-nav aqm-lightbox-prev" title="Previous Image">&#x34;</button>');
                prevBtn.css({
                    'position': 'absolute',
                    'z-index': '100000',
                    'background-color': 'rgba(0,0,0,0.5)',
                    'color': '#fff',
                    'font-size': '36px',
                    'font-family': 'ETmodules',
                    'width': '50px',
                    'height': '50px',
                    'line-height': '44px',
                    'text-align': 'center',
                    'border-radius': '50%',
                    'border': 'none',
                    'left': '20px',
                    'top': '50%',
                    'transform': 'translateY(-50%)',
                    'cursor': 'pointer'
                });
                
                // Create next arrow
                var nextBtn = $('<button class="aqm-lightbox-nav aqm-lightbox-next" title="Next Image">&#x35;</button>');
                nextBtn.css({
                    'position': 'absolute',
                    'z-index': '100000',
                    'background-color': 'rgba(0,0,0,0.5)',
                    'color': '#fff',
                    'font-size': '36px',
                    'font-family': 'ETmodules',
                    'width': '50px',
                    'height': '50px',
                    'line-height': '44px',
                    'text-align': 'center',
                    'border-radius': '50%',
                    'border': 'none',
                    'right': '20px',
                    'top': '50%',
                    'transform': 'translateY(-50%)',
                    'cursor': 'pointer'
                });
                
                // Append to lightbox
                $('.mfp-wrap').append(prevBtn);
                $('.mfp-wrap').append(nextBtn);
                
                // Add event listeners
                $('.aqm-lightbox-prev').on('click', function() {
                    navigateLightbox('prev');
                });
                
                $('.aqm-lightbox-next').on('click', function() {
                    navigateLightbox('next');
                });
                
                // Add keyboard navigation
                $(document).on('keydown.aqmLightbox', function(e) {
                    if ($('.mfp-wrap').length > 0) {
                        if (e.keyCode === 37) { // Left arrow
                            navigateLightbox('prev');
                        } else if (e.keyCode === 39) { // Right arrow
                            navigateLightbox('next');
                        }
                    }
                });
            }
        }
        
        // Function to navigate between images
        function navigateLightbox(direction) {
            var currentImg = $('.mfp-img').attr('src');
            var currentIndex = -1;
            
            // Find current image index
            for (var i = 0; i < galleryImages.length; i++) {
                if (galleryImages[i] === currentImg) {
                    currentIndex = i;
                    break;
                }
            }
            
            if (currentIndex !== -1) {
                // Calculate new index
                var newIndex;
                if (direction === 'next') {
                    newIndex = (currentIndex + 1) % galleryImages.length;
                } else {
                    newIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
                }
                
                // Change image
                $('.mfp-img').attr('src', galleryImages[newIndex]);
            }
        }
        
        // Add direct click handling to our custom gallery items
        $(document).on('click', '.aqm-gallery-lightbox', function(e) {
            e.preventDefault();
            
            // Ensure we have a collection of all gallery images before opening
            galleryImages = [];
            $('.et_pb_gallery_image').each(function() {
                galleryImages.push($(this).attr('href'));
            });
            $('.aqm-gallery-lightbox').each(function() {
                galleryImages.push($(this).attr('href'));
            });
            
            // Use Magnific Popup directly
            var items = [];
            for (var i = 0; i < galleryImages.length; i++) {
                items.push({
                    src: galleryImages[i],
                    type: 'image'
                });
            }
            
            var clickedIndex = galleryImages.indexOf($(this).attr('href'));
            if (clickedIndex === -1) clickedIndex = 0;
            
            $.magnificPopup.open({
                items: items,
                gallery: {
                    enabled: true
                },
                type: 'image',
                removalDelay: 500,
                mainClass: 'mfp-fade',
                callbacks: {
                    open: function() {
                        setTimeout(addNavigationArrows, 100);
                    },
                    change: function() {
                        setTimeout(addNavigationArrows, 100);
                    }
                }
            }, clickedIndex);
        });
        
        // Watch for any Divi lightbox openings
        $(document).on('click', '.et_pb_gallery .et_pb_gallery_image a', function() {
            setTimeout(addNavigationArrows, 100);
        });
        
        // Observer for dynamically added elements
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    // Check if the lightbox was added
                    if ($('.mfp-wrap').length > 0 && $('.aqm-lightbox-nav').length === 0) {
                        addNavigationArrows();
                    }
                }
            });
        });
        
        // Start observing the body for changes
        observer.observe(document.body, { childList: true, subtree: true });
    }
    
    // Also call on document ready to handle already loaded content
    $(document).ready(function() {
        setTimeout(initLightboxNavigation, 500);
    });
    
})(jQuery);
