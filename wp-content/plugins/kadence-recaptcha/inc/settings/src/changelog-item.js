/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { withFilters } from '@wordpress/components';
 
 export const ChangelogItem = ( version ) => {
	 return (
		 <div className="changelog-version">
			 <h3 className="version-head">{ version.item.head }</h3>
			 { version.item.add && (
				 <>
					 { version.item.add.map( ( adds, index ) => {
						 return <div className="version-add">{ adds }</div>;
					 } ) }
				 </>
			 ) }
			 { version.item.update && (
				 <>
					 { version.item.update.map( ( updates, index ) => {
						 return <div className="version-update">{ updates }</div>;
					 } ) }
				 </>
			 ) }
			 { version.item.fix && (
				 <>
					 { version.item.fix.map( ( fixes, index ) => {
						 return <div className="version-fix">{ fixes }</div>;
					 } ) }
				 </>
			 ) }
		 </div>
	 );
 };
 
 export default withFilters( 'kadence_settings_changelog' )( ChangelogItem );