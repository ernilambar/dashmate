import React, { useState, useEffect } from 'react';
import GenericSettingsForm from './GenericSettingsForm';

// Simple layout settings component using the existing GenericSettingsForm
export default function LayoutSettings( { onClose, dashboardId = 'main' } ) {
	// Load all settings from localStorage with defaults
	const loadSettings = () => {
		const storageKey = `dashmate_layout_settings_${ dashboardId }`;
		const storedSettings = localStorage.getItem( storageKey );
		const defaultSettings = {
			max_columns: '2',
		};

		return storedSettings
			? { ...defaultSettings, ...JSON.parse( storedSettings ) }
			: defaultSettings;
	};

	const [ values, setValues ] = useState( loadSettings );

	// Simple schema - only max_columns field for now, but easily extensible
	const schema = {
		max_columns: {
			type: 'buttonset',
			label: 'Columns Number',
			description: 'Maximum number of columns to display in the dashboard.',
			choices: [
				{ label: 'One', value: '1' },
				{ label: 'Two', value: '2' },
				{ label: 'Three', value: '3' },
				{ label: 'Four', value: '4' },
			],
		},
	};

	const handleChange = async ( newValues, needsRefresh ) => {
		console.log( 'Layout settings changed:', newValues );

		// Update local state
		const updatedValues = { ...values, ...newValues };
		setValues( updatedValues );

		// Save all settings to localStorage
		const storageKey = `dashmate_layout_settings_${ dashboardId }`;
		localStorage.setItem( storageKey, JSON.stringify( updatedValues ) );

		// If refresh is needed, reload the page
		if ( needsRefresh ) {
			window.location.reload();
		}
	};

	return (
		<GenericSettingsForm
			schema={ schema }
			values={ values }
			onChange={ handleChange }
			onClose={ onClose }
			title="Layout Settings"
			showRemoveButton={ false }
			showCancelButton={ true }
		/>
	);
}
