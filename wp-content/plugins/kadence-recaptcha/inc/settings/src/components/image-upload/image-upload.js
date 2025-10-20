/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Button, DropZone, Spinner, __experimentalHStack as HStack, TextControl } from '@wordpress/components';
import { MediaUploadCheck, store as blockEditorStore, MediaPlaceholder, MediaUpload } from '@wordpress/block-editor';
import {
	image,
	closeSmall,
	plusCircleFilled,
} from '@wordpress/icons';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState, useRef } from '@wordpress/element';
import { isBlobURL } from '@wordpress/blob';

/**
 * Build the Measure controls
 * @returns {object} Measure settings.
 */
 export default function ImageUploadControl( {
	field,
	onChange,
	value,
} ) {
	const hasImage = value ? true : false;
	const fieldSettings = {
		title:'',
		show_preview: true,
		show_url: true,
		data_type: 'url',
		...field,
	};
	let imageID = '';
	let imageUrl = '';
	if ( hasImage ) {
		switch ( fieldSettings.data_type ) {
			case 'id':
				imageID = parseInt( value );
				break;
			case 'object':
				imageID = value?.id ? parseInt( value.id ) : '';
				imageUrl = value?.url ? value.url : '';
				break;
			default:
				imageUrl = value;
				break;
		}
	}
	const toggleRef = useRef();
	return (
		<div className={ 'components-base-control kadence-settings-image-upload-control' }>
			{ field.title && (
				<label className="components-base-control__label">
					{ field.title }
				</label>
			) }
			{ fieldSettings.show_preview && hasImage && (
				<MediaUpload
					onSelect={ ( img ) => {
						switch ( fieldSettings.data_type ) {
							case 'id':
								onChange( img.id );
								break;
							case 'object':
								const tempImg = {
									id: img.id,
									url: img.url,
									width: img.width,
									height: img.height,
								};
								onChange( tempImg );
								break;
							default:
								onChange( img.url );
								break;
						}
					} }
					allowedTypes={ [ 'image' ] }
					value={ imageID }
					render={ ( { open } ) => (
						<div className="kadence-settings-preview-image__container">
							<Button
								ref={ toggleRef }
								className={
									! imageID
										? 'kadence-settings-preview-image__toggle'
										: 'kadence-settings-preview-image__preview'
								}
								onClick={ open }
								aria-label={
									! imageID
										? null
										: __( 'Edit or replace the image' )
								}
								aria-describedby={
									! imageID
										? null
										: `kadence-settings-preview-image-${ imageID }-describedby`
								}
							>
								{ imageUrl && (
									<img
										className="kadence-settings-preview-image__preview-image"
										src={ imageUrl }
										alt=""
									/>
								) }
							</Button>
							{ imageUrl && (
								<HStack className="kadence-settings-preview-image__actions">
									<Button
										className="kadence-settings-preview-image__action"
										onClick={ open }
									>
										{ __( 'Replace' ) }
									</Button>
									<Button
										className="kadence-settings-preview-image__action"
										isDestructive={true}
										onClick={ () => {
											onChange( '' );
										} }
									>
										{ __( 'Remove' ) }
									</Button>
								</HStack>
							) }
						</div>
					) }
				/>
			) }
			<div className="kadence-settings-image-inner-container">
				<MediaUpload
					onSelect={ ( img ) => {
						switch ( fieldSettings.data_type ) {
							case 'id':
								onChange( img.id );
								break;
							case 'object':
								const tempImg = {
									id: img.id,
									url: img.url,
									width: img.width,
									height: img.height,
								};
								onChange( tempImg );
								break;
							default:
								onChange( img.url );
								break;
						}
					} }
					allowedTypes={ [ 'image' ] }
					value={ imageID }
					render={ ( { open } ) => (
							<Button
								className={ 'components-button components-icon-button kadence-settings-image-select' }
								variant='secondary'
								onClick={ open }
								icon={ image }
							>
								{ __( 'Select Image', 'kadence-settings' ) }
							</Button>
						) }
				/>
				{ fieldSettings.show_url && fieldSettings.data_type !== 'id' && (
					<div className="kadence-settings-image-upload-url">
						<TextControl
							label={ '' }
							placeholder={ __( 'Image URL', 'kadence-settings' ) }
							className={ 'kadence-settings-component-' + field.id }
							value={ imageUrl }
							onChange={ ( tempUrl ) => {
								switch ( fieldSettings.data_type ) {
									case 'object':
										const tempImg = {
											id: value?.id,
											url: tempUrl,
											width: value?.width,
											height: value?.height,
										};
										onChange( tempImg );
										break;
									default:
										onChange( tempUrl );
										break;
								}
							} }
						/>
					</div>
				) }
				{ hasImage && ! fieldSettings.show_preview && (
					<Button
						icon={ closeSmall }
						label={ __( 'Remove Image', 'kadence-settings' ) }
						isDestructive={true}
						className={ 'components-button components-icon-button kadence-settings-image-remove' }
						onClick={ () => onChange( '' ) }
					/>
				) }
			</div>
			{ field.help && (
				<div className="components-base-control__help">
					{ field.help }
				</div>
			) }
		</div>
	);
}

