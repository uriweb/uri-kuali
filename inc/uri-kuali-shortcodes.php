<?php
/**
 * URI KUALI SHORTCODES
 *
 * @package uri-kuali
 */

 /**
  * Create a shortcode for displaying courses.
  */
 function uri_kuali_shortcode( $attributes, $content, $shortcode ) {
 	// normalize attribute keys, lowercase
 	$attributes = array_change_key_case( (array)$attributes, CASE_LOWER );

 	// default attributes
 	$attributes = shortcode_atts( array(
 		'subject' => 'AAF', // slug, slug2, slug3
 		'before' => '<div class="uri-courses">',
 		'after' => '</div>',
 	), $attributes, $shortcode );

 	$results = uri_kuali_api_get_subject( $attributes['subject'] );

  var_dump( $results );

  return false;

 }
 add_shortcode( 'courses', 'uri_kuali_shortcode' );
