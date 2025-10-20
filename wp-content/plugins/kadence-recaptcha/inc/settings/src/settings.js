/* Global kadenceSettingsParams kadenceSettingsOptions */
/**
 * WordPress dependencies
 */
import SettingsField from './field';
import ResponsiveField from './responsivefield';
import kadenceTryParseJSON from './components/common/try-parse';
import { __ } from '@wordpress/i18n';
import { dispatch, useDispatch, useSelect } from '@wordpress/data';
import { hasFilter } from '@wordpress/hooks';
import { Component, RawHTML, render, useState, useEffect } from '@wordpress/element';
import { TabPanel, Panel, PanelBody, PanelRow, Button, Spinner } from '@wordpress/components';
import { useKadenceSettings } from './data/context';
import { store as noticesStore } from '@wordpress/notices';
import apiFetch from '@wordpress/api-fetch';
export default function SettingsPanel( props ) {
	const { section } = props;
	const { state, dispatch } = useKadenceSettings();
	const { settings } = state;
	const [ optName, setOptName] = useState( kadenceSettingsParams.opt_name );
	const {createErrorNotice, createSuccessNotice} = useDispatch(noticesStore);
	const saveSettings = async () => {
		dispatch({ type: 'SET_SAVING_STATUS', payload: true });
		const wpSettings = await apiFetch({
			path: '/wp/v2/settings',
			method: 'POST',
			data: {
				[optName]: JSON.stringify( settings ),
			},
		} ).catch( ( error ) => {
			createErrorNotice(__('Error Saving Settings', 'kadence-settings'), {
				type: 'snackbar',
			});
			console.error(error);
		});
		if (wpSettings) {
			// console.log('wpSettings', kadenceTryParseJSON( wpSettings[optName]));
			createSuccessNotice(__('Settings Saved', 'kadence-settings'), {
				type: 'snackbar',
			});
		}
		dispatch({ type: 'SET_SAVING_STATUS', payload: false });
	};
	const onChange = ( setting_id, value ) => {
		const temp = { ...settings, ...{[setting_id]:value} };
		dispatch({ type: 'SET_SETTINGS', payload: temp });
	}
	const requiredSingleCheck = ( setting, condition, value ) => {
		switch (condition) {
			case '!=':
				if ( settings[ setting ] != value ) {
					return true;
				} else {
					return false;
				}
				break;
		
			default:
				if ( settings[ setting ] == value ) {
					return true;
				} else {
					return false;
				}
				break;
		}
	}
	const requiredVisibleCheck = ( field ) => {
		if ( ! field.required ) {
			return true;
		}
		if ( undefined === field.required[0] ) {
			return true;
		}
		if ( Array.isArray( field.required[0] ) ) {
			let arrayLength = field.required.length;
			let show        = true;
			for( let i = 0 ; i < arrayLength; i++) {
				if ( undefined === field.required[i] || undefined === field.required[i][0] || undefined === field.required[i][1] || undefined === field.required[i][2] ) {
					return true;
				}
				let setting = field.required[i][0];
				let condition = field.required[i][1];
				let value = field.required[i][2];
				if ( 'true' === value ) {
					value = true;
				} else if ( 'false' === value ) {
					value = false;
				}
				if ( ! requiredSingleCheck( setting, condition, value ) ) {
					return false;
					break;
				}
			}
			return true;
		} else {
			// Make sure we have the data.
			if ( undefined === field.required[0] || undefined === field.required[1] || undefined === field.required[2] ) {
				return true;
			}
			const setting = field.required[0];
			const condition = field.required[1];
			let value = field.required[2];
			if ( 'true' === value ) {
				value = true;
			} else if ( 'false' === value ) {
				value = false;
			}
			return requiredSingleCheck( setting, condition, value );
		}
	}
	if ( ! section?.fields ) {
		return null
	}
	return (
		<>
		<div className='section-header-wrap'>
			{ ( section?.long_title || section?.title ) && (
				<h2 className="section-header">{ section?.long_title ? section.long_title : section?.title }</h2>
			)}
			{ section?.desc && (
				<p className="section-description">{ section.desc }</p>
			)}
		</div>
		<div className='section-body-wrap'>
			{ Object.keys( section.fields ).map( function( key, index ) {
				if ( ! section?.fields?.[ key ] ) {
					return;
				}
				if ( section.fields?.[ key ]?.responsive ) {
					return (
						<>
							{ requiredVisibleCheck( section.fields[ key ] ) ? 
								<ResponsiveField
									field={ {
										desktop: section.fields[ key ].desktop,
										tablet: section.fields[ key ].tablet,
										mobile: section.fields[ key ].mobile,
									} }
									fieldValue={ {
										desktop: ( undefined !== settings[ section.fields[ key ].desktop.id ] ? settings[ section.fields[ key ].desktop.id ] : undefined ),
										tablet: ( undefined !== settings[ section.fields[ key ].tablet.id ] ? settings[ section.fields[ key ].tablet.id ] : undefined ),
										mobile: ( undefined !== settings[ section.fields[ key ].mobile.id ] ? settings[ section.fields[ key ].mobile.id ] : undefined ),

									} }
									onChange={ {
										desktop: ( value ) => onChange( section.fields[ key ].desktop.id, value ),
										tablet: ( value ) => onChange( section.fields[ key ].tablet.id, value ),
										mobile: ( value ) => onChange( section.fields[ key ].mobile.id, value ),
									} }
								/>
							:
								''
							}
						</>
					);
				}
				return (
					<>
						{ requiredVisibleCheck( section.fields[ key ] ) ? 
							<SettingsField key={ section.fields[ key ]?.id } field={ section.fields[ key ] } />
						:
							''
						}
					</>
				);
			} ) }
			<div className="kadence-settings-save-wrap">
				<Button
					className="kadence-settings-save"
					variant='primary'
					onClick={ () => saveSettings() }
				>
					{ __( 'Save Settings', 'kadence-settings' ) }
				</Button>
			</div>
		</div>
		</>
	);
}