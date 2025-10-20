<?php
/**
 * Form Field Conditions Handling.
 *
 * @package Kadence Blocks Pro
 */

//phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Main plugin class
 */
class KBP_Form_Conditional {
	/**
	 * Instance Control
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Instance Control
	 *
	 * @var null
	 */
	public static $columns = array();

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'trigger_script_load' ), 10 );
		add_filter( 'kadence_advanced_form_input_attributes', array( $this, 'build_conditional_attribute' ), 10, 2 );
		add_filter( 'render_block', array( $this, 'render_blocks' ), 13, 3 );
	}
	/**
	 * Register the script.
	 */
	public function trigger_script_load() {
		if ( ! empty( self::$columns ) && wp_script_is( 'kadence-form-conditional', 'registered' ) && wp_script_is( 'kadence-form-conditional', 'enqueued' ) ) {
			wp_localize_script(
				'kadence-form-conditional',
				'conditionalExtras',
				array(
					'columns' => self::$columns,
				)
			);
		}
	}
	/**
	 * Register the script.
	 */
	public function register_script() {
		wp_register_script( 'kadence-form-conditional', KBP_URL . 'includes/assets/js/kb-conditional-fields.min.js', array(), KBP_VERSION, true );
	}

	/**
	 * Build the conditional attribute from an input attribtues
	 *
	 * @param string $additional_attributes The incoming attributes string.
	 * @param array  $attributes The block attributes.
	 *
	 * @return string
	 */
	public function build_conditional_attribute( $additional_attributes, $attributes ) {
		if ( isset( $attributes['kadenceFieldConditional']['conditionalData']['enable'] ) && true == $attributes['kadenceFieldConditional']['conditionalData']['enable'] && ! empty( $attributes['kadenceFieldConditional']['conditionalData']['rules'] ) ) {
			$conditions_string = $this->get_conditional_rules_string( $attributes );
			return $additional_attributes . ' data-conditional-rules="' . $conditions_string . '" ';
		}
		return $additional_attributes;
	}

	/**
	 * Build a formatted, json encoded string for the conditional rules.
	 *
	 * @param array   $attributes The block attributes.
	 * @param boolean $column The column.
	 *
	 * @return string
	 */
	public function get_conditional_rules_string( $attributes, $column = false ) {
		$condtional_rules = $attributes['kadenceFieldConditional']['conditionalData']['rules'];
		$combine          = ! empty( $attributes['kadenceFieldConditional']['conditionalData']['combine'] ) ? $attributes['kadenceFieldConditional']['conditionalData']['combine'] : 'or';
		$action           = ! empty( $attributes['kadenceFieldConditional']['conditionalData']['action'] ) ? $attributes['kadenceFieldConditional']['conditionalData']['action'] : 'hide';
		if ( $column ) {
			$form_id = ! empty( $attributes['kadenceFieldConditional']['conditionalData']['formID'] ) ? $attributes['kadenceFieldConditional']['conditionalData']['formID'] : '';
		} else {
			$form_id = ! empty( $attributes['formID'] ) ? $attributes['formID'] : '';
		}
		$container = $column ? '.kadence-column' . $attributes['uniqueID'] : '.kb-field' . $form_id . $attributes['uniqueID'];

		$rules_group = array();

		foreach ( $condtional_rules as $key => $rule ) {
			$field   = 'field' . $form_id . $rule['field'];
			$compare = $this->translate_compare( $rule['compare'] );
			$value   = $rule['value'];

			$rules_group[] = array(
				'name' => $field,
				'operator' => $compare,
				'value' => $value,
			);
		}
		if ( $column ) {
			return array(
				'container' => $container,
				'action' => $action,
				'logic' => $combine,
				'rules' => $rules_group,
			);
		}
		return htmlspecialchars(
			wp_json_encode(
				array(
					'container' => $container,
					'action' => $action,
					'logic' => $combine,
					'rules' => $rules_group,
				)
			)
		);
	}

	/**
	 * Translate a compare value to one used in the mf-conditionals lib.
	 *
	 * @param string $compare The kadence compare value.
	 *
	 * @return string
	 */
	public function translate_compare( $compare ) {
		$translations = array(
			'not_empty' => 'isnotempty',
			'is_empty' => 'isempty',
			'equals' => 'is',
			'not_equals' => 'isnot',
			'equals_or_greater' => 'equalgreaterthan',
			'equals_or_less' => 'equallessthan',
			'greater' => 'greaterthan',
			'less' => 'lessthan',
		);
		return isset( $translations[ $compare ] ) ? $translations[ $compare ] : $compare;
	}

	/**
	 * Add the dynamic content to blocks.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block info.
	 * @param object $wp_block The block class object.
	 */
	public function render_blocks( $block_content, $block, $wp_block ) {
		$blockattr = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		if ( isset( $blockattr['kadenceFieldConditional']['conditionalData']['enable'] ) && true == $blockattr['kadenceFieldConditional']['conditionalData']['enable'] ) {
			if ( ! wp_script_is( 'kadence-form-conditional', 'registered' ) ) {
				$this->register_script();
			}
			if ( ! wp_script_is( 'kadence-form-conditional', 'enqueued' ) ) {
				wp_enqueue_script( 'kadence-form-conditional' );
			}
			if ( ! empty( $block['blockName'] ) && 'kadence/column' === $block['blockName'] && ! empty( $blockattr['uniqueID'] ) ) {
				self::$columns[] = array(
					'formID' => ! empty( $blockattr['kadenceFieldConditional']['conditionalData']['formID'] ) ? $blockattr['kadenceFieldConditional']['conditionalData']['formID'] : '',
					'condition' => $this->get_conditional_rules_string( $blockattr, true ),
					'class' => '.kadence-column' . $blockattr['uniqueID'],
				);
			}
		}

		return $block_content;
	}
}

KBP_Form_Conditional::get_instance();
