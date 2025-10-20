/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
 import { TabPanel, Panel, PanelBody, withFilters } from '@wordpress/components';
 import kadenceTryParseJSON from './components/common/try-parse';
 export const StartedTab = () => {
	const started = ( kadenceSettingsParams.started ? kadenceTryParseJSON( kadenceSettingsParams.started ) : {} );
	 return (
		<>
			{ started && (
				<div className="kadence-desk-help-inner">
					{ started.title && (
						<h2>{ started.title }</h2>
					) }
					{ started.description && (
						<p>{ started.description }</p>
					) }
					{ started.video_url && (
						<div className="video-container">
							<iframe width="560" height="315" src={ started.video_url } frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
						</div>
					) }
					{ started.link_url && started.link_text && (
						<a href={ started.link_url } className="kadence-desk-button kadence-desk-button-second" target="_blank">{ started.link_text }</a>
					) }
				</div>
			) }
		</>
	);
};
 
 export default withFilters( 'kadence_settings_started' )( StartedTab );