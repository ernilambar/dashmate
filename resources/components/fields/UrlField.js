import React from 'react';
import FieldWrapper from './FieldWrapper';

export default function UrlField( { label, description, value, onChange, fieldKey } ) {
	return (
		<FieldWrapper label={ label } description={ description }>
			<input
				type="url"
				value={ value || '' }
				onChange={ ( e ) => onChange( fieldKey, e.target.value ) }
			/>
		</FieldWrapper>
	);
}
