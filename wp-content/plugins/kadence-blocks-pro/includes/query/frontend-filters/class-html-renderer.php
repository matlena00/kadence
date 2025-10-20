<?php
/**
 * HTML Renderer Class
 *
 * @package Kadence Blocks Pro
 */

namespace KadenceWP\KadenceBlocksPro\Query\Frontend_Filters;

use Kadence_Blocks_Svg_Render;
use Kadence_Blocks_Pro_CSS;

/**
 * Renders HTML for filter options
 */
class HTML_Renderer {
	/**
	 * Render select dropdown
	 *
	 * @param array  $options Options array.
	 * @param string $placeholder Placeholder text.
	 * @param array  $attrs Additional attributes.
	 * @return string
	 */
	public function render_select( array $options, $placeholder = '', $attrs = [] ) {
		if ( empty( $options ) ) {
			return '';
		}

		$attributes = $this->build_attributes( array_merge( [
			'class' => 'kb-filter',
			'aria-label' => esc_attr( __( 'Filter results', 'kadence-blocks-pro' ) ),
		], $attrs ) );

		$blank_option = $placeholder ? '<option value="">' . esc_html( $placeholder ) . '</option>' : '';
		$options_html = $this->render_options( $options );

		return sprintf( '<select %s>%s%s</select>', $attributes, $blank_option, $options_html );
	}

	/**
	 * Render checkbox list
	 *
	 * @param array  $options Options array.
	 * @param string $field_name Field name.
	 * @param array  $attrs Block attributes.
	 * @return string
	 */
	public function render_checkboxes( array $options, $field_name, $attrs = [] ) {
		if ( empty( $options ) ) {
			return '';
		}

		$html = '';
		$this->walk_checkbox_options( $options, $field_name, $html, $attrs );
		return $html;
	}

	/**
	 * Render button list
	 *
	 * @param array $options Options array.
	 * @param array $attrs Block attributes.
	 * @return string
	 */
	public function render_buttons( array $options, $attrs = [] ) {
		if ( empty( $options ) ) {
			return '';
		}

		$html = '';
		foreach ( $options as $option ) {
			$html .= $this->render_button( $option, $attrs );
		}
		return $html;
	}

	/**
	 * Render sort dropdown
	 *
	 * @param array  $options Options array.
	 * @param string $placeholder Placeholder text.
	 * @return string
	 */
	public function render_sort( array $options, $placeholder = '' ) {
		if ( empty( $options ) ) {
			return '';
		}

		$attributes = $this->build_attributes( [
			'class' => 'kb-sort',
			'aria-label' => esc_attr( __( 'Sort results', 'kadence-blocks-pro' ) ),
		] );

		$blank_option = '<option value="">' . esc_html( $placeholder ) . '</option>';
		$options_html = $this->render_options( $options );

		return sprintf( '<select %s>%s%s</select>', $attributes, $blank_option, $options_html );
	}

