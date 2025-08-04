<?php
namespace TPC\Core;

use TPC\Admin\AdminInterface;
use TPC\Database\CategoryManager;
use TPC\Utils\PluginAnalyzer;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin controller class.
 */
class Plugin {

    /**
     * Singleton instance.
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * The database manager.
     *
     * @var CategoryManager
     */
    public $db_manager;

    /**
     * The admin interface handler.
     *
     * @var AdminInterface
     */
    public $admin_interface;

    /**
     * The plugin analyzer.
     *
     * @var PluginAnalyzer
     */
    public $plugin_analyzer;

    /**
     * Get the singleton instance of the plugin.
     *
     * @return Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Plugin constructor.
     */
    private function __construct() {
        // Initialize core components.
        $this->db_manager      = new CategoryManager();
        $this->plugin_analyzer = new PluginAnalyzer();
        $this->admin_interface = new AdminInterface();

        // Register hooks.
        $this->register_hooks();
    }

    /**
     * Register WordPress hooks.
     */
    private function register_hooks() {
        // Hook for adding the admin menu page.
        add_action( 'admin_menu', array( $this->admin_interface, 'add_menu_page' ) );

        // Hook for enqueuing scripts and styles in the admin area.
        add_action( 'admin_enqueue_scripts', array( $this->admin_interface, 'enqueue_assets' ) );

        // Hooks for adding and displaying the custom category column on the plugins list page.
        add_filter( 'manage_plugins_columns', array( $this->admin_interface, 'add_plugin_category_column' ) );
        add_action( 'manage_plugins_custom_column', array( $this->admin_interface, 'display_plugin_category_column' ), 10, 2 );

        // AJAX hooks for saving categories.
        add_action( 'wp_ajax_tpc_save_category', array( $this->admin_interface, 'handle_save_category' ) );
        add_action( 'wp_ajax_tpc_remove_category', array( $this->admin_interface, 'handle_remove_category' ) );
    }

    /**
     * Plugin activation hook.
     * This is a static method as required by WordPress hook system.
     */
    public static function activate() {
        $manager = new CategoryManager();
        $manager->create_table();
    }

    /**
     * Plugin deactivation hook.
     * This is a static method as required by WordPress hook system.
     */
    public static function deactivate() {
        // No action needed on deactivation for this version.
        // We will not drop the table to preserve user data.
    }
}
