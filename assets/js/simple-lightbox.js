/**
 * Simple Lightbox with Navigation for AQM Divi Gallery
 * A lightweight, standalone lightbox implementation that doesn't rely on existing lightboxes
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        initSimpleLightbox();
    });
    
    function initSimpleLightbox() {
        console.log('AQM Gallery: Initializing simple lightbox');
        
        // Create the lightbox container if it doesn't exist
        if ($('#aqm-simple-lightbox').length === 0) {
            var lightboxHTML = `
                <div id="aqm-simple-lightbox" style="display:none;">
                    <div class="aqm-lightbox-overlay"></div>
                    <div class="aqm-lightbox-container">
                        <div class="aqm-lightbox-content">
                            <img src="" alt="" />
                            <div class="aqm-lightbox-title"></div>
                        </div>
                        <button class="aqm-lightbox-close" aria-label="Close">×</button>
                        <button class="aqm-lightbox-prev" aria-label="Previous Image">&#x34;</button>
                        <button class="aqm-lightbox-next" aria-label="Next Image">&#x35;</button>
                    </div>
                </div>
            `;
            
            // Add CSS for the lightbox
            var lightboxCSS = `
                #aqm-simple-lightbox {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 999999;
                }
                .aqm-lightbox-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.9);
                }
                .aqm-lightbox-container {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    max-width: 90%;
                    max-height: 90%;
                }
                .aqm-lightbox-content {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }
                .aqm-lightbox-content img {
                    max-width: 100%;
                    max-height: 80vh;
                    object-fit: contain;
                }
                .aqm-lightbox-title {
                    color: white;
                    margin-top: 10px;
                    font-size: 16px;
                    text-align: center;
                    padding: 0 50px;
                }
                /* Static navigation controls - NO TRANSITIONS, NO HOVER EFFECTS */
                .aqm-lightbox-close, 
                .aqm-lightbox-prev, 
                .aqm-lightbox-next {
                    font-family: "ETmodules";
                    background-color: rgba(0, 0, 0, 0.7);
                    color: white;
                    border: none;
                    border-radius: 50%;
                    width: 44px;
                    height: 44px;
                    font-size: 32px;
                    line-height: 44px;
                    text-align: center;
                    cursor: pointer;
                    position: absolute;
                    padding: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    /* NO TRANSITIONS AT ALL */
                    transition: none;
                    transform: none !important;
                    will-change: auto;
                }
                
                /* NO HOVER EFFECTS AT ALL - just a pointer cursor */
                .aqm-lightbox-close:hover, 
                .aqm-lightbox-prev:hover, 
                .aqm-lightbox-next:hover {
                    /* NO CHANGES ON HOVER */
                    background-color: rgba(0, 0, 0, 0.7);
                    transform: none !important;
                    transition: none;
                }
                
                /* Close button */
                .aqm-lightbox-close {
                    top: -22px;
                    right: -22px;
                    font-size: 40px;
                }
                
                /* Previous button - fixed position without transforms */
                .aqm-lightbox-prev {
                    left: -60px;
                    top: 50%;
                    margin-top: -22px; /* Half the height */
                }
                
                /* Next button - fixed position without transforms */
                .aqm-lightbox-next {
                    right: -60px;
                    top: 50%;
                    margin-top: -22px; /* Half the height */
                }
                @media (max-width: 767px) {
                    .aqm-lightbox-prev {
                        left: 10px;
                        top: 50%;
                        margin-top: -22px;
                    }
                    .aqm-lightbox-next {
                        right: 10px;
                        top: 50%;
                        margin-top: -22px;
                    }
                }
            `;
            
            // Add to page
            $('body').append(lightboxHTML);
            $('<style id="aqm-lightbox-styles"></style>').text(lightboxCSS).appendTo('head');
        }
        
        // Handle lightbox open
        $(document).on('click', '.aqm-gallery-lightbox', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $clicked = $(this);
            var gallery = $clicked.closest('.aqm-gallery-container');
            var galleryItems = [];
            
            // Collect all images in this gallery
            gallery.find('.aqm-gallery-lightbox').each(function() {
                galleryItems.push({
                    href: $(this).attr('href'), // This will be the full-size image for the lightbox
                    thumbSrc: $(this).data('thumb-src') || $(this).attr('href'), // Use medium size for thumbnails if available
                    title: $(this).attr('title') || ''
                });
            });
            
            // Find clicked image index
            var currentIndex = 0;
            for (var i = 0; i < galleryItems.length; i++) {
                if (galleryItems[i].href === $clicked.attr('href')) {
                    currentIndex = i;
                    break;
                }
            }
            
            // Store gallery data
            $('#aqm-simple-lightbox').data('gallery-items', galleryItems);
            $('#aqm-simple-lightbox').data('current-index', currentIndex);
            
            // Show the image
            showLightboxImage(currentIndex);
            
            // Show lightbox
            $('#aqm-simple-lightbox').fadeIn(300);
            $('body').css('overflow', 'hidden');
        });
        
        // Handle lightbox close
        $(document).on('click', '.aqm-lightbox-close, .aqm-lightbox-overlay', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Handle next/prev
        $(document).on('click', '.aqm-lightbox-prev', function() {
            navigateLightbox('prev');
        });
        
        $(document).on('click', '.aqm-lightbox-next', function() {
            navigateLightbox('next');
        });
        
        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if ($('#aqm-simple-lightbox').is(':visible')) {
                if (e.keyCode === 27) { // ESC
                    closeLightbox();
                } else if (e.keyCode === 37) { // Left arrow
                    navigateLightbox('prev');
                } else if (e.keyCode === 39) { // Right arrow
                    navigateLightbox('next');
                }
            }
        });
        
        // Functions
        function showLightboxImage(index) {
            var items = $('#aqm-simple-lightbox').data('gallery-items');
            if (!items || items.length === 0) return;
            
            var item = items[index];
            $('#aqm-simple-lightbox img').attr('src', item.href).attr('alt', item.title);
            $('#aqm-simple-lightbox .aqm-lightbox-title').text(item.title);
            $('#aqm-simple-lightbox').data('current-index', index);
        }
        
        function navigateLightbox(direction) {
            var items = $('#aqm-simple-lightbox').data('gallery-items');
            var currentIndex = $('#aqm-simple-lightbox').data('current-index');
            
            if (!items || items.length <= 1) return;
            
            var newIndex;
            if (direction === 'next') {
                newIndex = (currentIndex + 1) % items.length;
            } else {
                newIndex = (currentIndex - 1 + items.length) % items.length;
            }
            
            showLightboxImage(newIndex);
        }
        
        function closeLightbox() {
            $('#aqm-simple-lightbox').fadeOut(300);
            $('body').css('overflow', '');
        }
    }
    
})(jQuery);
