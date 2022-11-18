<?php
/**
 * URI KUALI API
 *
 * @package uri-kuali
 */


/**
 * Return the API header
 */
function uri_kuali_api_get_header() {

  $client_id = get_site_option( 'uri_kuali_client_id' );
	$user_agent = 'URI WordPress Plugin; ' . get_bloginfo('url'); // So the api can easily figure out who we are

  if ( ! empty ( $client_id ) ) {
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
function uri_kuali_api_call( $url, $args ) {

  $response = wp_safe_remote_get( $url, $args );

  if ( isset( $response['body'] ) && !empty( $response['body'] ) && '200' == wp_remote_retrieve_response_code( $response ) ) {
		// hooray, all is well!
		return json_decode ( wp_remote_retrieve_body ( $response ) );
	} else {

		// still here?  Then we have an error condition

		if ( is_wp_error ( $response ) ) {
			$error_message = $response->get_error_message();
			echo 'There was an error with the URI Kuali Plugin: ' . $error_message;
			return FALSE;
		}
		if ( '200' != wp_remote_retrieve_response_code( $response ) ) {
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
function uri_kuali_get_data( $url ) {

  $cache = uri_kuali_cache_retrieve( $url );

  //var_dump( $cache );

  // if we have a good cache, use it
  if ( $cache ) {
    //echo '<br />using cache for ' . uri_kuali_hash_string( $url );
    return $cache;
  }

  // otherwise, call the api, cache it, and return the data
  //echo '<br />no cache for ' . uri_kuali_hash_string( $url );
  //echo '<br />calling api...';
  $data = uri_kuali_api_call( $url, uri_kuali_api_get_header() );
  uri_kuali_cache_update( $url, $data );
  return $data;

}

/**
 * Get the api base URL and set a default
 */
function uri_kuali_get_api_base() {
  $api_base = get_site_option( 'uri_kuali_url' );

  if ( empty( $api_base ) ) {
    $api_base = 'https://uri.kuali.co/api';
  }

  return $api_base;
}

/**
 * Get course subject data by three-letter course code
 */
function uri_kuali_api_get_subject_data( $subject ) {

  $api_base = uri_kuali_get_api_base();

  // @todo $subject should be sanitized somehow
  $url = $api_base . '/cm/options/types/subjectcodes?name=' . $subject;

  return uri_kuali_get_data( $url );

}



/**
 * Get the course list by subject id
 */
function uri_kuali_api_get_courses( $id, $atts ) {

  $api_base = uri_kuali_get_api_base();

  /* Build URL for a list of courses if a course number isn't specified */
  if (null === $atts['number']) {
    $url = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&sort=number&limit=' . $atts['limit'] . '&teachingMethod=Face to Face';
    return uri_kuali_get_data( $url );
  }

  /* If a course number is specified, build URL for single course */
  else {
  $url = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&number=' . $atts['number'] . '&teachingMethod=Face to Face';
  return uri_kuali_get_data( $url );
  }
}
