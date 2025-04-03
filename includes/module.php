<?php
/**
 * AQM Divi Gallery Module - Compatible Version
 */

if (!defined('ABSPATH')) exit;

class AQM_Gallery_Module extends ET_Builder_Module {
    function init() {
        $this->name = esc_html__('AQM Gallery with Download', 'aqm-divi-gallery');
        $this->plural = esc_html__('AQM Galleries with Download', 'aqm-divi-gallery');
        $this->slug = 'et_pb_aqm_gallery';
        $this->vb_support = 'on';
        $this->main_css_element = '%%order_class%% .aqm-gallery-container';
        
        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__('Content', 'aqm-divi-gallery'),
                    'display' => esc_html__('Display Settings', 'aqm-divi-gallery'),
                    'elements' => esc_html__('Elements', 'aqm-divi-gallery'),
                    'icons' => esc_html__('Icon Settings', 'aqm-divi-gallery'),
                ),
            ),
            'advanced' => array(
                'toggles' => array(
                    'button_styles' => esc_html__('Button Styles', 'aqm-divi-gallery'),
                    'overlay' => esc_html__('Overlay & Icons', 'aqm-divi-gallery'),
                ),
            ),
        );
    }
    
    /**
     * Get the current URL
     * 
     * Helper method to get the current URL for pagination links
     * 
     * @return string The current URL
     */
    private static function get_current_url() {
        global $wp;
        $current_url = add_query_arg($wp->query_vars, home_url($wp->request));
        return $current_url;
    }
    
    /**
     * Add dynamic inline CSS for all galleries
     * 
     * @return string CSS rules for the gallery
     */
    private function get_gallery_css() {
        // Add dynamic inline CSS for all galleries
        $inline_css = "
            /* Gallery icon spacing */
            .aqm-gallery-container .aqm-gallery-item-overlay .aqm-gallery-icons {
                gap: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            /* Ensure proper Divi icon styling */
            .aqm-gallery-container .aqm-gallery-lightbox .et-pb-icon,
            .aqm-gallery-container .aqm-gallery-download .et-pb-icon,
            .aqm-gallery-container .aqm-gallery-facebook .et-pb-icon,
            .aqm-gallery-container .aqm-gallery-email .et-pb-icon {
                font-family: 'ETmodules' !important;
                font-weight: normal !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                line-height: 1 !important;
                box-sizing: border-box !important;
                transition: all 0.3s ease !important;
            }
        ";
        
        return $inline_css;
    }
    
    function get_fields() {
        return array(

            'gallery_images' => array(
                'label' => esc_html__('Gallery Images', 'aqm-divi-gallery'),
                'type' => 'upload-gallery',
                'option_category' => 'basic_option',
                'toggle_slug' => 'main_content',
                'description' => esc_html__('Upload images to include in the gallery.', 'aqm-divi-gallery'),
            ),
            'sorting' => array(
                'label' => esc_html__('Image Sorting', 'aqm-divi-gallery'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'date_desc' => esc_html__('Date (Newest First)', 'aqm-divi-gallery'),
                    'date_asc' => esc_html__('Date (Oldest First)', 'aqm-divi-gallery'),
                    'title_asc' => esc_html__('Title (A-Z)', 'aqm-divi-gallery'),
                    'title_desc' => esc_html__('Title (Z-A)', 'aqm-divi-gallery'),
                    'random' => esc_html__('Random', 'aqm-divi-gallery'),
                ),
                'default' => 'date_desc',
                'toggle_slug' => 'main_content',
                'description' => esc_html__('Choose how to sort the images in your gallery.', 'aqm-divi-gallery'),
            ),
            'initial_count' => array(
                'label' => esc_html__('Initial Images to Show', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'default' => '8',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '100',
                    'step' => '1',
                ),
                'description' => esc_html__('Set the number of images to display initially.', 'aqm-divi-gallery'),
            ),
            'load_more_count' => array(
                'label' => esc_html__('Images per Load', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'default' => '6',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '20',
                    'step' => '1',
                ),
                'description' => esc_html__('Set the number of images to load when clicking the Load More button.', 'aqm-divi-gallery'),
                'show_if' => array('pagination_type' => 'load_more'),
            ),
            'pagination_type' => array(
                'label' => esc_html__('Pagination Type', 'aqm-divi-gallery'),
                'type' => 'select',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'options' => array(
                    'none' => esc_html__('None (Show All)', 'aqm-divi-gallery'),
                    'load_more' => esc_html__('Load More Button', 'aqm-divi-gallery'),
                    'numbered' => esc_html__('Numbered Pagination', 'aqm-divi-gallery'),
                ),
                'default' => 'none',
                'description' => esc_html__('Choose how to paginate the gallery.', 'aqm-divi-gallery'),
            ),
            'images_per_page' => array(
                'label' => esc_html__('Images per Page', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'default' => '12',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '48',
                    'step' => '1',
                ),
                'description' => esc_html__('Set the number of images to display per page when using pagination.', 'aqm-divi-gallery'),
                'show_if' => array('pagination_type' => 'numbered'),
            ),
            'layout_type' => array(
                'label' => esc_html__('Layout Type', 'aqm-divi-gallery'),
                'type' => 'select',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'options' => array(
                    'grid' => esc_html__('Grid (Even Columns)', 'aqm-divi-gallery'),
                    'masonry' => esc_html__('Masonry (Legacy - Variable Height)', 'aqm-divi-gallery'),
                    'standard' => esc_html__('Standard (Legacy - Regular Grid)', 'aqm-divi-gallery'),
                ),
                'default' => 'grid',
                'description' => esc_html__('Choose the layout type for your gallery.', 'aqm-divi-gallery'),
            ),
            'columns' => array(
                'label' => esc_html__('Columns', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'default' => '3',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '12',
                    'step' => '1',
                ),
                'description' => esc_html__('Set the number of columns in the gallery.', 'aqm-divi-gallery'),
            ),
            'gap' => array(
                'label' => esc_html__('Gap Between Images', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'basic_option',
                'toggle_slug' => 'display',
                'default' => '10',
                'range_settings' => array(
                    'min' => '0',
                    'max' => '100',
                    'step' => '1',
                ),
                'description' => esc_html__('Set the gap between images in pixels.', 'aqm-divi-gallery'),
            ),
            'show_lightbox' => array(
                'label' => esc_html__('Enable Lightbox', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off'  => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'description' => esc_html__('Enable lightbox for the gallery images.', 'aqm-divi-gallery'),
            ),
            'show_download' => array(
                'label' => esc_html__('Enable Download', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'description' => esc_html__('Enable download button for images.', 'aqm-divi-gallery'),
            ),
            'show_facebook' => array(
                'label' => esc_html__('Enable Facebook Share', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'off',
                'description' => esc_html__('Enable Facebook sharing for images.', 'aqm-divi-gallery'),
            ),
            'show_email' => array(
                'label' => esc_html__('Enable Email Share', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'off',
                'description' => esc_html__('Enable email sharing for images.', 'aqm-divi-gallery'),
            ),
            'email_subject' => array(
                'label' => esc_html__('Email Subject', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'default' => 'Check out this image',
                'description' => esc_html__('Subject line for email sharing.', 'aqm-divi-gallery'),
                'show_if' => array('show_email' => 'on'),
            ),
            'email_body' => array(
                'label' => esc_html__('Email Body Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'default' => 'I thought you might like this image:',
                'description' => esc_html__('Body text for email sharing.', 'aqm-divi-gallery'),
                'show_if' => array('show_email' => 'on'),
            ),
            'show_select_all' => array(
                'label' => esc_html__('Show Select All Button', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'description' => esc_html__('Show a button to select all images.', 'aqm-divi-gallery'),
                'show_if' => array('show_download' => 'on'),
            ),
            'show_download_selected' => array(
                'label' => esc_html__('Show Download Selected Button', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'description' => esc_html__('Show a button to download selected images.', 'aqm-divi-gallery'),
                'show_if' => array('show_download' => 'on'),
            ),
            'show_download_all' => array(
                'label' => esc_html__('Show Download All Button', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'description' => esc_html__('Show a button to download all images.', 'aqm-divi-gallery'),
                'show_if' => array('show_download' => 'on'),
            ),
            'load_more_text' => array(
                'label' => esc_html__('Load More Button Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'default' => 'Load More',
                'description' => esc_html__('Text for the load more button.', 'aqm-divi-gallery'),
            ),
            'select_all_text' => array(
                'label' => esc_html__('Select All Button Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'default' => 'Select All',
                'description' => esc_html__('Text for the select all button.', 'aqm-divi-gallery'),
                'show_if' => array('show_select_all' => 'on'),
            ),
            'download_selected_text' => array(
                'label' => esc_html__('Download Selected Button Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'default' => 'Download Selected',
                'description' => esc_html__('Text for the download selected button.', 'aqm-divi-gallery'),
                'show_if' => array('show_download_selected' => 'on'),
            ),
            'download_all_text' => array(
                'label' => esc_html__('Download All Button Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'toggle_slug' => 'elements',
                'default' => 'Download All',
                'description' => esc_html__('Text for the download all button.', 'aqm-divi-gallery'),
                'show_if' => array('show_download_all' => 'on'),
            ),
            // Icon selection options
            'lightbox_icon' => array(
                'label' => esc_html__('Lightbox Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'basic_option',
                'toggle_slug' => 'icons',
                'default' => 'e0f8||divi||400',
                'description' => esc_html__('Choose an icon to use for the lightbox button.', 'aqm-divi-gallery'),
                'show_if' => array('show_lightbox' => 'on'),
                'mobile_options' => false,
                'hover' => 'tabs',
            ),
            'download_icon' => array(
                'label' => esc_html__('Download Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'basic_option',
                'toggle_slug' => 'icons',
                'default' => 'e092||divi||400',
                'description' => esc_html__('Choose an icon to use for the download button.', 'aqm-divi-gallery'),
                'show_if' => array('show_download' => 'on'),
                'mobile_options' => false,
                'hover' => 'tabs',
            ),
            'facebook_icon' => array(
                'label' => esc_html__('Facebook Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'basic_option',
                'toggle_slug' => 'icons',
                'default' => 'e093||divi||400',
                'description' => esc_html__('Choose an icon to use for the Facebook share button.', 'aqm-divi-gallery'),
                'show_if' => array('show_facebook' => 'on'),
                'mobile_options' => false,
                'hover' => 'tabs',
            ),
            'email_icon' => array(
                'label' => esc_html__('Email Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'basic_option',
                'toggle_slug' => 'icons',
                'default' => 'e0be||divi||400',
                'description' => esc_html__('Choose an icon to use for the email share button.', 'aqm-divi-gallery'),
                'show_if' => array('show_email' => 'on'),
                'mobile_options' => false,
                'hover' => 'tabs',
            ),
            'icon_size' => array(
                'label' => esc_html__('Icon Size', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'layout',
                'toggle_slug' => 'icons',
                'default' => '24px',
                'default_unit' => 'px',
                'default_on_front' => '24px',
                'range_settings' => array(
                    'min' => '16',
                    'max' => '48',
                    'step' => '1',
                ),
                'validate_unit' => true,
                'description' => esc_html__('Adjust the size of the icons.', 'aqm-divi-gallery'),
            ),
            'icon_circle_size' => array(
                'label' => esc_html__('Icon Circle Size', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'layout',
                'toggle_slug' => 'icons',
                'default' => '40px',
                'default_unit' => 'px',
                'default_on_front' => '40px',
                'range_settings' => array(
                    'min' => '24',
                    'max' => '64',
                    'step' => '1',
                ),
                'validate_unit' => true,
                'description' => esc_html__('Adjust the size of the icon circles.', 'aqm-divi-gallery'),
            ),
        );
    }
    
    function render($attrs, $render_slug, $content = null) {
        // Parse attributes
        $gallery_images = $this->props['gallery_images'];
        $pagination_type = isset($this->props['pagination_type']) ? $this->props['pagination_type'] : 'none';
        $initial_count = $pagination_type === 'load_more' ? intval($this->props['initial_count']) : 0;
        $load_more_count = $pagination_type === 'load_more' ? intval($this->props['load_more_count']) : 6;
        $images_per_page = $pagination_type === 'numbered' ? intval($this->props['images_per_page']) : 12;
        $layout_type = isset($this->props['layout_type']) ? $this->props['layout_type'] : 'masonry';
        $columns = intval($this->props['columns']);
        $gap = intval($this->props['gap']);
        $show_lightbox = $this->props['show_lightbox'];
        $show_download = $this->props['show_download'];
        $show_facebook = $this->props['show_facebook'];
        $show_email = $this->props['show_email'];
        
        // Get icon settings
        $icon_size = isset($this->props['icon_size']) ? $this->props['icon_size'] : '24px';
        $icon_circle_size = isset($this->props['icon_circle_size']) ? $this->props['icon_circle_size'] : '40px';
        $email_subject = $this->props['email_subject'];
        $email_body = $this->props['email_body'];
        $show_select_all = $this->props['show_select_all'];
        $show_download_selected = $this->props['show_download_selected'];
        $show_download_all = $this->props['show_download_all'];
        $load_more_text = $this->props['load_more_text'];
        $select_all_text = $this->props['select_all_text'];
        $download_selected_text = $this->props['download_selected_text'];
        $download_all_text = $this->props['download_all_text'];
        
        // Get current page for numbered pagination
        $current_page = isset($_GET['aqm_gallery_page']) ? intval($_GET['aqm_gallery_page']) : 1;
        if ($current_page < 1) $current_page = 1;
        
        // Get icon settings
        $lightbox_icon = isset($this->props['lightbox_icon']) ? $this->props['lightbox_icon'] : 'e0f8||divi||400';
        $download_icon = isset($this->props['download_icon']) ? $this->props['download_icon'] : 'e092||divi||400';
        $facebook_icon = isset($this->props['facebook_icon']) ? $this->props['facebook_icon'] : 'e093||divi||400';
        $email_icon = isset($this->props['email_icon']) ? $this->props['email_icon'] : 'e0be||divi||400';
        
        // Get image IDs
        $image_ids = array();
        
        // Get sorting setting for gallery
        $sorting = isset($this->props['sorting']) ? $this->props['sorting'] : 'date_desc';
        
        // Get manually selected images
        if (!empty($gallery_images)) {
            $gallery_images_array = explode(',', $gallery_images);
            foreach ($gallery_images_array as $image_id) {
                $image_ids[] = $image_id;
            }
            
            // Sort manual gallery images if not using the default order
            if ($sorting !== 'default' && !empty($image_ids)) {
                global $wpdb;
                $ids_str = implode(',', array_map('intval', $image_ids));
                
                // Prepare order by clause based on sorting setting
                $order_by = 'post_date DESC'; // default fallback
                
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
                    case 'date_desc':
                    default:
                        $order_by = 'post_date DESC';
                        break;
                }
                
                // Query to get sorted image IDs
                $query = "SELECT ID FROM {$wpdb->posts} WHERE ID IN ({$ids_str}) AND post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY {$order_by}";
                $sorted_ids = $wpdb->get_col($query);
                
                // Only use sorted IDs if we got results back
                if (!empty($sorted_ids)) {
                    $image_ids = $sorted_ids;
                }
            }
        }
        
        // Ensure the script is enqueued
        wp_enqueue_script('aqm-divi-gallery-script');
        
        // Add AJAX URL for the JavaScript
        wp_localize_script('aqm-divi-gallery-script', 'aqm_gallery_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aqm_divi_gallery_nonce')
        ));
        
        // Add required scripts and styles
        wp_enqueue_style('aqm-divi-gallery-style');
        
        // Enqueue Font Awesome for more reliable icons
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
        
        // Make sure Masonry is enqueued
        wp_enqueue_script('masonry');
        
        // Make sure imagesLoaded is enqueued
        wp_enqueue_script('imagesloaded');

        // Set up inline CSS with icon styling and column fixes
        // Store the gallery ID for reference in CSS
        $this->render_count = isset($this->render_count) ? $this->render_count + 1 : 1;
        $gallery_id = 'aqm-gallery-' . $this->render_count . '-' . rand(1000, 9999);
        
        $inline_css = "
            .aqm-gallery-icons a {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                margin: 0 5px;
                color: #ffffff;
                text-align: center;
                background-color: rgba(0,0,0,0.7);
                transition: all 0.3s ease;
            }
            .aqm-gallery-icons a:hover {
                background-color: #000000;
                transform: scale(1.1);
            }
            
            .aqm-gallery-icons a i {
                font-size: 16px;
            }
            
            /* Column specific styles - using the gallery ID for higher specificity */
            #{$gallery_id} {
                --gallery-columns: {$columns};
                --gallery-gap: {$gap}px;
            }
            
            /* Force column settings to override defaults */
            #{$gallery_id}.aqm-gallery-container {
                --aqm-columns: {$columns};
            }
            
            /* Grid layout with columns */
            .aqm-gallery-grid .aqm-gallery-items {
                display: grid;
                grid-template-columns: repeat(var(--gallery-columns), 1fr);
                grid-gap: var(--gallery-gap);
            }
            
            /* Make sure gallery items fill their columns */
            #{$gallery_id} .aqm-gallery-item {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            
            /* Make sure inner containers fill their parent */
            #{$gallery_id} .aqm-gallery-item-inner {
                width: 100% !important;
                height: 100% !important;
                position: relative !important;
            }
            
            /* Lightbox icon styling, similar to Divi */
            #{$gallery_id} .aqm-gallery-lightbox i {
                font-size: 16px !important;
                background-color: rgba(255, 255, 255, 0.7) !important;
                color: #333 !important;
                border-radius: 50% !important;
                width: 32px !important;
                height: 32px !important;
                line-height: 32px !important;
                text-align: center !important;
                transition: all 0.3s ease !important;
            }
            
            #{$gallery_id} .aqm-gallery-lightbox:hover i {
                background-color: #fff !important;
                transform: scale(1.1) !important;
            }
            
            /* Force images to fill their containers */
            #{$gallery_id} .aqm-gallery-item .aqm-gallery-image,
            #{$gallery_id} .aqm-gallery-item .aqm-gallery-image img {
                width: 100% !important;
                height: auto !important;
                display: block !important;
            }
            
            /* Masonry layout - fallback flex structure */
            .aqm-gallery-masonry:not(.masonry-initialized) .aqm-gallery-items {
                display: flex;
                flex-wrap: wrap;
                margin: 0 calc(-1 * var(--gallery-gap) / 2);
            }
            
            /* Specific selector for this gallery to override defaults */
            #{$gallery_id}.aqm-gallery-masonry:not(.masonry-initialized) .aqm-gallery-items .aqm-gallery-item,
            #{$gallery_id} .aqm-gallery-masonry:not(.masonry-initialized) .aqm-gallery-items .aqm-gallery-item {
                width: calc((100% / {$columns}) - var(--gallery-gap)) !important;
                margin: calc(var(--gallery-gap) / 2);
            }
            
            /* General selector for all galleries */
            .aqm-gallery-masonry:not(.masonry-initialized) .aqm-gallery-items .aqm-gallery-item {
                width: calc((100% / var(--gallery-columns)) - var(--gallery-gap));
                margin: calc(var(--gallery-gap) / 2);
            }
            
            /* Strong specificity for this gallery's grid columns */
            #{$gallery_id} .aqm-gallery-items {
                display: grid !important;
                grid-template-columns: repeat({$columns}, 1fr) !important;
                grid-gap: {$gap}px !important;
            }
            
            /* Keep responsiveness but respect column settings */
            @media (max-width: 980px) {
                #{$gallery_id} .aqm-gallery-items {
                    /* Don't override columns - removed override */
                }
            }
            
            @media (max-width: 767px) {
                #{$gallery_id} .aqm-gallery-items {
                    /* Force single column on mobile regardless of settings */
                    grid-template-columns: repeat(1, 1fr) !important;
                }
            }
            
            /* Custom icon sizes */
            #{$gallery_id} .aqm-gallery-lightbox,
            #{$gallery_id} .aqm-gallery-download,
            #{$gallery_id} .aqm-gallery-facebook,
            #{$gallery_id} .aqm-gallery-email {
                width: {$icon_circle_size} !important;
                height: {$icon_circle_size} !important;
                border-radius: 50% !important;
                background-color: rgba(255, 255, 255, 0.9) !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
            }
            
            #{$gallery_id} .aqm-gallery-lightbox:hover,
            #{$gallery_id} .aqm-gallery-download:hover,
            #{$gallery_id} .aqm-gallery-facebook:hover,
            #{$gallery_id} .aqm-gallery-email:hover {
                background-color: #ffffff !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
            }
            
            #{$gallery_id} .aqm-gallery-lightbox i,
            #{$gallery_id} .aqm-gallery-download i,
            #{$gallery_id} .aqm-gallery-facebook i,
            #{$gallery_id} .aqm-gallery-email i,
            #{$gallery_id} .aqm-gallery-lightbox .et-pb-icon,
            #{$gallery_id} .aqm-gallery-download .et-pb-icon,
            #{$gallery_id} .aqm-gallery-facebook .et-pb-icon,
            #{$gallery_id} .aqm-gallery-email .et-pb-icon {
                font-size: {$icon_size} !important;
                width: auto !important;
                height: auto !important;
                color: #333333 !important;
            }
            
            #{$gallery_id} .aqm-gallery-lightbox:hover .et-pb-icon,
            #{$gallery_id} .aqm-gallery-download:hover .et-pb-icon,
            #{$gallery_id} .aqm-gallery-facebook:hover .et-pb-icon,
            #{$gallery_id} .aqm-gallery-email:hover .et-pb-icon {
                color: #2ea3f2 !important;
                transform: scale(1.1) !important;
            }
        ";
        wp_add_inline_style('aqm-divi-gallery-style', $inline_css);
        
        // Start building the gallery HTML
        $container_class = 'aqm-gallery-container';
        
        // Add layout-specific class
        if ($layout_type === 'masonry') {
            $container_class .= ' aqm-gallery-masonry';
        } elseif ($layout_type === 'grid') {
            $container_class .= ' aqm-gallery-grid';
        } else {
            $container_class .= ' aqm-gallery-standard';
        }
        
        // Add data attributes for JavaScript
        $data_attributes_html = '';
        foreach (array(
            'layout-type' => $layout_type,
            'columns' => $columns,
            'gap' => $gap,
            'initial-count' => $initial_count,
            'load-more-count' => $load_more_count
        ) as $key => $value) {
            $data_attributes_html .= sprintf(' data-%s="%s"', esc_attr($key), esc_attr($value));
        }
        
        // Add data attributes for gallery
        $data_attributes_html .= sprintf(' data-gallery-source="manual"');
        
        // Add the image IDs as a data attribute
        $data_attributes_html .= sprintf(' data-gallery-images="%s"', esc_attr($gallery_images));
        
        // Add sorting attribute
        $data_attributes_html .= sprintf(' data-sorting="%s"', esc_attr($sorting));
        
        // Add the full list of all image IDs for load more functionality
        // This is important when we have many images but only show a few initially
        $full_image_ids_string = implode(',', $image_ids);
        $data_attributes_html .= sprintf(' data-all-images="%s"', esc_attr($full_image_ids_string));
        
        // Create a unique gallery ID for lightbox navigation
        $unique_gallery_id = 'aqm-gallery-' . esc_attr($gallery_id) . '-' . uniqid();
        $output = '<div id="' . esc_attr($unique_gallery_id) . '" class="' . esc_attr($container_class) . '" data-original-id="' . esc_attr($gallery_id) . '"' . $data_attributes_html . '>';
        
        // Add gallery controls if needed
        if ($show_download == 'on' && ($show_select_all == 'on' || $show_download_selected == 'on' || $show_download_all == 'on')) {
            $output .= '<div class="aqm-gallery-controls">';
            
            if ($show_select_all == 'on') {
                $output .= '<button class="aqm-select-all-button"><i class="fas fa-check-square" style="margin-right: 8px;"></i>' . esc_html($select_all_text) . '</button>';
            }
            
            if ($show_download_selected == 'on') {
                $output .= '<button class="aqm-download-selected-button"><i class="fas fa-download" style="margin-right: 8px;"></i>' . esc_html($download_selected_text) . '</button>';
            }
            
            if ($show_download_all == 'on') {
                $output .= '<button class="aqm-download-all-button" data-gallery-id="' . esc_attr($gallery_id) . '"><i class="fas fa-cloud-download-alt" style="margin-right: 8px;"></i>' . esc_html($download_all_text) . '</button>';
            }
            
            $output .= '</div>';
        }
        
        // Create direct inline mobile-first CSS to ensure proper column display
        $mobile_style = "@media only screen and (max-width: 767px) {";
        $mobile_style .= "#" . esc_attr($gallery_id) . " .aqm-gallery-items { display: grid !important; grid-template-columns: repeat(1, 1fr) !important; grid-gap: " . esc_attr($gap) . "px !important; }";
        $mobile_style .= "#" . esc_attr($gallery_id) . " .aqm-gallery-item { width: 100% !important; max-width: 100% !important; }";
        $mobile_style .= "}";
        
        // Add the mobile CSS directly to the page
        $output .= '<style>' . $mobile_style . '</style>';
        
        // Start building the gallery items - CRITICAL MOBILE FIX
        // Use dynamically generated mobile-first CSS that targets small screens directly
        // We are not using media queries here - we're directly checking screen size with JS
        $output .= '<div class="aqm-gallery-items" id="aqm-gallery-items-' . esc_attr($gallery_id) . '" data-columns="' . esc_attr($columns) . '" data-gap="' . esc_attr($gap) . '">';
        
        // Direct inline script to force mobile columns - this is the most direct approach possible
        $output .= '<script>
        (function() {
            function setGalleryColumns() {
                var container = document.getElementById("aqm-gallery-items-' . esc_attr($gallery_id) . '");
                if (!container) return;
                
                var isMobile = window.innerWidth < 768;
                var columns = isMobile ? 1 : ' . esc_attr($columns) . ';
                var gap = ' . esc_attr($gap) . ';
                
                container.style.display = "grid";
                container.style.gridTemplateColumns = "repeat(" + columns + ", 1fr)";
                container.style.gridGap = gap + "px";
                
                // Also set width on all items if mobile
                if (isMobile) {
                    var items = container.querySelectorAll(".aqm-gallery-item");
                    for (var i = 0; i < items.length; i++) {
                        items[i].style.width = "100%";
                        items[i].style.maxWidth = "100%";
                    }
                }
            }
            
            // Run immediately
            setGalleryColumns();
            
            // Run on resize
            window.addEventListener("resize", setGalleryColumns);
            
            // Run after window load
            window.addEventListener("load", setGalleryColumns);
            
            // Run periodically for 5 seconds to ensure it takes effect
            var interval = setInterval(setGalleryColumns, 500);
            setTimeout(function() { clearInterval(interval); }, 5000);
        })();
        </script>';
        
        // Process gallery images
        $count = 0;
        $ids_for_download = array(); // Keep track of all IDs for download feature
        $has_more = false; // Track if there are more images to load
        $total_images = 0; // Track total number of images
        $displayed_image_ids = array(); // Images to display on the current page
        
        // Get total number of images
        $total_images = count($image_ids);
        
        // Determine which images to display based on pagination type
        if ($pagination_type === 'none') {
            // Show all images
            $displayed_image_ids = $image_ids;
            $has_more = false;
        } else if ($pagination_type === 'load_more') {
            // For load more, only show initial count and enable load more if there are more
            $displayed_image_ids = array_slice($image_ids, 0, $initial_count);
            $has_more = ($total_images > $initial_count);
        } else if ($pagination_type === 'numbered') {
            // For numbered pagination, calculate offset based on current page
            $offset = ($current_page - 1) * $images_per_page;
            $displayed_image_ids = array_slice($image_ids, $offset, $images_per_page);
            $has_more = false; // No load more for numbered pagination
        }
        
        // Debug information to help troubleshoot
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('AQM Gallery - Total images: ' . $total_images);
            error_log('AQM Gallery - Pagination type: ' . $pagination_type);
            if ($pagination_type === 'load_more') {
                error_log('AQM Gallery - Initial count: ' . $initial_count);
                error_log('AQM Gallery - Load more count: ' . $load_more_count);
            } else if ($pagination_type === 'numbered') {
                error_log('AQM Gallery - Images per page: ' . $images_per_page);
                error_log('AQM Gallery - Current page: ' . $current_page);
                error_log('AQM Gallery - Offset: ' . (($current_page - 1) * $images_per_page));
            }
            error_log('AQM Gallery - Displayed images count: ' . count($displayed_image_ids));
            error_log('AQM Gallery - Has more: ' . ($has_more ? 'Yes' : 'No'));
        }
        
        foreach ($displayed_image_ids as $image_id) {
            // Get image URLs in different sizes for different purposes
            $thumbnail_url = wp_get_attachment_image_url($image_id, 'thumbnail'); // Use thumbnail size for grid display
            $large_url = wp_get_attachment_image_url($image_id, 'large'); // Use large for social/email sharing (smaller than full)
            $full_image_url = wp_get_attachment_image_url($image_id, 'full'); // Use full size only for lightbox and downloads
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            $image_title = get_the_title($image_id);
            
            if (!$thumbnail_url || !$full_image_url) {
                continue;
            }
            
            // Store the image URLs for different uses
            $image_url = $thumbnail_url; // For display in the grid
            $share_url = $large_url; // For sharing (Facebook, email)
            
            $class = 'aqm-gallery-item';
            $style = 'box-sizing: border-box; overflow: hidden; width: 100%; max-width: 100%;';
            
            $output .= sprintf(
                '<div class="%1$s" data-id="%2$s" style="%3$s">',
                esc_attr($class),
                esc_attr($image_id),
                esc_attr($style)
            );
            
            // Create a wrapper for both image and overlay to ensure proper positioning
            $output .= '<div class="aqm-gallery-item-inner" style="width: 100%; height: 100%; position: relative;">';
            
            // Image with natural height to avoid extra space in rows
            $output .= sprintf(
                '<div class="aqm-gallery-image" style="width: 100%%; overflow: hidden;">
                    <img src="%1$s" alt="%2$s" title="%3$s" style="width: 100%%; height: auto; display: block;" />
                </div>',
                esc_url($image_url),
                esc_attr($image_alt),
                esc_attr($image_title)
            );
            
            // Overlay
            $output .= '<div class="aqm-gallery-overlay">';
            
            // Checkbox for selection
            if ($show_download == 'on' && $show_download_selected == 'on') {
                $output .= '<div class="aqm-gallery-checkbox"><input type="checkbox" id="checkbox-' . esc_attr($image_id) . '" /><label for="checkbox-' . esc_attr($image_id) . '"></label></div>';
            }
            
            // Icons
            $output .= '<div class="aqm-gallery-icons">';
            
            // Lightbox
            if ($show_lightbox == 'on') {
                // Use our custom lightbox only
                $output .= sprintf(
                    '<a href="%1$s" class="aqm-gallery-lightbox" title="%2$s" data-attachment-id="%5$d" data-img-src="%1$s" data-thumb-src="%6$s" style="width: %4$s; height: %4$s;">
                        <i class="et-pb-icon" style="font-size: %3$s; line-height: %4$s; width: %4$s; height: %4$s; display: flex; align-items: center; justify-content: center;">&#x55;</i>
                    </a>',
                    esc_url($full_image_url), // Full size for lightbox view
                    esc_attr($image_title),
                    esc_attr($icon_size),
                    esc_attr($icon_circle_size),
                    intval($image_id),
                    esc_url($image_url) // Store thumbnail size URL as data attribute
                );
            }
            
            // Download
            if ($show_download == 'on') {
                $nonce = wp_create_nonce('aqm_divi_gallery_nonce');
                $download_url = admin_url('admin-ajax.php?action=aqm_divi_gallery_download_image&attachment_id=' . $image_id . '&nonce=' . $nonce);
                
                // Use download icon
                $output .= sprintf(
                    '<a href="%1$s" class="aqm-gallery-download" download style="width: %3$s; height: %3$s;">
                        <i class="et-pb-icon" style="font-size: %2$s; line-height: %3$s; width: %3$s; height: %3$s; display: flex; align-items: center; justify-content: center;">&#xe092;</i>
                    </a>',
                    esc_url($download_url),
                    esc_attr($icon_size),
                    esc_attr($icon_circle_size)
                );
            }
            
            // Facebook Share
            if ($show_facebook == 'on') {
                // Use Facebook icon - use share_url (large size) instead of full size
                $output .= sprintf(
                    '<a href="https://www.facebook.com/sharer/sharer.php?u=%1$s" class="aqm-gallery-facebook" target="_blank" style="width: %3$s; height: %3$s;">
                        <i class="et-pb-icon" style="font-size: %2$s; line-height: %3$s; width: %3$s; height: %3$s; display: flex; align-items: center; justify-content: center;">&#xe093;</i>
                    </a>',
                    esc_url($share_url),
                    esc_attr($icon_size),
                    esc_attr($icon_circle_size)
                );
            }
            
            // Email Share
            if ($show_email == 'on') {
                // Use share_url (large size) instead of full size for email sharing
                $email_body_text = urlencode($email_body . ' ' . $share_url);
                $email_subject_text = urlencode($email_subject);
                
                // Use email icon
                $output .= sprintf(
                    '<a href="mailto:?subject=%1$s&body=%2$s" class="aqm-gallery-email" style="width: %4$s; height: %4$s;">
                        <i class="et-pb-icon" style="font-size: %3$s; line-height: %4$s; width: %4$s; height: %4$s; display: flex; align-items: center; justify-content: center;">&#xe076;</i>
                    </a>',
                    esc_attr($email_subject_text),
                    esc_attr($email_body_text),
                    esc_attr($icon_size),
                    esc_attr($icon_circle_size)
                );
            }
            
            $output .= '</div>'; // End icons
            $output .= '</div>'; // End overlay
            $output .= '</div>'; // End gallery-item-inner
            $output .= '</div>'; // End gallery item
            
            $count++;
        }
        
        $output .= '</div>'; // End gallery items
        
        // Add Load More button if needed
        if ($pagination_type === 'load_more' && $has_more) {
            $output .= '<div class="aqm-load-more-wrap">';
            
            // Debug data attributes
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('AQM Gallery - Adding Load More button with attributes:');
                error_log('  - Loaded: ' . $initial_count);
                error_log('  - Total: ' . $total_images);
                error_log('  - Gallery source: ' . $gallery_source);
                error_log('  - Gallery images count: ' . count(explode(',', $gallery_images)));
            }
            
            // Add all data attributes to the button directly - this is important
            $button_data_attributes = sprintf(
                ' data-loaded="%1$d" data-total="%2$d" data-nonce="%3$s" data-gallery-source="%4$s"',
                intval($initial_count),
                intval($total_images),
                esc_attr(wp_create_nonce('aqm_divi_gallery_nonce')),
                esc_attr($gallery_source)
            );
            
            // Add gallery-specific attributes
            if ($gallery_source === 'manual') {
                $button_data_attributes .= sprintf(' data-gallery-images="%s"', esc_attr($gallery_images));
            } else {
                // For folder gallery, add necessary attributes
                $button_data_attributes .= sprintf(' data-folder-ids="%s"', esc_attr($folder_ids));
                $button_data_attributes .= sprintf(' data-include-subfolders="%s"', esc_attr($include_subfolders));
                $button_data_attributes .= sprintf(' data-sorting="%s"', esc_attr($sorting));
                // Also add gallery_images for convenience
                $button_data_attributes .= sprintf(' data-gallery-images="%s"', esc_attr($gallery_images));
            }
            
            // Output the button with all data attributes
            $output .= sprintf(
                '<button class="aqm-load-more-button"%s>%s</button>',
                $button_data_attributes,
                esc_html($load_more_text)
            );
            
            // Add visible counter text
            $output .= sprintf(
                '<div class="aqm-load-more-counter">%1$d of %2$d images loaded</div>',
                intval($initial_count),
                intval($total_images)
            );
            
            $output .= '</div>';
        }
        
        // Add numbered pagination if needed
        if ($pagination_type === 'numbered' && $total_images > $images_per_page) {
            // Calculate total pages
            $total_pages = ceil($total_images / $images_per_page);
            
            // Get current URL without pagination parameter
            $current_url = remove_query_arg('aqm_gallery_page', self::get_current_url());
            
            $output .= '<div class="aqm-pagination-wrap">';
            
            // Add pagination navigation
            $output .= '<div class="aqm-pagination">';
            
            // Previous page link
            if ($current_page > 1) {
                $prev_url = add_query_arg('aqm_gallery_page', ($current_page - 1), $current_url);
                $output .= sprintf('<a href="%s" class="aqm-pagination-prev" title="Previous Page"><span class="et-pb-icon">&#x34;</span></a>', esc_url($prev_url));
            } else {
                $output .= '<span class="aqm-pagination-prev disabled" title="Previous Page"><span class="et-pb-icon">&#x34;</span></span>';
            }
            
            // Page numbers
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $start_page + 4);
            
            if ($start_page > 1) {
                $output .= sprintf('<a href="%s" class="aqm-pagination-num">1</a>', esc_url(add_query_arg('aqm_gallery_page', 1, $current_url)));
                if ($start_page > 2) {
                    $output .= '<span class="aqm-pagination-dots">...</span>';
                }
            }
            
            for ($i = $start_page; $i <= $end_page; $i++) {
                // Add special mobile-hide class to some page numbers for responsive design
                $mobile_class = '';
                if ($i !== $start_page && $i !== $end_page && $i !== $current_page && 
                    $i !== $current_page - 1 && $i !== $current_page + 1) {
                    $mobile_class = ' mobile-hide';
                }
                
                if ($i === $current_page) {
                    $output .= sprintf('<span class="aqm-pagination-current">%d</span>', $i);
                } else {
                    $output .= sprintf(
                        '<a href="%s" class="aqm-pagination-num%s">%d</a>',
                        esc_url(add_query_arg('aqm_gallery_page', $i, $current_url)),
                        $mobile_class,
                        $i
                    );
                }
            }
            
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    $output .= '<span class="aqm-pagination-dots">...</span>';
                }
                $output .= sprintf(
                    '<a href="%s" class="aqm-pagination-num">%d</a>',
                    esc_url(add_query_arg('aqm_gallery_page', $total_pages, $current_url)),
                    $total_pages
                );
            }
            
            // Next page link
            if ($current_page < $total_pages) {
                $next_url = add_query_arg('aqm_gallery_page', ($current_page + 1), $current_url);
                $output .= sprintf('<a href="%s" class="aqm-pagination-next" title="Next Page"><span class="et-pb-icon">&#x35;</span></a>', esc_url($next_url));
            } else {
                $output .= '<span class="aqm-pagination-next disabled" title="Next Page"><span class="et-pb-icon">&#x35;</span></span>';
            }
            
            $output .= '</div>'; // End pagination
            
            // Page info
            $output .= sprintf(
                '<div class="aqm-pagination-info">Page %d of %d</div>',
                $current_page,
                $total_pages
            );
            
            $output .= '</div>'; // End pagination wrap
        }
        
        $output .= '</div>'; // End gallery container
        
        // Create a hidden iframe for downloading images
        $output .= '<iframe id="aqm-gallery-download-frame" style="display: none;"></iframe>';
        
        return $output;
    }
}

new AQM_Gallery_Module;
