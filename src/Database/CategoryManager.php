<?php
namespace TPC\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manages the custom database table for plugin categories.
 */
class CategoryManager {

    /**
     * The database table name.
     *
     * @var string
     */
    private $table_name;

    /**
     * CategoryManager constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'tpc_plugin_categories';
    }

    /**
     * Create the custom database table on plugin activation.
     */
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            plugin_slug varchar(255) NOT NULL,
            category varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY plugin_slug (plugin_slug)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Get the category for a specific plugin slug.
     *
     * @param string $plugin_slug The plugin slug.
     * @return string|false The category name or false if not found.
     */
    public function get_category( $plugin_slug ) {
        global $wpdb;
        $sql = $wpdb->prepare( "SELECT category FROM $this->table_name WHERE plugin_slug = %s", $plugin_slug );
        return $wpdb->get_var( $sql );
    }

    /**
     * Get all categorized plugins.
     *
     * @return array An associative array of plugin slugs and their categories.
     */
    public function get_all_categorized_plugins() {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT plugin_slug, category FROM $this->table_name", ARRAY_A );
        $categorized_plugins = array();
        foreach ( $results as $row ) {
            $categorized_plugins[ $row['plugin_slug'] ] = $row['category'];
        }
        return $categorized_plugins;
    }

    /**
     * Save or update a plugin's category.
     *
     * @param string $plugin_slug The plugin slug.
     * @param string $category    The category name.
     * @return bool True on success, false on failure.
     */
    public function save_category( $plugin_slug, $category ) {
        global $wpdb;
        $existing_category = $this->get_category( $plugin_slug );

        if ( $existing_category ) {
            // Update existing category.
            $result = $wpdb->update(
                $this->table_name,
                array( 'category' => $category ),
                array( 'plugin_slug' => $plugin_slug ),
                array( '%s' ),
                array( '%s' )
            );
        } else {
            // Insert new category.
            $result = $wpdb->insert(
                $this->table_name,
                array( 'plugin_slug' => $plugin_slug, 'category' => $category ),
                array( '%s', '%s' )
            );
        }

        return $result !== false;
    }

    /**
     * Remove a plugin's category.
     *
     * @param string $plugin_slug The plugin slug.
     * @return bool True on success, false on failure.
     */
    public function remove_category( $plugin_slug ) {
        global $wpdb;
        $result = $wpdb->delete(
            $this->table_name,
            array( 'plugin_slug' => $plugin_slug ),
            array( '%s' )
        );
        return $result !== false;
    }
}
