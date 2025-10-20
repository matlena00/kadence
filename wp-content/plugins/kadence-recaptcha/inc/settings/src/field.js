/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import RecaptchaPreview from './components/validation/recaptcha-preview';
import kadenceTryParseJSON from './components/common/try-parse';
import TextRepeater from './components/repeater/text-repeater';
import TextRepeaterExpanded from './components/repeater/text-repeater-expand';
import ImageSelectControl from './components/image-select/image-select-control';
import RadioSelectControl from './components/radio-select/radio-select-control';
import ImageUploadControl from './components/image-upload/image-upload';
import { useKadenceSettings } from './data/context';
import {
	useState,
} from '@wordpress/element';
import { ColorPalette, TextControl, TextareaControl, SelectControl, ToggleControl, RangeControl, Panel, PanelBody, PanelRow, Button } from '@wordpress/components';
function SettingsField( {
	field,
} ) {
	const { state, dispatch } = useKadenceSettings();
	const { settings } = state;
	const onChange = ( value ) => {
		const temp = { ...settings, ...{[field.id]:value} };
		dispatch({ type: 'SET_SETTINGS', payload: temp });
	}
	const [ stateValue, setStateValue ] = useState( ( undefined !== settings[field.id] ? settings[field.id] : field?.default ) );
	if ( field.type ) {
		switch ( field.type ) {
			case 'text':
				const savedValue = ( undefined !== settings[field.id] ? settings[field.id] : field?.default );
				const currentVal = ( field.obfuscate && savedValue && savedValue === stateValue ? stateValue.replace(/(\w| )(?=(\w| ){4})/g, 'X') : stateValue );
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextControl
							label={ field.title }
							className={ 'kadence-settings-component-' + field.id }
							value={ currentVal }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
							help={ field.help ? ( field.helpLink ? <a href={ field.helpLink } target={ '_blank' }>{ field.help }</a> : <span dangerouslySetInnerHTML={ { __html: field.help } }/> ) : undefined }
						/>
					</div>
				);
			case 'textarea':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextareaControl
							label={ field.title }
							className={ 'kadence-settings-component-' + field.id }
							value={ stateValue }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
							help={ field.help ? ( field.helpLink ? <a href={ field.helpLink } target={ '_blank' }>{ field.help }</a> : <span dangerouslySetInnerHTML={ { __html: field.help } }/> ) : undefined }
							placeholder={ field.placeholder ? field.placeholder : undefined }
						/>
					</div>
				);
			case 'text_repeater':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextRepeater
							field={ field }
							value={ stateValue }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
						/>
					</div>
				);
			case 'text_repeater_expanded':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<TextRepeaterExpanded
							field={ field }
							value={ stateValue }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
						/>
					</div>
				);
			case 'select':
				const options = Object.keys( field.options ).map( function( key, index ) {
					return { value: key, label: field.options[ key ] }
				} );
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<SelectControl
							label={ field.title }
							value={ stateValue }
							className={ 'kadence-settings-component-' + field.id }
							options={ options }
							help={ field.help ? field.help : undefined }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
						/>
					</div>
				);
			case 'image_select':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<ImageSelectControl
							field={ field }
							value={ stateValue }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
						/>
					</div>
				);
			case 'radio_select':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<RadioSelectControl
							field={ field }
							value={ stateValue }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
						/>
					</div>
				);
			case 'image':
					return (
						<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
							<ImageUploadControl
								field={ field }
								value={ stateValue }
								onChange={ ( value ) => {
									setStateValue( value );
									onChange( value );
								} }
							/>
						</div>
					);
			case 'range':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<RangeControl
							label={ field.title }
							value={ stateValue && ! Number.isFinite( stateValue ) ? Number( stateValue ) : stateValue }
							className={ 'kadence-settings-component-' + field.id }
							help={ field.help ? field.help : undefined }
							initialPosition={ field.default ? field.default : undefined }
							min={ field.min ? field.min : undefined }
							max={ field.max ? field.max : undefined }
							step={ field.step ? field.step : undefined }
							allowReset={ true }
							onChange={ ( value ) => {
								setStateValue( value );
								onChange( value );
							} }
						/>
					</div>
				);
			case 'color':
				// const themeColors = useSetting( 'color.palette.theme' );
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<div className={ 'components-base-control' }>
							{ field.title && (
								<label className="components-base-control__label">
									{ field.title }
								</label>
							) }
							{ field.help && (
								<span className="components-base-control__help" dangerouslySetInnerHTML={ { __html: field.help } }>
								</span>
							) }
							<ColorPalette
								colors={ kadenceSettingsParams.themeColors && kadenceSettingsParams.themeColors[0] ? kadenceSettingsParams.themeColors[0] : [] }
								value={ stateValue ? stateValue : undefined }
								className={ `kadence-settings-component-${ field.id }${ field?.class ? ' ' + field.class : '' }` }
								default={ field?.default ? field.default : undefined }
								clearable={ false }
								onChange={ ( value ) => {
									setStateValue( value );
									onChange( value );
								} }
							/>
						</div>
					</div>
				);
			case 'switch':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<ToggleControl
							key={ 'toggle-' + field.id }
							label={ field.title ? field.title : undefined }
							className={ `kadence-settings-component-${ field.id }${ field?.class ? ' ' + field.class : '' }` }
							checked={ ( undefined !== stateValue && 0 == stateValue ? false : true ) }
							help={ field.help ? <span dangerouslySetInnerHTML={ { __html: field.help } }/> : undefined }
							onChange={ ( value ) => {
								if ( ! value ) {
									setStateValue( 0 );
									onChange( 0 );
								} else {
									setStateValue( value );
									onChange( value );
								}
							} }
						/>
					</div>
				);
			case 'code_info':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<div className={ 'components-base-control kadence-settings-text-repeater-control' }>
							{ field.title && (
								<span className="components-base-control__label">
									{ field.title }
								</span>
							) }
							{ field.help && (
								<span className="components-base-control__help">
									{ field.help }
								</span>
							) }
							{ field.content && (
								<code className="components-base-control__code">
									{ field.content }
								</code>
							) }
						</div>
					</div>
				);
			case 'title':
				return (
					<>
						{ field.title && (
							<h2 className="kadence-settings-title">{ field.title }</h2>
						)}
					</>
				);
			case 'raw':
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type + ' kadence-settings-field-id-' + field.id }>
						<div className={ 'components-base-control' }>
							{ field.title && (
								<label className="components-base-control__label">
									{ field.title }
								</label>
							) }
							{ field.help && (
								<span className="components-base-control__help" dangerouslySetInnerHTML={ { __html: field.help } }>
								</span>
							) }
							{ field.content && (
								<div className="components-base-control__raw" dangerouslySetInnerHTML={ { __html: field.content } }>
								</div>
							) }
						</div>
					</div>
				);
			case 'recaptcha_preview':
				return (
					<div className={ 'kadence-settings-component-field kadence-settings-field-type-' + field.type }>
						<div className={ 'components-base-control' }>
							{ field.title && (
								<label className="components-base-control__label">
									{ field.title }
								</label>
							) }
							<RecaptchaPreview 
								settings={settings}
							/>
						</div>
					</div>
				);
			default:
				return (
					<div key={ field.id } className={ 'kadence-settings-component-field' }>
						{ field.title }
					</div>
				);
		}
	}
}

 export default SettingsField;