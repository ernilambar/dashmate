import React from 'react';

// Renders a form based on a widget schema and current values
export default function WidgetSettingsForm( { schema, values, onChange } ) {
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
							onChange={ ( e ) => onChange( { ...values, [ key ]: e.target.value } ) }
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
							onChange={ ( e ) => onChange( { ...values, [ key ]: e.target.value } ) }
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
								onChange={ ( e ) =>
									onChange( { ...values, [ key ]: e.target.checked } )
								}
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
							onChange={ ( e ) => onChange( { ...values, [ key ]: e.target.value } ) }
						>
							{ fieldSchema.options.map( ( opt ) => (
								<option key={ opt.value } value={ opt.value }>
									{ opt.label }
								</option>
							) ) }
						</select>
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
										onChange( { ...values, [ key ]: newArr } );
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
								onChange( { ...values, [ key ]: [ ...( value || [] ), newItem ] } );
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
		<form>
			{ Object.entries( schema ).map( ( [ key, fieldSchema ] ) =>
				renderField( key, fieldSchema, values[ key ] )
			) }
		</form>
	);
}
