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
							{ fieldSchema.choices &&
								Array.isArray( fieldSchema.choices ) &&
								fieldSchema.choices.map( ( choice ) => (
									<option key={ choice.value } value={ choice.value }>
										{ choice.label }
									</option>
								) ) }
						</select>
					</div>
				);
			case 'number':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<div className="number-with-choices">
							<input
								type="number"
								min={ fieldSchema.min }
								max={ fieldSchema.max }
								value={ value || fieldSchema.default || '' }
								onChange={ ( e ) =>
									handleFieldChange( key, parseInt( e.target.value ) || 0 )
								}
							/>
							{ fieldSchema.choices &&
								Array.isArray( fieldSchema.choices ) &&
								fieldSchema.choices.length > 0 && (
									<div className="number-choices">
										{ fieldSchema.choices.map( ( choice ) => {
											// Support both simple values and {value, label} objects
											const choiceValue =
												typeof choice === 'object' ? choice.value : choice;
											const choiceLabel =
												typeof choice === 'object' ? choice.label : choice;
											const isActive =
												( value || fieldSchema.default ) === choiceValue;

											return (
												<button
													key={ choiceValue }
													type="button"
													onClick={ () =>
														handleFieldChange( key, choiceValue )
													}
													className={ isActive ? 'active' : '' }
													title={ `Set to ${ choiceLabel }` }
												>
													{ choiceLabel }
												</button>
											);
										} ) }
									</div>
								) }
						</div>
					</div>
				);
			case 'radio':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<div style={ { marginTop: 4 } }>
							{ fieldSchema.choices &&
								Array.isArray( fieldSchema.choices ) &&
								fieldSchema.choices.length > 0 &&
								fieldSchema.choices.map( ( choice ) => (
									<label
										key={ choice.value }
										style={ {
											display: 'block',
											marginBottom: 4,
											cursor: 'pointer',
										} }
									>
										<input
											type="radio"
											name={ key }
											value={ choice.value }
											checked={
												( value || fieldSchema.default ) === choice.value
											}
											onChange={ ( e ) =>
												handleFieldChange( key, e.target.value )
											}
											style={ { marginRight: 6 } }
										/>
										{ choice.label }
									</label>
								) ) }
							{ ( ! fieldSchema.choices ||
								! Array.isArray( fieldSchema.choices ) ||
								fieldSchema.choices.length === 0 ) && (
								<div
									style={ {
										color: '#666',
										fontStyle: 'italic',
										fontSize: '12px',
									} }
								>
									No choices defined for radio field
								</div>
							) }
						</div>
					</div>
				);
			case 'buttonset':
				return (
					<div key={ key } style={ { marginBottom: 12 } }>
						<label>{ fieldSchema.label }</label>
						<div className="buttonset-container">
							{ fieldSchema.choices &&
								Array.isArray( fieldSchema.choices ) &&
								fieldSchema.choices.length > 0 &&
								fieldSchema.choices.map( ( choice, index ) => {
									const isActive =
										( value || fieldSchema.default ) === choice.value;

									return (
										<button
											key={ choice.value }
											type="button"
											onClick={ () => handleFieldChange( key, choice.value ) }
											className={ isActive ? 'active' : '' }
											title={ choice.label }
										>
											{ choice.label }
										</button>
									);
								} ) }
							{ ( ! fieldSchema.choices ||
								! Array.isArray( fieldSchema.choices ) ||
								fieldSchema.choices.length === 0 ) && (
								<div
									style={ {
										color: '#666',
										fontStyle: 'italic',
										fontSize: '12px',
									} }
								>
									No choices defined for buttonset field
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
