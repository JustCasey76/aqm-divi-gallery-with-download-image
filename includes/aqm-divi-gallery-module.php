<?php
/**
 * AQM Divi Gallery with Download Image - Module Class
 */

if (!defined('ABSPATH')) exit;

// Let's wait for proper Divi initialization
if (!function_exists('et_pb_register_module')) {
    return;
}

/**
 * Register the module with Divi
 */
class AQM_ET_Builder_Module_Gallery extends ET_Builder_Module {
    function init() {
        $this->name = esc_html__('AQM Gallery with Download Image', 'aqm-divi-gallery');
        $this->plural = esc_html__('AQM Gallery with Download Images', 'aqm-divi-gallery');
        $this->slug = 'et_pb_aqm_gallery';
        $this->vb_support = 'on';
        $this->main_css_element = '%%order_class%% .aqm-gallery-container';
        
        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__('Content', 'aqm-divi-gallery'),
                    'display' => esc_html__('Display Settings', 'aqm-divi-gallery'),
                    'elements' => esc_html__('Elements', 'aqm-divi-gallery'),
                ),
            ),
            'advanced' => array(
                'toggles' => array(
                    'button_styles' => esc_html__('Button Styles', 'aqm-divi-gallery'),
                    'overlay' => esc_html__('Overlay & Icons', 'aqm-divi-gallery'),
                ),
            ),
        );
        
        // Enqueue Divi icons to ensure they're available
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function enqueue_scripts() {
        // Ensure Divi icons are loaded
        wp_enqueue_style('et-builder-modules-style', ET_BUILDER_URI . '/styles/frontend-builder-plugin-style.css');
    }

    public function get_fields() {
        return array(
            'gallery_source' => array(
                'label' => esc_html__('Gallery Source', 'aqm-divi-gallery'),
                'type' => 'select',
                'option_category' => 'basic_option',
                'options' => array(
                    'manual' => esc_html__('Manual Selection', 'aqm-divi-gallery'),
                    'folder' => esc_html__('Folders Pro', 'aqm-divi-gallery'),
                ),
                'default' => 'manual',
                'toggle_slug' => 'main_content',
                'description' => esc_html__('Choose where to get images from. Manual selection allows you to pick specific images, while Folders Pro lets you select entire folders.', 'aqm-divi-gallery'),
            ),
            'gallery_images' => array(
                'label' => esc_html__('Gallery Images', 'aqm-divi-gallery'),
                'type' => 'upload_gallery',
                'option_category' => 'basic_option',
                'toggle_slug' => 'main_content',
                'description' => esc_html__('Upload or select images for your gallery.', 'aqm-divi-gallery'),
                'show_if' => array('gallery_source' => 'manual'),
            ),
            'folder_ids' => array(
                'label' => esc_html__('Image Folders', 'aqm-divi-gallery'),
                'type' => 'select_multiple',
                'option_category' => 'basic_option',
                'toggle_slug' => 'main_content',
                'options' => $this->get_folder_options(),
                'description' => esc_html__('Select the Folders Pro folders containing the images you want to display.', 'aqm-divi-gallery'),
                'show_if' => array('gallery_source' => 'folder'),
                'default' => '',
            ),
            'initial_count' => array(
                'label' => esc_html__('Initial Images to Show', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => '8',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '50',
                    'step' => '1',
                ),
                'tab_slug' => 'general',
                'toggle_slug' => 'display',
                'description' => esc_html__('Set the number of images to show initially.', 'aqm-divi-gallery'),
            ),
            'load_more_count' => array(
                'label' => esc_html__('Images to Load on Click', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'configuration',
                'default' => '4',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '20',
                    'step' => '1',
                ),
                'tab_slug' => 'general',
                'toggle_slug' => 'display',
                'description' => esc_html__('Set the number of additional images to load when the Load More button is clicked.', 'aqm-divi-gallery'),
            ),
            'use_masonry' => array(
                'label' => esc_html__('Use Masonry Layout', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                ),
                'default' => 'off',
                'tab_slug' => 'general',
                'toggle_slug' => 'display',
                'description' => esc_html__('Enable to use a masonry grid layout for your gallery.', 'aqm-divi-gallery'),
            ),
            'columns' => array(
                'label' => esc_html__('Columns', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'layout',
                'default' => '3',
                'range_settings' => array(
                    'min' => '1',
                    'max' => '6',
                    'step' => '1',
                ),
                'mobile_options' => true,
                'responsive' => true,
                'tab_slug' => 'general',
                'toggle_slug' => 'display',
                'description' => esc_html__('Set the number of columns for your gallery grid.', 'aqm-divi-gallery'),
            ),
            'image_size' => array(
                'label' => esc_html__('Image Size', 'aqm-divi-gallery'),
                'type' => 'select',
                'option_category' => 'configuration',
                'options' => array(
                    'thumbnail' => esc_html__('Thumbnail', 'aqm-divi-gallery'),
                    'medium' => esc_html__('Medium', 'aqm-divi-gallery'),
                    'large' => esc_html__('Large', 'aqm-divi-gallery'),
                    'full' => esc_html__('Full Size', 'aqm-divi-gallery'),
                ),
                'default' => 'large',
                'tab_slug' => 'general',
                'toggle_slug' => 'display',
                'description' => esc_html__('Select the size of images to display in the gallery.', 'aqm-divi-gallery'),
            ),
            'enable_download' => array(
                'label' => esc_html__('Enable Download', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Enable this to add a download button to each image.', 'aqm-divi-gallery'),
            ),
            'download_icon' => array(
                'label' => esc_html__('Download Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'configuration',
                'default' => '&#xe076;',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Choose an icon for the download button.', 'aqm-divi-gallery'),
                'show_if' => array('enable_download' => 'on'),
            ),
            'enable_facebook_share' => array(
                'label' => esc_html__('Enable Facebook Share', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'off',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Enable this to add a Facebook share button to each image.', 'aqm-divi-gallery'),
            ),
            'facebook_icon' => array(
                'label' => esc_html__('Facebook Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'configuration',
                'default' => '&#xe093;',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Choose an icon for the Facebook share button.', 'aqm-divi-gallery'),
                'show_if' => array('enable_facebook_share' => 'on'),
            ),
            'enable_email_share' => array(
                'label' => esc_html__('Enable Email Share', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                ),
                'default' => 'off',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Enable this to add an email share option to each image.', 'aqm-divi-gallery'),
            ),
            'email_icon' => array(
                'label' => esc_html__('Email Icon', 'aqm-divi-gallery'),
                'type' => 'select_icon',
                'option_category' => 'configuration',
                'default' => '&#xe0be;',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Choose an icon for the email share button.', 'aqm-divi-gallery'),
                'show_if' => array('enable_email_share' => 'on'),
            ),
            'email_subject' => array(
                'label' => esc_html__('Email Subject', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'configuration',
                'default' => esc_html__('Check out this image', 'aqm-divi-gallery'),
                'toggle_slug' => 'elements',
                'description' => esc_html__('The subject line for the email share.', 'aqm-divi-gallery'),
                'show_if' => array('enable_email_share' => 'on'),
            ),
            'email_body' => array(
                'label' => esc_html__('Email Body Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'configuration',
                'default' => esc_html__('I thought you might like this image:', 'aqm-divi-gallery'),
                'toggle_slug' => 'elements',
                'description' => esc_html__('The default body text for the email share.', 'aqm-divi-gallery'),
                'show_if' => array('enable_email_share' => 'on'),
            ),
            'enable_bulk_download' => array(
                'label' => esc_html__('Enable Bulk Download', 'aqm-divi-gallery'),
                'type' => 'yes_no_button',
                'option_category' => 'configuration',
                'options' => array(
                    'on' => esc_html__('Yes', 'aqm-divi-gallery'),
                    'off' => esc_html__('No', 'aqm-divi-gallery'),
                ),
                'default' => 'on',
                'tab_slug' => 'general',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Enable or disable the option to select and download multiple images.', 'aqm-divi-gallery'),
            ),
            'load_more_text' => array(
                'label' => esc_html__('Load More Button Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'configuration',
                'default' => esc_html__('Load More', 'aqm-divi-gallery'),
                'tab_slug' => 'general',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Set the text for the Load More button.', 'aqm-divi-gallery'),
            ),
            'load_more_bg_color' => array(
                'label' => esc_html__('Load More Button Background', 'aqm-divi-gallery'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#2ea3f2',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'button_styles',
                'description' => esc_html__('Set the background color for the Load More button.', 'aqm-divi-gallery'),
            ),
            'load_more_text_color' => array(
                'label' => esc_html__('Load More Button Text Color', 'aqm-divi-gallery'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#ffffff',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'button_styles',
                'description' => esc_html__('Set the text color for the Load More button.', 'aqm-divi-gallery'),
            ),
            'load_more_border_radius' => array(
                'label' => esc_html__('Load More Button Border Radius', 'aqm-divi-gallery'),
                'type' => 'range',
                'option_category' => 'layout',
                'default' => '3px',
                'range_settings' => array(
                    'min' => '0',
                    'max' => '100',
                    'step' => '1',
                ),
                'tab_slug' => 'advanced',
                'toggle_slug' => 'button_styles',
                'description' => esc_html__('Set the border radius for the Load More button.', 'aqm-divi-gallery'),
                'allowed_units' => array('px', '%'),
            ),
            'icon_color' => array(
                'label' => esc_html__('Icon Color', 'aqm-divi-gallery'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#ffffff',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'overlay',
                'description' => esc_html__('Set the color for the download and share icons.', 'aqm-divi-gallery'),
            ),
            'overlay_bg_color' => array(
                'label' => esc_html__('Overlay Background', 'aqm-divi-gallery'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => 'rgba(0,0,0,0.7)',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'overlay',
                'description' => esc_html__('Set the background color for the image overlay.', 'aqm-divi-gallery'),
            ),
            'bulk_download_text' => array(
                'label' => esc_html__('Bulk Download Button Text', 'aqm-divi-gallery'),
                'type' => 'text',
                'option_category' => 'configuration',
                'default' => esc_html__('Download Selected', 'aqm-divi-gallery'),
                'tab_slug' => 'general',
                'toggle_slug' => 'elements',
                'description' => esc_html__('Set the text for the Bulk Download button.', 'aqm-divi-gallery'),
                'show_if' => array('enable_bulk_download' => 'on'),
            ),
            'bulk_download_bg_color' => array(
                'label' => esc_html__('Bulk Download Button Background', 'aqm-divi-gallery'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#4CAF50',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'button_styles',
                'description' => esc_html__('Set the background color for the Bulk Download button.', 'aqm-divi-gallery'),
                'show_if' => array('enable_bulk_download' => 'on'),
            ),
            'bulk_download_text_color' => array(
                'label' => esc_html__('Bulk Download Button Text Color', 'aqm-divi-gallery'),
                'type' => 'color-alpha',
                'custom_color' => true,
                'default' => '#ffffff',
                'tab_slug' => 'advanced',
                'toggle_slug' => 'button_styles',
                'description' => esc_html__('Set the text color for the Bulk Download button.', 'aqm-divi-gallery'),
                'show_if' => array('enable_bulk_download' => 'on'),
            ),
        );
    }

    public function get_folder_options() {
        $options = array();
        
        // Check if Premio Folders is active
        if (!taxonomy_exists('folder')) {
            return array('none' => esc_html__('Premio Folders not active', 'aqm-divi-gallery'));
        }
        
        // Get all folders using the taxonomy
        $folders = get_terms(array(
            'taxonomy' => 'folder',
            'hide_empty' => false,
        ));
        
        if (!empty($folders) && !is_wp_error($folders)) {
            foreach ($folders as $folder) {
                $folder_name = esc_html($folder->name);
                $folder_name = str_replace('-', ' ', $folder_name);
                $folder_name = ucwords($folder_name);
                $options[$folder->term_id] = $folder_name;
            }
        }
        
        return $options;
    }

    public function render($attrs, $content = null, $render_slug) {
        // Enqueue scripts and styles
        wp_enqueue_style(
            'aqm-divi-gallery-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/gallery.css',
            array(),
            '1.0.0'
        );

        // Enqueue Masonry
        wp_enqueue_script('masonry');

        wp_enqueue_script(
            'aqm-divi-gallery-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/gallery.js',
            array('jquery', 'masonry'),
            '1.0.0',
            true
        );

        // Add localized script for AJAX
        wp_localize_script(
            'aqm-divi-gallery-script',
            'aqm_divi_gallery',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('aqm_divi_gallery_nonce'),
            )
        );
        
        // Get the settings
        $gallery_source = $this->props['gallery_source'];
        $gallery_images = $this->props['gallery_images'];
        $folder_ids = $this->props['folder_ids'];
        $initial_count = intval($this->props['initial_count']);
        $load_more_count = intval($this->props['load_more_count']);
        $use_masonry = $this->props['use_masonry'];
        $image_size = $this->props['image_size'];
        $enable_download = $this->props['enable_download'];
        $download_icon = $this->props['download_icon'];
        $enable_facebook_share = $this->props['enable_facebook_share'];
        $facebook_icon = $this->props['facebook_icon'];
        $enable_email_share = $this->props['enable_email_share'];
        $email_icon = $this->props['email_icon'];
        $email_subject = $this->props['email_subject'];
        $email_body = $this->props['email_body'];
        $enable_bulk_download = $this->props['enable_bulk_download'];
        $load_more_text = $this->props['load_more_text'];
        $load_more_bg_color = $this->props['load_more_bg_color'];
        $load_more_text_color = $this->props['load_more_text_color'];
        $load_more_border_radius = $this->props['load_more_border_radius'];
        $bulk_download_text = $this->props['bulk_download_text'];
        $bulk_download_bg_color = $this->props['bulk_download_bg_color'];
        $bulk_download_text_color = $this->props['bulk_download_text_color'];
        $icon_color = $this->props['icon_color'];
        $overlay_bg_color = $this->props['overlay_bg_color'];

        // Add custom styles
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .aqm-load-more-button',
            'declaration' => sprintf(
                'background-color: %1$s; color: %2$s; border-radius: %3$s;',
                esc_attr($load_more_bg_color),
                esc_attr($load_more_text_color),
                esc_attr($load_more_border_radius)
            ),
        ));

        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .aqm-bulk-download-button',
            'declaration' => sprintf(
                'background-color: %1$s; color: %2$s;',
                esc_attr($bulk_download_bg_color),
                esc_attr($bulk_download_text_color)
            ),
        ));

        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .aqm-gallery-item-overlay',
            'declaration' => sprintf(
                'background-color: %1$s;',
                esc_attr($overlay_bg_color)
            ),
        ));

        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .aqm-gallery-item-overlay i',
            'declaration' => sprintf(
                'color: %1$s;',
                esc_attr($icon_color)
            ),
        ));

        // Get the gallery images
        $image_ids = array();
        
        if ('manual' === $gallery_source) {
            if (!empty($gallery_images)) {
                $image_ids = explode(',', $gallery_images);
            }
        } elseif ('folder' === $gallery_source) {
            // Initialize all image IDs array
            $all_image_ids = array();
            
            // Get selected folder IDs
            $selected_folder_ids = array();
            if (!empty($folder_ids)) {
                $selected_folder_ids = explode(',', $folder_ids);
                $selected_folder_ids = array_map('intval', $selected_folder_ids);
            }
            
            // Get attachments from all selected folders
            foreach ($selected_folder_ids as $folder_id) {
                // Get all objects (attachments) in this folder
                $folder_attachment_ids = get_objects_in_term($folder_id, 'folder');
                if (!empty($folder_attachment_ids) && !is_wp_error($folder_attachment_ids)) {
                    $all_image_ids = array_merge($all_image_ids, $folder_attachment_ids);
                }
            }
            
            // Remove duplicates
            $all_image_ids = array_unique($all_image_ids);
            
            // Filter to include only image attachments
            if (!empty($all_image_ids)) {
                global $wpdb;
                
                // Convert array to comma-separated string for SQL query
                $ids_str = implode(',', array_map('intval', $all_image_ids));
                
                // Only proceed if we have IDs
                if (!empty($ids_str)) {
                    // Query to get only image attachments
                    $query = "SELECT ID FROM {$wpdb->posts} 
                        WHERE ID IN ({$ids_str}) 
                        AND post_type = 'attachment' 
                        AND post_mime_type LIKE 'image/%' 
                        ORDER BY post_date DESC";
                    
                    // Get all image IDs
                    $image_ids = $wpdb->get_col($query);
                }
            }
        }

        $total_images = count($image_ids);

        // Calculate columns classes
        $desktop_columns = "aqm-gallery-columns-{$this->props['columns']}";
        $tablet_columns  = isset($this->props['columns_tablet']) ? "aqm-gallery-columns-tablet-{$this->props['columns_tablet']}" : "aqm-gallery-columns-tablet-2";
        $phone_columns   = isset($this->props['columns_phone']) ? "aqm-gallery-columns-phone-{$this->props['columns_phone']}" : "aqm-gallery-columns-phone-1";

        // Build output
        $output = '';

        // Masonry class
        $masonry_class = ('on' === $use_masonry) ? 'aqm-gallery-masonry' : '';

        // Start the gallery container
        $output .= sprintf(
            '<div class="aqm-gallery-container %1$s %2$s %3$s %4$s" data-initial="%5$s" data-load-more-count="%6$s" data-total="%7$s">',
            esc_attr($desktop_columns),
            esc_attr($tablet_columns),
            esc_attr($phone_columns),
            esc_attr($masonry_class),
            esc_attr($initial_count),
            esc_attr($load_more_count),
            esc_attr($total_images)
        );

        // Add the gallery header with bulk download if enabled
        if ('on' === $enable_bulk_download) {
            $output .= '<div class="aqm-gallery-header">';
            $output .= '<div class="aqm-gallery-bulk-actions">';
            $output .= sprintf(
                '<button type="button" class="aqm-download-button aqm-bulk-download-button" disabled>%s</button>',
                esc_html__('Download Selected', 'aqm-divi-gallery')
            );
            $output .= sprintf(
                '<button type="button" class="aqm-download-button aqm-download-all-button">%s</button>',
                esc_html__('Download All', 'aqm-divi-gallery')
            );
            $output .= '</div>';
            $output .= '</div>';
        }

        // Add the gallery items
        $output .= '<div class="aqm-gallery-items-wrap">';
        
        foreach ($image_ids as $index => $image_id) {
            $is_hidden = $index >= $initial_count ? 'aqm-gallery-item-hidden' : '';
            
            // Get the image information
            $image_full_url = wp_get_attachment_image_url($image_id, 'full');
            $image_url = wp_get_attachment_image_url($image_id, $image_size);
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            
            // Start the gallery item
            $output .= sprintf(
                '<div class="aqm-gallery-item %1$s" data-image-id="%2$s">',
                esc_attr($is_hidden),
                esc_attr($image_id)
            );
            
            // Add bulk selection checkbox if enabled
            if ('on' === $enable_bulk_download) {
                $output .= '<div class="aqm-gallery-item-select">';
                $output .= '<input type="checkbox" class="aqm-gallery-item-checkbox" />';
                $output .= '</div>';
            }
            
            // Add the image
            $output .= '<div class="aqm-gallery-item-inner">';
            
            // Add the image
            $output .= sprintf(
                '<img src="%1$s" alt="%2$s" />',
                esc_url($image_url),
                esc_attr($image_alt)
            );
            
            // Add the overlay with icons
            $output .= '<div class="aqm-gallery-item-overlay">';
            
            // Add download icon if enabled
            if ('on' === $enable_download) {
                $icon_code = $download_icon ? et_pb_process_font_icon($download_icon) : '&#xe076;';
                $output .= sprintf(
                    '<a href="%1$s" class="aqm-gallery-download-link" data-attachment-id="%2$s"><i class="et-pb-icon">%3$s</i></a>',
                    esc_url($image_url),
                    esc_attr($image_id),
                    $icon_code
                );
            }
            
            // Add Facebook share icon if enabled
            if ('on' === $enable_facebook_share) {
                $icon_code = $facebook_icon ? et_pb_process_font_icon($facebook_icon) : '&#xe093;';
                $output .= sprintf(
                    '<a href="https://www.facebook.com/sharer/sharer.php?u=%1$s" target="_blank" class="aqm-gallery-facebook-link"><i class="et-pb-icon">%2$s</i></a>',
                    urlencode($image_full_url),
                    $icon_code
                );
            }
            
            // Add email share icon if enabled
            if ('on' === $enable_email_share) {
                $icon_code = $email_icon ? et_pb_process_font_icon($email_icon) : '&#xe0be;';
                $output .= sprintf(
                    '<a href="mailto:?subject=%1$s&body=%2$s %3$s" class="aqm-gallery-email-link"><i class="et-pb-icon">%4$s</i></a>',
                    esc_attr($email_subject),
                    esc_attr($email_body),
                    esc_url($image_full_url),
                    $icon_code
                );
            }
            
            $output .= '</div>'; // End overlay
            $output .= '</div>'; // End gallery item inner
            $output .= '</div>'; // End gallery item
        }
        
        $output .= '</div>'; // End gallery items wrap
        
        // Add load more button if there are more images to load
        if ($total_images > $initial_count) {
            $output .= sprintf(
                '<div class="aqm-gallery-load-more"><button class="aqm-load-more-button">%1$s</button></div>',
                esc_html($load_more_text)
            );
        }
        
        $output .= '</div>'; // End gallery container
        
        // Create a hidden iframe for downloading images
        $output .= '<iframe id="aqm-gallery-download-frame" style="display: none;"></iframe>';
        
        return $output;
    }
}

// Register the module with Divi
new AQM_ET_Builder_Module_Gallery();
