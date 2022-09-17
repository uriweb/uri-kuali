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
 		'before' => '<div class="uri-kuali">',
 		'after' => '</div>',
 	), $attributes, $shortcode );

 	$subject_id = uri_kuali_api_get_subject_id( $attributes['subject'] );

  $course_list = uri_kuali_api_get_courses( $subject_id );

  $output = uri_kuali_render_course_list( $course_list->res, $attributes );

  //var_dump( $course_list->res );

  return $output;

 }
 add_shortcode( 'courses', 'uri_kuali_shortcode' );
