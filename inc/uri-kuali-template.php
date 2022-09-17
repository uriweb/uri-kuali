<?php
/**
 * URI KUALI TEMPLATE
 *
 * The plugin will look for this template in the theme first:
 * /themes/{theme-name}/template-parts/uri-kuali-course.php
 *
 * If the theme does not supply a template, we'll default to this one
 *
 * @package uri-kuali
 */

?>

<div class="uri-kuali-course">
  <div class="uri-kuali-course-header">
    <span class="uri-kuali-course-code"><?php print $attributes['subject'] . ' ' . $course->number; ?></span>
    <h3 class="uri-kuali-course-title"><?php print $course->title; ?></h3>
  </div>
  <p class="uri-kuali-course-description"><?php print $course->description; ?></p>
</div>
