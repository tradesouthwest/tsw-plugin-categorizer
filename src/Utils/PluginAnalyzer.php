<?php
namespace TPC\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Suggests categories based on plugin data.
 */
class PluginAnalyzer {

    /**
     * Get suggested categories for a list of plugins.
     *
     * @param array $plugins An array of plugin data.
     * @return array An associative array of plugin slugs and suggested categories.
     */
    public function get_suggested_categories( $plugins ) {
        $suggestions = array();
        foreach ( $plugins as $plugin_slug => $plugin_data ) {
            $suggestion = $this->analyze_single_plugin( $plugin_data );
            if ( $suggestion ) {
                $suggestions[ $plugin_slug ] = $suggestion;
            }
        }
        return $suggestions;
    }

    /**
     * Analyze a single plugin to suggest a category.
     *
     * @param array $plugin_data The plugin data.
     * @return string|null The suggested category or null.
     */
    private function analyze_single_plugin( $plugin_data ) {
        $description = strtolower( $plugin_data['Description'] );
        $name        = strtolower( $plugin_data['Name'] );
        $tags        = isset( $plugin_data['Tags'] ) ? strtolower( $plugin_data['Tags'] ) : '';
        $text        = $name . ' ' . $description . ' ' . $tags;

        $keywords = array(
            'SEO'        => array( 'seo', 'search engine', 'meta title', 'sitemap', 'analytics' ),
            'Security'   => array( 'security', 'firewall', 'malware', 'login', '2fa' ),
            'Performance' => array( 'performance', 'cache', 'speed', 'optimization', 'minify' ),
            'Media'      => array( 'media', 'gallery', 'image', 'video', 'slider' ),
            'eCommerce'  => array( 'ecommerce', 'store', 'woocommerce', 'shop', 'cart' ),
            'Utility'    => array( 'utility', 'toolkit', 'tools', 'helper', 'customization' ),
            'Scheduling' => array( 'schedule', 'booking', 'appointment', 'calendar' ),
            'Business'   => array( 'business', 'forms', 'crm', 'project management' ),
            'Community'  => array( 'community', 'forum', 'social media', 'membership' ),
            'Education'  => array( 'education', 'lms', 'elearning', 'course' ),
        );

        foreach ( $keywords as $category => $terms ) {
            foreach ( $terms as $term ) {
                if ( strpos( $text, $term ) !== false ) {
                    return $category;
                }
            }
        }

        return null;
    }
}
