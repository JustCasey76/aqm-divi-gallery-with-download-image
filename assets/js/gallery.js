/**
 * AQM Divi Gallery with Download Image
 * JavaScript functionality
 */
(function($) {
    'use strict';

    // Initialize on document ready for faster initial load
    $(document).ready(function() {
        // Initialize all galleries
        $('.aqm-gallery-container').each(function() {
            initGallery($(this));
        });
        
        // Initialize feature functions for all galleries
        $('.aqm-gallery-container').each(function() {
            const $gallery = $(this);
            
            console.log('Gallery initialized on document.ready');
        });
    });
    
    // Also initialize on window load to ensure Divi is fully loaded
    $(window).on('load', function() {
        // Allow a short delay for Divi to initialize its components
        setTimeout(function() {
            $('.aqm-gallery-container').each(function() {
                // Reinitialize lightbox to make sure Divi's lightbox is properly connected
                initLightbox($(this));
            });
            console.log('All AQM galleries lightboxes reinitialized on window.load');
        }, 500);
    });
    
    // Initialize feature functions for all galleries
    function initFeatures($gallery) {
        // Initialize buttons functionality
        initSelectAll($gallery);
        initDownloadSelected($gallery);
        initDownloadAll($gallery);
        
        // Initialize item checkboxes events
        initCheckboxes($gallery);
        
        // Initialize lightbox
        initLightbox($gallery);
    }
        
        // Force layout refresh on window load to ensure proper layout after all content is loaded
        $(window).on('load', function() {
            console.log('Window load event - refreshing all gallery layouts');
            $('.aqm-gallery-masonry').each(function() {
                var $gallery = $(this);
                setTimeout(function() {
                    refreshMasonry($gallery);
                }, 100);
            });
            
            // Handle image loading for grid layout
            $('.aqm-gallery-grid').each(function() {
                var $gallery = $(this);
                $gallery.find('img').on('load error', function() {
                    $(this).addClass('loaded');
                });
            });
        });
        
        // Use a debounced window resize handler for better performance
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                console.log('Window resize event - refreshing all gallery layouts');
                $('.aqm-gallery-masonry').each(function() {
                    var $gallery = $(this);
                    refreshMasonry($gallery);
                });
                
                // Update column widths for grid layout
                $('.aqm-gallery-grid, .aqm-gallery-standard').each(function() {
                    var $gallery = $(this);
                    var columns = parseInt($gallery.data('columns'), 10) || 3;
                    var gap = parseInt($gallery.data('gap'), 10) || 10;
                    
                    // Update CSS variables if possible for more responsive handling
                    $gallery.css('--gallery-columns', columns);
                    $gallery.css('--gallery-gap', gap + 'px');
                });
            }, 250); // 250ms debounce delay
        });

        // Handle Divi Builder Preview Mode
        $(document).on('DOMNodeInserted', function(e) {
            const $element = $(e.target);
            
            if ($element.find('.aqm-gallery-container').length > 0) {
                $element.find('.aqm-gallery-container').each(function() {
                    initGallery($(this));
                });
            }
        });
    });

    /**
     * Initialize a gallery
     */
    function initGallery($gallery) {
        // Get gallery settings - defaulting to grid layout regardless of previous setting
        const columns = parseInt($gallery.data('columns'), 10) || 3;
        const gap = parseInt($gallery.data('gap'), 10) || 10;
        
        console.log('Gallery initialization - ID:', $gallery.attr('id'), 'Columns:', columns, 'Gap:', gap);
        
        // Log gallery settings - using inline styles now directly on HTML elements instead of CSS variables
        console.log('Gallery columns should be:', columns);
        console.log('Gallery gap should be:', gap + 'px');
        
        // Add grid layout class regardless of previous settings
        $gallery.addClass('aqm-gallery-grid');
        $gallery.removeClass('aqm-gallery-masonry');
        $gallery.removeClass('aqm-gallery-standard');
        
        // Initialize all features (buttons, lightbox, etc.)
        initFeatures($gallery);
        
        // Get the gallery items container
        const $items = $gallery.find('.aqm-gallery-items');
        
        // Apply consistent styles to gallery items
        $items.find('.aqm-gallery-item').css({
            'box-sizing': 'border-box',
            'overflow': 'hidden',
            'border-radius': '3px',
            'height': 'auto' // Allow natural height based on content
        });
        
        // Log initialization
        console.log('Gallery initialized with grid layout, columns:', columns, 'gap:', gap);
        
        // For a clean layout, log the desired column count but don't override inline styles
        console.log('Grid layout should use', columns, 'columns with', gap + 'px gap');
        
        // These styles are now added inline by PHP in the module.php file
        // We don't want to override them in JavaScript since they might not apply
        // in time for the layout
        
        // Force images to maintain aspect ratio
        $items.find('img').css({
            'width': '100%',
            'height': 'auto',
            'display': 'block'
        });
        
        // Initialize other gallery features - no need for layout type check anymore
        // We're just doing grid now
        
        // Initialize item selection functionality
        initCheckboxes($gallery);
        
        // Initialize load more button
        initLoadMore($gallery);

        // Initialize Select All functionality
        initSelectAll($gallery);

        // Initialize Download Selected functionality
        initDownloadSelected($gallery);
        
        // Initialize Download All functionality
        initDownloadAll($gallery);
        
        // Initialize lightbox
        initLightbox($gallery);
    }
    
    /**
     * Calculate item width based on columns and gap
     */
    function calculateItemWidth(columns, gap) {
        // Ensure columns is a valid number
        columns = parseInt(columns, 10) || 3;
        gap = parseInt(gap, 10) || 10;
        
        console.log('Calculating width with columns:', columns, 'gap:', gap);
        
        // Always respect the columns setting from the module
        console.log('Using', columns, 'columns as set in the module');
        return 'calc(' + (100/columns) + '% - ' + gap + 'px)';
    }
    
    /**
     * Apply regular grid layout for non-masonry galleries
     */
    function applyRegularGrid($gallery) {
        const columns = parseInt($gallery.data('columns'), 10) || 3;
        const gap = parseInt($gallery.data('gap'), 10) || 10;
        const $items = $gallery.find('.aqm-gallery-items');
        
        $items.css({
            'display': 'flex',
            'flex-wrap': 'wrap',
            'margin': '0 -' + (gap/2) + 'px'
        });
        
        $items.find('.aqm-gallery-item').css({
            'width': 'calc(' + (100/columns) + '% - ' + gap + 'px)',
            'margin': (gap/2) + 'px',
            'box-sizing': 'border-box'
        });
    }
        
        // Initialize lightbox
        initLightbox($gallery);
    } else {
        // Apply regular grid layout
            const $items = $gallery.find('.aqm-gallery-items');
            $items.css({
                'margin': '0 -' + (gap/2) + 'px'
            });
            
            $items.find('.aqm-gallery-item').css({
                'width': 'calc(' + (100/columns) + '% - ' + gap + 'px)',
                'margin': (gap/2) + 'px'
            });
        }

        // Load more functionality
        initLoadMore($gallery);

        // Select all functionality
        initSelectAll($gallery);

        // Download selected functionality
        initDownloadSelected($gallery);
        
        // Download all functionality
        initDownloadAll($gallery);
        
        // Initialize lightbox
        initLightbox($gallery);
    }

    /**
     * Initialize masonry layout
     */
    function initMasonryLayout($gallery) {
        const $items = $gallery.find('.aqm-gallery-items');
        const columns = parseInt($gallery.data('columns'), 10) || 3;
        const gap = parseInt($gallery.data('gap'), 10) || 10;
        
        console.log('Setting up masonry layout');
        
        // Use imagesLoaded to wait for all images to fully load before initializing masonry
        if (typeof $.fn.imagesLoaded !== 'undefined') {
            $items.imagesLoaded(function() {
                console.log('Images loaded, initializing masonry');
                setupMasonry($gallery, $items);
            });
        } else {
            console.log('imagesLoaded not available, using setTimeout fallback');
            // Fallback if imagesLoaded isn't available
            setTimeout(function() {
                setupMasonry($gallery, $items);
            }, 500);
        }
    }
    
    /**
     * Set up masonry layout
     */
    function setupMasonry($gallery, $items) {
        const columns = parseInt($gallery.data('columns'), 10) || 3;
        const gap = parseInt($gallery.data('gap'), 10) || 10;
        
        // First, destroy any existing masonry instance
        if ($items.data('masonry')) {
            $items.masonry('destroy');
        }
        
        // Reset container styles for masonry
        $items.css({
            'display': 'block',
            'width': '100%',
            'margin': '0 auto',
            'position': 'relative' // Important for masonry positioning
        });
        
        // Make sure item styling is correct
        const itemWidth = calculateItemWidth(columns, gap);
        
        console.log('Setting masonry items width to:', itemWidth, 'with', columns, 'columns');
        
        $items.find('.aqm-gallery-item').css({
            'width': itemWidth,
            'margin-bottom': gap + 'px',
            'transition': 'none', // Temporarily disable transitions for faster initial layout
            'box-sizing': 'border-box'
        });
        
        // Initialize Masonry
        try {
            $items.masonry({
                itemSelector: '.aqm-gallery-item',
                percentPosition: true,
                columnWidth: '.aqm-gallery-item',
                gutter: gap,
                transitionDuration: '0s', // Start with no transition
                initLayout: true
            });
            
            // After a short delay, re-enable transitions for smooth animations
            setTimeout(function() {
                $items.find('.aqm-gallery-item').css('transition', 'transform 0.3s ease, opacity 0.3s ease');
                $items.masonry('layout');
            }, 100);
            
            // Mark gallery as having masonry initialized
            $gallery.addClass('masonry-initialized');
            
            console.log('Masonry initialized successfully');
        } catch (e) {
            console.error('Error initializing masonry:', e);
            // Fall back to CSS grid if masonry fails
            fallbackToCssGrid($gallery, $items);
        }
    }

    /**
     * Initialize Load More functionality
     */
    function initLoadMore($gallery) {
        const $loadMoreBtn = $gallery.find('.aqm-load-more-button');
        
        if (!$loadMoreBtn.length) {
            return; // No load more button found
        }
        
        console.log('Initializing load more functionality for gallery:', $gallery.attr('id'));
        
        // Get gallery source - either 'manual' or 'folder'
        const gallerySource = $gallery.data('gallery-source') || 'manual';
        
        // Get attributes needed for load more
        const galleryImages = $gallery.data('gallery-images') || '';
        const nonce = $loadMoreBtn.data('nonce') || aqm_gallery_params.nonce;
        const loadMoreCount = parseInt($gallery.data('load-more-count'), 10) || 10;
        const layoutType = $gallery.data('layout-type') || 'grid';
        
        // Get additional folder-based attributes when needed
        const folderIds = gallerySource === 'folder' ? ($gallery.data('folder-ids') || '') : '';
        const includeSubfolders = gallerySource === 'folder' ? ($gallery.data('include-subfolders') || 'off') : 'off';
        const sorting = gallerySource === 'folder' ? ($gallery.data('sorting') || 'date_desc') : 'date_desc';
        
        // Get initial load count and total
        let currentOffset = parseInt($loadMoreBtn.data('loaded'), 10) || 0;
        const totalImages = parseInt($loadMoreBtn.data('total'), 10) || 0;
        
        console.log('Load more initialized with:', {
            gallerySource: gallerySource,
            galleryImages: galleryImages ? (galleryImages.length > 50 ? galleryImages.substring(0, 50) + '...' : galleryImages) : 'None',
            currentOffset: currentOffset,
            totalImages: totalImages,
            loadMoreCount: loadMoreCount,
            folderIds: folderIds,
            includeSubfolders: includeSubfolders,
            sorting: sorting
        });
        
        // If we have no images, exit
        if ((!galleryImages && gallerySource === 'manual') || !$loadMoreBtn.length) {
            console.error('❌ Missing gallery images or load more button not found');
            if (!galleryImages) console.error('Gallery images data attribute is missing');
            return;
        }
        
        // Mark gallery as using load more for other functions to detect
        $gallery.data('using-load-more', true);
        
        // Update button visibility initially
        if (currentOffset >= totalImages) {
            $loadMoreBtn.parent().hide();
        }
        
        $loadMoreBtn.on('click', function(e) {
            e.preventDefault();
            
            // Disable button to prevent multiple clicks
            $loadMoreBtn.prop('disabled', true);
            
            // Add loading indicator
            const $loadingIndicator = $('<div class="aqm-gallery-loading"><span class="aqm-gallery-loading-text">Loading...</span></div>');
            $gallery.append($loadingIndicator);
            
            // Get gallery container for appending new items
            const $galleryItems = $gallery.find('.aqm-gallery-items');
            
            // Make AJAX request to load more images
            console.log('Loading more images with offset:', currentOffset, 'for gallery:', $gallery.attr('id'));
            console.log('AJAX URL:', aqm_gallery_params.ajax_url);
            console.log('Using action:', 'aqm_divi_gallery_load_more');
            console.log('--- AJAX REQUEST PAYLOAD ---');
            console.log('Action:', 'aqm_divi_gallery_load_more');
            console.log('Nonce:', nonce);
            console.log('Gallery Images:', galleryImages);
            console.log('Offset:', currentOffset);
            console.log('Count:', loadMoreCount);
            console.log('Layout Type:', layoutType);
            
            // For debugging - show popup with gallery data
            console.log('Gallery Data:', $gallery.data());
            
            // Create a data object with all parameters for easier logging
            const ajaxData = {
                action: 'aqm_divi_gallery_load_more',
                nonce: nonce,
                offset: currentOffset,
                count: loadMoreCount,
                layout_type: layoutType,
                columns: parseInt($gallery.data('columns'), 10) || 3,
                gap: parseInt($gallery.data('gap'), 10) || 10,
                show_lightbox: $gallery.data('show-lightbox') || 'on',
                show_download: $gallery.data('show-download') || 'on',
                show_facebook: $gallery.data('show-facebook') || 'on',
                show_email: $gallery.data('show-email') || 'on',
                email_subject: $gallery.data('email-subject') || '',
                email_body: $gallery.data('email-body') || '',
                show_download_selected: $gallery.data('show-download-selected') || 'on',
                lightbox_icon: $gallery.data('lightbox-icon') || '',
                download_icon: $gallery.data('download-icon') || '',
                facebook_icon: $gallery.data('facebook-icon') || '',
                // Add support for folder-based galleries
                gallery_source: gallerySource,
                gallery_images: galleryImages, // Pass for all gallery types
                folder_ids: folderIds,
                include_subfolders: includeSubfolders,
                sorting: sorting,
                email_icon: $gallery.data('email-icon') || ''
            };
            
            console.log('AJAX request data:', ajaxData);
            
            // Show debug info in case of errors
            if (typeof aqm_gallery_params === 'undefined' || !aqm_gallery_params.ajax_url) {
                console.error('AJAX URL is not defined. Make sure aqm_gallery_params is properly localized');
                alert('Error: WordPress AJAX URL is not defined. Please check your browser console for details.');
                $loadMoreBtn.prop('disabled', false);
                $loadingIndicator.remove();
                return;
            }
            
            $.ajax({
                url: aqm_gallery_params.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: ajaxData,
                success: function(response) {
                    console.log('AJAX response received:', response);
                    
                    // Log the full response for debugging
                    console.log('Full response data:', response);
                    
                    // Check for success status
                    if (!response.success) {
                        console.error('Error in AJAX response:', response);
                        let errorMsg = 'Unknown error';
                        
                        // Try to extract a meaningful error message
                        if (response.data && typeof response.data === 'object' && response.data.message) {
                            errorMsg = response.data.message;
                        } else if (response.data && typeof response.data === 'string') {
                            errorMsg = response.data;
                        }
                        
                        alert('Error loading more images: ' + errorMsg);
                        $loadMoreBtn.prop('disabled', false);
                        $loadingIndicator.fadeOut(300, function() {
                            $(this).remove();
                        });
                        return;
                    }
                    
                    // Verify that we received HTML content in the response
                    if (!response.data || !response.data.html) {
                        console.error('AJAX response missing HTML data:', response);
                        alert('Error loading more images: Missing HTML content');
                        $loadMoreBtn.prop('disabled', false);
                        $loadingIndicator.fadeOut(300, function() {
                            $(this).remove();
                        });
                        return;
                    }
                    
                    // Log the loaded content details
                    console.log('HTML content received, length:', response.data.html.length);
                    console.log('New offset:', response.data.offset);
                    console.log('Total images:', response.data.total);
                    console.log('Images loaded so far:', response.data.loaded);
                    console.log('Remaining images:', response.data.remaining);
                    
                    console.log('Successfully received HTML content, length:', response.data.html.length);
                    if (response.success && response.data.html) {
                        // Convert the HTML string to jQuery objects
                        const $newItems = $(response.data.html);
                        
                        // Set initial opacity to 0 for fade-in effect
                        $newItems.css('opacity', 0);
                        
                        // Get the current columns and gap setting
                        const columns = parseInt($gallery.data('columns'), 10) || 3;
                        const gap = parseInt($gallery.data('gap'), 10) || 10;
                        
                        console.log('Loaded more items with columns:', columns, 'gap:', gap);
                        
                        // For all new items, focus on consistent styling but let the grid layout handle positioning
                        $newItems.css({
                            'box-sizing': 'border-box',
                            'overflow': 'hidden',
                            'height': 'auto'
                        });
                        
                        // Force images to maintain aspect ratio
                        $newItems.find('img').css({
                            'width': '100%',
                            'height': 'auto',
                            'display': 'block'
                        });
                        
                        console.log('Added new items with columns:', columns, 'gap:', gap);
                        
                        // Append new items to gallery
                        $galleryItems.append($newItems);
            
                        // Update layout based on layout type
                        if (layoutType === 'masonry') {
                            if (typeof $.fn.masonry !== 'undefined' && $gallery.hasClass('masonry-initialized')) {
                                // If images are loaded, update the layout
                                if (typeof $.fn.imagesLoaded !== 'undefined') {
                                    $newItems.imagesLoaded().done(function() {
                                        setTimeout(function() {
                                            const $masonryContainer = $gallery.find('.aqm-gallery-items');
                                            $masonryContainer.masonry('appended', $newItems);
                                            $masonryContainer.masonry('layout');
                                            // Reveal items with a fade-in effect
                                            $newItems.animate({opacity: 1}, 400);
                                        }, 100);
                                    });
                                } else {
                                    // If imagesLoaded isn't available, wait a short time for images to load
                                    setTimeout(function() {
                                        const $masonryContainer = $gallery.find('.aqm-gallery-items');
                                        $masonryContainer.masonry('appended', $newItems);
                                        $masonryContainer.masonry('layout');
                                        // Reveal items with a fade-in effect
                                        $newItems.animate({opacity: 1}, 400);
                                    }, 500);
                                }
                            } else {
                                // Use CSS grid as fallback
                                refreshMasonry($gallery);
                                // Reveal items with a fade-in effect
                                $newItems.animate({opacity: 1}, 400);
                            }
                        } else if (layoutType === 'grid') {
                            // For grid layout, wait for images to load then finish
                            if (typeof $.fn.imagesLoaded !== 'undefined') {
                                $newItems.imagesLoaded().done(function() {
                                    $newItems.animate({opacity: 1}, 400);
                                });
                            } else {
                                // Without imagesLoaded, just use a timeout
                                setTimeout(function() {
                                    $newItems.animate({opacity: 1}, 400);
                                }, 300);
                            }
                        } else {
                            // Standard layout doesn't need special handling
                            setTimeout(function() {
                                $newItems.animate({opacity: 1}, 400);
                            }, 300);
                        }
                        
                        // Initialize event listeners for new items
                        initLightbox($gallery);
                        
                        // Initialize checkboxes for new items
                        if ($gallery.find('.aqm-gallery-checkbox').length > 0) {
                            initCheckboxes($gallery);
                        }
                        
                        // Update the offset for the next batch
                        currentOffset = response.data.offset;
                        
                        // Get total count from response (more accurate than initial value)
                        if (response.data.total) {
                            totalImages = response.data.total;
                            console.log('Updated total images count from AJAX response:', totalImages);
                        }
                        
                        // Update the button data attribute
                        $loadMoreBtn.data('loaded', currentOffset);
                        $loadMoreBtn.attr('data-loaded', currentOffset);
                        
                        // Update the counter display if present
                        const $counter = $gallery.find('.aqm-load-more-counter');
                        if ($counter.length) {
                            $counter.text(currentOffset + ' of ' + totalImages + ' images loaded');
                        }
                        
                        // Hide the button if we've loaded all images
                        if (currentOffset >= totalImages) {
                            console.log('All images loaded, hiding load more button');
                            $loadMoreBtn.parent().fadeOut(300);
                        } else {
                            console.log('More images available: ' + currentOffset + ' of ' + totalImages + ' loaded');
                        }
                        
                        // Enable the button again
                        $loadMoreBtn.prop('disabled', false);
                    } else {
                        console.error('Error loading more images:', response);
                    }
                    
                    // Remove loading indicator regardless of success/failure
                    $loadingIndicator.fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.error('Status:', status);
                    console.error('Response text:', xhr.responseText);
                    
                    // Try to parse the response for more details
                    let errorMessage = 'Error loading more images. Please try again.';
                    try {
                        const jsonResponse = JSON.parse(xhr.responseText);
                        if (jsonResponse && jsonResponse.data && jsonResponse.data.message) {
                            errorMessage = jsonResponse.data.message;
                            console.error('Server error message:', errorMessage);
                            
                            // Log more details if available
                            if (jsonResponse.data.missing) {
                                console.error('Missing parameters:', jsonResponse.data.missing);
                            }
                        }
                    } catch (e) {
                        console.error('Could not parse error response:', e);
                    }
                    
                    // Show detailed error message
                    alert('Error: ' + errorMessage);
                    
                    // Log the request data that caused the error
                    console.error('Request data that caused the error:', ajaxData);
                    
                    // Enable the button again
                    $loadMoreBtn.prop('disabled', false);
                    
                    // Remove loading indicator
                    $loadingIndicator.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });
        });
    }

    /**
     * Initialize Select All functionality
     */
    function initSelectAll($gallery) {
        const $selectAllBtn = $gallery.find('.aqm-select-all-button');
        
        if ($selectAllBtn.length === 0) {
            return;
        }
        
        const $checkboxes = $gallery.find('.aqm-gallery-item input[type="checkbox"]');
        
        if ($checkboxes.length === 0) {
            // Hide button if no checkboxes
            $selectAllBtn.hide();
            return;
        }
        
        // Store original button text (without the icon)
        let originalText = $selectAllBtn.text().replace(/(^\s*\S+\s*)/, '') || 'Select All';
        const deselectText = 'Deselect All';
        
        if (originalText.trim() === '') {
            originalText = 'Select All';
        }
        
        $selectAllBtn.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Check if all checkboxes are currently checked
            const allChecked = $checkboxes.length === $checkboxes.filter(':checked').length;
            
            // Toggle checkboxes
            $checkboxes.prop('checked', !allChecked);
            
            // Toggle button text and class
            if (allChecked) {
                // If all are checked, we'll uncheck them all
                $selectAllBtn.html('<i class="fas fa-check-square" style="margin-right: 8px;"></i>' + originalText).removeClass('active partial');
                $checkboxes.closest('.aqm-gallery-item').removeClass('selected');
                
                // Hide checkboxes
                $gallery.find('.aqm-gallery-checkbox').css('opacity', '0.7');
            } else {
                // If some or none are checked, we'll check them all
                $selectAllBtn.html('<i class="fas fa-check-square" style="margin-right: 8px;"></i>' + deselectText).addClass('active').removeClass('partial');
                $checkboxes.closest('.aqm-gallery-item').addClass('selected');
                
                // Show checkboxes
                $gallery.find('.aqm-gallery-checkbox').css('opacity', '1');
            }
            
            // Trigger change event on checkboxes for any listeners
            $checkboxes.trigger('change');
        });
        
        // Initialize button state
        updateSelectAllButtonState($gallery, $selectAllBtn, $checkboxes, originalText, deselectText);
        
        // Make sure to update button text when checkboxes change
        $checkboxes.on('change', function() {
            updateSelectAllButtonState($gallery, $selectAllBtn, $checkboxes, originalText, deselectText);
        });
    }
    
    /**
     * Update the state of the Select All button based on checkbox status
     */
    function updateSelectAllButtonState($gallery, $selectAllBtn, $checkboxes, originalText, deselectText) {
        const checkedCount = $checkboxes.filter(':checked').length;
        const allChecked = $checkboxes.length === checkedCount;
        const noneChecked = checkedCount === 0;
        
        if (noneChecked) {
            $selectAllBtn.html('<i class="fas fa-check-square" style="margin-right: 8px;"></i>' + originalText).removeClass('active');
        } else if (allChecked) {
            $selectAllBtn.html('<i class="fas fa-check-square" style="margin-right: 8px;"></i>' + deselectText).addClass('active');
        } else {
            // Partial selection
            $selectAllBtn.html('<i class="fas fa-check-square" style="margin-right: 8px;"></i>' + deselectText).addClass('partial');
        }
        
        // Log status for debugging
        console.log('Checkboxes:', $checkboxes.length, 'Checked:', checkedCount);
    }
    
    /**
     * Fall back to CSS grid layout if masonry fails
     */
    function fallbackToCssGrid($gallery, $items) {
        const columns = parseInt($gallery.data('columns'), 10) || 3;
        const gap = parseInt($gallery.data('gap'), 10) || 10;
        
        // Determine column count based on screen size
        let columnCount = columns;
        if (window.innerWidth <= 980 && window.innerWidth > 767) {
            columnCount = 2;
        } else if (window.innerWidth <= 767) {
            columnCount = 1;
        }
        
        console.log('Falling back to CSS grid with ' + columnCount + ' columns');
        
        // Apply CSS grid layout
        $items.css({
            'display': 'grid',
            'grid-template-columns': 'repeat(' + columnCount + ', 1fr)',
            'grid-gap': gap + 'px',
            'width': '100%'
        });
        
        // Reset item styles for grid layout
        $items.find('.aqm-gallery-item').css({
            'width': '100%',
            'margin': '0',
            'box-sizing': 'border-box'
        });
        
        // Remove masonry-initialized class
        $gallery.removeClass('masonry-initialized');
    }
    
    /**
     * Refresh masonry layout
     */
    function refreshMasonry($gallery) {
        const $items = $gallery.find('.aqm-gallery-items');
        
        console.log('Refreshing masonry layout');
        
        // Only refresh if masonry is already initialized
        if ($gallery.hasClass('masonry-initialized') && $items.data('masonry')) {
            $items.masonry('layout');
            console.log('Masonry layout refreshed');
            // Reinitialize if not properly set up
            initMasonryLayout($gallery);
        }
    }
    
    /**
     * Initialize custom checkboxes
     */
    function initCheckboxes($gallery) {
        // Make checkboxes interact with images
        const $checkboxes = $gallery.find('.aqm-gallery-item input[type="checkbox"]');
        const $galleryItems = $gallery.find('.aqm-gallery-item');
        
        if ($checkboxes.length === 0) {
            return; // No checkboxes to initialize
        }
        
        // Make the entire gallery item clickable to toggle checkbox
        $galleryItems.on('click', function(e) {
            // Don't toggle if clicking on a link, icon, or the checkbox itself
            if (!$(e.target).is('a, a *, input[type="checkbox"], .aqm-gallery-icons, .aqm-gallery-icons *')) {
                const $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
                e.preventDefault();
                e.stopPropagation();
            }
        });
        
        // Make checkboxes always visible on hover
        $galleryItems.on('mouseenter', function() {
            $(this).find('.aqm-gallery-checkbox').css('opacity', '1');
        }).on('mouseleave', function() {
            // Keep checkbox visible if checked
            if (!$(this).find('input[type="checkbox"]').prop('checked')) {
                $(this).find('.aqm-gallery-checkbox').css('opacity', '0.7');
            }
        });
        
        // Add styling for selected items
        $checkboxes.on('change', function() {
            const $item = $(this).closest('.aqm-gallery-item');
            if ($(this).prop('checked')) {
                $item.addClass('selected');
                $(this).closest('.aqm-gallery-checkbox').css('opacity', '1');
            } else {
                $item.removeClass('selected');
                if (!$item.is(':hover')) {
                    $(this).closest('.aqm-gallery-checkbox').css('opacity', '0.7');
                }
            }
        });
        
        // Initialize the checkbox states
        $checkboxes.each(function() {
            const $item = $(this).closest('.aqm-gallery-item');
            if ($(this).prop('checked')) {
                $item.addClass('selected');
                $(this).closest('.aqm-gallery-checkbox').css('opacity', '1');
            } else {
                $(this).closest('.aqm-gallery-checkbox').css('opacity', '0.7');
            }
        });
    }

    /**
     * Initialize Download Selected functionality
     */
    function initDownloadSelected($gallery) {
        const $downloadSelectedBtn = $gallery.find('.aqm-download-selected-button');
        
        if ($downloadSelectedBtn.length === 0) {
            return;
        }
        
        $downloadSelectedBtn.on('click', function(e) {
            e.preventDefault();
            
            // Get selected image IDs
            const $selectedItems = $gallery.find('.aqm-gallery-item input[type="checkbox"]:checked').closest('.aqm-gallery-item');
            const selectedImageIds = [];
            
            $selectedItems.each(function() {
                selectedImageIds.push($(this).data('id'));
            });
            
            if (selectedImageIds.length === 0) {
                alert('Please select at least one image to download.');
                return;
            }
            
            // Start download
            downloadImages(selectedImageIds);
        });
    }

    /**
     * Initialize Download All functionality
     */
    function initDownloadAll($gallery) {
        const $downloadAllBtn = $gallery.find('.aqm-download-all-button');
        
        if ($downloadAllBtn.length === 0) {
            return;
        }
        
        $downloadAllBtn.on('click', function(e) {
            e.preventDefault();
            
            // Add loading state to button
            const originalText = $downloadAllBtn.text();
            $downloadAllBtn.text('Preparing...').prop('disabled', true).css('opacity', 0.7);
            
            // Get all image IDs
            const $allItems = $gallery.find('.aqm-gallery-item');
            const allImageIds = [];
            
            $allItems.each(function() {
                const imageId = $(this).data('id');
                if (imageId) {
                    allImageIds.push(imageId);
                }
            });
            
            if (allImageIds.length === 0) {
                alert('No images found in the gallery.');
                $downloadAllBtn.text(originalText).prop('disabled', false).css('opacity', 1);
                return;
            }
            
            // Start download
            downloadImages(allImageIds, function() {
                // Reset button state on completion
                $downloadAllBtn.text(originalText).prop('disabled', false).css('opacity', 1);
            });
        });
    }

    // Legacy Magnific Popup implementation has been removed
    // Now using Divi's built-in lightbox through the initLightbox function below
    
    /**
     * Download images via AJAX
     * @param {Array} imageIds - Array of image IDs to download
     * @param {Function} callback - Optional callback function to execute after download starts
     */
    function downloadImages(imageIds, callback) {
        if (!imageIds || !imageIds.length) {
            if (typeof callback === 'function') callback();
            return;
        }
        
        // Filter out any null or undefined values
        imageIds = imageIds.filter(function(id) {
            return id != null; // loose inequality check to catch null and undefined
        });
        
        if (imageIds.length === 0) {
            if (typeof callback === 'function') callback();
            return;
        }
        
        // Create form and submit
        const $form = $('<form>', {
            action: aqm_gallery_params.ajax_url,
            method: 'POST',
            target: '_blank'
        });
        
        // Add action
        $form.append($('<input>', {
            type: 'hidden',
            name: 'action',
            value: 'aqm_divi_gallery_download_images'
        }));
        
        // Add nonce
        $form.append($('<input>', {
            type: 'hidden',
            name: 'nonce',
            value: aqm_gallery_params.nonce
        }));
        
        // Add image IDs
        $form.append($('<input>', {
            type: 'hidden',
            name: 'image_ids',
            value: imageIds.join(',')
        }));
        
        // Append to body and submit
        $form.appendTo('body').submit().remove();
        
        // Call the callback function if provided
        if (typeof callback === 'function') {
            // Short delay to let the form submission complete
            setTimeout(callback, 500);
        }
    }

    /**
     * Initialize lightbox functionality
     */
    function initLightbox($gallery) {
        // MUCH simpler approach: since we're using et_pb_lightbox_image class directly in HTML,
        // we just need to initialize Divi's lightbox system
        console.log('Initializing Divi lightbox');
        
        // Just make sure Divi's lightbox is initialized
        if (typeof et_pb_reinit_gallery === 'function') {
            et_pb_reinit_gallery();
        } else if (typeof et_pb_init_modules === 'function') {
            et_pb_init_modules();
        } else if (window.et_core_api && typeof window.et_core_api.init_modules === 'function') {
            window.et_core_api.init_modules();
        }
        
        // Also re-initialize when all content is loaded
        $(window).on('load', function() {
            setTimeout(function() {
                console.log('Re-initializing Divi gallery on window load');
                if (typeof et_pb_reinit_gallery === 'function') {
                    et_pb_reinit_gallery();
                } else if (typeof et_pb_init_modules === 'function') {
                    et_pb_init_modules();
                }
            }, 500);
        });

        console.log('Divi lightbox initialized');
    }
    
    /**
     * Initialize lazy loading functionality for images
     * Only activates when pagination is off
     */
    function initLazyLoading($gallery) {
        // Find all lazy images in the gallery
        const $lazyImages = $gallery.find('.aqm-lazy-image');
        
        // If no lazy images or if IntersectionObserver is not supported, exit
        if ($lazyImages.length === 0 || !('IntersectionObserver' in window)) {
            // If IntersectionObserver isn't supported, just load all images normally
            $lazyImages.each(function() {
                const $img = $(this);
                if ($img.data('src')) {
                    $img.attr('src', $img.data('src'));
                }
            });
            return;
        }
        
        console.log('Initializing lazy loading for', $lazyImages.length, 'images');
        
        // Create observer with options
        const observerOptions = {
            root: null, // viewport
            rootMargin: '0px 0px 200px 0px', // 200px below viewport
            threshold: 0.1 // 10% of the item must be visible
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                // When image is visible
                if (entry.isIntersecting) {
                    const $img = $(entry.target);
                    const dataSrc = $img.data('src');
                    
                    if (dataSrc) {
                        // Create a new image to preload
                        const preloadImg = new Image();
                        
                        // When the image has loaded, swap the src
                        preloadImg.onload = function() {
                            $img.attr('src', dataSrc);
                            $img.removeAttr('data-src');
                            $img.addClass('loaded');
                        };
                        
                        // Start loading the image
                        preloadImg.src = dataSrc;
                        
                        // Stop observing this image
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, observerOptions);
        
        // Start observing each lazy image
        $lazyImages.each(function() {
            observer.observe(this);
        });
        
        // Also load images when Divi's built-in animations run
        $(window).on('et_pb_animation_starting', function() {
            $lazyImages.each(function() {
                const $img = $(this);
                if ($img.data('src') && !$img.hasClass('loaded')) {
                    $img.attr('src', $img.data('src'));
                    $img.removeAttr('data-src');
                    $img.addClass('loaded');
                    observer.unobserve(this);
                }
            });
        });
    }

})(jQuery);
