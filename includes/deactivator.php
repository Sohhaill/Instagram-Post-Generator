<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ipg_deactivate_plugin() {
    global $wpdb;

    // ❗ WARNING: This drops the entire table
    // Comment this line once your table structure is final

    $table_name = $wpdb->prefix . 'ipg_requests';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
