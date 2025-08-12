import React from 'react';
import FieldWrapper from './FieldWrapper';

export default function RepeaterField( {
	label,
	description,
	value,
	onChange,
	fieldKey,
	fields = {},
} ) {
	const currentValue = value || [];

	const handleAddItem = () => {
		const newItem = {};
		Object.entries( fields ).forEach( ( [ subKey, subSchema ] ) => {
			newItem[ subKey ] = subSchema.default || '';
		} );
		onChange( fieldKey, [ ...currentValue, newItem ] );
	};

	const handleRemoveItem = ( index ) => {
		const newArr = [ ...currentValue ];
		newArr.splice( index, 1 );
		onChange( fieldKey, newArr );
	};

	const handleSubFieldChange = ( itemIndex, subKey, newValue ) => {
		const newArr = [ ...currentValue ];
		newArr[ itemIndex ] = { ...newArr[ itemIndex ], [ subKey ]: newValue };
		onChange( fieldKey, newArr );
	};

	// Recursive function to render sub-fields
	const renderSubField = ( subKey, subSchema, subValue, itemIndex ) => {
		// This would need to be implemented based on the field type
		// For now, we'll use a simple text input
		return (
			<div key={ subKey } style={ { marginBottom: 8 } }>
				<label>{ subSchema.label }</label>
				<input
					type="text"
					value={ subValue || '' }
					onChange={ ( e ) => handleSubFieldChange( itemIndex, subKey, e.target.value ) }
				/>
			</div>
		);
	};

	return (
		<FieldWrapper label={ label } description={ description }>
			{ currentValue.map( ( item, idx ) => (
				<div
					key={ idx }
					style={ { marginBottom: 8, border: '1px solid #eee', padding: 8 } }
				>
					{ Object.entries( fields ).map( ( [ subKey, subSchema ] ) =>
						renderSubField( subKey, subSchema, item[ subKey ], idx )
					) }
					<button type="button" onClick={ () => handleRemoveItem( idx ) }>
						Remove
					</button>
				</div>
			) ) }
			<button type="button" onClick={ handleAddItem }>
				Add
			</button>
		</FieldWrapper>
	);
}
