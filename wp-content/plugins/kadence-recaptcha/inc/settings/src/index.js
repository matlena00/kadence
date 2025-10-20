/* Global kadenceSettingsParams */
/**
 * Internal dependencies
 */
import kadenceTryParseJSON from './components/common/try-parse';
import { KadenceSettingsProvider } from './data/context';
import Main from './main';
 /**
 * Import Css
 */
import './editor.scss';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { render, useEffect, useState } from '@wordpress/element';
import { store as noticesStore } from '@wordpress/notices';
const KadenceSettingsDashboard = () => {
	const {createErrorNotice, createSuccessNotice} = useDispatch(noticesStore);
	const [settingsData, setSettingsData] = useState( ( kadenceSettingsParams.settings ? kadenceTryParseJSON( kadenceSettingsParams.settings ) : {} ) );
	const fetchSettings = async () => {
		const wpSettings = await apiFetch({
			path: '/wp/v2/settings',
		});
		setSettingsData( kadenceTryParseJSON( wpSettings[kadenceSettingsParams.opt_name] ) );
	};
	useEffect( () => {
		fetchSettings().catch( ( error ) => {
			// Add a notice that the settings api isn't working and changes can't be saved.
			createErrorNotice(__('Error: WordPress settings API not working. Please reload and try again.', 'kadence-settings'));
			console.error( error );
		} );
	}, [] );
 
	return (
		<KadenceSettingsProvider value={settingsData}>
			<Main />
		</KadenceSettingsProvider>
	);
 }
 
 wp.domReady( () => {
	 render(
		 <KadenceSettingsDashboard />,
		 document.querySelector( '.kadence_settings_dashboard_main' )
	 );
 } );
 