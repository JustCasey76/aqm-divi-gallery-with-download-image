<?php
/**
 * Plugin Name: AQM Divi Gallery with Download Image
 * Description: A custom Divi gallery module with load more, image download, Facebook sharing, and masonry grid layout.
 * Version: 1.0.0
 * Author: AQ Marketing
 * Author URI: https://aqmarketing.com/
 */

if (!defined('ABSPATH')) exit;

// Include and register module
function aqm_divi_gallery_init() {
    // Only proceed if Divi is active
    if (class_exists('ET_Builder_Module')) {
        require_once plugin_dir_path(__FILE__) . 'includes/module.php';
    }
}
add_action('et_builder_ready', 'aqm_divi_gallery_init');

/**
 * Register and enqueue plugin assets
 */
function aqm_divi_gallery_enqueue_assets() {
    // Enqueue CSS
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/gallery.css')) {
        wp_enqueue_style(
            'aqm-divi-gallery-style',
            plugins_url('/assets/css/gallery.css', __FILE__),
            array(),
            '1.0.1'
        );
    }
    
    // Enqueue CSS overrides - loads after main CSS to ensure priority
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/gallery-override.css')) {
        wp_enqueue_style(
            'aqm-divi-gallery-override-style',
            plugins_url('/assets/css/gallery-override.css', __FILE__),
            array('aqm-divi-gallery-style'),
            '1.0.1'
        );
    }
    
    // Enqueue mobile-specific overrides - loads with highest priority
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/mobile-override.css')) {
        wp_enqueue_style(
            'aqm-divi-gallery-mobile-style',
            plugins_url('/assets/css/mobile-override.css', __FILE__),
            array('aqm-divi-gallery-style', 'aqm-divi-gallery-override-style'),
            '1.0.2'
        );
    }
    
    // Enqueue the ultra-aggressive force-mobile.css with maximum priority
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/force-mobile.css')) {
        wp_enqueue_style(
            'aqm-divi-gallery-force-mobile',
            plugins_url('/assets/css/force-mobile.css', __FILE__),
            array('et-builder-modules-style', 'aqm-divi-gallery-style', 'aqm-divi-gallery-override-style', 'aqm-divi-gallery-mobile-style'),
            '1.0.3'
        );
    }
    
    // Add inline CSS for absolute override - this will be added at the very end of the <head>
    add_action('wp_head', function() {
        echo '<style id="aqm-force-mobile-override">
        @media only screen and (max-width: 767px) {
            .aqm-gallery-items, .et-db #et-boc .et-l .aqm-gallery-items {
                display: grid !important;
                grid-template-columns: repeat(1, 1fr) !important;
            }
            .aqm-gallery-item, .et-db #et-boc .et-l .aqm-gallery-item {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        /* Row height fix - ensure rows are only as tall as the content */
        .aqm-gallery-items {
            grid-auto-rows: min-content !important;
        }
        .aqm-gallery-item img {
            display: block !important;
            object-fit: contain !important;
            margin: 0 !important;
        }
        .aqm-gallery-item {
            margin: 0 !important;
            padding: 0 !important;
        }
        </style>';
    }, 9999);
    
    // Enqueue the row height fix CSS
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/css/row-height-fix.css')) {
        wp_enqueue_style(
            'aqm-divi-gallery-row-height-fix',
            plugins_url('/assets/css/row-height-fix.css', __FILE__),
            array('et-builder-modules-style', 'aqm-divi-gallery-style', 'aqm-divi-gallery-override-style', 'aqm-divi-gallery-mobile-style', 'aqm-divi-gallery-force-mobile'),
            '1.0.0'
        );
    }
    
    // Enqueue Masonry (built into WordPress)
    wp_enqueue_script('masonry');
    
    // Enqueue imagesLoaded library
    wp_enqueue_script(
        'imagesloaded',
        'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js',
        array('jquery'),
        '5.0.0',
        true
    );
    
    // Enqueue JS
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/js/gallery.js')) {
        wp_enqueue_script(
            'aqm-divi-gallery-script',
            plugins_url('/assets/js/gallery.js', __FILE__),
            array('jquery', 'masonry', 'imagesloaded'),
            '1.0.1',
            true
        );
        
        // Localize script with AJAX URL and nonce
        wp_localize_script(
            'aqm-divi-gallery-script',
            'aqm_gallery_params',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aqm_divi_gallery_nonce')
            )
        );
    }
    
    // Enqueue the simple lightbox script
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/js/simple-lightbox.js')) {
        wp_enqueue_script(
            'aqm-divi-gallery-simple-lightbox',
            plugins_url('/assets/js/simple-lightbox.js', __FILE__),
            array('jquery', 'aqm-divi-gallery-script'),
            '1.0.0',
            true
        );
    }
    
    // Debugging - add this to wp-config.php to see AJAX errors
    if (WP_DEBUG) {
        error_log('AQM Gallery - AJAX URL: ' . admin_url('admin-ajax.php'));
    }
    
    // Enqueue the mobile-fix JS last with highest priority (999)
    if (file_exists(plugin_dir_path(__FILE__) . 'assets/js/mobile-fix.js')) {
        wp_enqueue_script(
            'aqm-divi-gallery-mobile-fix',
            plugins_url('/assets/js/mobile-fix.js', __FILE__),
            array('jquery', 'aqm-divi-gallery-script'),
            '1.0.0',
            true
        );
        // Set the highest possible priority to ensure it runs last
        add_action('wp_footer', function() {
            ?>
            <script type="text/javascript">
            /* Emergency fix for mobile columns */
            (function($) {
                $(window).on('load', function() {
                    setTimeout(function() {
                        if (window.innerWidth < 768) {
                            $('.aqm-gallery-items').css({
                                'display': 'grid',
                                'grid-template-columns': 'repeat(1, 1fr) !important'
                            });
                            $('.aqm-gallery-item').css({
                                'width': '100%',
                                'max-width': '100%'
                            });
                        }
                    }, 1000);
                });
            })(jQuery);
            </script>
            <?php
        }, 999);
    }
}
add_action('wp_enqueue_scripts', 'aqm_divi_gallery_enqueue_assets');

