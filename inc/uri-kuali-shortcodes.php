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
 		'subject' => 'AAF',
 		'before' => '<div class="uri-kuali">',
 		'after' => '</div>',
    'limit' => 200,
 	), $attributes, $shortcode );

 	$subject_data = uri_kuali_api_get_subject_data( $attributes['subject'] );
  $subject_id = $subject_data[0]->id;

  if ( null === $subject_id ) {
    return uri_kuali_render_no_results( $attributes );
  }

  $course_list = uri_kuali_api_get_courses( $subject_id, $attributes );

  $output = uri_kuali_render_course_list( $course_list->res, $attributes );

  //var_dump( $course_list->res );

  return $output;

 }
 add_shortcode( 'courses', 'uri_kuali_shortcode' );
