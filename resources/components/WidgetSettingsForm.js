import React from 'react';
import Icon from './Icon';
import {
	TextField,
	UrlField,
	CheckboxField,
	SelectField,
	NumberField,
	RadioField,
	ButtonsetField,
	MultiCheckField,
	HiddenField,
	RepeaterField,
	SortableField,
} from './fields';

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
	const [ localValues, setLocalValues ] = React.useState( values );
	const [ isSaving, setIsSaving ] = React.useState( false );

	// Update local values when props change
	React.useEffect( () => {
		setLocalValues( values );
	}, [ values ] );

	const handleFieldChange = ( key, newValue ) => {
		const newLocalValues = { ...localValues, [ key ]: newValue };
		setLocalValues( newLocalValues );

		// For fields that don't require refresh, apply changes immediately
		if ( schema[ key ]?.refresh !== true ) {
			onChange( newLocalValues, false );
		}
	};

	const handleSave = async () => {
		setIsSaving( true );

		try {
			// Only save fields that require refresh (have changed and refresh: true)
			const changedFields = Object.keys( localValues ).filter(
				( key ) => localValues[ key ] !== values[ key ]
			);

			const refreshFields = changedFields.filter(
				( key ) => schema[ key ]?.refresh === true
			);
			const needsRefresh = refreshFields.length > 0;

			// If no fields have changed, treat as success
			if ( changedFields.length === 0 ) {
				// Notify parent component of success status
				if ( onSaveStatus ) {
					onSaveStatus( 'success' );
				}
				// Close the settings form immediately
				if ( onClose ) {
					onClose();
				}
				return;
			}

			// Create settings object with all changed fields
			const settingsToSave = {};
			changedFields.forEach( ( key ) => {
				settingsToSave[ key ] = localValues[ key ];
			} );

			await onChange( settingsToSave, needsRefresh );
			// Notify parent component of success status
			if ( onSaveStatus ) {
				onSaveStatus( 'success' );
			}
			// Close the settings form immediately
			if ( onClose ) {
				onClose();
			}
		} catch ( error ) {
			// Notify parent component of error status
			if ( onSaveStatus ) {
				onSaveStatus( 'error' );
			}
		} finally {
			setIsSaving( false );
		}
	};

	// Helper to render a single field
	const renderField = ( key, fieldSchema, value ) => {
		const commonProps = {
			key,
			value,
			onChange: handleFieldChange,
			fieldKey: key,
		};

		switch ( fieldSchema.type ) {
			case 'text':
				return (
					<TextField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
					/>
				);
			case 'url':
				return (
					<UrlField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
					/>
				);
			case 'checkbox':
				return (
					<CheckboxField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
					/>
				);
			case 'select':
				return (
					<SelectField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'number':
				return (
					<NumberField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						min={ fieldSchema.min }
						max={ fieldSchema.max }
						defaultValue={ fieldSchema.default }
						choices={ fieldSchema.choices }
					/>
				);
			case 'radio':
				return (
					<RadioField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'buttonset':
				return (
					<ButtonsetField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'multi-check':
				return (
					<MultiCheckField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'sortable':
				return (
					<SortableField
						key={ key }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices || [] }
						value={
							Array.isArray( value )
								? value
								: Array.isArray( fieldSchema.default )
								? fieldSchema.default
								: []
						}
						onChange={ ( newValue ) => handleFieldChange( key, newValue ) }
					/>
				);
			case 'repeater':
				return (
					<RepeaterField
						{ ...commonProps }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						fields={ fieldSchema.fields }
					/>
				);
			case 'hidden':
				return <HiddenField { ...commonProps } defaultValue={ fieldSchema.default } />;
			default:
				return null;
		}
	};

	const handleRemoveWidget = () => {
		if ( onRemove && widgetId ) {
			// Show confirmation dialog
			if (
				window.confirm(
					'Are you sure you want to remove this widget? This action cannot be undone.'
				)
			) {
				onRemove( widgetId );
			}
		}
	};

	// Check if there are any settings fields
	const hasSettingsFields = Object.keys( schema ).length > 0;

	return (
		<div>
			<div className="widget-settings-header">
				<h4>Widget Settings</h4>
			</div>

			{ hasSettingsFields ? (
				<form>
					{ Object.entries( schema ).map( ( [ key, fieldSchema ] ) =>
						renderField( key, fieldSchema, localValues[ key ] )
					) }
				</form>
			) : (
				<div className="widget-no-settings">
					<p>No settings available for this widget.</p>
				</div>
			) }

			<div className="widget-settings-actions">
				{ hasSettingsFields && (
					<button
						type="button"
						className="widget-save-button"
						onClick={ handleSave }
						disabled={ isSaving }
					>
						{ isSaving ? 'Saving...' : 'Save Settings' }
					</button>
				) }
				<button
					type="button"
					className="widget-remove-button"
					onClick={ handleRemoveWidget }
					title="Remove Widget"
				>
					<Icon name="close" size="small" />
				</button>
			</div>
		</div>
	);
}
