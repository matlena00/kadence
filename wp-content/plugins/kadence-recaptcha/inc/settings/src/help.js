/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
 import { TabPanel, Panel, PanelBody, withFilters } from '@wordpress/components';
 import kadenceTryParseJSON from './components/common/try-parse';
 export const Help = () => {
	const args = ( kadenceSettingsParams.args ? kadenceTryParseJSON( kadenceSettingsParams.args ) : {} );
	 return (
		<>
			{ args.sidebar && (
				<>
					{ Object.keys( args.sidebar ).map( function( key, index ) {
						const help = args.sidebar[key];
						return (
							<div className="kadence-desk-help-inner-wrap">
								{ help?.title && (
									<h2>{ help.title }</h2>
								) }
								{ help?.description && (
									<p>{ help.description }</p>
								) }
								{ help?.link && help?.link_text && (
									<a href={ help.link } className="sidebar-link" target="_blank">{ help.link_text }</a>
								) }
							</div>
						);
					} ) }
				</>
			) }
		</>
	);
};
 
 export default withFilters( 'kadence_settings_help' )( Help );