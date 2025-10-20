/* Global kadenceSettingsParams */
/**
 * Internal dependencies
 */
import InfoPanel from './panel';
import Notices from './notices';
import SaveOverlay from './overlay';
import kadenceTryParseJSON from './components/common/try-parse';
import settingIcons from './components/common/icons';
import ChangelogTab from './changelog';
import Help from './help';
window.kadenceSettingsOptions = '';
 /**
 * Import Css
 */
import './editor.scss';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { TabPanel, Panel, PanelBody, Button, Popover } from '@wordpress/components';
import { store as noticesStore } from '@wordpress/notices';
export default function Main() {
	const queryParameters = new URLSearchParams(window.location.search);
	const licenseShow = queryParameters.get('license');
	const [ sections, setSections ] = useState( ( kadenceSettingsParams.sections ? kadenceTryParseJSON( kadenceSettingsParams.sections ) : {} ) );
	const [ groups, setGroups ] = useState( ( kadenceSettingsParams.groups ? kadenceTryParseJSON( kadenceSettingsParams.groups ) : {} ) );
	const [ tabs, setTabs ] = useState( ( kadenceSettingsParams.tabs ? kadenceTryParseJSON( kadenceSettingsParams.tabs ) : {} ) );
	const [ args, setArgs ] = useState( ( kadenceSettingsParams.args ? kadenceTryParseJSON( kadenceSettingsParams.args ) : {} ) );
	const [ isVisible, setIsVisible ] = useState( false );
    const toggleVisible = () => {
		if ( isHelpVisible ) {
			setHelpIsVisible( false );
		}
        setIsVisible( ( state ) => ! state );
    };
	const [ isHelpVisible, setHelpIsVisible ] = useState( false );
    const toggleHelpVisible = () => {
		if ( isVisible ) {
			setIsVisible( false );
		}
        setHelpIsVisible( ( state ) => ! state );
    };
	const [ isLicenseVisible, setLicenseIsVisible ] = useState( false );
    const toggleLicenseVisible = () => {
		if ( isVisible ) {
			setIsVisible( false );
		}
		if ( isHelpVisible ) {
			setHelpIsVisible( false );
		}
		let para = document.getElementById("kadence-settings-extra-items");
		if ( ! isLicenseVisible ) {
			queryParameters.set('license', 'show');
			history.replaceState(null, '', window.location.pathname + '?' + queryParameters.toString());
            para.classList.add("kadence-show-license");
		} else {
			queryParameters.delete('license');
			history.replaceState(null, '', window.location.pathname + '?' + queryParameters.toString());
			para.classList.remove("kadence-show-license");
		}
        setLicenseIsVisible( ( state ) => ! state );
    };
	useEffect( () => {
		if (licenseShow) {
			toggleLicenseVisible();
		}
	}, [] );
	// const real_tabs = [];
	// console.log( 'load tabs', Object.keys(tabs).length );
	// { Object.keys( tabs ).map( function( key, index ) {
	// 	real_tabs.push( {
	// 		name: tabs[ key ].id,
	// 		title: tabs[ key ].title,
	// 		className: 'kadence-settings-tab-' + tabs[ key ].id,
	// 	} );
	// } ) }
	const getRealTabs = () => {
		const tempTabs = [];
		{ Object.keys( tabs ).map( function( key, index ) {
			tempTabs.push( {
				name: tabs[ key ].id,
				title: tabs[ key ].title,
				className: 'kadence-settings-tab-' + tabs[ key ].id,
			} );
		}
		) }
		return tempTabs;
	};
	const KadenceDashTabPanel = () => (
		<TabPanel className="kadence-dashboard-tab-panel"
				activeclassName="active-tab"
				tabs={ getRealTabs() }>
				{
					( tab ) => {
					return (
						<Panel className="dashboard-section tab-section">
							<PanelBody
								opened={ true }
							>
								<div className="dashboard-modules-wrapper">
									<InfoPanel panel={ tab.name } groups={ groups } sections={ sections } saveSettings={ () => saveSettings() } />
								</div>
							</PanelBody>
						</Panel>
					);
					}
				}
			</TabPanel>
		);
		const MainPanel = () => (
		<>
			{ args?.v2 && (
				<div className="kadence_settings_dash_head">
					<div className="kadence_settings_dash_head_container">
						<div className="kadence_settings_dash_head_left">
							<div className="kadence_settings_dash_logo">
								<img src={args.logo} />
							</div>
							<div className="kadence_settings_dash_title">
								<h1>{args.page_title}</h1>
							</div>
						</div>
						<div className="kadence_settings_dash_head_right">
							{ args?.license && (
								<div className="kadence-settings-dash-license">
									<Button className={`kadence-settings-license-btn${ args?.licenseActive ? '' : ' license-inactive'}`} variant="secondary" icon={ settingIcons.key } iconSize="10px" isPressed={ isLicenseVisible } onClick={ toggleLicenseVisible }>
										{__( 'License', 'kadence-settings' )}
									</Button>
								</div>
							) }
							{ args?.sidebar && (
								<div className="kadence-settings-dash-help">
									<Button className="kadence-settings-help-btn" variant="secondary" icon={ settingIcons.question } iconSize="14px" onClick={ toggleHelpVisible }>
										{__( 'Help', 'kadence-settings' )}
									</Button>
									{ isHelpVisible && <Popover className="kadence-settings-help-popover" noArrow={false} placement={'bottom-end'} focusOnMount={false} onClose={()=> setHelpIsVisible( false ) }><Help /></Popover> }
								</div>
							) }
							{ args?.changelog && (
								<div className="kadence_settings_dash_version">
									<Button className="kadence-settings-changelog-btn" variant="secondary" icon={ settingIcons.changelog } iconSize="12px" onClick={ toggleVisible }>
										<span className='version'>{args.version}</span>
										{__( 'Changelog', 'kadence-settings' )}
									</Button>
									{ isVisible && <Popover className="kadence-settings-changelog-popover" noArrow={false} placement={'bottom-end'} onClose={()=> setIsVisible( false ) }><ChangelogTab /></Popover> }
								</div>
							) }
						</div>
					</div>
				</div>
			)}
			{ ! isLicenseVisible && (
				<div className="tab-panel">
					{ Object.keys(tabs)?.length &&  Object.keys(tabs)?.length > 1 ?
						<KadenceDashTabPanel />
					:
						<Panel className="dashboard-section dashboard-main-wrap tab-section">
							<PanelBody
								opened={ true }
							>
								<div className="dashboard-modules-wrapper">
									<InfoPanel panel={ 'settings' } groups={ groups } sections={ sections } saveSettings={ () => console.log( 'sav?') } />
								</div>
							</PanelBody>
						</Panel>
					}
				</div>
			) }
			</>
		);
		return (
			<>
				<Notices />
				<MainPanel />
				<SaveOverlay />
			</>
		);
		// return (
		// 	<>
		// 		<Notices />
		// 		<MainPanel />
		// 		<SaveOverlay show={ isSaving } />
		// 	</>
		// );
 }
 