<?php
/**
 * Kadence_Recaptcha Settings Class
 *
 * @package Kadence Recaptcha
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Kadence_Recaptcha_Settings class
 */
class Kadence_Recaptcha_Settings {
	const OPT_NAME = 'kt_recaptcha';

	/**
	 * Action on init.
	 */
	public function __construct() {
		require_once KT_RECAPTCHA_PATH . 'inc/settings/load.php';
		// Need to load this with priority higher then 10 so class is loaded.
		add_action( 'after_setup_theme', [ $this, 'add_sections' ], 20 );
	}
	/**
	 * Add sections to settings.
	 */
	public function add_sections() {
		if ( ! class_exists( 'Kadence_Settings_Engine' ) ) {
			return;
		}
		$args            = [
			'v2'                 => true,
			'opt_name'           => self::OPT_NAME,
			'menu_icon'          => '',
			'menu_title'         => __( 'CAPTCHA Settings', 'kadence-recaptcha' ),
			'page_title'         => __( 'Kadence CAPTCHA Settings', 'kadence-recaptcha' ),
			'page_slug'          => 'kadence-recaptcha-settings',
			'page_permissions'   => 'manage_options',
			'menu_type'          => 'submenu',
			'page_parent'        => ( apply_filters( 'kadence_recaptcha_network', false ) ? 'settings.php' : 'options-general.php' ),
			'page_priority'      => null,
			'footer_credit'      => '',
			'class'              => '',
			'admin_bar'          => false,
			'admin_bar_priority' => 999,
			'admin_bar_icon'     => '',
			'show_import_export' => false,
			'version'            => KT_RECAPTCHA_VERSION,
			'logo'               => KT_RECAPTCHA_URL . 'inc/kadence-logo.png',
			'changelog'          => KT_RECAPTCHA_PATH . 'changelog.txt',
			'network_admin'      => apply_filters( 'kadence_recaptcha_network', false ),
			'database'           => ( apply_filters( 'kadence_recaptcha_network', false ) ? 'network' : '' ),
			'license'            => 'hidden-side-panel',
		];
		$args['tabs']    = [
			'settings' => [
				'id'    => 'settings',
				'title' => __( 'Settings', 'kadence-recaptcha' ),
			],
		];
		$args['sidebar'] = [
			'facebook' => [
				'title'       => __( 'Web Creators Community', 'kadence-recaptcha' ),
				'description' => __( 'Join our community of fellow kadence users creating effective websites! Share your site, ask a question and help others.', 'kadence-recaptcha' ),
				'link'        => 'https://www.facebook.com/groups/webcreatorcommunity',
				'link_text'   => __( 'Join our Facebook Group', 'kadence-recaptcha' ),
			],
			'docs'     => [
				'title'       => __( 'Documentation', 'kadence-recaptcha' ),
				'description' => __( 'Need help? We have a knowledge base full of articles to get you started.', 'kadence-recaptcha' ),
				'link'        => 'https://www.kadencewp.com/knowledge-base/configure-kadence-recaptcha/',
				'link_text'   => __( 'Browse Docs', 'kadence-recaptcha' ),
			],
			'support'  => [
				'title'       => __( 'Support', 'kadence-recaptcha' ),
				'description' => __( 'Have a question, we are happy to help! Get in touch with our support team.', 'kadence-recaptcha' ),
				'link'        => 'https://www.kadencewp.com/premium-support-tickets/',
				'link_text'   => __( 'Submit a Ticket', 'kadence-recaptcha' ),
			],
		];
		Kadence_Settings_Engine::set_args( self::OPT_NAME, $args );
		Kadence_Settings_Engine::set_section(
			self::OPT_NAME,
			[
				'id'         => 'kc_general',
				'title'      => __( 'API Keys', 'kadence-recaptcha' ),
				'long_title' => __( 'CAPTCHA API Keys', 'kadence-recaptcha' ),
				'desc'       => '',
				'fields'     => [
					[
						'id'      => 'enable_v3',
						'type'    => 'select',
						'title'   => __( 'CAPTCHA Type', 'kadence-recaptcha' ),
						'options' => [
							0 => __( 'Google v2', 'kadence-recaptcha' ),
							1 => __( 'Google v3', 'kadence-recaptcha' ),
							2 => __( 'Turnstile', 'kadence-recaptcha' ),
						],
						'default' => 0,
					],
					[
						'id'        => 'kt_re_site_key',
						'type'      => 'text',
						'title'     => __( 'V2 Site Key:', 'kadence-recaptcha' ),
						'help'      => sprintf( __( 'Get API keys here: %s', 'kadence-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/</a>' ),
						'obfuscate' => true,
						'required'  => [ [ 'enable_v3', '!=', 1 ], [ 'enable_v3', '!=', 2 ] ],
					],
					[
						'id'        => 'kt_re_secret_key',
						'type'      => 'text',
						'title'     => __( 'V2 Secret Key:', 'kadence-recaptcha' ),
						'help'      => sprintf( __( 'Get API keys here: %s', 'kadence-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/</a>' ),
						'obfuscate' => true,
						'required'  => [ [ 'enable_v3', '!=', 1 ], [ 'enable_v3', '!=', 2 ] ],
					],
					[
						'id'        => 'v3_re_site_key',
						'type'      => 'text',
						'title'     => __( 'V3 Site Key:', 'kadence-recaptcha' ),
						'help'      => sprintf( __( 'Get API keys here: %s', 'kadence-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/</a>' ),
						'obfuscate' => true,
						'required'  => [ 'enable_v3', '=', 1 ],
					],
					[
						'id'        => 'v3_re_secret_key',
						'type'      => 'text',
						'title'     => __( 'V3 Secret Key:', 'kadence-recaptcha' ),
						'help'      => sprintf( __( 'Get API keys here: %s', 'kadence-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/</a>' ),
						'obfuscate' => true,
						'required'  => [ 'enable_v3', '=', 1 ],
					],
					[
						'id'        => 'turnstile_site_key',
						'type'      => 'text',
						'title'     => __( 'Turnstile Site Key:', 'kadence-recaptcha' ),
						'help'      => sprintf( __( 'Get API keys here: %s', 'kadence-recaptcha' ), '<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">https://dash.cloudflare.com/</a>' ),
						'obfuscate' => true,
						'required'  => [ 'enable_v3', '=', 2 ],
					],
					[
						'id'        => 'turnstile_secret_key',
						'type'      => 'text',
						'title'     => __( 'Turnstile Secret Key:', 'kadence-recaptcha' ),
						'help'      => sprintf( __( 'Get API keys here: %s', 'kadence-recaptcha' ), '<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">https://dash.cloudflare.com/</a>' ),
						'obfuscate' => true,
						'required'  => [ 'enable_v3', '=', 2 ],
					],
					[
						'id'    => 'recaptcha_preview',
						'type'  => 'recaptcha_preview',
						'title' => __( 'Validate Keys', 'kadence-recaptcha' ),
					],
					[
						'id'       => 'v3_pass_score',
						'type'     => 'select',
						'title'    => __( 'reCAPTCHA score threshold', 'kadence-recaptcha' ),
						'options'  => [
							'0'   => __( '0', 'kadence-recaptcha' ),
							'0.1' => __( '0.1', 'kadence-recaptcha' ),
							'0.2' => __( '0.2', 'kadence-recaptcha' ),
							'0.3' => __( '0.3', 'kadence-recaptcha' ),
							'0.4' => __( '0.4', 'kadence-recaptcha' ),
							'0.5' => __( '0.5', 'kadence-recaptcha' ),
							'0.6' => __( '0.6', 'kadence-recaptcha' ),
							'0.7' => __( '0.7', 'kadence-recaptcha' ),
							'0.8' => __( '0.8', 'kadence-recaptcha' ),
							'0.9' => __( '0.9', 'kadence-recaptcha' ),
							'1'   => __( '1', 'kadence-recaptcha' ),
						],
						'help'     => __( 'reCAPTCHA will rank traffic and interactions based on a score of 0.0 to 1.0, with a 1.0 being a good interaction and scores closer to 0.0 indicating a good likelihood that the traffic was generated by bots', 'kadence-recaptcha' ),
						'default'  => '0.5',
						'required' => [ 'enable_v3', '=', 1 ],
					],
					[
						'id'       => 'recaptcha_url',
						'type'     => 'select',
						'title'    => __( 'reCAPTCHA URL', 'kadence-recaptcha' ),
						'options'  => [
							'google'    => __( 'google.com', 'kadence-recaptcha' ),
							'recaptcha' => __( 'recaptcha.net', 'kadence-recaptcha' ),
						],
						'help'     => __( 'Both options still use google reCAPTCHA, however recaptcha.net is more likly to work in countries that block google.', 'kadence-recaptcha' ),
						'default'  => 'google',
						'required' => [ 'enable_v3', '!=', 2 ],
					],
					[
						'id'       => 'recaptcha_lang',
						'type'     => 'text',
						'title'    => __( 'Force Specific Language', 'kadence-recaptcha' ),
						'help'     => sprintf( __( 'View language codes here: %s', 'kadence-recaptcha' ), '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">https://developers.google.com/recaptcha/docs/language/</a>' ),
						'required' => [ 'enable_v3', '!=', 2 ],
					],
				],
			]
		);
		Kadence_Settings_Engine::set_section(
			self::OPT_NAME,
			[
				'id'         => 'kc_forms',
				'title'      => __( 'Form Settings', 'kadence-recaptcha' ),
				'long_title' => __( 'Form Settings', 'kadence-recaptcha' ),
				'desc'       => '',
				'fields'     => [
					[
						'id'      => 'enable_for_comments',
						'type'    => 'switch',
						'title'   => __( 'Enable for Post and Page Comments', 'kadence-recaptcha' ),
						'default' => 1,
					],
					[
						'id'      => 'enable_for_login',
						'type'    => 'switch',
						'title'   => __( 'Enable for Login', 'kadence-recaptcha' ),
						'default' => 0,
					],
					[
						'id'      => 'enable_for_lost_password',
						'type'    => 'switch',
						'title'   => __( 'Enable for Lost Password Form', 'kadence-recaptcha' ),
						'default' => 0,
					],
					[
						'id'      => 'enable_for_registration',
						'type'    => 'switch',
						'title'   => __( 'Enable for Registration', 'kadence-recaptcha' ),
						'default' => 0,
					],
					[
						'id'      => 'enable_for_woocommerce_checkout',
						'type'    => 'switch',
						'title'   => __( 'Enable for Woocommerce Checkout', 'kadence-recaptcha' ),
						'default' => 0,
					],
					[
						'id'      => 'enable_for_woocommerce',
						'type'    => 'switch',
						'title'   => __( 'Enable for Woocommerce Reviews', 'kadence-recaptcha' ),
						'default' => 1,
					],
				],
			]
		);
		Kadence_Settings_Engine::set_section(
			self::OPT_NAME,
			[
				'id'         => 'kc_design',
				'title'      => __( 'Design Settings', 'kadence-recaptcha' ),
				'long_title' => __( 'Design Settings', 'kadence-recaptcha' ),
				'desc'       => '',
				'fields'     => [
					[
						'id'       => 'hide_v3_badge',
						'type'     => 'switch',
						'title'    => __( 'Hide reCAPTCHA badge', 'kadence-recaptcha' ),
						'help'     => sprintf( __( 'Hiding requires that information about recaptcha be added to your form: %s', 'kadence-recaptcha' ), '<a href="https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed" target="_blank">See FAQ</a>' ),
						'default'  => 0,
						'required' => [ 'enable_v3', '=', 1 ],
					],
					[
						'id'       => 'show_v3_notice',
						'type'     => 'switch',
						'title'    => __( 'Add reCAPTCHA notice info to form', 'kadence-recaptcha' ),
						'help'     => __( 'This will add the required reCAPTCHA version 3 informtion to your form.', 'kadence-recaptcha' ),
						'default'  => 0,
						'required' => [ 
							[ 'enable_v3', '=', 1 ],
							[ 'hide_v3_badge', '=', 1 ],
						],
					],
					[
						'id'       => 'v3_notice',
						'type'     => 'textarea',
						'title'    => __( 'Optional - Custom Notice Info', 'kadence-recaptcha' ),
						'help'     => 'If left empty then "This site is protected by reCAPTCHA and the Google Privacy Policy and Terms of Service apply" is used.',
						'required' => [ 
							[ 'enable_v3', '=', 1 ],
							[ 'hide_v3_badge', '=', 1 ],
							[ 'show_v3_notice', '=', 1 ],
						],
					],
					[
						'id'       => 'kt_re_theme',
						'type'     => 'select',
						'title'    => __( 'Choose a theme', 'kadence-recaptcha' ),
						'options'  => [
							'light' => __( 'Light', 'kadence-recaptcha' ),
							'dark'  => __( 'Dark', 'kadence-recaptcha' ),
						],
						'default'  => 'light',
						'width'    => 'width:60%',
						'required' => [ 'enable_v3', '!=', 1 ],
					],
					[
						'id'       => 'kt_re_size',
						'type'     => 'select',
						'title'    => __( 'Choose a size', 'kadence-recaptcha' ),
						'options'  => [
							'normal'  => __( 'Normal', 'kadence-recaptcha' ),
							'compact' => __( 'Compact', 'kadence-recaptcha' ),
						],
						'default'  => 'normal',
						'width'    => 'width:60%',
						'required' => [ 'enable_v3', '=', 0 ],
					],
					[
						'id'       => 'kt_re_type',
						'type'     => 'select',
						'title'    => __( 'Choose a type', 'kadence-recaptcha' ),
						'options'  => [
							'image' => __( 'Image', 'kadence-recaptcha' ),
							'audio' => __( 'Audio', 'kadence-recaptcha' ),
						],
						'default'  => 'image',
						'width'    => 'width:60%',
						'required' => [ 'enable_v3', '=', 0 ],
					],
					[
						'id'       => 'kt_re_align',
						'type'     => 'select',
						'title'    => __( 'Choose a alignment', 'kadence-recaptcha' ),
						'options'  => [
							'left'   => __( 'Left', 'kadence-recaptcha' ),
							'center' => __( 'Center', 'kadence-recaptcha' ),
							'right'  => __( 'Right', 'kadence-recaptcha' ),
						],
						'default'  => 'left',
						'width'    => 'width:60%',
						'required' => [ 'enable_v3', '!=', 1 ],
					],
					[
						'id'       => 'kt_recaptcha_gdpr_info',
						'type'     => 'info',
						'desc'     => __( 'Privacy Consent', 'kadence-recaptcha' ),
						'required' => [ 'enable_v3', '=', 0 ],
					],
					[
						'id'       => 'enable_permission',
						'type'     => 'switch',
						'title'    => __( 'Enable privacy consent required before reCAPTCHA scripts are loaded', 'kadence-recaptcha' ),
						'default'  => 0,
						'required' => [ 'enable_v3', '=', 0 ],
					],
					[
						'id'       => 'consent_label',
						'type'     => 'text',
						'title'    => __( 'Consent Label', 'kadence-recaptcha' ),
						'required' => [
							[ 'enable_permission', '=', 1 ],
							[ 'enable_v3', '=', 0 ],
						],
					],
					[
						'id'       => 'consent_btn',
						'type'     => 'text',
						'title'    => __( 'Consent Button Text', 'kadence-recaptcha' ),
						'required' => [
							[ 'enable_permission', '=', 1 ],
							[ 'enable_v3', '=', 0 ],
						],
					],
					[
						'id'       => 'consent_cookie',
						'type'     => 'text',
						'title'    => __( 'Consent Cookie Name', 'kadence-recaptcha' ),
						'subtitle' => __( 'You can use a custom cookie name or one that matches another consent plugin.', 'kadence-recaptcha' ),
						'required' => [
							[ 'enable_permission', '=', 1 ],
							[ 'enable_v3', '=', 0 ],
						],
					],
				],
			]
		);
		if ( Kadence_Recaptcha::is_kadence_theme() ) {
			Kadence_Settings_Engine::set_section(
				self::OPT_NAME,
				[
					'id'         => 'kc_theme',
					'title'      => __( 'Theme Form Settings', 'kadence-recaptcha' ),
					'long_title' => __( 'Theme Form Settings', 'kadence-recaptcha' ),
					'desc'       => '',
					'fields'     => [
						[
							'id'      => 'enable_for_contact',
							'type'    => 'switch',
							'title'   => __( 'Enable for theme contact form', 'kadence-recaptcha' ),
							'default' => 0,
						],
						[
							'id'      => 'enable_for_testimonial',
							'type'    => 'switch',
							'title'   => __( 'Enable for testimonial form', 'kadence-recaptcha' ),
							'default' => 0,
						],
					],
				]
			);
		}
	}
}
new Kadence_Recaptcha_Settings();
