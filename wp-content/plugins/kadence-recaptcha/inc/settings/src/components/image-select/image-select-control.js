/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button, ButtonGroup } from '@wordpress/components';
import map from 'lodash/map';
/**
 * Build the Measure controls
 * @returns {object} Measure settings.
 */
 export default function ImageSelectControl( {
	field,
	onChange,
	value,
} ) {
	return (
		<div className={ 'components-base-control kadence-settings-image-select-control' }>
			{ field.title && (
				<label className="components-base-control__label">
					{ field.title }
				</label>
			) }
			{ field.options && field.options instanceof Array && (
				<ButtonGroup className="kt-flex-select-group kt-image-select-group">
					{ map( field.options, ( item, index ) => (
						<Button
							key={ index }
							showTooltip={ true }
							label={ item.alt }
							className="kt-image-select-btn"
							isPressed={ value === item.value }
							onClick={ () => onChange( item.value ) }
						>
							<img src={ item.img }/>
						</Button>
					) ) }
				</ButtonGroup>
			) }
		</div>
	);
}
