<?php
/**
 * Plugin Name:       TSW Plugin Categorizer
 * Plugin URI:        https://example.com/tsw-plugin-categorizer/
 * Description:       A WordPress admin tool to categorize plugins for better management.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tsw-plugin-categorizer
 * Domain Path:       /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'TPC_VERSION', '1.0.0' );
// This gets the absolute path to the main plugin directory.
define( 'TPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TPC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TPC_SLUG', 'tsw-plugin-categorizer' );

/**
 * Autoload classes using the namespace and file structure.
 * This function is automatically called by PHP when a class is not found.
 */
spl_autoload_register( 'tpc_autoloader' );

function tpc_autoloader( $class ) {
    // Check if the class is within our defined namespace.
    if ( strpos( $class, 'TPC\\' ) === 0 ) {
        // Remove the namespace prefix from the class name.
        $relative_class = str_replace( 'TPC\\', '', $class );

        // Convert the namespace structure to a file path.
        // For example, TPC\Core\Plugin becomes Core/Plugin.
        $file = TPC_PLUGIN_DIR . 'src/' . str_replace( '\\', '/', $relative_class ) . '.php';

        // Check if the file exists before including it.
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }
}

/**
 * Main plugin function to retrieve the singleton instance.
 * We use a function instead of a direct class call to make it more accessible.
 * @return \TPC\Core\Plugin
 */
function TPC() {
    return \TPC\Core\Plugin::get_instance();
}

// Initialize the plugin. This is where the singleton is first created.
TPC();

/**
 * Register activation and deactivation hooks.
 * The static methods are defined in the main Plugin class.
 */
register_activation_hook( __FILE__, array( 'TPC\Core\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TPC\Core\Plugin', 'deactivate' ) );
