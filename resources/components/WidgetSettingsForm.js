import React from 'react';

// Renders a form based on a widget schema and current values
export default function WidgetSettingsForm( { schema, values, onChange, onClose, onSaveStatus } ) {
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
		switch ( fieldSchema.type ) {
			case 'text':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<input
							type="text"
							value={ value || '' }
							onChange={ ( e ) => handleFieldChange( key, e.target.value ) }
						/>
					</div>
				);
			case 'url':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<input
							type="url"
							value={ value || '' }
							onChange={ ( e ) => handleFieldChange( key, e.target.value ) }
						/>
					</div>
				);
			case 'checkbox':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>
							<input
								type="checkbox"
								checked={ !! value }
								onChange={ ( e ) => handleFieldChange( key, e.target.checked ) }
							/>
							{ fieldSchema.label }
						</label>
					</div>
				);
			case 'select':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<select
							value={ value || fieldSchema.default }
							onChange={ ( e ) => handleFieldChange( key, e.target.value ) }
						>
							{ fieldSchema.options.map( ( opt ) => (
								<option key={ opt.value } value={ opt.value }>
									{ opt.label }
								</option>
							) ) }
						</select>
					</div>
				);
			case 'number':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<div style={ { display: 'flex', gap: '8px', alignItems: 'center' } }>
							<input
								type="number"
								min={ fieldSchema.min }
								max={ fieldSchema.max }
								value={ value || fieldSchema.default || '' }
								onChange={ ( e ) =>
									handleFieldChange( key, parseInt( e.target.value ) || 0 )
								}
								style={ { flex: 1 } }
							/>
							{ fieldSchema.presets && fieldSchema.presets.length > 0 && (
								<div style={ { display: 'flex', gap: '4px' } }>
									{ fieldSchema.presets.map( ( preset ) => (
										<button
											key={ preset }
											type="button"
											onClick={ () => handleFieldChange( key, preset ) }
											style={ {
												padding: '4px 8px',
												fontSize: '12px',
												border: '1px solid #ddd',
												backgroundColor:
													( value || fieldSchema.default ) === preset
														? '#0073aa'
														: '#f8f9fa',
												color:
													( value || fieldSchema.default ) === preset
														? 'white'
														: '#333',
												borderRadius: '3px',
												cursor: 'pointer',
												transition: 'all 0.2s ease',
											} }
											title={ `Set to ${ preset }` }
										>
											{ preset }
										</button>
									) ) }
								</div>
							) }
						</div>
					</div>
				);
			case 'repeater':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						{ ( value || [] ).map( ( item, idx ) => (
							<div
								key={ idx }
								style={ { marginBottom: 8, border: '1px solid #eee', padding: 8 } }
							>
								{ Object.entries( fieldSchema.fields ).map(
									( [ subKey, subSchema ] ) =>
										renderField(
											`${ key }.${ idx }.${ subKey }`,
											subSchema,
											item[ subKey ]
										)
								) }
								<button
									type="button"
									onClick={ () => {
										const newArr = [ ...value ];
										newArr.splice( idx, 1 );
										handleFieldChange( key, newArr );
									} }
								>
									Remove
								</button>
							</div>
						) ) }
						<button
							type="button"
							onClick={ () => {
								const newItem = {};
								Object.entries( fieldSchema.fields ).forEach(
									( [ subKey, subSchema ] ) => {
										newItem[ subKey ] = subSchema.default || '';
									}
								);
								handleFieldChange( key, [ ...( value || [] ), newItem ] );
							} }
						>
							Add
						</button>
					</div>
				);
			default:
				return null;
		}
	};

	return (
		<div>
			<form>
				{ Object.entries( schema ).map( ( [ key, fieldSchema ] ) =>
					renderField( key, fieldSchema, localValues[ key ] )
				) }
			</form>

			<div style={ { marginTop: 16, textAlign: 'right' } }>
				<button
					type="button"
					onClick={ handleSave }
					disabled={ isSaving }
					style={ {
						backgroundColor: '#0073aa',
						color: 'white',
						border: 'none',
						padding: '8px 16px',
						borderRadius: 4,
						cursor: isSaving ? 'not-allowed' : 'pointer',
						opacity: isSaving ? 0.6 : 1,
					} }
				>
					{ isSaving ? 'Saving...' : 'Save Settings' }
				</button>
			</div>
		</div>
	);
}
