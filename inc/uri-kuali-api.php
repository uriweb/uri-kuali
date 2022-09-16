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

  $client_id = get_option( 'uri_kuali_client_id' );
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
 * Get course subject data by three-letter course code
 */
function uri_kuali_api_get_subject_data( $subject ) {

  $api_base = get_option( 'uri_kuali_url' );
  $args = uri_kuali_api_get_header();

  // @todo $subject should be sanitized somehow
  $url = $api_base . '/cm/options/types/subjectcodes?name=' . $subject;

  return uri_kuali_api_call( $url, $args );

}

/**
 * Get the course subject id
 */
function uri_kuali_api_get_subject_id( $subject ) {

  $data = uri_kuali_api_get_subject_data( $subject );
  return reset( $data )->id;

}

/**
 * Get the course list by subject id
 */
function uri_kuali_api_get_courses( $id ) {

  $api_base = get_option( 'uri_kuali_url' );
  $args = uri_kuali_api_get_header();

  $url = $api_base . '/cm/courses/queryAll?subjectCode=' . $id . '&sort=number';

  return uri_kuali_api_call( $url, $args );

}
