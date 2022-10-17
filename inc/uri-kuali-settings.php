<?php
/**
 * Create admin settings menu for the Kuali Plugin
 *
 * @package uri-kuali
 */


/**
 * Register settings
 */
function uri_kuali_register_settings() {

	register_setting(
		'uri_kuali',
		'uri_kuali_url',
		'uri_kuali_santize_url'
	);

	register_setting(
		'uri_kuali',
		'uri_kuali_client_id',
		'sanitize_text_field'
	);

	register_setting(
		'uri_kuali',
		'uri_kuali_recency',
		'sanitize_text_field'
	);

	add_settings_section(
		'uri_kuali_settings',
		__( 'URI Kuali Settings', 'uri' ),
		'uri_kuali_settings_section',
		'uri_kuali'
	);


	// register field
	add_settings_field(
		'uri_kuali_url', // id: as of WP 4.6 this value is used only internally
		__( 'URL of API', 'uri' ), // title
		'uri_kuali_url_field', // callback
		'uri_kuali', // page
		'uri_kuali_settings', //section
		array( //args
			'label_for' => 'uri-kuali-field-url',
			'class' => 'uri_kuali_row',
		)
	);
	add_settings_field(
		'uri_kuali_client_id', // id: as of WP 4.6 this value is used only internally
		__( 'Client ID', 'uri' ), // title
		'uri_kuali_client_id_field', // callback
		'uri_kuali', // page
		'uri_kuali_settings', //section
		array( //args
			'label_for' => 'uri-kuali-field-client-id',
			'class' => 'uri_kuali_row',
		)
	);
	add_settings_field(
		'uri_kauli_recency', // id: as of WP 4.6 this value is used only internally
		__( 'Recency', 'uri' ), // title
		'uri_kuali_recency_field', // callback
		'uri_kuali', // page
		'uri_kuali_settings', //section
		array( //args
			'label_for' => 'uri-kuali-field-recency',
			'class' => 'uri_kuali_row',
		)
	);

}
add_action( 'admin_init', 'uri_kuali_register_settings' );

/**
 * Callback for a settings section
 * @param arr $args has the following keys defined: title, id, callback.
 * @see add_settings_section()
 */
function uri_kuali_settings_section( $args ) {
	$intro = 'URI Kuali automatically displays course information.';
	echo '<p id="' . esc_attr( $args['id'] ) . '">' . esc_html_e( $intro, 'uri' ) . '</p>';
}


/**
 * Add the settings page to the settings menu
 * @see https://developer.wordpress.org/reference/functions/add_options_page/
 */
function uri_kuali_settings_page() {
	add_submenu_page(
		'settings.php',
		__( 'URI Kuali Settings', 'uri' ),
		__( 'URI Kuali', 'uri' ),
		'manage_options',
		'uri-kuali-settings',
		'uri_kuali_settings_page_html'
	);
}
add_action( 'network_admin_menu', 'uri_kuali_settings_page' );



/**
 * callback to render the HTML of the settings page.
 * renders the HTML on the settings page
 */
function uri_kuali_settings_page_html() {
	// check user capabilities
	// on web.uri, we have to leave this pretty loose
	// because web com doesn't have admin privileges.
	if ( ! current_user_can( 'manage_options' ) ) {
		echo '<div id="setting-message-denied" class="updated settings-error notice is-dismissible">
<p><strong>You do not have permission to save this form.</strong></p>
<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		return;
	}
	?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
					// output security fields for the registered setting "uri_today"
					settings_fields( 'uri_kuali' );
					// output setting sections and their fields
					// (sections are registered for "uri_courses", each field is registered to a specific section)
					do_settings_sections( 'uri_kuali' );
					// output save settings button
					submit_button( 'Save Settings' );
				?>
			</form>
		</div>
	<?php
}


/**
 * Validate the URL
 */
function uri_kuali_santize_url( $value ) {
	$safe = sanitize_text_field( $value );

	if($safe !== $value) {
		$message = 'The domain failed validation, it was sanitized before it was saved. Doublecheck it.';
	} else {
		if ( isset( $_GET['settings-updated'] ) ) {
			$message = 'Settings Saved.';
		}
	}

	if( isset ( $message ) ) {
		add_settings_error(
			'uri_kuali_messages',
			'uri_kuali_message',
			__( $message, 'uri' ),
			'updated'
		);
	}

	return $safe;
}


/**
 * Field callback
 * outputs the field
 * @see add_settings_field()
 */
function uri_kuali_url_field( $args ) {
	// get the value of the setting we've registered with register_setting()
	$setting = get_option( 'uri_kuali_url' );
	// output the field
	?>
		<input type="text" class="regular-text" aria-describedby="uri-kuali-field-url" name="uri_kuali_url" id="uri-kuali-field-url" value="<?php print ($setting!==FALSE) ? esc_attr($setting) : ''; ?>">
		<p class="uri-kuali-field-url">
			<?php
				esc_html_e( 'Provide the base URL for the API. Omit trailing slash.', 'uri' );
				echo '<br />';
				esc_html_e( 'For example: https://uri.kuali.co/api', 'uri' );
			?>
		</p>
	<?php
}

/**
 * Field callback
 * outputs the field
 * @see add_settings_field()
 * @see uri_today_field_domain_callback()
 */
function uri_kuali_client_id_field( $args ) {
	// get the value of the setting we've registered with register_setting()
	$setting = get_option( 'uri_kuali_client_id' );
	// output the field
	?>
		<input type="text" class="regular-text" aria-describedby="uri-kuali-field-client-id" name="uri_kuali_client_id" id="uri-kuali-field-client-id" value="<?php print ($setting!==FALSE) ? esc_attr($setting) : ''; ?>">
		<p class="uri-kuali-field-client-id">
			<?php
				esc_html_e( 'Provide your client ID for the API.', 'uri' );
			?>
		</p>
	<?php
}

/**
 * Field callback
 * outputs the field
 * @see add_settings_field()
 * @see uri_today_field_domain_callback()
 */
function uri_kuali_recency_field( $args ) {
	// get the value of the setting we've registered with register_setting()
	$setting = get_option( 'uri_kuali_recency' );
	// output the field
	?>
		<input type="text" class="regular-text" aria-describedby="uri-kuali-field-recency" name="uri_kuali_recency" id="uri-kuali-field-recency" value="<?php print ($setting!==FALSE) ? esc_attr($setting) : ''; ?>">
		<p class="uri-kuali-field-recency">
			<?php
				esc_html_e( 'How long should the results be cached locally? e.g. "1 day"', 'uri' );
			?>
		</p>
	<?php
}
