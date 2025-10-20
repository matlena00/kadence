/**
 * WordPress dependencies
 */
import { createContext, useReducer, useContext } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import kadenceTryParseJSON from '../components/common/try-parse';

const initialState = {
	settings: ( kadenceSettingsParams.settings ? kadenceTryParseJSON( kadenceSettingsParams.settings ) : {} ),
	saveStatus: false,
};

const KadenceSettingsContext = createContext();

function kadenceSettingsReducer(state, action) {
	switch (action.type) {
		case 'SET_SETTINGS':
			return {
				...state,
				settings: action.payload,
			};
		case 'SET_SAVING_STATUS':
			return {
				...state,
				saveStatus: action.payload,
			};

		default:
			return state;
	}
}

function initializeKadenceSettingsState(data) {
	return {
		...initialState,
		...data,
	};
}

function KadenceSettingsProvider(props) {
	const [state, dispatch] = useReducer(kadenceSettingsReducer, props.value, initializeKadenceSettingsState);
	const value = { state, dispatch };

	return <KadenceSettingsContext.Provider value={value}>{props.children}</KadenceSettingsContext.Provider>;
}

function useKadenceSettings() {
	const context = useContext(KadenceSettingsContext);

	if (context === undefined) {
		throw new Error('useKadenceSettings must be used with KadenceSettingsProvider');
	}

	return context;
}

export { KadenceSettingsProvider, useKadenceSettings };
