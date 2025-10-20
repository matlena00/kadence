<?php
/**
 * Form submit actions.
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Squiz.Commenting.FunctionComment.Missing, Generic.Commenting.DocComment.MissingShort, Squiz.Commenting.FunctionComment.MissingParamTag, Squiz.Commenting.FunctionComment.MissingParamType, WordPressVIPMinimum.Functions.RestrictedFunctions.url_to_postid_url_to_postid, WordPress.WP.AlternativeFunctions.json_encode_json_encode, WordPress.PHP.DevelopmentFunctions.error_log_error_log, Squiz.NamingConventions.ValidVariableName.StringNotCamelCaps, WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid, WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

/**
 * Form submit actions class.
 */
class Kadence_Blocks_Pro_Advanced_Form_Submit_Actions {

	/**
	 * Form arguments
	 *
	 * @var array
	 */
	public $form_args;

	/**
	 * Form responses
	 *
	 * @var array
	 */
	public $responses;

	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $post_id;

	public function __construct( $form_args, $responses, $post_id ) {
		$this->form_args = $form_args;
		$this->responses = $responses;
		$this->post_id   = $post_id;
	}

	/**
	 * @param $map
	 * @param $no_email
	 *
	 * @return array
	 */
	public function get_mapped_attributes_from_responses( $map, $no_email = true ) {
		$mapped_attributes = array();

		if ( ! empty( $map ) ) {
			foreach ( $this->responses as $key => $data ) {
				$unique_id = $data['uniqueID'];
				if ( isset( $map[ $unique_id ] ) && ( ! empty( $map[ $unique_id ] ) ) ) {
					if ( $no_email && 'email' === $map[ $unique_id ] ) {
						continue;
					} elseif ( 'none' === $map[ $unique_id ] ) {
						continue;
					} elseif ( 'OPT_IN' === $map[ $unique_id ] ) {
						if ( $data['value'] ) {
							$mapped_attributes[ $map[ $unique_id ] ] = true;
						} else {
							$mapped_attributes[ $map[ $unique_id ] ] = false;
						}
					} else {
						$mapped_attributes[ $map[ $unique_id ] ] = $data['value'];
					}
				}
			}
		}

		return $mapped_attributes;
	}

	public function get_email_from_responses( $map ) {
		$email        = '';
		$mapped_email = '';

		foreach ( $this->responses as $key => $data ) {
			$unique_id = $data['uniqueID'];
			if ( $map && isset( $map[ $unique_id ] ) && 'email' === $map[ $unique_id ] && ! $email ) {
				$mapped_email = $data['value'];
			} elseif ( 'email' === $data['type'] ) {
				$email = $data['value'];
			}
		}

		return $mapped_email ? $mapped_email : $email;
	}

	public function get_response_field_by_name( $name ) {
		foreach ( $this->responses as $response ) {
			if ( isset( $response['name'] ) && $response['name'] == $name ) {
				return $response;
			}
		}
		return '';
	}

	public function do_field_replacements( $text ) {
		if ( strpos( $text, '{' ) !== false && strpos( $text, '}' ) !== false ) {
			preg_match_all( '/{(.*?)}/', $text, $match );
			if ( is_array( $match ) && isset( $match[1] ) && is_array( $match[1] ) ) {
				foreach ( $match[1] as $field_name ) {
					if ( isset( $field_name ) ) {
						$field_to_insert = $this->get_response_field_by_name( $field_name );
						$replace         = '';
						if ( $field_to_insert && isset( $field_to_insert['value'] ) ) {
							$replace = $field_to_insert['value'];
						} 
						$text = str_replace( '{' . $field_name . '}', $replace, $text );
					}
				}
			}
		}

		if ( strpos( $text, '{page_title}' ) !== false ) {
			global $post;
			$refer_id = is_object( $post ) ? $post->ID : url_to_postid( wp_get_referer() );
			$text     = str_replace( '{page_title}', get_the_title( $refer_id ), $text );
		}

		return $text;
	}

