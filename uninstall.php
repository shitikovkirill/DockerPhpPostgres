<?php
/**
 * Uninstall plugin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Save for Later
 * @version 1.0.0
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

//remove any additional options and custom table
$sql = "DROP TABLE `" . $wpdb->yith_wsfl_table. "`";
$wpdb->query( $sql );
?>