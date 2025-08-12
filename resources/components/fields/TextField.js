import React from 'react';
import FieldWrapper from './FieldWrapper';

export default function TextField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper label={ label } description={ description } fieldType="text">
			<input
				type="text"
				value={ value || '' }
				onChange={ ( e ) => onChange( fieldKey, e.target.value ) }
			/>
		</FieldWrapper>
	);
}
