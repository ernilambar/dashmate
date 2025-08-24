import React from 'react';
import GenericSettingsForm from './GenericSettingsForm';

// Renders a form based on a widget schema and current values
export default function WidgetSettingsForm( {
	schema,
	values,
	onChange,
	onClose,
	onSaveStatus,
	onRemove,
	widgetId,
} ) {
	return (
		<GenericSettingsForm
			schema={ schema }
			values={ values }
			onChange={ onChange }
			onClose={ onClose }
			onSaveStatus={ onSaveStatus }
			onRemove={ onRemove }
			itemId={ widgetId }
			title="Widget Settings"
			showRemoveButton={ true }
			removeButtonText="Remove Widget"
			removeButtonTitle="Remove Widget"
		/>
	);
}
