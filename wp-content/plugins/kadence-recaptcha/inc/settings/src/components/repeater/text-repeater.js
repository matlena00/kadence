/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import GenerateKey from '../common/generate-key';
import { Fragment, Component, RawHTML, render } from '@wordpress/element';
import map from 'lodash/map';
import { TabPanel, TextControl, SelectControl, ToggleControl, Panel, PanelBody, PanelRow, Button } from '@wordpress/components';
/**
 * Build the Measure controls
 * @returns {object} Measure settings.
 */
 export default function TextRepeater( {
	field,
	onChange,
	value,
} ) {
	function onChangeItem( subValue, index ) {
		const newValue = value;
		newValue[index] = subValue;
		onChange( newValue );
	}
	function onChangeRemoveItem( index ) {
		const newValue = value;
		newValue.splice(index, 1);
		onChange( newValue );
	}
	function addKey() {
		let newValue = value;
		if ( ! newValue ) {
			newValue = [];
		}
		newValue.push( GenerateKey( 12 ) );
		onChange( newValue );
	}
	return (
		<div className={ 'components-base-control kadence-settings-text-repeater-control' }>
			{ field.title && (
				<label className="components-base-control__label">
					{ field.title }
				</label>
			) }
			{ value && value instanceof Array && (
				<>
					{ map( value, ( item, index ) => (
						<div className={ 'components-base-control__inner kadence-settings-text-repeater-control-item' }>
							<TextControl
								disabled={ ( field.editable ? false : true ) }
								value={ item }
								onChange={ ( value ) => onChangeItem( value, index ) }
							/>
							<Button
								key={ index }
								size='small'
								isDestructive
								onClick={ () => onChangeRemoveItem( index ) }
							>
								{ field.remove_button ?  field.remove_button : __( 'Remove', 'kadence-settings' ) }
							</Button>
						</div>
					) ) }
				</>
			) }
			<div className="kadence-settings-clear"></div>
			<Button
				className={ 'kadence-settings-repeater-add' }
				variant='secondary'
				size='small'
				onClick={ () => addKey() }
			>
				{ field.add_button ?  field.add_button : __( 'Add', 'kadence-settings' ) }
			</Button>
		</div>
	);
}
