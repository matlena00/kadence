/**
 * WordPress dependencies
 */
 import { Spinner } from '@wordpress/components';
 import { useKadenceSettings } from './data/context';
 export default function SaveOverlay() {
	const { state, dispatch } = useKadenceSettings();
	const { saveStatus } = state;
	 return (
		<>
			{ saveStatus && (
				<div className="kadence-settings-saving-overlay">
					<Spinner />
				</div>
			) }
		</>
	 );
}