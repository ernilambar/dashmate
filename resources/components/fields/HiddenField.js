import React from 'react';

export default function HiddenField( { value, onChange, fieldKey, defaultValue } ) {
	return (
		<input
			type="hidden"
			value={ value || defaultValue || '' }
			onChange={ ( e ) => onChange( fieldKey, e.target.value ) }
		/>
	);
}
