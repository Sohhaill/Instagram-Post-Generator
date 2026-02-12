<?php

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ipg_activate_plugin() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'ipg_requests';
    $charset_collate = $wpdb->get_charset_collate();

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Create DB table only
   $sql = "CREATE TABLE $table_name (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT(20) NOT NULL,
    username VARCHAR(200) NOT NULL,
    user_email VARCHAR(200) NOT NULL,
    selected_category VARCHAR(255) NOT NULL,
    selected_styles LONGTEXT NOT NULL,
    additional_notes LONGTEXT NULL,
    admin_prompt LONGTEXT NULL,
    no_of_posts INT(11) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
    is_free_user VARCHAR(10) DEFAULT 'yes',
    free_images_generated INT(11) DEFAULT 0,
    first_image_url LONGTEXT NULL,
    second_image_url LONGTEXT NULL,
    PRIMARY KEY (id)
) $charset_collate;";



    dbDelta( $sql );
}
