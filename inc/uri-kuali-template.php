<?php
/**
 * URI KUALI TEMPLATE
 *
 * @package uri-kuali
 */

?>

<div class="uri-kuali-course">
  <div class="uri-kuali-course-header">
    <span class="uri-kuali-course-code"><?php print $attributes['subject'] . $course->number; ?></span>
    <h3 class="uri-kuali-course-title"><?php print $course->title; ?></h3>
  </div>
  <p class="uri-kuali-course-description"><?php print $course->description; ?></p>
</div>