/**
 * Add AJAX handler for downloading zip file
 */
function aqm_divi_gallery_download_zip() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aqm_divi_gallery_nonce')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }
    
    // Get the image IDs
    if (!isset($_POST['image_ids']) || empty($_POST['image_ids'])) {
        wp_send_json_error('No images selected');
        exit;
    }
    
    $image_ids = $_POST['image_ids'];
    $zipname = 'gallery-download-' . time() . '.zip';
    $zip_path = wp_upload_dir()['basedir'] . '/' . $zipname;
    $zip_url = wp_upload_dir()['baseurl'] . '/' . $zipname;
    
    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
        wp_send_json_error('Cannot create zip file');
        exit;
    }
    
    foreach ($image_ids as $image_id) {
        $file_path = get_attached_file($image_id);
        if ($file_path && file_exists($file_path)) {
            $filename = basename($file_path);
            $zip->addFile($file_path, $filename);
        }
    }
    
    $zip->close();
    
    // Return the URL to the zip file
    wp_send_json_success(array('url' => $zip_url));
    exit;
}
add_action('wp_ajax_aqm_divi_gallery_download_zip', 'aqm_divi_gallery_download_zip');
add_action('wp_ajax_nopriv_aqm_divi_gallery_download_zip', 'aqm_divi_gallery_download_zip');

/**
 * Add AJAX handler for getting image URLs
 */
function aqm_divi_gallery_get_image_urls() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aqm_divi_gallery_nonce')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }
    
    // Get the image IDs
    if (!isset($_POST['image_ids']) || empty($_POST['image_ids'])) {
        wp_send_json_error('No images selected');
        exit;
    }
    
    $image_ids = $_POST['image_ids'];
    $urls = array();
    
    foreach ($image_ids as $image_id) {
        $url = wp_get_attachment_url($image_id);
        if ($url) {
            $urls[$image_id] = $url;
        }
    }
    
    wp_send_json_success($urls);
    exit;
}
add_action('wp_ajax_aqm_divi_gallery_get_image_urls', 'aqm_divi_gallery_get_image_urls');
add_action('wp_ajax_nopriv_aqm_divi_gallery_get_image_urls', 'aqm_divi_gallery_get_image_urls');

