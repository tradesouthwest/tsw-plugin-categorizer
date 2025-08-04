<?php
namespace TPC\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use TPC\Core\Plugin;

/**
 * Handles the admin interface and AJAX requests.
 */
class AdminInterface {

    /**
     * The available categories.
     *
     * @var array
     */
    private $categories;

    /**
     * AdminInterface constructor.
     */
    public function __construct() {
        $this->categories = array(
            'SEO', 'Security', 'Performance', 'Media', 'eCommerce', 'Utility',
            'Scheduling', 'Business', 'Community', 'Education'
        );
    }

    /**
     * Add the admin menu page.
     */
    public function add_menu_page() {
        add_management_page(
            esc_html__( 'Plugin Categorizer', 'tsw-plugin-categorizer' ),
            esc_html__( 'Plugin Categorizer', 'tsw-plugin-categorizer' ),
            'manage_options',
            TPC_SLUG,
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Enqueue scripts and styles.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        if ( 'tools_page_' . TPC_SLUG !== $hook ) {
            return;
        }

        // Enqueue CSS.
        wp_enqueue_style( TPC_SLUG . '-admin-css', TPC_PLUGIN_URL . 'assets/css/admin.css', array(), TPC_VERSION );

        // Enqueue JavaScript.
        wp_enqueue_script( TPC_SLUG . '-admin-js', TPC_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), TPC_VERSION, true );

        // Pass data to the JavaScript file.
        wp_localize_script( TPC_SLUG . '-admin-js', 'tpc_ajax_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'tpc_admin_nonce' ),
            'categories' => $this->categories,
            'i18n'     => array(
                'categorySaved' => esc_html__( 'Category saved!', 'tsw-plugin-categorizer' ),
                'categoryRemoved' => esc_html__( 'Category removed!', 'tsw-plugin-categorizer' ),
            ),
        ) );
    }

    /**
     * Render the admin page template.
     */
    public function render_admin_page() {
        $all_plugins        = get_plugins();
        $categorized_plugins = Plugin::get_instance()->db_manager->get_all_categorized_plugins();
        $uncategorized_plugins = array();
        
        // Build the list of uncategorized plugins.
        foreach ( $all_plugins as $plugin_slug => $plugin_data ) {
            if ( ! isset( $categorized_plugins[ $plugin_slug ] ) ) {
                $uncategorized_plugins[ $plugin_slug ] = $plugin_data;
            }
        }

        // Get suggested categories for uncategorized plugins.
        $suggested_categories = Plugin::get_instance()->plugin_analyzer->get_suggested_categories( $uncategorized_plugins );

        // Include the template.
        include TPC_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /**
     * Add a custom column to the plugins list table.
     *
     * @param array $columns The columns array.
     * @return array The filtered columns array.
     */
    public function add_plugin_category_column( $columns ) {
        $columns['tpc_category'] = esc_html__( 'Category', 'tsw-plugin-categorizer' );
        return $columns;
    }

    /**
     * Display content for the custom column.
     *
     * @param string $column_name The name of the column.
     * @param string $plugin_file The plugin file path.
     */
    public function display_plugin_category_column( $column_name, $plugin_file ) {
        if ( 'tpc_category' === $column_name ) {
            $category = Plugin::get_instance()->db_manager->get_category( $plugin_file );
            if ( $category ) {
                echo '<span class="tpc-category-label">' . esc_html( $category ) . '</span>';
            } else {
                echo '<span class="tpc-category-label tpc-uncategorized">' . esc_html__( 'Uncategorized', 'tsw-plugin-categorizer' ) . '</span>';
            }
        }
    }

    /**
     * AJAX handler to save a plugin's category.
     */
    public function handle_save_category() {
        check_ajax_referer( 'tpc_admin_nonce', 'nonce' );

        $plugin_slug = sanitize_text_field( $_POST['plugin_slug'] );
        $category    = sanitize_text_field( $_POST['category'] );

        if ( ! current_user_can( 'manage_options' ) || empty( $plugin_slug ) || empty( $category ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Permission denied or invalid data.', 'tsw-plugin-categorizer' ) ) );
        }

        $success = Plugin::get_instance()->db_manager->save_category( $plugin_slug, $category );

        if ( $success ) {
            wp_send_json_success( array( 'message' => esc_html__( 'Category saved successfully.', 'tsw-plugin-categorizer' ) ) );
        } else {
            wp_send_json_error( array( 'message' => esc_html__( 'Failed to save category.', 'tsw-plugin-categorizer' ) ) );
        }
    }

    /**
     * AJAX handler to remove a plugin's category.
     */
    public function handle_remove_category() {
        check_ajax_referer( 'tpc_admin_nonce', 'nonce' );

        $plugin_slug = sanitize_text_field( $_POST['plugin_slug'] );

        if ( ! current_user_can( 'manage_options' ) || empty( $plugin_slug ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Permission denied or invalid data.', 'tsw-plugin-categorizer' ) ) );
        }

        $success = Plugin::get_instance()->db_manager->remove_category( $plugin_slug );

        if ( $success ) {
            wp_send_json_success( array( 'message' => esc_html__( 'Category removed successfully.', 'tsw-plugin-categorizer' ) ) );
        } else {
            wp_send_json_error( array( 'message' => esc_html__( 'Failed to remove category.', 'tsw-plugin-categorizer' ) ) );
        }
    }
}
