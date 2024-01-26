<?php
/*
Plugin Name: URI Kuali
Plugin URI: https://www.uri.edu
Description: Implements a shortcode to display course data from Kuali's API. [courses subject="AAF"]
Version: 1.4.0
Author: Brandon Fuller
Author: Alexandra Gauss
Author URI:
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

define( 'URI_KUALI_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Add some generic styles
 */
function uri_kuali_enqueue() {
	wp_enqueue_style( 'uri-kuali-styles', plugins_url( 'css/uri-kuali.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'uri_kuali_enqueue' );

// Include settings
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-settings.php' );

// Include shortcodes
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-shortcodes.php' );

// Include api
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-api.php' );

// Include caching functions
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-caching.php' );

// Include helper functions
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-helpers.php' );
