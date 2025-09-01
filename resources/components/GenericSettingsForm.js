import React from 'react';
import { Button } from '@wordpress/components';
import Icon from './Icon';
import {
	TextField,
	EmailField,
	PasswordField,
	UrlField,
	CheckboxField,
	SelectField,
	NumberField,
	RadioField,
	ButtonsetField,
	MulticheckboxField,
	ToggleField,
	HiddenField,
	SortableField,
	TextareaField,
} from './fields';

// Generic settings form that can be used for widgets, layouts, or any other settings
export default function GenericSettingsForm( {
	schema,
	values,
	onChange,
	onClose,
	onSaveStatus,
	onRemove,
	title = 'Settings',
	showRemoveButton = false,
	removeButtonText = 'Remove',
	removeButtonTitle = 'Remove Item',
	itemId,
	showCancelButton = false,
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
			value,
			onChange: handleFieldChange,
			fieldKey: key,
		};

		switch ( fieldSchema.type ) {
			case 'text':
				return (
					<TextField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						placeholder={ fieldSchema.placeholder }
					/>
				);
			case 'email':
				return (
					<EmailField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						placeholder={ fieldSchema.placeholder }
					/>
				);
			case 'password':
				return (
					<PasswordField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						placeholder={ fieldSchema.placeholder }
					/>
				);
			case 'url':
				return (
					<UrlField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						placeholder={ fieldSchema.placeholder }
					/>
				);
			case 'textarea':
				return (
					<TextareaField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						placeholder={ fieldSchema.placeholder }
					/>
				);
			case 'checkbox':
				return (
					<CheckboxField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						message={ fieldSchema.message }
					/>
				);
			case 'select':
				return (
					<SelectField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'number':
				return (
					<NumberField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
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
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'buttonset':
				return (
					<ButtonsetField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'multicheckbox':
				return (
					<MulticheckboxField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						choices={ fieldSchema.choices }
						defaultValue={ fieldSchema.default }
					/>
				);
			case 'toggle':
				return (
					<ToggleField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						label={ fieldSchema.label }
						description={ fieldSchema.description }
						message={ fieldSchema.message }
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
			case 'hidden':
				return (
					<HiddenField
						key={ key }
						value={ commonProps.value }
						onChange={ commonProps.onChange }
						fieldKey={ commonProps.fieldKey }
						defaultValue={ fieldSchema.default }
					/>
				);
			default:
				return null;
		}
	};

	const handleRemove = () => {
		if ( onRemove && itemId ) {
			// Show confirmation dialog
			if (
				window.confirm(
					`Are you sure you want to ${ removeButtonText.toLowerCase() } this item? This action cannot be undone.`
				)
			) {
				onRemove( itemId );
			}
		}
	};

	// Check if there are any settings fields
	const hasSettingsFields = Object.keys( schema ).length > 0;

	return (
		<div>
			<div className="settings-header">
				<h4>{ title }</h4>
			</div>

			{ hasSettingsFields ? (
				<form>
					{ Object.entries( schema ).map( ( [ key, fieldSchema ] ) =>
						renderField( key, fieldSchema, localValues[ key ] )
					) }
				</form>
			) : (
				<div className="no-settings">
					<p>No settings available for this item.</p>
				</div>
			) }

			<div className="settings-actions">
				{ hasSettingsFields && (
					<Button
						isPrimary
						onClick={ handleSave }
						disabled={ isSaving }
						className="save-button"
					>
						{ isSaving ? 'Saving...' : 'Save Settings' }
					</Button>
				) }
				{ showRemoveButton && (
					<Button
						isDestructive
						onClick={ handleRemove }
						title={ removeButtonTitle }
						className="remove-button"
					>
						<Icon name="close-line" library="remixicon" size="sm" />
					</Button>
				) }
				{ showCancelButton && (
					<Button
						isDestructive
						onClick={ onClose }
						title="Cancel"
						className="remove-button"
					>
						<Icon name="close-line" library="remixicon" size="sm" />
					</Button>
				) }
			</div>
		</div>
	);
}