	/**
	 * Render search input
	 *
	 * @param string $placeholder Placeholder text.
	 * @param array  $attrs Block attributes.
	 * @return string
	 */
	public function render_search( $placeholder = '', $attrs = [] ) {
		$placeholder = $placeholder ?: __( 'Search', 'kadence-blocks-pro' );
		
		// Build wrapper attributes for auto search
		$wrapper_attrs = array( 'class' => 'kb-filter-search-wrap' );
		
		if ( ! empty( $attrs['autoSearch'] ) ) {
			$wrapper_attrs['data-auto-search'] = 'true';
		}
		
		$wrapper_attributes = $this->build_attributes( $wrapper_attrs );
		
		$html = sprintf( '<div %s>', $wrapper_attributes );
		$html .= sprintf( 
			'<input type="text" class="kb-filter-search" placeholder="%s" aria-label="%s"/>',
			esc_attr( $placeholder ),
			esc_attr( __( 'Search results', 'kadence-blocks-pro' ) )
		);
		$html .= sprintf( 
			'<button class="kb-filter-search-btn" aria-label="%s">%s</button>',
			esc_attr( __( 'Search', 'kadence-blocks-pro' ) ),
			Kadence_Blocks_Svg_Render::render( 'fe_search', 'none', 3, '', true )
		);
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render date input
	 *
	 * @return string
	 */
	public function render_date() {
		return '<input type="date" class="kb-filter-date" />';
	}

	/**
	 * Render rating filter
	 *
	 * @param array $attrs Filter attributes.
	 * @param array $options_array Optional options array with counts.
	 * @return string
	 */
	public function render_rating( array $attrs, array $options_array = [] ) {
		$icon = ! empty( $attrs['ratingIcon'] ) ? $attrs['ratingIcon'] : 'fe_star';
		$stroke_width = isset( $attrs['iconStrokeWidth'] ) ? $attrs['iconStrokeWidth'] : 1;
		$star = Kadence_Blocks_Svg_Render::render( $icon, 'currentColor', $stroke_width, 'star', false );
		$after_text = ! empty( $attrs['afterText'] ) ? $attrs['afterText'] : '';
		$display_type = isset( $attrs['displayType'] ) ? $attrs['displayType'] : 'single';

		$html = '';

		if ( 'list' === $display_type ) {
			// Use options array if provided (for result counts)
			if ( ! empty( $options_array ) ) {
				foreach ( $options_array as $option ) {
					$rating_value = (int) $option['value'];
					$html .= sprintf( '<span class="kbp-ql-rating kbp-ql-rating-%d" data-value="%d">', $rating_value, $rating_value );
					for ( $j = 0; $j < $rating_value; $j++ ) {
						$html .= $star;
					}
					$html .= '</span>';
					$html .= esc_html( $after_text );
					// Show count if included in the label
					if ( strpos( $option['label'], '(' ) !== false ) {
						preg_match( '/\((\d+)\)/', $option['label'], $matches );
						if ( ! empty( $matches[1] ) ) {
							$html .= ' (' . esc_html( $matches[1] ) . ')';
						}
					}
					$html .= '<br>';
				}
			} else {
				// Fallback to default rendering without counts
				for ( $i = 5; $i > 0; $i-- ) {
					$html .= sprintf( '<span class="kbp-ql-rating kbp-ql-rating-%d" data-value="%d">', $i, $i );
					for ( $j = 0; $j < $i; $j++ ) {
						$html .= $star;
					}
					$html .= '</span>';
					$html .= esc_html( $after_text );
					$html .= '<br>';
				}
			}
		} else {
			for ( $i = 0; $i < 5; $i++ ) {
				$html .= sprintf( 
					'<span class="kbp-ql-rating kbp-ql-rating-single kbp-ql-rating-%d" data-value="%d" data-uid="%s">%s</span>',
					$i + 1,
					$i + 1,
					esc_attr( $attrs['uniqueID'] ),
					$star
				);
			}
			$html .= esc_html( $after_text );
		}

		return $html;
	}

	/**
	 * Render range slider
	 *
	 * @param int   $min Minimum value.
	 * @param int   $max Maximum value.
	 * @param array $attrs Filter attributes.
	 * @return string
	 */
	public function render_range( $min, $max, array $attrs ) {
		$css = new Kadence_Blocks_Pro_CSS();
		
		$slider_color = ! empty( $attrs['sliderColor'] ) ? $css->sanitize_color( $attrs['sliderColor'] ) : 'var(--global-palette-9, #C6C6C6)';
		$slider_highlight_color = ! empty( $attrs['sliderHighlightColor'] ) ? $css->sanitize_color( $attrs['sliderHighlightColor'] ) : 'var(--global-palette-2, #2F2FFC)';

		return sprintf(
			'<div class="range_container">
				<div class="form_control">
					<div class="form_control_container">
						<div class="form_control_container__label">Min</div>
						<input class="form_control_container__input fromInput" type="number" placeholder="%1$d" min="%1$d" max="%2$d"/>
					</div>
					<div class="form_control_container">
						<div class="form_control_container__label">Max</div>
						<input class="form_control_container__input toInput" type="number" placeholder="%2$d" min="%1$d" max="%2$d"/>
					</div>
				</div>
				<div class="sliders_control">
					<input class="fromSlider" type="range" value="%1$d" min="%1$d" max="%2$d"/>
					<input class="toSlider" data-sliderColor="%3$s" data-sliderHighlightColor="%4$s" type="range" value="%2$d" min="%1$d" max="%2$d"/>
					<div class="from-display" role="presentation">%1$d</div>
					<div class="to-display" role="presentation">%2$d</div>
				</div>
			</div>',
			$min,
			$max,
			esc_attr( $slider_color ),
			esc_attr( $slider_highlight_color )
		);
	}

	/**
	 * Render options recursively
	 *
	 * @param array $options Options array.
	 * @param int   $depth Current depth.
	 * @return string
	 */
	private function render_options( array $options, $depth = 0 ) {
		$html = '';
		
		foreach ( $options as $option ) {
			$depth_indicator = str_repeat( '- ', $depth );
			$html .= sprintf( 
				'<option value="%s">%s%s</option>',
				esc_attr( $option['value'] ),
				esc_html( $depth_indicator ),
				esc_html( $option['label'] )
			);

			if ( ! empty( $option['children'] ) ) {
				$html .= $this->render_options( $option['children'], $depth + 1 );
			}
		}

		return $html;
	}

	/**
	 * Walk checkbox options recursively
	 *
	 * @param array  $options Options array.
	 * @param string $field_name Field name.
	 * @param string $html HTML output.
	 * @param array  $attrs Block attributes.
	 * @param int    $depth Current depth.
	 */
	private function walk_checkbox_options( array $options, $field_name, &$html, $attrs = [], $depth = 0 ) {
		if ( $depth > 20 ) {
			return;
		}

		foreach ( $options as $option ) {
			$field_id = $field_name . '_' . uniqid();
			list( $swatch_class, $swatch_style ) = $this->get_swatch_attributes( $option, $attrs );

			$html .= sprintf(
				'<div class="kb-radio-check-item %s" data-value="%s" style="margin-left: %dpx;">
					<input class="kb-checkbox-style" id="%s" type="checkbox" name="%s[]" value="%s" style="%s">
					<label for="%s">%s</label>
				</div>',
				esc_attr( $swatch_class ),
				esc_attr( $option['value'] ),
				$depth * 20,
				esc_attr( $field_id ),
				esc_attr( $field_name ),
				esc_attr( $option['value'] ),
				esc_attr( $swatch_style ),
				esc_attr( $field_id ),
				esc_html( $option['label'] )
			);

			if ( ! empty( $option['children'] ) ) {
				$this->walk_checkbox_options( $option['children'], $field_name, $html, $attrs, $depth + 1 );
			}
		}
	}

	/**
	 * Render a single button
	 *
	 * @param array $option Option data.
	 * @param array $attrs Block attributes.
	 * @return string
	 */
	private function render_button( array $option, array $attrs ) {
		$classes = $this->get_button_classes( $attrs );

		$button_args = array(
			'class' => implode( ' ', $classes ),
			'data-value' => $option['value'],
			'id' => 'field' . $attrs['uniqueID'] . '_' . uniqid(),
			'type' => 'submit',
		);

		if ( ! empty( $attrs['label'] ) ) {
			$button_args['aria-label'] = $attrs['label'];
		}

		$attributes = $this->build_attributes( $button_args );
		
		$icon_html = $this->get_button_icon_html( $attrs );
		$icon_left = ! empty( $icon_html ) && ! empty( $attrs['iconSide'] ) && 'left' === $attrs['iconSide'] ? $icon_html : '';
		$icon_right = ! empty( $icon_html ) && ! empty( $attrs['iconSide'] ) && 'right' === $attrs['iconSide'] ? $icon_html : '';

		$content = sprintf( 
			'<button %s>%s%s%s</button>',
			$attributes,
			$icon_left,
			esc_html( $option['label'] ),
			$icon_right
		);

		return '<div class="btn-inner-wrap">' . $content . '</div>';
	}

	/**
	 * Get button classes
	 *
	 * @param array $attrs Block attributes.
	 * @return array
	 */
	private function get_button_classes( array $attrs ) {
		$classes = array( 'kb-button', 'kt-button', 'button', 'kb-query-filter-filter-button' );
		$classes[] = ! empty( $attrs['sizePreset'] ) ? 'kt-btn-size-' . $attrs['sizePreset'] : 'kt-btn-size-standard';
		$classes[] = ! empty( $attrs['widthType'] ) ? 'kt-btn-width-type-' . $attrs['widthType'] : 'kt-btn-width-type-auto';
		$classes[] = ! empty( $attrs['inheritStyles'] ) ? 'kb-btn-global-' . $attrs['inheritStyles'] : 'kb-btn-global-outline';
		$classes[] = ! empty( $attrs['text'] ) ? 'kt-btn-has-text-true' : 'kt-btn-has-text-false';
		$classes[] = ! empty( $attrs['icon'] ) ? 'kt-btn-has-svg-true' : 'kt-btn-has-svg-false';
		
		return $classes;
	}

	/**
	 * Get button icon HTML
	 *
	 * @param array $attrs Block attributes.
	 * @return string
	 */
	private function get_button_icon_html( array $attrs ) {
		if ( empty( $attrs['icon'] ) ) {
			return '';
		}

		$type = substr( $attrs['icon'], 0, 2 );
		$line_icon = ( ! empty( $type ) && 'fe' == $type ? true : false );
		$fill = ( $line_icon ? 'none' : 'currentColor' );
		$stroke_width = $line_icon ? 2 : false;

		$svg_icon = Kadence_Blocks_Svg_Render::render( $attrs['icon'], $fill, $stroke_width );
		
		if ( empty( $svg_icon ) ) {
			return '';
		}

		$side_class = ! empty( $attrs['iconSide'] ) ? 'kt-btn-icon-side-' . $attrs['iconSide'] : 'kt-btn-icon-side-right';
		
		return sprintf( 
			'<span class="kb-svg-icon-wrap kb-svg-icon-%s %s">%s</span>',
			esc_attr( $attrs['icon'] ),
			esc_attr( $side_class ),
			$svg_icon
		);
	}

	/**
	 * Build HTML attributes string
	 *
	 * @param array $attributes Attributes array.
	 * @return string
	 */
	private function build_attributes( array $attributes ) {
		$attribute_strings = array();
		
		foreach ( $attributes as $key => $value ) {
			if ( null !== $value && false !== $value ) {
				$attribute_strings[] = sprintf( '%s="%s"', $key, esc_attr( $value ) );
			}
		}

		return implode( ' ', $attribute_strings );
	}

	/**
	 * Get swatch attributes for WooCommerce options
	 *
	 * @param array $option Option data.
	 * @param array $attrs Block attributes.
	 * @return array Array containing swatch class and style.
	 */
	private function get_swatch_attributes( array $option, array $attrs ) {
		$swatch_class = '';
		$swatch_style = '';

		// Handle WooCommerce attribute swatches
		if ( ! empty( $attrs['blockName'] ) && 'kadence/query-filter-woo-attribute' === $attrs['blockName'] ) {
			$swatch_map = ! empty( $attrs['swatchMap'] ) ? $attrs['swatchMap'] : array();
			$swatch_option = ! empty( $swatch_map[ $option['value'] ] ) ? $swatch_map[ $option['value'] ] : array();
			$swatch_image = ! empty( $swatch_option['swatchImage'] ) ? $swatch_option['swatchImage'] : '';
			$swatch_class = 'has-swatch ' . ( $swatch_image ? 'has-image' : '' );

			// Try to populate color if not set in swatch map
			if ( ! isset( $swatch_map[ $option['value'] ] ) ) {
				$swatch_style = 'background-color:' . $option['label'];
			}
		}

		return array( $swatch_class, $swatch_style );
	}
}