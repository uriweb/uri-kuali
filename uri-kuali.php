<?php
/*
Plugin Name: URI Kuali
Plugin URI: https://www.uri.edu
Description: Implements a shortcode to display course data from Kuali's API. [courses subject="AAF"]
Version: 0.1
Author: Brandon Fuller
Author: Alexandra Gauss
Author URI:
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

define( 'URI_KUALI_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Include settings
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-settings.php' );

// Include shortcodes
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-shortcodes.php' );

// Include api
include( URI_KUALI_DIR_PATH . 'inc/uri-kuali-api.php' );
