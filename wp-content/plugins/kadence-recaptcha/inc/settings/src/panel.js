/* Global kadenceSettingsParams */
/**
 * WordPress dependencies
 */
import SettingsPanel from './settings';
import ChangelogTab from './changelog';
import StartedTab from './started';
import DashTab from './dash';
import kadenceTryParseJSON from './components/common/try-parse';
import { __ } from '@wordpress/i18n';
const {
	localStorage,
} = window;
import { useEffect, useState } from '@wordpress/element';
import { TabPanel, Panel, PanelBody, PanelRow, Button, Spinner } from '@wordpress/components';
export default function InfoPanel( props ) {
	const { panel, sections, groups } = props;
	const [ subTabs, setSubTabs ] = useState( [] );
	const [ activePanel, setActivePanel ] = useState( localStorage.getItem( 'kadenceSettingsPanel' ) ? kadenceTryParseJSON( localStorage.getItem( 'kadenceSettingsPanel' ) ) : {} );
	const [ activeSettingTab, setActiveSettingTab ] = useState( activePanel && activePanel[kadenceSettingsParams.opt_name] ? activePanel[kadenceSettingsParams.opt_name] : undefined );
	useEffect( () => {
		const tempTabs = [];
		{ Object.keys( sections ).map( function( key, index ) {
			tempTabs.push( {
				name: sections[ key ].id,
				title: sections[ key ].title,
				className: 'kadence-sections-tab-' + sections[ key ].id,
			} );
		} ) }
		setSubTabs( tempTabs );
	}, [] );
	const onTabSelect = ( tabName ) => {
		const activeTab = activePanel;
		activeTab[kadenceSettingsParams.opt_name] = tabName;
		localStorage.setItem( 'kadenceSettingsPanel', JSON.stringify( activeTab ) );
		setActiveSettingTab( tabName );
	};
	const KadenceSettingsGroups = () => (
		<div className="kadence-settings-dashboard-section-tabs-container">
			<div className="kadence-settings-dashboard-section-tabslist">
			{ Object.keys( groups ).map( function( key, index ) {
				let tempSections = [];
				{ groups[key]['sections'].map( ( nin ) => {
					tempSections.push( {
						name: nin.id,
						title: nin.title,
						className: 'kadence-sections-tab-' + nin.id,
					} );
				} ) }
				// console.log( 'active', activeSettingTab );
				// console.log( 'group', tempSections );
				return (
					<div className='kadence-settings-dashboard-sub-section-wrap'>
						<h3 className='kadence-settings-dashboard-sub-section-title'>{groups[key]['title']}</h3>
						<TabPanel
							className="kadence-settings-dashboard-sub-section-tabs"
							activeClass="active-tab"
							orientation="vertical"
							initialTabName={ activeSettingTab }
							onSelect={ onTabSelect }
							tabs={ tempSections }
						>
							{
							( tab ) => {
								return (
									<div className="test-dashboard-modules-wrapper"></div>
								);
							}
						}
						</TabPanel>
					</div>
				);
			} ) }
			</div>
			<Panel className="dashboard-section sub-tab-section">
				<PanelBody
					opened={ true }
				>
					<div className="dashboard-modules-wrapper">
						<SettingsPanel section={ sections[ activeSettingTab ] } />
					</div>
					<div className="dashboard-illustration-wrapper">
						{ sections[ activeSettingTab ]?.illustration && (
							<img src={sections[ activeSettingTab ].illustration } />
						) }
					</div>
				</PanelBody>
			</Panel>
		</div>
	);

		const KadenceSettingsPanel = () => (
			<>
				{ 1 < subTabs.length ?
					<TabPanel
					className="kadence-settings-dashboard-section-tabs kadence-settings-dashboard-section-tabs-container"
					activeClass="active-tab"
					orientation="vertical"
					initialTabName={ activePanel && activePanel[kadenceSettingsParams.opt_name] ? activePanel[kadenceSettingsParams.opt_name] : undefined }
					onSelect={ onTabSelect }
					tabs={ subTabs }
				>
					{
						( tab ) => {
							return (
								<Panel className="dashboard-section sub-tab-section">
									<PanelBody
										opened={ true }
									>
										<div className="dashboard-modules-wrapper">
											<SettingsPanel section={ sections[ tab.name ] } />
										</div>
									</PanelBody>
								</Panel>
							);
						}
					}
				</TabPanel>
			:
				<Panel className="dashboard-section sub-tab-section">
					<PanelBody
						opened={ true }
					>
						<div className="dashboard-modules-wrapper">
							<SettingsPanel section={ sections[Object.keys(sections)[0]] } />
						</div>
					</PanelBody>
				</Panel>
			}
		</>
	);
	return (
		<>
		   { 'settings' === panel && (
				<>
					{ groups && Object.keys(groups).length !== 0 ? (
						<>
						<KadenceSettingsGroups />
						</>
					) : (
						<KadenceSettingsPanel />
					) }
				</>
		   ) }
		   { 'changelog' === panel &&  (
			   <ChangelogTab />
		   ) }
		   { 'started' === panel &&  (
			   <StartedTab />
		   ) }
		   { 'dash' === panel &&  (
			   <DashTab />
		   ) }
		</>
	);
};
 