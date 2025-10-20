<?php
/**
 * Kadence Blocks Helper Functions
 *
 * @since   1.8.0
 * @package Kadence Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\get_license_key;
use function KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\get_original_domain;

/**
 * Get the license data for the plugin.
 */
function kadence_starter_templates_get_license_data() {
	$data = [];
	if ( function_exists( 'kadence_blocks_get_current_license_data' ) ) {
		$data = kadence_blocks_get_current_license_data();
	}
	if ( empty( $data['key'] ) && function_exists( 'KadenceWP\KadencePro\StellarWP\Uplink\get_license_key' ) ) {
		$data = [ 
			'key'     => \KadenceWP\KadencePro\StellarWP\Uplink\get_license_key( 'kadence-theme-pro' ),
			'product' => 'kadence-theme-pro',
			'email'   => '',
		];
	}
	if ( empty( $data['key'] ) ) {
		$data = [ 
			'key'     => get_license_key( 'kadence-starter-templates' ),
			'product' => 'kadence-starter-templates',
			'email'   => '',
		];
	}
	$license_data = [
		'api_key'   => ( ! empty( $data['key'] ) ? $data['key'] : '' ),
		'api_email' => ( ! empty( $data['email'] ) ? $data['email'] : '' ), // Backwards compatibility with older licensing.
		'site_url'  => get_original_domain(),
		'product_slug' => ( ! empty( $data['product'] ) ? $data['product'] : 'kadence-starter-templates' ),
		'env'       => kadence_starter_templates_get_current_env(),
	];
	return $license_data;
}

/**
 * Get the current environment.
 */
function kadence_starter_templates_get_current_env() {
	if ( defined( 'STELLARWP_UPLINK_API_BASE_URL' ) ) {
		switch ( STELLARWP_UPLINK_API_BASE_URL ) {
			case 'https://licensing-dev.stellarwp.com':
				return 'dev';
			case 'https://licensing-staging.stellarwp.com':
				return 'staging';
		}
	}
	return '';
}