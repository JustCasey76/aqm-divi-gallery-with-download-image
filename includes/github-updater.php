<?php
/**
 * GitHub Updater for AQM Divi Gallery with Download Image
 *
 * This file handles checking for and installing updates from GitHub
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * GitHub Updater Class
 */
class AQM_GitHub_Updater {
    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $username;
    private $repository;
    private $authorize_token;
    private $github_response;

    /**
     * Constructor
     * 
     * @param string $file Path to the plugin file
     */
    public function __construct($file) {
        $this->file = $file;
        add_action('admin_init', array($this, 'set_plugin_properties'));

        // Set GitHub repository information
        $this->username = 'JustCasey76';
        $this->repository = 'aqm-divi-gallery-with-download-image';
        $this->authorize_token = null; // No token needed for public repos

        add_filter('pre_set_site_transient_update_plugins', array($this, 'modify_transient'), 10, 1);
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
    }

    /**
     * Set plugin properties
     */
    public function set_plugin_properties() {
        $this->plugin = get_plugin_data($this->file);
        $this->basename = plugin_basename($this->file);
        $this->active = is_plugin_active($this->basename);
    }

    /**
     * Get repository info
     */
    private function get_repository_info() {
        if (!empty($this->github_response)) {
            return;
        }

        // Use GitHub API to get the latest release
        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->username, $this->repository);
        
        // Setup request arguments
        $request_args = array();
        if ($this->authorize_token) {
            $request_args['headers'] = array('Authorization' => "Bearer {$this->authorize_token}");
        }

        $response = wp_remote_get($request_uri, $request_args);

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return false;
        }

        $response = json_decode(wp_remote_retrieve_body($response));
        
        if (!is_object($response) || !isset($response->tag_name)) {
            return false;
        }

        // Remove 'v' prefix from version number
        $response->tag_name = str_replace('v', '', $response->tag_name);
        
        // Set download URL
        if (isset($response->assets) && is_array($response->assets) && !empty($response->assets)) {
            if (isset($response->assets[0]) && isset($response->assets[0]->browser_download_url)) {
                $response->zipball_url = $response->assets[0]->browser_download_url;
            }
        }
        
        // If there's no asset download URL, use the zipball_url instead
        if (!isset($response->zipball_url) || empty($response->zipball_url)) {
            $response->zipball_url = sprintf('https://api.github.com/repos/%s/%s/zipball/%s', 
                $this->username, 
                $this->repository, 
                $response->tag_name
            );
        }

        $this->github_response = $response;
    }

    /**
     * Modify the transient for plugin updates
     */
    public function modify_transient($transient) {
        if (!isset($transient->checked)) {
            return $transient;
        }

        // Get info from GitHub
        $this->get_repository_info();

        // If there's no response or no version, return unmodified
        if (!isset($this->github_response->tag_name) || !isset($transient->checked[$this->basename])) {
            return $transient;
        }

        // Check if a new version is available
        $github_version = $this->github_response->tag_name;
        $current_version = $transient->checked[$this->basename];
        
        if (version_compare($github_version, $current_version, '>')) {
            $obj = new stdClass();
            $obj->slug = $this->basename;
            $obj->plugin = $this->basename;
            $obj->new_version = $github_version;
            $obj->url = $this->plugin["PluginURI"];
            $obj->package = $this->github_response->zipball_url;
            $obj->tested = '6.4.3'; // Set to a recent WordPress version
            $obj->icons = array(
                '1x' => 'https://ps.w.org/divi-gallery/assets/icon-128x128.png', // Default icon if yours doesn't exist
                '2x' => 'https://ps.w.org/divi-gallery/assets/icon-256x256.png', // Default icon if yours doesn't exist
            );
            
            $transient->response[$this->basename] = $obj;
        }

        return $transient;
    }

    /**
     * Plugin popup information
     */
    public function plugin_popup($result, $action, $args) {
        if (!isset($args->slug) || $args->slug !== $this->basename) {
            return $result;
        }

        // Get repository info
        $this->get_repository_info();

        if (!isset($this->github_response->tag_name)) {
            return $result;
        }

        $plugin = array(
            'name'              => $this->plugin["Name"],
            'slug'              => $this->basename,
            'version'           => $this->github_response->tag_name,
            'author'            => $this->plugin["AuthorName"],
            'author_profile'    => $this->plugin["AuthorURI"],
            'last_updated'      => $this->github_response->published_at,
            'homepage'          => $this->plugin["PluginURI"],
            'short_description' => $this->plugin["Description"],
            'sections'          => array(
                'Description'   => $this->plugin["Description"],
                'Updates'       => $this->github_response->body,
            ),
            'download_link'     => $this->github_response->zipball_url,
            'icons'             => array(
                '1x' => 'https://ps.w.org/divi-gallery/assets/icon-128x128.png', // Default icon if yours doesn't exist
                '2x' => 'https://ps.w.org/divi-gallery/assets/icon-256x256.png', // Default icon if yours doesn't exist
            ),
            'banners'           => array(),
            'banners_rtl'       => array(),
            'tested'            => '6.4.3', // Set to a recent WordPress version
            'requires_php'      => '7.0',
            'compatibility'     => array(),
        );

        return (object) $plugin;
    }

    /**
     * After installation tasks
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        if ($this->active) {
            activate_plugin($this->basename);
        }

        return $result;
    }
}
