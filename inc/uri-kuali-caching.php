<?php
/**
 * URI KUALI CACHING
 *
 * @package uri-kuali
 */

/**
 * Save the data retrieved from the API as a WordPress option
 * we use the same array to store data from every course shortcode on the site
 * each has its own key (based on the URL hash) and date when it was stored
 */
function uri_kuali_cache_update( $url, $kuali_data ) {

  $hash = uri_kuali_hash_string( $url );

  $cache = array();
  $cache['date'] = strtotime('now');
  $cache['res'] = $kuali_data;

  $data = get_option( 'uri_kuali_cache' );
 	if ( empty ( $data ) ) {
    $data = array();
  }

  $data[$hash] = $cache;
  update_option( 'uri_kuali_cache', $data, TRUE );
  //echo '<br/> cache updated for ' . $hash;

  //var_dump($data);

}


function uri_kuali_cache_retrieve( $url ) {

  $data = get_option( 'uri_kuali_cache' );
  $hash = uri_kuali_hash_string( $url );

  //var_dump($data);

  if ( array_key_exists( $hash, $data ) ) {

    //echo '<br />cache exists for ' . $hash;
    $cache = $data[$hash];

    if ( uri_kuali_cache_is_expired( $cache['date'] ) ) {
      //echo '<br />cache is expired for ' . $hash;
      return false;
    }

    //echo '<br />using cache for ' . $hash;
    return $cache['res'];

  }

  //echo '<br />no cache exists for ' . $hash;
  return false;

}
