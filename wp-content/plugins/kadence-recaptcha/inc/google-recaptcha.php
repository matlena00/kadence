<?php
/**
 * Goolge Recapcha v2 Class.
 *
 * @package Kadence reCAPTCHA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'KT_ReCaptchaResponse' ) ) {
	/**
	 * Class to handle recaptcha response.
	 */
	class KT_ReCaptchaResponse {
		/**
		 * The success var.
		 *
		 * @var null
		 */
		public $success;
		/**
		 * The error codes.
		 *
		 * @var null
		 */
		public $error_codes;
		/**
		 * The hostname var.
		 *
		 * @var null
		 */
		public $hostname;

		/**
		 * The constructor.
		 *
		 * @param string $success the success string.
		 * @param array  $error_codes the error codes array.
		 * @param string $hostname the host name string.
		 */
		public function __construct( $success, array $error_codes = array(), $hostname = null ) {
			$this->success     = $success;
			$this->error_codes = $error_codes;
			$this->hostname    = $hostname;
		}
		/**
		 * Is success?
		 *
		 * @return boolean
		 */
		public function is_success() {
			return $this->success;
		}

		/**
		 * Get error codes.
		 *
		 * @return array
		 */
		public function get_error_codes() {
			return $this->error_codes;
		}

		/**
		 * Get hostname.
		 *
		 * @return string
		 */
		public function get_hostname() {
			return $this->hostname;
		}
	}
}
if ( ! class_exists( 'KT_ReCaptcha' ) ) {
	/**
	 * Class to handle recaptcha response.
	 */
	class KT_ReCaptcha {
		/**
		 * The recatcha admin link.
		 *
		 * @var string.
		 */
		private static $signup_url = 'https://www.google.com/recaptcha/admin';
		/**
		 * The recatcha verify url.
		 *
		 * @var string.
		 */
		private static $site_verify_url = 'https://www.google.com/recaptcha/api/siteverify';
		/**
		 * The recatcha secret key.
		 *
		 * @var string.
		 */
		private $secret;
		/**
		 * Constructor.
		 *
		 * @param string $secret shared secret between site and ReCAPTCHA server.
		 */
		public function __construct( $secret ) {
			if ( null == $secret || '' == $secret ) {
				die(
					"To use reCAPTCHA you must get an API key from <a href='" . esc_url( self::$signup_url ) . "'>" . esc_html( self::$signup_url ) . '</a>'
				);
			}
			$this->secret = $secret;
		}

		/**
		 * Calls the reCAPTCHA siteverify API to verify whether the user passes
		 * CAPTCHA test.
		 *
		 * @param string $response response string from recaptcha verification.
		 * @param string $remote_ip IP address of end user.
		 *
		 * @return ReCaptchaResponse
		 */
		public function verify( $response, $remote_ip ) {

			if ( empty( $response ) ) {
				return new KT_ReCaptchaResponse( false, array( 'missing-input' ) );
			}

			$getresponse = wp_safe_remote_post(
				self::$site_verify_url,
				array(
					'body' => array(
						'secret'   => $this->secret,
						'response' => $response,
						'remoteip' => $remote_ip,
					),
				)
			);
			if ( 200 != wp_remote_retrieve_response_code( $getresponse ) ) {
				return new KT_ReCaptchaResponse( false, array( 'no connection' ) );
			}
			$getresponse = wp_remote_retrieve_body( $getresponse );
			$response_data = json_decode( $getresponse, true );

			if ( ! $response_data ) {
				return new KT_ReCaptchaResponse( false, array( 'invalid-json' ) );
			}

			$hostname = isset( $response_data['hostname'] ) ? $response_data['hostname'] : null;

			if ( isset( $response_data['success'] ) && true == $response_data['success'] ) {
				return new KT_ReCaptchaResponse( true, array(), $hostname );
			}

			if ( isset( $response_data['error-codes'] ) && is_array( $response_data['error-codes'] ) ) {
				return new KT_ReCaptchaResponse( false, $response_data['error-codes'], $hostname );
			}

			return new KT_ReCaptchaResponse( false, array(), $hostname );
		}
	}
}