/**
 * Add AJAX handler for downloading a single image
 */
function aqm_divi_gallery_download_image() {
    // Check nonce for security
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'aqm_divi_gallery_nonce')) {
        wp_die('Invalid nonce');
    }
    
    // Get the image ID
    if (!isset($_GET['attachment_id']) || empty($_GET['attachment_id'])) {
        wp_die('No image selected');
    }
    
    $attachment_id = intval($_GET['attachment_id']);
    $file_path = get_attached_file($attachment_id);
    
    if ($file_path && file_exists($file_path)) {
        $filename = basename($file_path);
        
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Output file
        readfile($file_path);
        exit;
    } else {
        wp_die('Image not found');
    }
}
add_action('wp_ajax_aqm_divi_gallery_download_image', 'aqm_divi_gallery_download_image');
add_action('wp_ajax_nopriv_aqm_divi_gallery_download_image', 'aqm_divi_gallery_download_image');

/**
 * Get all child folder IDs for a parent folder
 * 
 * This helper function recursively retrieves all child folder IDs of a parent folder
 * 
 * @param int $parent_id The parent folder ID
 * @return array Array of child folder IDs
 */
function aqm_get_all_child_folders($parent_id) {
    $child_ids = array();
    
    // Get immediate children
    $child_terms = get_terms(array(
        'taxonomy' => 'folder',
        'hide_empty' => false,
        'parent' => $parent_id,
        'fields' => 'ids', // Only retrieve the term IDs
    ));
    
    if (is_wp_error($child_terms) || empty($child_terms)) {
        return $child_ids;
    }
    
    // Add immediate children
    $child_ids = $child_terms;
    
    // Recursively add grandchildren
    foreach ($child_terms as $child_id) {
        $grandchildren = aqm_get_all_child_folders($child_id);
        if (!empty($grandchildren)) {
            $child_ids = array_merge($child_ids, $grandchildren);
        }
    }
    
    return $child_ids;
}

/**
 * AJAX handler for loading more images
 */
