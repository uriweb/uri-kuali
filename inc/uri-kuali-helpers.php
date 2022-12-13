<?php
/**
 * URI KUALI HELPER FUNCTIONS
 *
 * @package uri-kuali
 */

/**
 * Render the list of courses
 * @param arr array of courses
 * @param arr attributes
 * @return str HTML list of courses
 */
function uri_kuali_render_course_list( $courses, $attributes ) {

  // Get shortcode template file
  // Priority:
  // 1. /themes/{theme-name}/template-parts/uri-kuali-course.php
  // 2. /plugins/uri-kuali/inc/uri-kuali-template.php
  $template = get_stylesheet_directory() . '/template-parts/uri-kuali-course.php';

	if ( ! is_file( $template ) ) {
		$template = 'uri-kuali-template.php';
	}

  // Do the templating
	ob_start();

	print $attributes['before'];

	foreach( $courses as $course ) {
		include $template;
	}

	print $attributes['after'];

	$output = ob_get_clean();

	return $output;

}

/**
 * Render a message if there are no results
 * @param arr attributes
 * @return str HTML message
 */
function uri_kuali_render_no_results( $attributes ) {

  ob_start();

  print $attributes['before'];

  ?>

  <div class="uri-kuali-no-results">There are no courses matching this subject.</div>

  <?php

  print $attributes['after'];

  return ob_get_clean();

}

/**
 * hash a string; currently md5, someday something else.
 * @param str $string the string to hash
 * @return str
 */
function uri_kuali_hash_string ( $string ) {
  $hash = md5( $string );
  return $hash;
}

/**
 * check if a date has recency
 * @param int date
 * @return bool
 */
function uri_kuali_cache_is_expired( $date ) {

	$recency = get_site_option( 'uri_kuali_recency', '1 day' );
	$expiry = strtotime( '-' . $recency, strtotime('now') );

	return ( $date < $expiry );

}
