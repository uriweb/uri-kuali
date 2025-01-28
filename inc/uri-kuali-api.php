<?php

/**
 * URI KUALI API
 *
 * @package uri-kuali
 */


/**
 * Return the API header
 */
function uri_kuali_api_get_header()
{

  $client_id = get_site_option('uri_kuali_client_id');
  $user_agent = 'URI WordPress Plugin; ' . get_bloginfo('url'); // So the api can easily figure out who we are

  if (! empty($client_id)) {
    // Set ClientID in header here
    $args = array(
      'user-agent'  => $user_agent,
      'headers'     => [
        "Content-Type" => 'application/json',
        "Authorization" => 'Bearer ' . $client_id
      ]
    );
  }

  return $args;
}

/**
 * Call the API
 */
function uri_kuali_api_call($url, $args)
{

  $response = wp_safe_remote_get($url, $args);

  if (isset($response['body']) && !empty($response['body']) && '200' == wp_remote_retrieve_response_code($response)) {
    // hooray, all is well!
    return json_decode(wp_remote_retrieve_body($response));
  } else {

    // still here?  Then we have an error condition

    if (is_wp_error($response)) {
      $error_message = $response->get_error_message();
      echo 'There was an error with the URI Kuali Plugin: ' . $error_message;
      return FALSE;
    }
    if ('200' != wp_remote_retrieve_response_code($response)) {
      echo $response;
      return FALSE;
    }

    // still here?  the error condition is indeed unexpected
    echo "Empty response from server?";
    return FALSE;
  }
}

/**
 * Get data from either the cache or api, depending on what's fresher
 * @param str $url the url
 */
function uri_kuali_get_data($url)
{

  $cache = uri_kuali_cache_retrieve($url);

  //var_dump( $cache );

  // if we have a good cache, use it
  if ($cache) {
    //echo '<br />using cache for ' . uri_kuali_hash_string( $url );
    return $cache;
  }

  // otherwise, call the api, cache it, and return the data
  //echo '<br />no cache for ' . uri_kuali_hash_string( $url );
  //echo '<br />calling api...';


  $data = uri_kuali_api_call($url, uri_kuali_api_get_header());
  uri_kuali_cache_update($url, $data);
  return $data;
}

/**
 * Get the api base URL and set a default
 */
function uri_kuali_get_api_base()
{
  $api_base = get_site_option('uri_kuali_url');

  if (empty($api_base)) {
    $api_base = 'https://uri.kuali.co/api';
  }

  return $api_base;
}

/**
 * Get course subject data by three-letter course code
 */
function uri_kuali_api_get_subject_data($subject)
{

  $api_base = uri_kuali_get_api_base();

  // @todo $subject should be sanitized somehow
  $url = $api_base . '/cm/options/types/subjectcodes?name=' . $subject;

  return uri_kuali_get_data($url);
}

/**
 * Return only the newest course versions from a list of all active courses
 */
/*
function uri_kuali_api_return_newest_course_versions( $res, $api_base ) {

  $pids = array();
  $course_list = array();

  foreach( $res as $course ) {

    $pid = $course->pid;

    // If we've already pushed a course with this PID, we know we already have the latest version
    if ( in_array( $pid, $pids ) ) {
      continue;
    }

    // Otherwise, let's log the PID and push the course to the course list
    array_push( $pids, $pid );
    array_push( $course_list, $course );

  }

  return $course_list;

}
  */


/**
 * Find duplicate pids in the course list
 */
function uri_kuali_find_duplicate_pids($pids)
{

  $duplicates = array_filter($pids, function ($item) use ($pids) {
    return count(array_keys($pids, $item)) > 1;
  });
  return array_values(array_unique($duplicates));
}

/**
 * Create array of duplicated pids and an array of unique pids
 */
function uri_kuali_get_pids($res)
{
  $pids = array();
  //create array of all pids
  foreach ($res as $course) {
    $pid = $course->pid;
    array_push($pids, $pid);
  }


  //get array of duplicated pids 
  $duplicate_pids = uri_kuali_find_duplicate_pids($pids);

  //remove duplicates from all_pids array
  $all_pids = array_unique($pids);

  //separate unique pids from duplicate pids
  $unique_pids = array_diff($all_pids, $duplicate_pids);

  return array($duplicate_pids, $unique_pids);
}


function uri_kuali_api_return_newest_course_versions($res, $api_base)
{
  //Get duplicated pid array and unique pid array
  list($duplicate_pids, $unique_pids) = uri_kuali_get_pids($res);
  $course_list = array();
  $used_pids = array();



  foreach ($res as $course) {

    $pid = $course->pid;


    //if pid is in the array of duplicated pids, then call the api to get the lastest active version, log it in $used_pids array, and push it to the course_list 

    if (in_array($pid, $duplicate_pids) && !in_array($pid, $used_pids)) {
      //build api url
      $url_version = $api_base . '/cm/courses/' . $pid . '/latestActive';
      //call the api
      $get_active_version = uri_kuali_get_data($url_version);
      //log pid
      array_push($used_pids, $pid);
      //push course to course_list
      array_push($course_list, $get_active_version);
    }

    //if pid is a unique course, then push to course_list
    if (in_array($pid, $unique_pids)) {
      array_push($course_list, $course);
    }
  }

  return $course_list;
}



/**
 * Get the course list by subject id
 */
function uri_kuali_api_get_courses($id, $atts)
{

  $api_base = uri_kuali_get_api_base();
  /*
    $url1 = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&sort=number&limit=' . $atts['limit'] . '&status=active&skip=' .$atts['skip'];
    $url2 = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&sort=number&limit=100&status=active&skip=100';
    $all_queries = array( $url1, $url2);
    var_dump($all_queries);

    foreach ($all_queries as $url ) {
      return uri_kuali_get_data( $url );
    }

    */
  /* Build URL queries for a list of all courses in intervals of 100 if a course number isn't specified */
  if (null === $atts['number']) {

    $url_base = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&sort=number&limit=100&status=active&fields=number,title,description,_id,pid';

    $urls = array(
      $url_base . '&skip=',
      $url_base . '&skip=100',
      $url_base . '&skip=200',
    );

    $getdata1 = uri_kuali_get_data($urls[0]);
    $getdata2 = uri_kuali_get_data($urls[1]);
    $getdata3 = uri_kuali_get_data($urls[2]);

    $data = (object)array_merge_recursive((array)$getdata1, (array)$getdata2, (array)$getdata3);

    // @todo Contend with caching for this part
    $course_list = uri_kuali_api_return_newest_course_versions($data->res, $api_base);

    //var_dump($course_list[0]);

    return $course_list;
  }

  /* If a course number is specified, build URL for single course */ else {
    $url = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&number=' . $atts['number'] . '&status=active&fields=number,title,description,_id,pid';
    $getdata = uri_kuali_get_data($url);
    $course_list = uri_kuali_api_return_newest_course_versions($getdata->res, $api_base);
    return $course_list;
  }
}
