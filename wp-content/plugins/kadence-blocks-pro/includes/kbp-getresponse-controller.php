<?php
/**
 * KBP_Getresponse
 */

//phpcs:disable WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout, WordPress.WP.AlternativeFunctions.json_encode_json_encode, Squiz.Commenting.FunctionComment.MissingParamComment, Squiz.Commenting.FunctionComment.MissingParamTag, Squiz.Commenting.FunctionComment.ParamCommentFullStop

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * KBP_Getresponse controller class.
 */
class KBP_Getresponse {

	/**
	 * The getresponse base url.
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * The getresponse API key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * The getresponse API Request Headers.
	 *
	 * @var string
	 */
	private $headers;

	/**
	 * Constructor.
	 */
	public function __construct( $api_url, $api_key ) {
		$this->api_url = $api_url;
		$this->api_key = $api_key;
		$this->headers = array(
			'Accept'       => 'application/json',
			'Content-Type' => 'application/json',
			'X-Auth-Token'    => 'api-key ' . $this->api_key,
		);
	}
	/**
	 * Make Request.
	 *
	 * @param string     $method
	 * @param string     $endpoint
	 * @param array/null $query items to add to url
	 * @param array/null $body items to add to body
	 *
	 * @return Contact|null
	 */
	public function make_request( $method, $endpoint, $query = null, $body = null ) {
		$args = array(
			'method'  => $method,
			'timeout' => 10,
			'headers' => $this->headers,
		);
		if ( ! empty( $body ) ) {
			$args['body'] = json_encode( $body );
		}
		$request_url = rtrim( $this->api_url, '/' ) . '/' . $endpoint;
		if ( ! empty( $query ) ) {
			$request_url = add_query_arg( $query, $request_url );
		}
		if ( 'GET' === $method ) {
			$response = wp_safe_remote_get( $request_url, $args );
		} else {
			$response = wp_safe_remote_post( $request_url, $args );
		}
		if ( is_wp_error( $response ) ) {
			return false;
		}
		if ( 200 != (int) wp_remote_retrieve_response_code( $response ) && 201 != (int) wp_remote_retrieve_response_code( $response ) && 202 != (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}
		$info = wp_remote_retrieve_body( $response );
		if ( empty( $info ) ) {
			return false;
		} else {
			return json_decode( $info, true );
		}
	}
	/**
	 * Find contact by email.
	 *
	 * @param string $email
	 *
	 * @return Contact|null
	 */
	public function find_contact( $email ) {
		$response = $this->make_request( 'GET', 'contacts', array( 'query' => array( 'email' => $email ) ) );
		if ( ! $response ) {
			return false;
		} elseif ( ! empty( $response[0] ) && ! empty( $response[0]['contactId'] ) ) {
			return $response[0];
		}

		return false;
	}
	/**
	 * Create new contact.
	 *
	 * @param array $contact array with contact information.
	 * @param array $tags_array
	 *
	 * @return boolean
	 */
	public function create_contact( $contact, $tags_array = array() ) {
		if ( empty( $contact['email'] ) ) {
			return false;
		}

		if ( ! empty( $tags_array ) ) {
			$contact['tags'] = $tags_array;
		}

		$response = $this->make_request( 'POST', 'contacts', null, $contact );
		if ( false === $response ) {
			return false;
		}

		return true;
	}
	/**
	 * Update a contact.
	 *
	 * @param string $contact_id The contact id.
	 * @param array  $contact The contact.
	 * @param array  $tags_array The tags.
	 *
	 * @return boolean
	 */
	public function update_contact( $contact_id, $contact, $tags_array = array() ) {
		if ( ! empty( $tags_array ) ) {
			$contact['tags'] = $tags_array;
		}

		$response = $this->make_request( 'POST', 'contacts/' . $contact_id, null, $contact );
		if ( ! $response ) {
			return false;
		} elseif ( ! isset( $response['contacts'] ) ) {
			return false;
		} elseif ( ! empty( $response['contacts'] ) ) {
			return true;
		}
		return false;
	}
}
