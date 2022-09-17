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
	if ( ! is_array ( $courses ) ) {
		// courses isn't an array...
		return '<p class="error">I couldn’t find courses matching <kbd>' . $attributes['subject'] . '</kbd>.</p>';
	}

  // Get shortcode template file
  // Priority:
  // 1. /themes/theme/template-parts/cl/cl-template-*.php
  // 2. /plugins/uri-component-library/templates/cl-template-*.php
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