	public function sib_rest_call( $api_url, $method, $body ) {
		$api_key = get_option( 'kadence_blocks_send_in_blue_api' );
		if ( empty( $api_key ) ) {
			return false;
		}

		$response = wp_remote_post(
			$api_url,
			array(
				'method'  => $method,
				'timeout' => 10,//phpcs:ignore
				'headers' => array(
					'accept'       => 'application/json',
					'content-type' => 'application/json',
					'api-key'      => $api_key,
				),
				'body'    => json_encode( $body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log( "Something went wrong: $error_message" );

			return false;
		} else {
			if ( ! isset( $response['response'] ) || ! isset( $response['response']['code'] ) ) {
				error_log( __( 'No Response from SendInBlue', 'kadence-blocks-pro' ) );

				return false;
			}
			if ( 400 === $response['response']['code'] ) {
				error_log( $response['response']['message'] );

				return false;
			}
		}
		return $response;
	}

	public function sendInBlue() {
		$api_key = get_option( 'kadence_blocks_send_in_blue_api' );
		if ( empty( $api_key ) ) {
			return;
		}
		$sendinblue_default = array(
			'lists'           => array(),
			'map'            => array(),
			'doubleOptin'    => false,
			'templateId'     => '',
			'redirectionUrl' => '',
		);

		$sendinblue_args = ( isset( $this->form_args['attributes']['sendinblue'] ) && is_array( $this->form_args['attributes']['sendinblue'] ) && isset( $this->form_args['attributes']['sendinblue'] ) ? $this->form_args['attributes']['sendinblue'] : $sendinblue_default );
		$lists           = ( isset( $sendinblue_args['lists'] ) ? $sendinblue_args['lists'] : '' );
		$map             = ( isset( $sendinblue_args['map'] ) && is_array( $sendinblue_args['map'] ) ? $sendinblue_args['map'] : array() );
		$templateId      = ( isset( $sendinblue_args['templateId'] ) && ! empty( $sendinblue_args['templateId'] ) ? $sendinblue_args['templateId'] : false );
		if ( $templateId ) {
			$redirectionUrl = ( isset( $sendinblue_args['redirectionUrl'] ) && ! empty( $sendinblue_args['redirectionUrl'] ) ? $sendinblue_args['redirectionUrl'] : false );
			if ( $redirectionUrl ) {
				$doubleOptin = ( isset( $sendinblue_args['doubleOptin'] ) ? $sendinblue_args['doubleOptin'] : false );
			} else {
				$doubleOptin = false;
			}
		} else {
			$doubleOptin = false;
		}
		$body = array();
		if ( $doubleOptin ) {
			$body['templateId']     = $templateId;
			$body['redirectionUrl'] = $redirectionUrl;
		}
		$email = false;

		$mapped_attributes = $this->get_mapped_attributes_from_responses( $map );
		$email             = $this->get_email_from_responses( $map );

		$body['email'] = $email;

		if ( ! empty( $lists ) ) {
			$lists_ids = array(
				'listIds' => array(),
			);
			foreach ( $lists as $key => $value ) {
				$lists_ids['listIds'][] = $value['value'];
			}
		} else {
			$lists_ids = array(
				'listIds' => array(),
			);
		}
		if ( $doubleOptin ) {
			$body['includeListIds'] = $lists_ids['listIds'];
		} else {
			$body['listIds']       = $lists_ids['listIds'];
			$body['updateEnabled'] = true;
			if ( ! empty( $mapped_attributes ) ) {
				$body['attributes'] = $mapped_attributes;
			}
		}

		if ( isset( $body['email'] ) ) {
			// Create contact.
			$api_url = ( $doubleOptin ? 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation' : 'https://api.brevo.com/v3/contacts' );
			$method  = 'POST';

			$response = $this->sib_rest_call( $api_url, $method, $body );

			if ( $doubleOptin && $response && $mapped_attributes ) {
				// Update contact.
				$update_api_url = 'https://api.brevo.com/v3/contacts/' . urlencode( $email );
				$update_method  = 'PUT';

				$update_body = array(
					'attributes' => array(),
				);

				$update_body['attributes'] = $mapped_attributes;

				$update_response = $this->sib_rest_call( $update_api_url, $update_method, $update_body );
				$temp            = 1;
			}
		}
	}

	public function mailchimp_rest_call( $api_url, $method, $body ) {
		$api_key = get_option( 'kadence_blocks_mail_chimp_api' );
		if ( empty( $api_key ) ) {
			return false;
		}

		$response = wp_remote_post(
			$api_url,
			array(
				'method'  => $method,
				'timeout' => 10,//phpcs:ignore
				'headers' => array(
					'accept'        => 'application/json',
					'content-type'  => 'application/json',
					'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
				),
				'body'    => json_encode( $body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log( "Something went wrong: $error_message" );
			return false;
		} else {
			if ( ! isset( $response['response'] ) || ! isset( $response['response']['code'] ) ) {
				error_log( __( 'Failed to Connect to MailChimp', 'kadence-blocks-pro' ) );
				return false;
			}
			if ( 400 === $response['response']['code'] || 404 === $response['response']['code'] ) {
				error_log( $response['response']['message'] );
				return false;
			}
		}
		return $response;
	}

	public function mailchimp() {
		$api_key = get_option( 'kadence_blocks_mail_chimp_api' );
		if ( empty( $api_key ) ) {
			return;
		}

		$mailchimp_default = array(
			'list'        => array(),
			'groups'      => array(),
			'tags'        => array(),
			'map'         => array(),
			'doubleOptin' => false,
		);

		$mailchimp_args = ( isset( $this->form_args['attributes']['mailchimp'] ) && is_array( $this->form_args['attributes']['mailchimp'] ) && isset( $this->form_args['attributes']['mailchimp'] ) ? $this->form_args['attributes']['mailchimp'] : $mailchimp_default );
		$list           = ( isset( $mailchimp_args['list'] ) ? $mailchimp_args['list'] : '' );
		$groups         = ( isset( $mailchimp_args['groups'] ) && is_array( $mailchimp_args['groups'] ) ? $mailchimp_args['groups'] : array() );
		$tags           = ( isset( $mailchimp_args['tags'] ) && is_array( $mailchimp_args['tags'] ) ? $mailchimp_args['tags'] : array() );
		$map            = ( isset( $mailchimp_args['map'] ) && is_array( $mailchimp_args['map'] ) ? $mailchimp_args['map'] : array() );
		$doubleOptin    = ( isset( $mailchimp_args['doubleOptin'] ) ? $mailchimp_args['doubleOptin'] : false );
		$body           = array(
			'email_address' => '',
			'status_if_new' => 'subscribed',
			'status'        => 'subscribed',
		);
		if ( $doubleOptin ) {
			$body['status_if_new'] = 'pending';
			$body['double_optin']  = true;
		}

		if ( empty( $list ) || ! is_array( $list ) ) {
			return;
		}
		$key_parts = explode( '-', $api_key );
		if ( empty( $key_parts[1] ) || 0 !== strpos( $key_parts[1], 'us' ) ) {
			return;
		}
		$base_url = 'https://' . $key_parts[1] . '.api.mailchimp.com/3.0/';
		$email    = false;
		if ( ! empty( $groups ) ) {
			foreach ( $groups as $id => $label ) {
				if ( ! isset( $body['interests'] ) ) {
					$body['interests'] = array();
				}
				$body['interests'][ $label['value'] ] = true;
			}
		}
		$tags_array = array();
		if ( ! empty( $tags ) ) {
			foreach ( $tags as $id => $tag_item ) {
				if ( ! isset( $body['tags'] ) ) {
					$body['tags'] = array();
				}
				$body['tags'][] = $tag_item['label'];
				$tags_array[]   = array(
					'name'   => $tag_item['label'],
					'status' => 'active',
				);
			}
		}

		$mapped_attributes = $this->get_mapped_attributes_from_responses( $map );
		$email             = $this->get_email_from_responses( $map );

		// Don't send merge_fields if empty
		if ( ! empty( $mapped_attributes ) ) {
			$body['merge_fields'] = $mapped_attributes;
		}
		$body['email_address'] = $email;


		$list_id = ( isset( $list['value'] ) && ! empty( $list['value'] ) ? $list['value'] : '' );
		if ( empty( $list_id ) ) {
			return;
		}
		if ( isset( $body['email_address'] ) ) {
			$subscriber_hash = md5( strtolower( $body['email_address'] ) );
			$api_url         = $base_url . 'lists/' . $list_id . '/members/' . $subscriber_hash;

			$response = $this->mailchimp_rest_call( $api_url, 'PUT', $body );

			if ( $response && 200 === $response['response']['code'] ) {
				// need to check if tags were added.
				$needs_update = false;
				$body         = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( ! empty( $tags_array ) && empty( $body['tags'] ) ) {
					$needs_update = true;
				} elseif ( ! empty( $tags_array ) && ! empty( $body['tags'] ) && is_array( $body['tags'] ) ) {
					$current_tags = array();
					foreach ( $body['tags'] as $key => $data ) {
						$current_tags[] = $data['name'];
					}
					foreach ( $tags_array as $key => $data ) {
						if ( ! in_array( $data['name'], $current_tags ) ) {
							$needs_update = true;
							break;
						}
					}
				}
				if ( $needs_update ) {
					$tag_url = $base_url . 'lists/' . $list_id . '/members/' . $subscriber_hash . '/tags';

					$tag_response = $this->mailchimp_rest_call( $tag_url, 'POST', array( 'tags' => $tags_array ) );
				}
			}
		}
	}

	public function convertkit() {
		$api_key            = get_option( 'kadence_blocks_convertkit_api' );
		$base_url           = 'https://api.convertkit.com/v3/';
		$convertKitSettings = $this->form_args['attributes']['convertkit'];
		$map                = ( isset( $convertKitSettings['map'] ) && is_array( $convertKitSettings['map'] ) ? $convertKitSettings['map'] : array() );


		if ( empty( $api_key ) ) {
			return;
		}

		$mapped_attributes = $this->get_mapped_attributes_from_responses( $map );

		// first_name is special attribute
		$first_name = '';
		if ( isset( $mapped_attributes['first_name'] ) ) {
			$first_name = $mapped_attributes['first_name'];
			unset( $mapped_attributes['first_name'] );
		}

		$email = $this->get_email_from_responses( $map );

		if ( ! $email ) {
			return;
		}

		$tag_ids = array();
		if ( ! empty( $convertKitSettings['tags'] ) ) {
			$tag_ids = array_column( $convertKitSettings['tags'], 'value' );
		}

		$fields = array();
		if ( ! empty( $mapped_attributes ) ) {
			$fields = $mapped_attributes;
		}

		$request_args = array(
			'method'  => 'POST',
			'timeout' => 10,//phpcs:ignore
			'headers' => array(
				'accept'       => 'application/json',
				'content-type' => 'application/json',
			),
			'body'    => json_encode(
				array(
					'api_key'    => $api_key,
					'email'      => $email,
					'tags'       => $tag_ids,
					'fields'     => $fields,
					'first_name' => $first_name,
				) 
			),
		);

		// Add to form.
		if ( ! empty( $convertKitSettings['form']['value'] ) && is_numeric( $convertKitSettings['form']['value'] ) ) {
			$response = wp_remote_post( $base_url . 'forms/' . $convertKitSettings['form']['value'] . '/subscribe', $request_args );
		}

		// Add to sequence.
		if ( ! empty( $convertKitSettings['sequence']['value'] ) && is_numeric( $convertKitSettings['sequence']['value'] ) ) {
			$response = wp_remote_post( $base_url . 'sequences/' . $convertKitSettings['sequence']['value'] . '/subscribe', $request_args );
		}

		// Add tags. (Requires secret, not key)
		// if ( ! empty( $convertKitSettings['tags'] ) ) {
		// $tag_ids = array_column( $convertKitSettings['tags'], 'value' );

		// foreach ( $tag_ids as $tag_id ) {
		// $response = wp_remote_post( $base_url . 'tags/' . $tag_id . '/subscribe', $request_args );
		// }
		// }
	}
	/**
	 * Run Get Response API
	 */
	public function getresponse() {
		$api_key  = get_option( 'kadence_blocks_getresponse_api_key' );
		$api_base = get_option( 'kadence_blocks_getresponse_api_endpoint' );

		$getresponse_settings = $this->form_args['attributes']['getresponse'];
		$map                  = ( isset( $getresponse_settings['map'] ) ? $getresponse_settings['map'] : array() );
		$double_optin         = ( isset( $getresponse_settings['doubleOptin'] ) ? $getresponse_settings['doubleOptin'] : false );

		if ( empty( $api_key ) || empty( $api_base ) ) {
			return;
		}

		$field_map = array();
		if ( ! empty( $map ) ) {
			foreach ( $this->responses as $key => $data ) {
				$unique_id = $data['uniqueID'];
				if ( isset( $map[ $unique_id ] ) && ! empty( $map[ $unique_id ] ) ) {
					if ( 'none' === $map[ $unique_id ] ) {
						continue;
					} elseif ( 'OPT_IN' === $map[ $unique_id ] ) {
						if ( $data['value'] ) {
							$field_map[ $map[ $unique_id ] ] = true;
						} else {
							$field_map[ $map[ $unique_id ] ] = false;
						}
					} elseif ( in_array( $map[ $unique_id ], [ 'name', 'email' ] ) ) {
						$field_map[ $map[ $unique_id ] ] = $data['value'];
					} else {
						$field_map['customFieldValues'][] = array(
							'customFieldId' => $map[ $unique_id ],
							'value' => [ $data['value'] ],
						);
					}
				}
			}
		} else {
			foreach ( $this->responses as $key => $data ) {
				if ( 'email' === $data['type'] ) {
					$email              = $data['value'];
					$field_map['email'] = $data['value'];
					break;
				}
			}
		}

		// By the end of mapping (or not mapping), we must have a valid email address.
		$email = isset( $field_map['email'] ) ? $field_map['email'] : '';
		if ( ! $email || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return;
		}

		if ( ! empty( $getresponse_settings['listMulti'][0] ) && ! empty( $getresponse_settings['listMulti'][0]['value'] ) ) {
			$field_map['campaign'] = array( 'campaignId' => $getresponse_settings['listMulti'][0]['value'] );

			// Tags.
			$tags_array = array();
			if ( ! empty( $getresponse_settings['tags'] ) ) {
				foreach ( $getresponse_settings['tags'] as $tag_index => $tag_item ) {
					$tags_array[] = array( 'tagId' => $tag_item['value'] );
				}
			}

			$getresponse   = new KBP_Getresponse( $api_base, $api_key );
			$found_contact = $getresponse->find_contact( $email );

			if ( false === $found_contact ) {
				$create_contact = $getresponse->create_contact( $field_map, $tags_array );

				if ( $create_contact !== false ) {
					error_log( __( 'Unable to create contact in GetResponse.', 'kadence-blocks-pro' ) );
					return;
				}           
			} else {
				$contact_id     = $found_contact['contactId'];
				$update_contact = $getresponse->update_contact( $contact_id, $field_map, $tags_array );
			}
		}
	}

	/**
	 * Run ActiveCampaign API
	 */
	public function activecampaign() {
		$api_key  = get_option( 'kadence_blocks_activecampaign_api_key' );
		$api_base = get_option( 'kadence_blocks_activecampaign_api_base' );

		$active_campaign_settings = $this->form_args['attributes']['activecampaign'];
		$map                      = ( isset( $active_campaign_settings['map'] ) ? $active_campaign_settings['map'] : array() );
		$double_optin             = ( isset( $active_campaign_settings['doubleOptin'] ) ? $active_campaign_settings['doubleOptin'] : false );

		if ( empty( $api_key ) || empty( $api_base ) ) {
			return;
		}

		$field_map = array();
		if ( ! empty( $map ) ) {
			foreach ( $this->responses as $key => $data ) {
				$unique_id = $data['uniqueID'];
				if ( isset( $map[ $unique_id ] ) && ! empty( $map[ $unique_id ] ) ) {
					if ( 'none' === $map[ $unique_id ] ) {
						continue;
					} elseif ( 'OPT_IN' === $map[ $unique_id ] ) {
						if ( $data['value'] ) {
							$field_map[ $map[ $unique_id ] ] = true;
						} else {
							$field_map[ $map[ $unique_id ] ] = false;
						}
					} elseif ( in_array( $map[ $unique_id ], [ 'firstName', 'phone', 'lastName', 'email' ] ) ) {
						$field_map[ $map[ $unique_id ] ] = $data['value'];
					} else {
						$field_map['fieldValues'][] = array(
							'field' => $map[ $unique_id ],
							'value' => $data['value'],
						);
					}
				}
			}
		} else {
			foreach ( $this->responses as $key => $data ) {
				if ( 'email' === $data['type'] ) {
					$email              = $data['value'];
					$field_map['email'] = $data['value'];
					break;
				}
			}
		}

		// By the end of mapping (or not mapping), we must have a valid email address.
		$email = isset( $field_map['email'] ) ? $field_map['email'] : '';
		if ( ! $email || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			return;
		}
		$active_campaign = new KBP_Active_Campaign( $api_base, $api_key );
		$found_contact   = $active_campaign->update_or_create_contact( $field_map );
		if ( empty( $found_contact ) ) {
			error_log( __( 'No Response from Active Campaign', 'kadence-blocks-pro' ) );
			return;
		}
		// Add to List.
		$list_array = array();
		if ( ! empty( $active_campaign_settings['listMulti'] ) ) {
			foreach ( $active_campaign_settings['listMulti'] as $list_index => $list_item ) {
				$list_array[] = $list_item['value'];
			}
		}
		if ( empty( $list_array ) ) {
			if ( ! empty( $active_campaign_settings['list']['value'] ) && is_numeric( $active_campaign_settings['list']['value'] ) ) {
				$list_array[] = $active_campaign_settings['list']['value'];
			}
		}
		if ( empty( $list_array ) ) {
			return;
		}
		$contact_list = $active_campaign->add_lists_to_contact( $found_contact, $list_array, $double_optin );

		// Add to automation.
		if ( ! empty( $active_campaign_settings['automation']['value'] ) && is_numeric( $active_campaign_settings['automation']['value'] ) ) {
			$contact_automation = $active_campaign->add_contact_to_automation( $found_contact, $active_campaign_settings['automation']['value'] );
		}
		// Add Tags.
		$tags_array = array();
		if ( ! empty( $active_campaign_settings['tags'] ) ) {
			foreach ( $active_campaign_settings['tags'] as $tag_index => $tag_item ) {
				$tags_array[] = $tag_item['value'];
			}
		}
		if ( ! empty( $tags_array ) ) {
			$contact_tag = $active_campaign->add_tags_to_contact( $found_contact, $tags_array );
		}
	}

	public function webhook() {
		$webhook_defaults = array(
			'url' => '',
		);

		$webhookSettings = $this->form_args['attributes']['webhook'];


		$webhook_args = ( isset( $webhookSettings ) && is_array( $webhookSettings ) ) ? $webhookSettings : $webhook_defaults;

		if ( empty( $webhook_args['url'] ) ) {
			return;
		}

		$map     = ( isset( $webhook_args['map'] ) && is_array( $webhook_args['map'] ) ? $webhook_args['map'] : array() );
		$user_ip = $this->get_client_ip();
		$browser = $this->get_browser();

		$name = esc_attr( strip_tags( get_the_title( $this->post_id ) ) );//phpcs:ignore
		$body = array(
			'post_name'    => $name,
			'post_url'     => wp_get_referer(),
			'post_id'      => $this->post_id,
			'form_id'      => 'form_id',
			'user_id'      => get_current_user_id(),
			'user_ip'      => $user_ip,
			'user_device'  => ( $browser ? $browser['name'] . '/' . $browser['platform'] : esc_html__( 'Not Collected', 'kadence-blocks-pro' ) ),
			'date_created' => date_i18n( get_option( 'date_format' ) ),
			'time_created' => date_i18n( get_option( 'time_format' ) ),
		);

		$map = array_filter( $map );

		// If there's no mapped attributes, send everything with reasonable labels
		if ( empty( $map ) ) {
			$mapped_attributes = array();
			foreach ( $this->responses as $response ) {
				$simple_label                       = strtolower( str_replace( ' ', '_', $response['label'] ) );
				$mapped_attributes[ $simple_label ] = $response['value'];
			}
		} else {
			$mapped_attributes = $this->get_mapped_attributes_from_responses( $map, false );
		}
		$body = array_merge( $body, $mapped_attributes );

		$args     = apply_filters( 'kadence_blocks_pro_webhook_args', array( 'body' => $body ) );
		$response = wp_remote_post( $webhook_args['url'], $args );

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return;
		}
	}

	public function entry( $post_id ) {
		$entry_defaults = array(
			'userIP'     => true,
			'userDevice' => true,
		);

		$submission_results = array(
			'success' => false,
			'entry_id' => '',
		);

		$entry_args = ( isset( $this->form_args['attributes']['entry'] ) && is_array( $this->form_args['attributes']['entry'] ) ) ? $this->form_args['attributes']['entry'] : $entry_defaults;
		$user_ip    = ( ! isset( $entry_args['userIP'] ) || ( isset( $entry_args['userIP'] ) && $entry_args['userIP'] ) ? $this->get_client_ip() : ip2long( '0.0.0.0' ) );
		$browser    = ( ! isset( $entry_args['userDevice'] ) || ( isset( $entry_args['userDevice'] ) && $entry_args['userDevice'] ) ? $this->get_browser() : false );
		$form_name  = esc_attr( strip_tags( get_the_title( $post_id ) ) );//phpcs:ignore
		$referer    = wp_get_referer();
		if ( strlen( $referer ) > 255 ) {
			$referer = strtok( $referer, '?' );
		}
		if ( strlen( $referer ) > 255 ) {
			$referer = substr( $referer, 0, 255 );
		}
		$data = array(
			'name'         => $form_name,
			'form_id'      => $post_id,
			'post_id'      => $this->post_id,
			'user_id'      => get_current_user_id(),
			'date_created' => current_time( 'mysql' ),
			'user_ip'      => $user_ip,
			'referer'      => $referer,
			'user_device'  => ( $browser ? $browser['name'] . '/' . $browser['platform'] : esc_html__( 'Not Collected', 'kadence-blocks-pro' ) ),
		);

		$entries  = new KBP\Queries\Entry();
		$entry_id = $entries->add_item( $data );

		if ( $entry_id ) {
			foreach ( $this->responses as $key => $meta_data ) {
				$response = $this->add_field( $entry_id, 'kb_field_' . $key, $meta_data );
			}

			$submission_results = array(
				'success' => true,
				'entry_id' => $entry_id,
			);
		}

		return $submission_results;
	}

	public function autoEmail() {
		$auto_defaults = array(
			'subject'   => __( 'Thanks for contacting us!', 'kadence-blocks-pro' ),
			'message'   => __( 'Thanks for getting in touch, we will respond within the next 24 hours.', 'kadence-blocks-pro' ),
			'fromEmail' => '',
			'fromName'  => '',
			'replyTo'   => '',
			'cc'        => '',
			'bcc'       => '',
			'html'      => true,
		);

		$auto_email_args = ( isset( $this->form_args['attributes']['autoEmail'] ) && is_array( $this->form_args['attributes']['autoEmail'] ) ) ? $this->form_args['attributes']['autoEmail'] : $auto_defaults;
		$subject         = isset( $auto_email_args['subject'] ) && ! empty( trim( $auto_email_args['subject'] ) ) ? $auto_email_args['subject'] : __( 'Thanks for contacting us!', 'kadence-blocks-pro' );
		$message         = isset( $auto_email_args['message'] ) && ! empty( trim( $auto_email_args['message'] ) ) ? $auto_email_args['message'] : __( 'Thanks for getting in touch, we will respond within the next 24 hours.', 'kadence-blocks-pro' );
		$reply_email     = isset( $auto_email_args['replyTo'] ) && ! empty( trim( $auto_email_args['replyTo'] ) ) ? sanitize_email( trim( $auto_email_args['replyTo'] ) ) : false;
		$to              = isset( $auto_email_args['emailTo'] ) && ! empty( trim( $auto_email_args['emailTo'] ) ) ? $auto_email_args['emailTo'] : false;

		$subject = $this->do_field_replacements( $subject );
		$message = $this->do_field_replacements( $message );

		if ( ! $to ) {
			foreach ( $this->responses as $key => $data ) {
				if ( 'email' === $data['type'] ) {
					$to = $data['value'];
					break;
				}
			}
		}
		// Can't find someone to email?
		if ( ! $to ) {
			return;
		}

		if ( ! isset( $auto_email_args['html'] ) || ( isset( $auto_email_args['html'] ) && $auto_email_args['html'] ) ) {
			$args          = array(
				'message' => $message,
				'fields'  => $this->responses,
			);
			$email_content = kadence_blocks_pro_get_template_html( 'form-auto-email.php', $args );
		} else {
			$email_content = $message . "\n\n";
		}
		$body = $email_content;
		if ( ! isset( $auto_email_args['html'] ) || ( isset( $auto_email_args['html'] ) && $auto_email_args['html'] ) ) {
			$headers = 'Content-Type: text/html; charset=UTF-8' . "\r\n";
		} else {
			$headers = 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
		}
		if ( $reply_email ) {
			$headers .= 'Reply-To: <' . $reply_email . '>' . "\r\n";
		}
		if ( isset( $auto_email_args['fromEmail'] ) && ! empty( trim( $auto_email_args['fromEmail'] ) ) ) {
			$headers .= 'From: ' . ( isset( $auto_email_args['fromName'] ) && ! empty( trim( $auto_email_args['fromName'] ) ) ? trim( $auto_email_args['fromName'] ) . ' ' : '' ) . '<' . sanitize_email( trim( $auto_email_args['fromEmail'] ) ) . '>' . "\r\n";
		}
		if ( isset( $auto_email_args['cc'] ) && ! empty( trim( $auto_email_args['cc'] ) ) ) {
			$cc_emails = array_map( 'trim', explode( ',', $auto_email_args['cc'] ) );
			$sanitized_cc_emails = array_map( 'sanitize_email', $cc_emails );
			$valid_cc_emails = array_filter( $sanitized_cc_emails, 'is_email' );
			if ( ! empty( $valid_cc_emails ) ) {
				$headers .= 'Cc: ' . implode( ',', $valid_cc_emails ) . "\r\n";
			}
		}
		if ( isset( $auto_email_args['bcc'] ) && ! empty( trim( $auto_email_args['bcc'] ) ) ) {
			$bcc_emails = array_map( 'trim', explode( ',', $auto_email_args['bcc'] ) );
			$sanitized_bcc_emails = array_map( 'sanitize_email', $bcc_emails );
			$valid_bcc_emails = array_filter( $sanitized_bcc_emails, 'is_email' );
			if ( ! empty( $valid_bcc_emails ) ) {
				$headers .= 'Bcc: ' . implode( ',', $valid_bcc_emails ) . "\r\n";
			}
		}

		wp_mail( $to, $subject, $body, $headers );//phpcs:ignore
	}


	/**
	 * Add meta data field to a entry
	 *
	 * @since 3.0
	 *
	 * @param int    $entry_id   entry ID.
	 * @param string $meta_key   Meta data name.
	 * @param mixed  $meta_value Meta data value. Must be serializable if non-scalar.
	 * @param bool   $unique     Optional. Whether the same key should not be added. Default false.
	 *
	 * @return false|int
	 */
	public function add_field( $entry_id, $meta_key, $meta_value, $unique = false ) {

		if ( isset( $meta_value['type'] ) && $meta_value['type'] === 'file' ) {
			$file_name       = ! empty( $meta_value['file_name'] ) ? $meta_value['file_name'] : __( 'View File', 'kadence-blocks-pro' );
			$file_name_array = explode( ', ', $file_name );
			if ( count( $file_name_array ) > 1 ) {
				$file_value_array = explode( ', ', $meta_value['value'] );
				$value_output     = array();
				foreach ( $file_name_array as $key => $name ) {
					$value_output[] = '<a href="' . $file_value_array[ $key ] . '" target="_blank">' . $name . '</a>';
				}
				$meta_value['value'] = implode( ', ', $value_output );
			} else {
				$meta_value['value'] = '<a href="' . $meta_value['value'] . '" target="_blank">' . $file_name . '</a>';
			}
		}

		return add_metadata( 'kbp_form_entry', $entry_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Get the client IP address
	 *
	 * @return string
	 */
	public function get_client_ip() {
		$ipaddress = '';

		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];//phpcs:ignore
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {//phpcs:ignore
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];//phpcs:ignore
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];//phpcs:ignore
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];//phpcs:ignore
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];//phpcs:ignore
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {//phpcs:ignore
			$ipaddress = $_SERVER['REMOTE_ADDR'];//phpcs:ignore
		} else {
			$ipaddress = 'UNKNOWN';
		}

		return $ipaddress;
	}

	/**
	 * Get User Agent browser and OS type
	 *
	 * @return array
	 */
	public function get_browser() {
		$u_agent  = $_SERVER['HTTP_USER_AGENT'];//phpcs:ignore
		$bname    = 'Unknown';
		$platform = 'Unknown';
		$version  = '';
		$ub       = '';

		// First get the platform
		if ( preg_match( '/android/i', $u_agent ) ) {
			$platform = 'Android';
		} elseif ( preg_match( '/iphone|ipad|ipod/i', $u_agent ) ) {
			$platform = 'iOS';
		} elseif ( preg_match( '/linux/i', $u_agent ) ) {
			$platform = 'Linux';
		} elseif ( preg_match( '/macintosh|mac os x/i', $u_agent ) ) {
			$platform = 'Mac OS';
		} elseif ( preg_match( '/windows|win32/i', $u_agent ) ) {
			$platform = 'Windows';
		}

		// Next get the name of the useragent
		if ( preg_match( '/MSIE/i', $u_agent ) && ! preg_match( '/Opera/i', $u_agent ) ) {
			$bname = 'Internet Explorer';
			$ub    = 'MSIE';
		} elseif ( preg_match( '/Trident/i', $u_agent ) ) {
			// This condition is for IE11
			$bname = 'Internet Explorer';
			$ub    = 'rv';
		} elseif ( preg_match( '/Firefox/i', $u_agent ) ) {
			$bname = 'Mozilla Firefox';
			$ub    = 'Firefox';
		} elseif ( preg_match( '/Edg/i', $u_agent ) ) {
			$bname = 'Microsoft Edge';
			$ub    = 'Edg';
		} elseif ( preg_match( '/SamsungBrowser|Samsung/i', $u_agent ) ) {
			$bname = 'Samsung Browser';
			$ub    = 'SamsungBrowser';
		} elseif ( preg_match( '/Opera|OPR/i', $u_agent ) ) {
			$bname = 'Opera';
			$ub    = 'Opera';
		} elseif ( preg_match( '/Chrome/i', $u_agent ) ) {
			$bname = 'Google Chrome';
			$ub    = 'Chrome';
		} elseif ( preg_match( '/Safari/i', $u_agent ) ) {
			$bname = 'Apple Safari';
			$ub    = 'Safari';
		} elseif ( preg_match( '/Netscape/i', $u_agent ) ) {
			$bname = 'Netscape';
			$ub    = 'Netscape';
		}

		// Finally get the correct version number
		$known   = array( 'Version', $ub, 'other' );
		$pattern = '#(?<browser>' . join( '|', $known ) .
					')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if ( ! preg_match_all( $pattern, $u_agent, $matches ) ) {//phpcs:ignore
			// We have no matching number just continue
		}

		// See how many we have
		$i = count( $matches['browser'] );

		if ( $i != 1 ) {
			// We will have two since we are not using 'other' argument yet
			// See if version is before or after the name
			if ( strripos( $u_agent, 'Version' ) < strripos( $u_agent, $ub ) ) {
				$version = $matches['version'][0];
			} else {
				$version = $matches['version'][1];
			}
		} else {
			$version = $matches['version'][0];
		}

		// Check if we have a number
		if ( null === $version || '' === $version ) {
			$version = '';
		}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'   => $pattern,
		);
	}
}
