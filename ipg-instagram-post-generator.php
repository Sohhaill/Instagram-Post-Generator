<?php
/**
 * Plugin Name: Instagram Post Generator
 * Plugin URI:  https://yourwebsite.com/
 * Description: Generate Instagram-style posts using a custom form, admin prompt editor, and ChatGPT image API. Free Version – Phase 1.
 * Version:     1.0.0
 * Author:      Muhammad Sohail
 * Author URI:  https://yourwebsite.com/
 * Text Domain: ipg
 * Domain Path: /languages
 */

// Load frontend functionality
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';



register_deactivation_hook( __FILE__, 'ipg_deactivate_plugin' );
register_activation_hook( __FILE__, 'ipg_activate_plugin' );


function ipg_enqueue_public_assets() {

    $css_path = plugin_dir_path( __FILE__ ) . 'public/css/public.css';
    $css_url  = plugin_dir_url( __FILE__ ) . 'public/css/public.css';

    wp_enqueue_style(
        'ipg-public',
        $css_url,
        array(),
        filemtime( $css_path ) // Version changes whenever file is edited
    );
}
add_action( 'wp_enqueue_scripts', 'ipg_enqueue_public_assets' );
