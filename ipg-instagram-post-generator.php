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

if ( ! defined('ABSPATH') ) exit;

/* ---------------------------
   Include Required Files
----------------------------*/
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/activator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax-save.php'; // AJAX Save Handler
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';


/* ---------------------------
   Activation & Deactivation
----------------------------*/
register_activation_hook( __FILE__, 'ipg_activate_plugin' );
register_deactivation_hook( __FILE__, 'ipg_deactivate_plugin' );


/* ---------------------------
   Enqueue Public JS (with versioning)
----------------------------*/
function ipg_enqueue_public_js() {

    $js_path = plugin_dir_path( __FILE__ ) . 'public/js/public.js';
    $js_url  = plugin_dir_url( __FILE__ ) . 'public/js/public.js';

    wp_enqueue_script(
        'ipg-script',
        $js_url,
        ['jquery'],
        filemtime( $js_path ), // version updates when file changes
        true
    );

    wp_localize_script('ipg-script', 'ipg_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action( 'wp_enqueue_scripts', 'ipg_enqueue_public_js' );
add_action('admin_enqueue_scripts', 'ipg_enqueue_public_js'); 


/* ---------------------------
   Enqueue Public CSS (with versioning)
----------------------------*/
function ipg_enqueue_public_assets() {

    $css_path = plugin_dir_path( __FILE__ ) . 'public/css/public.css';
    $css_url  = plugin_dir_url( __FILE__ ) . 'public/css/public.css';

    wp_enqueue_style(
        'ipg-public',
        $css_url,
        [],
        filemtime( $css_path ) // version updates when file changes
    );
}
add_action( 'wp_enqueue_scripts', 'ipg_enqueue_public_assets' );