function aqm_divi_gallery_load_more() {
    // Enable error logging for debugging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('AQM Gallery - AJAX request received');
        error_log('AQM Gallery - POST data: ' . print_r($_POST, true));
    }
    
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'aqm_divi_gallery_nonce')) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AQM Gallery - Nonce verification failed');
            if (!isset($_POST['nonce'])) {
                error_log('AQM Gallery - Nonce is missing');
            } else {
                error_log('AQM Gallery - Invalid nonce provided: ' . $_POST['nonce']);
            }
        }
        wp_send_json_error([
            'message' => 'Invalid security token',
            'nonce_provided' => isset($_POST['nonce']) ? 'yes' : 'no'
        ]);
    }
    
    // Required parameters
    if (!isset($_POST['offset']) || !isset($_POST['count']) || 
        !isset($_POST['layout_type']) || !isset($_POST['columns']) || !isset($_POST['gap'])) {
        
        // Detailed error for debugging
        $missing = [];
        if (!isset($_POST['offset'])) $missing[] = 'offset';
        if (!isset($_POST['count'])) $missing[] = 'count';
        if (!isset($_POST['layout_type'])) $missing[] = 'layout_type';
        if (!isset($_POST['columns'])) $missing[] = 'columns';
        if (!isset($_POST['gap'])) $missing[] = 'gap';
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AQM Gallery - Missing required parameters: ' . implode(', ', $missing));
            error_log('AQM Gallery - Received parameters: ' . json_encode(array_keys($_POST)));
        }
        
        wp_send_json_error([            
            'message' => 'Missing required parameters',
            'missing' => $missing,
            'received' => array_keys($_POST) // Just send the keys for security reasons
        ]);
        return; // Make sure we exit after sending JSON response
    }
    
    // Get common parameters
    $offset = intval($_POST['offset']);
    $count = intval($_POST['count']);
    $layout_type = sanitize_text_field($_POST['layout_type']);
    $columns = intval($_POST['columns']);
    $gap = intval($_POST['gap']);
    $show_lightbox = isset($_POST['show_lightbox']) ? sanitize_text_field($_POST['show_lightbox']) : 'on';
    $show_download = isset($_POST['show_download']) ? sanitize_text_field($_POST['show_download']) : 'on';
    $show_facebook = isset($_POST['show_facebook']) ? sanitize_text_field($_POST['show_facebook']) : 'on';
    $show_email = isset($_POST['show_email']) ? sanitize_text_field($_POST['show_email']) : 'on';
    $email_subject = isset($_POST['email_subject']) ? sanitize_text_field($_POST['email_subject']) : '';
    $email_body = isset($_POST['email_body']) ? sanitize_text_field($_POST['email_body']) : '';
    $show_download_selected = isset($_POST['show_download_selected']) ? sanitize_text_field($_POST['show_download_selected']) : 'on';
    
    // Icon settings
    $lightbox_icon = isset($_POST['lightbox_icon']) ? sanitize_text_field($_POST['lightbox_icon']) : 'e0f8||divi||400';
    $download_icon = isset($_POST['download_icon']) ? sanitize_text_field($_POST['download_icon']) : 'e092||divi||400';
    $facebook_icon = isset($_POST['facebook_icon']) ? sanitize_text_field($_POST['facebook_icon']) : 'e093||divi||400';
    $email_icon = isset($_POST['email_icon']) ? sanitize_text_field($_POST['email_icon']) : 'e0be||divi||400';
    
    // Get the image IDs based on gallery source
    $image_ids = [];
    $gallery_source = isset($_POST['gallery_source']) ? sanitize_text_field($_POST['gallery_source']) : 'manual';
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('AQM Gallery - Gallery source: ' . $gallery_source);
    }
    
    if ($gallery_source === 'manual' || $gallery_source === 'folder') {
        // For both manual and folder sources, we need gallery_images to be set
        if (!isset($_POST['gallery_images']) || empty($_POST['gallery_images'])) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('AQM Gallery - Missing gallery_images parameter');
            }
            
            wp_send_json_error([
                'message' => 'Missing gallery_images parameter',
                'gallery_source' => $gallery_source,
                'params_received' => array_keys($_POST)
            ]);
            return;
        }
        
        $gallery_images = sanitize_text_field($_POST['gallery_images']);
        $all_image_ids = explode(',', $gallery_images);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AQM Gallery - Gallery images string: ' . substr($gallery_images, 0, 100) . '...');
            error_log('AQM Gallery - Total image IDs: ' . count($all_image_ids));
            error_log('AQM Gallery - Offset: ' . $offset . ', Count: ' . $count);
        }
        
        // Remove any empty items
        $all_image_ids = array_filter($all_image_ids, function($id) {
            return !empty($id) && is_numeric($id);
        });
        
        // Limit to requested batch
        $image_ids = array_slice($all_image_ids, $offset, $count);
    } else { // folder-based gallery
        // Check if folder_ids is set
        if (!isset($_POST['folder_ids'])) {
            wp_send_json_error([
                'message' => 'Missing folder_ids parameter for folder-based gallery',
                'received' => $_POST
            ]);
        }
        
        $folder_ids = sanitize_text_field($_POST['folder_ids']);
        $include_subfolders = isset($_POST['include_subfolders']) ? sanitize_text_field($_POST['include_subfolders']) : 'off';
        $sorting = isset($_POST['sorting']) ? sanitize_text_field($_POST['sorting']) : 'date_desc';
        
        // Get selected folder IDs
        $selected_folder_ids = explode(',', $folder_ids);
        $selected_folder_ids = array_map('intval', $selected_folder_ids);
        $selected_folder_ids = array_filter($selected_folder_ids, function($id) {
            return $id > 0;
        });
        
        if (empty($selected_folder_ids)) {
            wp_send_json_error([
                'message' => 'No valid folder IDs provided',
                'received' => $_POST
            ]);
        }
        
        // Build array of all folder IDs to fetch (including subfolders if needed)
        $all_folder_ids = $selected_folder_ids;
        
        // Include subfolders if enabled
        if ($include_subfolders === 'on') {
            foreach ($selected_folder_ids as $parent_id) {
                $child_folders = aqm_get_all_child_folders($parent_id);
                if (!empty($child_folders)) {
                    $all_folder_ids = array_merge($all_folder_ids, $child_folders);
                }
            }
            
            // Remove duplicates
            $all_folder_ids = array_unique($all_folder_ids);
        }
        
        // Get attachments from all selected folders
        $all_image_ids = [];
        foreach ($all_folder_ids as $folder_id) {
            $folder_attachments = get_objects_in_term($folder_id, 'folder');
            if (!is_wp_error($folder_attachments) && !empty($folder_attachments)) {
                $all_image_ids = array_merge($all_image_ids, $folder_attachments);
            }
        }
        
        // Remove duplicates (an image could be in multiple folders)
        $all_image_ids = array_unique($all_image_ids);
        
        // Filter to include only image attachments and apply sorting
        if (!empty($all_image_ids)) {
            global $wpdb;
            
            // Convert array to comma-separated string for SQL query
            $ids_str = implode(',', array_map('intval', $all_image_ids));
            
            // Only proceed if we have IDs
            if (!empty($ids_str)) {
                // Prepare order by clause based on sorting setting
                $order_by = 'post_date DESC'; // default
                
                switch ($sorting) {
                    case 'date_asc':
                        $order_by = 'post_date ASC';
                        break;
                    case 'title_asc':
                        $order_by = 'post_title ASC';
                        break;
                    case 'title_desc':
                        $order_by = 'post_title DESC';
                        break;
                    case 'random':
                        $order_by = 'RAND()';
                        break;
                    default: // date_desc
                        $order_by = 'post_date DESC';
                }
                
                // Query to get only image attachments with proper sorting
                $query = "SELECT ID FROM {$wpdb->posts} 
                    WHERE ID IN ({$ids_str}) 
                    AND post_type = 'attachment' 
                    AND post_mime_type LIKE 'image/%' 
                    ORDER BY {$order_by}";
                
                // Get all image IDs
                $all_image_ids = $wpdb->get_col($query);
                
                // Limit to requested batch
                $image_ids = array_slice($all_image_ids, $offset, $count);
            }
        }
    }
    
    // Output buffer to store HTML
    ob_start();
    
    // Generate HTML for each image
    foreach ($image_ids as $image_id) {
        $image_id = intval($image_id);
        if ($image_id <= 0) continue;
        
        // Use medium size for thumbnails and full size for lightbox
        $image_url = wp_get_attachment_image_url($image_id, 'medium');
        $full_image_url = wp_get_attachment_image_url($image_id, 'full');
        
        // If we couldn't get image URLs, log error and continue
        if (!$image_url || !$full_image_url) {
            error_log('AQM Gallery - Failed to get image URLs for ID: ' . $image_id);
            continue;
        }
        
        $image_title = get_the_title($image_id);
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        // Prepare classes
        $class = 'aqm-gallery-item';
        $style = '';
        
        // Force column width through inline style on each gallery item
        $style = 'box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%;';  // Important base styles
        
        // Output the gallery item
        echo sprintf(
            '<div class="%1$s" data-id="%2$s" style="%3$s">', 
            esc_attr($class),
            esc_attr($image_id),
            esc_attr($style)
        );
        
        // Create a wrapper for both image and overlay to ensure proper positioning
        echo '<div class="aqm-gallery-item-inner" style="width: 100%; height: 100%; position: relative;">';
        
        // Image with fixed height container for consistent sizing
        echo sprintf(
            '<div class="aqm-gallery-image" style="width: 100%%; height: 250px; overflow: hidden;">
                <img src="%1$s" alt="%2$s" title="%3$s" style="width: 100%%; height: 100%%; object-fit: cover; display: block;" />
            </div>',
            esc_url($image_url),
            esc_attr($image_alt),
            esc_attr($image_title)
        );
        
        // Overlay
        echo '<div class="aqm-gallery-overlay">';
        
        // Checkbox for selection
        if ($show_download == 'on' && $show_download_selected == 'on') {
            echo '<div class="aqm-gallery-checkbox"><input type="checkbox" id="checkbox-' . esc_attr($image_id) . '" /><label for="checkbox-' . esc_attr($image_id) . '"></label></div>';
        }
        
        // Icons
        echo '<div class="aqm-gallery-icons">';
        
        // Parse icons
        $lightbox_icon_parts = explode('||', $lightbox_icon);
        $download_icon_parts = explode('||', $download_icon);
        $facebook_icon_parts = explode('||', $facebook_icon);
        $email_icon_parts = explode('||', $email_icon);
        
        // Lightbox
        if ($show_lightbox == 'on') {
            // Using Font Awesome instead of Divi icons for consistency
            $icon_class = 'fas fa-search'; // Default search/magnifying glass icon
            
            echo sprintf(
                '<a href="%1$s" class="aqm-gallery-icon aqm-gallery-lightbox" title="%2$s">
                    <i class="%3$s"></i>
                </a>',
                esc_url($full_image_url),
                esc_attr($image_title),
                $icon_class
            );
        }
        
        // Download
        if ($show_download == 'on') {
            // Using Font Awesome instead of Divi icons for consistency
            $icon_class = 'fas fa-download'; // Download icon
            
            $download_url = add_query_arg(array(
                'action' => 'aqm_divi_gallery_download_image',
                'attachment_id' => $image_id,
                'nonce' => wp_create_nonce('aqm_divi_gallery_nonce')
            ), admin_url('admin-ajax.php'));
            
            echo sprintf(
                '<a href="%1$s" class="aqm-gallery-icon aqm-gallery-download" download>
                    <i class="%2$s"></i>
                </a>',
                esc_url($download_url),
                $icon_class
            );
        }
        
        // Facebook
        if ($show_facebook == 'on') {
            // Using Font Awesome instead of Divi icons for consistency
            $icon_class = 'fab fa-facebook-f'; // Facebook icon
            
            $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($full_image_url);
            
            echo sprintf(
                '<a href="%1$s" class="aqm-gallery-icon aqm-gallery-facebook" target="_blank">
                    <i class="%2$s"></i>
                </a>',
                esc_url($facebook_url),
                $icon_class
            );
        }
        
        // Email
        if ($show_email == 'on') {
            // Using Font Awesome instead of Divi icons for consistency
            $icon_class = 'fas fa-envelope'; // Email icon
            
            // Format email parameters
            $email_subject = !empty($email_subject) ? $email_subject : 'Check out this image';
            $email_body = !empty($email_body) ? $email_body : 'I thought you might like this image: ' . $full_image_url;
            
            $email_url = 'mailto:?subject=' . rawurlencode($email_subject) . '&body=' . rawurlencode($email_body);
            
            echo sprintf(
                '<a href="%1$s" class="aqm-gallery-icon aqm-gallery-email">
                    <i class="%2$s"></i>
                </a>',
                esc_url($email_url),
                $icon_class
            );
        }
        
        // Close icons
        echo '</div>';
        
        // Close overlay
        echo '</div>';
        
        // Close item inner wrapper
        echo '</div>';
        
    }
    
    // Get the output
    $output = ob_get_clean();
    
    // Calculate the new offset for the next batch
    $new_offset = $offset + count($image_ids);
    
    // Get total count of images - this will vary based on gallery source
    $total_count = 0;
    if ($gallery_source === 'manual') {
        // For manual gallery, use the gallery_images parameter
        $all_images = explode(',', sanitize_text_field($_POST['gallery_images']));
        $total_count = count($all_images);
    } else {
        // For folder gallery, we need to count the total filtered images
        // This is the full count of images that match the folder criteria
        global $wpdb;
        $all_image_ids = [];
        
        // Use the same query logic as before but don't limit the results
        // This gives us the total count of available images
        $total_count = $new_offset; // Default to current position if we can't determine
        
        // If we have the all_image_ids from earlier query processing, use that count
        if (isset($all_image_ids) && is_array($all_image_ids)) {
            $total_count = count($all_image_ids);
        }
    }
    
    // Send success response with HTML
    wp_send_json_success([
        'html' => $output,
        'offset' => $new_offset, // Update the offset for next batch
        'total' => $total_count, // Total count of images
        'loaded' => $new_offset, // How many we've loaded so far
        'remaining' => max(0, $total_count - $new_offset) // How many are left to load
    ]);
}

// Register AJAX hooks for load more functionality
add_action('wp_ajax_aqm_divi_gallery_load_more', 'aqm_divi_gallery_load_more');
add_action('wp_ajax_nopriv_aqm_divi_gallery_load_more', 'aqm_divi_gallery_load_more');
